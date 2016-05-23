<?php

/*
* Contains functions for dealing with intervals such as subscriptions or time windows.
*/

/*
* Computes the next UNIX time that the given interval will trigger at.
*/
function computeIntervalTime($intervalText,$relativeTo){
	
	if(!$intervalText){
		return 0;
	}
	
	// relativeTo is the starting time.
	// IntervalText is the text string, of the form:
	// xCy,!xCy
	// where:
	// ! means except
	// x is the interval
	// c is the type of thing, e.g. 2WD2 means every 2nd tuesday.
	// Must always round to the nearest in range value.
	// For example, *M31 is every last day of the month (every 31st day => always the last of the month).
	
	// *M means every month (on the start date/time unless otherwise specified)
	
	// TODO
	return 0;
	
}

?>