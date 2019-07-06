<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
|
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. For example,
| the database is not connected to automatically since no assumption
| is made regarding whether you intend to use it.  This file lets
| you globally define which systems you would like loaded with every
| request.
|
| -------------------------------------------------------------------
| Instructions
| -------------------------------------------------------------------
|
| These are the things you can load automatically:
|
| 1. Packages
| 2. Libraries
| 3. Helper files
| 4. Custom config files
| 5. Language files
| 6. Models
|
*/

/*
| -------------------------------------------------------------------
|  Auto-load Packges
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
|
*/

$autoload['packages'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
| These are the classes located in the system/libraries folder
| or in your application/libraries folder.
|
| Prototype:
|
|	$autoload['libraries'] = array('database', 'session', 'xmlrpc');
*/

$autoload['libraries'] = array('session','database','form_validation','vadmin','pagination','system_vars','encrypt');

/*
| 
| vAdministrator Load Modules Libraries
|
*/

$autoload['libraries'][] = 'vadmin_mods/Tb';
$autoload['libraries'][] = 'vadmin_mods/Tb';
$autoload['libraries'][] = 'vadmin_mods/Md5';
$autoload['libraries'][] = 'vadmin_mods/Rf';
$autoload['libraries'][] = 'vadmin_mods/Html';
$autoload['libraries'][] = 'vadmin_mods/Sb';
$autoload['libraries'][] = 'vadmin_mods/Rd';
$autoload['libraries'][] = 'vadmin_mods/Dt';
$autoload['libraries'][] = 'vadmin_mods/Date_time';
$autoload['libraries'][] = 'vadmin_mods/Dollar';
//$autoload['libraries'][] = 'vadmin_mods/M';
$autoload['libraries'][] = 'vadmin_mods/Fl';
$autoload['libraries'][] = 'vadmin_mods/lb';
$autoload['libraries'][] = 'vadmin_mods/Url';
$autoload['libraries'][] = 'vadmin_mods/Ta';
$autoload['libraries'][] = 'vadmin_mods/Bool';
$autoload['libraries'][] = 'vadmin_mods/Gd';
$autoload['libraries'][] = 'vadmin_mods/Attachment';

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['helper'] = array('url', 'file');
*/

$autoload['helper'] = array('url','cookie');


/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
|
*/

$autoload['config'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Language files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['language'] = array('lang1', 'lang2');
|
| NOTE: Do not include the "_lang" part of your file.  For example
| "codeigniter_lang.php" would be referenced as array('codeigniter');
|
*/

$autoload['language'] = array();


/*
| -------------------------------------------------------------------
|  Auto-load Models
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['model'] = array('model1', 'model2');
|
*/

$autoload['model'] = array('member','member_billing','site','readers','reader','member_funds','messages_model');


/* End of file autoload.php */
/* Location: ./application/config/autoload.php */