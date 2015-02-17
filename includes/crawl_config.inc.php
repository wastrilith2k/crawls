<?php
    
// This will eventually be database driven

// ******* CONFIG *******
$depth = 2;
$throttle = 100;
$successful_crawls = 0;
$initial_url = "http://kiwitobes.com/";

// Requires ALL variations of the host
$whitelist = array(); 
$blacklist = array('amazon');
// ******* CONFIG *******