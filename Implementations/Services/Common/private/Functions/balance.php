<?php

/*
* Functionality that handles balances.
*/

/*
* Receives the given amount into the given hex address.
*/
function receiveLocked($hexAddress,$amount,$commodity,$entityID=0){
	
	global $dz;
	
	// Find that address - if it exists, it must be the same commodity. If it doesn't exist, create it now.
	$com=$dz->get_row('select `Commodity`,`Entity` from `Root.Balances` where `Key`=UNHEX("'.$hexAddress.'")');

	if($com){
		
		// Make sure it matches and so does the entity:
		if($com['Commodity']!=$commodity){
			
			// Wrong commodity!
			error('balance/wrongcommodity');
			
		}
		
		if($entityID!=0 && $com['Entity']!=$entityID){
			
			// Wrong owner!
			error('balance/wrongowner');
			
		}
		
		// Lock the amount now. This is because if the balance goes empty during this issue request
		// the balance row is deleted. If we lock the amount, that can't happen.
		$dz->query('update `Root.Balances` set LockedBalance=LockedBalance+'.$amount.' where `Key`=UNHEX("'.$hexAddress.'")');
		
	}else{
		
		// Create it now, with the locked amount:
		$dz->query('insert into `Root.Balances`(`Commodity`,`Key`,`Entity`,`LockedBalance`) values ("'.$commodity.'",UNHEX("'.$hexAddress.'"),'.$entityID.','.$amount.')');
		
	}
	
}

/*
* Gets an address to send to. The remote bank associates the address with the given username, reference and a title.
* The title is, for example, 'Power bill payment'.
* Optionally, the from username can be provided. Providing it allows the receiver to e.g. respond with vouchers.
* Not providing it means the customer isn't exposed to the merchant (or their bank), retaining their privacy.
* This function can also return a delay if a transaction is expected to take longer than usual.
* Note: Username, reference, fromUsername and name MUST all have been 
* safety checked and escaped before they enter this function.
*/
function getAddress(&$transferData){
	
	global $dz,$thisEntity;
	
	$username=$transferData['Username'];
	$reference=$transferData['Reference'];
	$title=$transferData['Title'];
	
	
	// Get the entity of the user being paid:
	$toPay=$dz->get_row('select `Entity` from `Root.Usernames` where `Username`="'.$username.'"');
	
	if(!$toPay){
		// Username was not found.
		error('username/notfound');
	}

	// Is the entity the same as this one?
	$entityID=$toPay['Entity'];

	// Transaction delay, if one is applicable.
	// This will almost always be zero representing that a transaction is expected to be in the 2s limit.
	$delay='0';

	if($entityID==$thisEntity['ID']){
		
		// Yes! This is an internal transaction (i.e. within this bank). No addressing required.
		$toGroup=$thisEntity['Group'];
		// return null;
	}
	
	// This is a transaction between banks. We'll need an address to send to:
	$entityInfo=$dz->get_row('select `Group`,`Endpoint` from `Root.Entities` where ID='.$entityID);
	
	if(!$entityInfo){
		
		// This server has out-of-date root information.
		serverError();
		
	}
	
	// Got a from username? (Optional):
	if(isset($transferData['FromUsername'])){
		// Add it:
		$privText=',"from":"'.$transferData['FromUsername'].'","name":"'.$transferData['Name'].'"';
	}else{
		$privText='';
	}
	
	// Send the username and reference pair to the username/send API.
	// In response we'll get an address to send to.
	$error;
	$remote=sendTo(
		$entityInfo['Endpoint'],
		'username/send',
		'{"username":"'.$username.'","reference":"'.escape($reference,false).'","title":"'.escape($title,false).'","items":'.$transferData['ItemInformation'].$privText.'}',
		$error
	);
	
	// Did it error?
	if($error){
		
		// Remote server errored.
		error('remote/error',$error);
		
	}
	
	// Update the to group:
	$transferData['ToGroup']=$entityInfo['Group'];
	
	// JSON decode it:
	$remote=json_decode($remote,true);
	
	// Get the address info:
	$addrInfo=safe('address',VALID_ARRAY,$remote);
	
	// Optional delay info:
	$delay=safe('delay',VALID_NUMBER,$remote,true);
	
	// Get the status:
	$status=safe('status',VALID_ALPHA,$addrInfo);
	
	if($status!='OK'){
		
		// Remote server was unable to provide an address - try again shortly.
		error('remote/noaddr',$delay);
		
	}
	
	// Great - should have an address here:
	$address=safe('value',VALID_HEX,$addrInfo);
	
	if($delay){
		$transferData['Delay']=$delay;
	}
	
	$transferData['ToAddress']=$address;
	
	return $address;
	
}

/*
* Attempts to perform a transaction now using the given transaction information.
* The information is a full row in the Bank.Transactions table, optionally plus a ToAddress and ToGroup field.
* ToAddress and ToGroup are otherwise added using the Username field.
* This triggers 400 errors if there is anything wrong. Otherwise, it returns true.
* All of the incoming fields must have been verified and cleaned.
* NOTE: This function is not fully crash safe at the moment.
*/
function transfer($details,$fromAccount=true){
	
	// The sending account and the database:
	global $verifiedAccount,$dz;
	
	if(!isset($details['ToAddress'])){
		
		// Get an address for the username (inline updates details):
		getAddress($details);
		
	}
	
	// Get the commodity:
	$commodity=$details['Commodity'];
	
	// The amount too:
	$amount=$details['Amount'];
	
	// Get the to address:
	$toAddress=$details['ToAddress'];
	
	// Is this an internal transfer? (I.e. within this same bank):
	$internalTransfer=(!$toAddress);
	
	$balance=null;
	
	// Is it coming from the bank itself, or from an account?
	if($fromAccount){
		
		// Find a suitable 'from' balance.
		// (There should only ever be one. Just in case though, we don't want to lock more than one row):
		$balance=$dz->get_row('select `ID` from `Bank.Account.Balances` where `Account`='.$verifiedAccount.' and `Commodity`="'.$commodity.'" and `Amount`>='.$amount);
		
		if(!$balance){
			
			// Not enough funds in the right currency.
			error('account/nofunds');
			
		}
		
		// Lock the amount in that balance row.
		// If a locked amount is present at startup then a crash occured during a transaction.
		// If it's severe, the balances can be rebuilt from the transaction history.
		$locked=$dz->query(
			'update `Bank.Account.Balances` set `LockedAmount`=`LockedAmount`+'.$amount.
			',`Amount`=`Amount`-'.$amount.' where `ID`='.$balance['ID'].' and `Amount`>='.$amount
		);
		
		// Did we successfully lock?
		if(!$locked){
			
			// Not enough funds in the right currency.
			error('account/nofunds');
			
		}
		
		// Ok! We've locked the balance.
			
		// Create a transaction row:
		$dz->query(
			'insert into `Bank.Transactions`(`Account`,`Type`,`Reference`,`ItemInformation`,`Amount`,`Commodity`,`Username`,`Title`,`TimeAt`,`Name`) values('.
			$verifiedAccount.',2,"'.escape($details['Reference'],false).'","'.escape($details['ItemInformation'],false).'",'.$amount.',"'.$commodity.'","'.$details['Username'].'","'.escape($details['Title'],false).'",'.time().',"'.escape($details['Name'],false).'")');
		
	}
	
	if($internalTransfer){
		
		// It's an internal transaction.
		// Name needs to be changed first (it's the name of the sender, which we don't know at this point):
		$details['Name']='';
		
		// Update the receiving account:
		receive($details);
		
	}else{
		
		// Interbank transfer.
		// Need to select some suitable balances to use to send with.
		// For now, we'll just assume that a single balance can be found that has enough in it:
		$balanceRow=$dz->get_row('select `Bank.Balances`.`Key`,`Bank.Balances`.`Private`,`Root.Balances`.`Balance` from `Bank.Balances` left join `Root.Balances` on `Root.Balances`.`Key`=`Bank.Balances`.`Key` where `Root.Balances`.`Commodity`="'.$commodity.'" and `Root.Balances`.`Balance`>='.$amount);
		
		if(!$balanceRow){
			// This bank doesn't have a single balance with enough funds in it.
			// A more advanced implementation would combine multiple balances to ensure there's enough.
			// For now though, and to avoid making the consumer freak out, we'll just say the transaction amount is too high:
			error('amount/toohigh');
		}
		
		// Get the from address:
		$fromAddress=bin2hex($balanceRow['Key']);
		
		// Get the target group:
		$toGroup=$details['ToGroup'];
		
		if(strlen($toAddress)!=130){
			
			// It's binary.
			$toAddress=bin2hex($toAddress);
			
		}
		
		// Perform a global transfer now:
		globalTransfer($fromAddress,$toAddress,$toGroup,$amount,$balanceRow['Balance'],$balanceRow['Private']);
		
	}
	
	// Success!
	
	if($balance){
	
		// Now unlock the amount in the sender:
		$dz->query(
			'update `Bank.Account.Balances` set `LockedAmount`=`LockedAmount`-'.$amount.
			' where ID='.$balance['ID']
		);
	
	}
	
}

/*
* Performs a transaction from one of 'my' addresses to some other public address.
* Addresses are in hex, aside from the private key which is binary.
*/
function globalTransfer($fromAddress,$toAddress,$toGroup,$amount,$fromBalance,$private){
	
	// Request the root now:
	global $thisEntity;
	
	// Get the from group:
	$fromGroup=$thisEntity['Group'];
	
	// Build the signed data:
	$signed=$fromGroup.'/'.$fromAddress.'-'.$toGroup.'/'.$toAddress.'-'.$amount.'-'.$fromBalance;
	
	// Sign it:
	$signature=base64_encode( sign($signed,$private) );
	
	// Call the root API:
	$error;
	$response=callRoot(
		'transfer/create',
		'{"from":{"address":"'.$fromAddress.'","group":'.$fromGroup.',"balance":'.$fromBalance.'},"to":{"address":"'.$toAddress.'","group":'.$toGroup.'},"amount":'.$amount.',"signature":"'.$signature.'"}',
		$error
	);
	
	if($error){
		
		// Remote server generated an error:
		error('remote/error',$error);
		
	}
	
	// Success!
	return true;
	
}

/*
* Used when an account is receiving a transaction.
* As with all other functions, the data must've been escaped first.
*/
function receive($details,$accountID=0){
	
	global $dz;
	
	if($accountID==0){
		
		// Get the receiving username:
		$user=$details['Username'];
		
		// Get the account ID of the receiving user:
		$receiver=$dz->get_row('select `ID`,`FullName` from `Bank.Accounts` where `Username`="'.$user.'"');
		
		if(!$receiver){
			
			// We don't know of this account!
			error('username/notfound');
			
		}
		
		// Add name?
		if(isset($details['AddName']) && $details['AddName']){
			
			// Add their name too:
			$details['Name']=$receiver['FullName'];
			
		}
		
		// Get the account ID:
		$accountID=$receiver['ID'];
		
	}
	
	// The from username (optional):
	$fromUser=isset($details['FromUsername']) ? $details['FromUsername'] : '';
	
	// Get the commodity:
	$commodity=$details['Commodity'];
	
	// Get the amount:
	$amount=$details['Amount'];
	
	// Get or create the balance row now:
	$row=$dz->get_row('select `ID` from `Bank.Account.Balances` where `Account`='.$accountID.' and `Commodity`="'.$commodity.'"');
	
	// The row ID:
	$balanceID=0;
	
	if($row){
		
		// The row exists; get the ID:
		$balanceID=$row['ID'];
		
		// Update it's locked amount:
		$dz->query('update `Bank.Account.Balances` set `LockedAmount`=`LockedAmount`+'.$amount.' where ID='.$balanceID);
		
	}else{
		
		// Create the balance row:
		$dz->query('insert into `Bank.Account.Balances`(`Account`,`Commodity`,`LockedAmount`) values ('.$accountID.',"'.$commodity.'",'.$amount.')');
		
		// Update balanceID:
		$balanceID=$dz->insert_id();
		
	}
	
	// Create a transaction row:
	$dz->query(
		'insert into `Bank.Transactions`(`Account`,`Type`,`Reference`,`ItemInformation`,`Amount`,`Commodity`,`Username`,`Title`,`TimeAt`,`Name`) values('.
		$accountID.',1,"'.escape($details['Reference'],false).'","'.escape($details['ItemInformation'],false).'",'.$amount.',"'.$commodity.'","'.$fromUser.'","'.escape($details['Title'],false).'",'.time().',"'.escape($details['Name'],false).'")');
	
	// Unlock the balance; a LockedAmount of 0 essentially means the balance is valid (i.e. this completed without crashing):
	$dz->query('update `Bank.Account.Balances` set `LockedAmount`=`LockedAmount`-'.$amount.',`Amount`=`Amount`+'.$amount.' where ID='.$balanceID);
	
}

?>