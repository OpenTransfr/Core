<?php

// Subscription amount API (for a particular bank account).
postedTo();

if($verifiedAccount==0){
	
	// Account required!
	error('account/required');
	
}

// Which one?
$id=safe('id',VALID_NUMBER);

// Delete it:
$sub=$dz->query('delete from `Bank.Subscriptions` where `Account`='.$verifiedAccount.' and `ID`='.$id);

if(!$sub){
	
	// Not found:
	error('subscription/notfound');
	
}

?>