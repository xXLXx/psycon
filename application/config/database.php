<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'production';
$query_builder = TRUE;

if(!empty($_SERVER['ENVIRONMENT'])){
    $active_group = $_SERVER['ENVIRONMENT'];
}

//--- Production Credentials (CURRENTLY DOESN'T EXIST)
$db['production']['hostname'] = 'localhost';
$db['production']['username'] = '';
$db['production']['password'] = '';
$db['production']['database'] = '';
$db['production']['dbdriver'] = 'mysql';
$db['production']['dbprefix'] = '';
$db['production']['pconnect'] = FALSE;
$db['production']['db_debug'] = TRUE;
$db['production']['cache_on'] = FALSE;
$db['production']['cachedir'] = '';
$db['production']['char_set'] = 'utf8';
$db['production']['dbcollat'] = 'utf8_general_ci';
$db['production']['swap_pre'] = '';
$db['production']['autoinit'] = TRUE;
$db['production']['stricton'] = FALSE;

//--- Staging Credentials (dev.psychic-contact.com)
$db['development']['hostname'] = 'localhost';
$db['development']['username'] = 'tst5_main';
$db['development']['password'] = 'lkp9X8B4';
$db['development']['database'] = 'tst5_main';
$db['development']['dbdriver'] = 'mysql';
$db['development']['dbprefix'] = '';
$db['development']['pconnect'] = FALSE;
$db['development']['db_debug'] = TRUE;
$db['development']['cache_on'] = FALSE;
$db['development']['cachedir'] = '';
$db['development']['char_set'] = 'utf8';
$db['development']['dbcollat'] = 'utf8_general_ci';
$db['development']['swap_pre'] = '';
$db['development']['autoinit'] = TRUE;
$db['development']['stricton'] = FALSE;

//--- Woodjh Configuration
$db['woodjh']['hostname'] = 'owws.com';
$db['woodjh']['username'] = 'psycon';
$db['woodjh']['password'] = '****';
$db['woodjh']['database'] = 'psycon';
$db['woodjh']['dbdriver'] = 'mysql';
$db['woodjh']['dbprefix'] = '';
$db['woodjh']['pconnect'] = FALSE;
$db['woodjh']['db_debug'] = TRUE;
$db['woodjh']['cache_on'] = FALSE;
$db['woodjh']['cachedir'] = '';
$db['woodjh']['char_set'] = 'utf8';
$db['woodjh']['dbcollat'] = 'utf8_general_ci';
$db['woodjh']['swap_pre'] = '';
$db['woodjh']['autoinit'] = TRUE;
$db['woodjh']['stricton'] = FALSE;

//--- RCkehoe Configuration
$db['owws']['hostname'] = 'localhost';
$db['owws']['username'] = 'psycon';
$db['owws']['password'] = '****';
$db['owws']['database'] = 'psycon';
$db['owws']['dbdriver'] = 'mysql';
$db['owws']['dbprefix'] = '';
$db['owws']['pconnect'] = FALSE;
$db['owws']['db_debug'] = TRUE;
$db['owws']['cache_on'] = FALSE;
$db['owws']['cachedir'] = '';
$db['owws']['char_set'] = 'utf8';
$db['owws']['dbcollat'] = 'utf8_general_ci';
$db['owws']['swap_pre'] = '';
$db['owws']['autoinit'] = TRUE;
$db['owws']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */