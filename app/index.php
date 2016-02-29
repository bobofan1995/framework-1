<?php
define('APP_PATH', dirname(__DIR__));
define('VENDOR_PATH', APP_PATH.'/vendor');

$config = require(APP_PATH . '/config/main.php');
include(VENDOR_PATH . '/YC.php');
YC::run($config);