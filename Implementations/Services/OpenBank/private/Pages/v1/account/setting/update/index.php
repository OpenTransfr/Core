<?php

// Creates a setting (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Internal tag of this setting (e.g. readonly.overdraft):
$setting=trim ( strtolower( safe('setting',VALID_NAME) ) );

if($setting==''){
	
	// Setting required.
	error('field/invalid','setting');
	
}

// If the tag starts with 'readonly' then we can't set it.
if(substr($setting, 0, 9)=='readonly.'){
	
	// Can't modify this setting.
	error('setting/readonly');
	
}

// Value:
$value=safe('value',VALID_TEXT);

// Does the setting already exist?
$row=$dz->get_row('select `ID` from `Bank.Account.Settings` where `Account`='.$verifiedAccount.' and `Setting`="'.$setting.'"');

if($row){
	
	// Get the ID:
	$id=$row['ID'];
	
	if($value==''){
		
		// Delete it:
		$dz->query('delete from `Bank.Account.Settings` where ID='.$id);
		
	}else{
	
		// Update the row:
		$dz->query('update `Bank.Account.Settings` set `Value`="'.$value.'" where `ID`='.$id);
		
	}
	
}else{
	
	// Create it now:
	$dz->query('insert into `Bank.Account.Settings`(`Setting`,`Value`,`Account`) values("'.
		$tag.'","'.$value.'",'.$verifiedAccount.')');
	
	// Get the ID:
	$id=$dz->insert_id();

}

// Ok!
echo '{"id":'.$id.'}';

?>