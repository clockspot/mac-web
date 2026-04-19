<?php
date_default_timezone_set('America/New_York');

$showErrors = isset($_REQUEST['showerrors']) ? 1 : 0;
error_reporting($showErrors ? E_ALL : 0);
ini_set('display_errors', $showErrors ? 1 : 0);

define('SITE_TITLE', 'mac-web');

define('DBHOST', 'localhost');
define('DBUSER', 'youruser');
define('DBPASS', 'yourpass');
define('DBBASE', 'yourdb');

define('RILEY_TABLE', 'riley');
