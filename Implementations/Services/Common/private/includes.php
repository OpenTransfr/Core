<?php

/*
* Includes functionality.
*/

// Commodity tag functions:
include('../private/Functions/commodities.php');

// MySQL database functions:
include('../private/Functions/database.php');

// Cryptographic sign/verify wrappers:
include('../private/Functions/cryptography.php');

// Functions for dealing with the user input:
include('../private/Functions/input.php');

// Interval functionality:
include('../private/Functions/interval.php');

// Functions for obtaining majority:
include('../private/Functions/majority.php');

// Functions for formatting the output (including errors):
include('../private/Functions/output.php');

// Functions for forwarding to root group:
include('../private/Functions/root.php');

// GET/ POST functions:
include('../private/Functions/web.php');

// Functions for dealing with the change log:
include('../private/Functions/change.php');

// Functions for dealing with the change log:
include('../private/Functions/balance.php');

// Page caching functionality (Non-essential):
include('../private/Functions/caching.php');

// Country functionality:
include('../private/Functions/country.php');

?>