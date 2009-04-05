<?php

if ( ! defined('SCB_DIR') ) :

define('SCB_DIR', dirname(__FILE__));
define('SCB_CLASSES', implode(',', array('scbForms', 'scbOptions', 'scbOptionsPage', 'scbBoxesPage', 'scbWidget')));

function scb_autoload($className) {
//	if ( substr($className, 0, 3) != 'scb' )
	if ( ! in_array($className, explode(',', SCB_CLASSES)) )
		return false;

	$fname = SCB_DIR . DIRECTORY_SEPARATOR . substr($className, 3) . '.php';

	if ( ! @file_exists($fname) )
		return false;

	include_once($fname);
	return true;
}

if ( function_exists('spl_autoload_register') )
	// Load classes when needed
	spl_autoload_register('scb_autoload');
else
	// Load all classes manually
	foreach ( explode(',', SCB_CLASSES) as $class )
		scb_autoload($class);

endif;
