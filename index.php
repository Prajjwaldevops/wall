<?php
/**
 * Free Wallpaper Script
 *
 * Free Wallpaper Script by Vepa Halliyev is licensed under a Creative Commons Attribution-Share Alike 3.0 License.
 *
 * @package		Free Wallpaper Script
 * @author		Vepa Halliyev
 * @copyright	Copyright (c) 2009, Vepa Halliyev, veppa.com.
 * @license		http://www.veppa.com/free-wallpaper-script/
 * @link		http://www.veppa.com/free-wallpaper-script/
 * @since		Version 1.0
 * @filesource
 */
//  Directories --------------------------------------------------------------

define('FROG_ROOT', dirname(__FILE__));
define('CORE_ROOT', FROG_ROOT.'/sys'); 


define('APP_PATH',  CORE_ROOT.'/app');


define('SESSION_LIFETIME', 3600);
define('REMEMBER_LOGIN_LIFETIME', 1209600); // two weeks

define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_ACTION', 'index');

define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', null);
define('COOKIE_SECURE', false);


//  Init ---------------------------------------------------------------------
$config_file = CORE_ROOT.'/config.php';
if (!file_exists($config_file))
{
	require 'setup.php';
}
else
{
	require $config_file;
}



define('BASE_URL', URL_PUBLIC . (USE_MOD_REWRITE ? '': '?'));



include CORE_ROOT.'/Framework.php';



// TODO: check page cache here if required


// set auth and login controller alternatives
/*AutoLoader::addFile(array('AuthUser'=>APP_PATH.'/models/AuthUserSimple.php',
						'LoginController'=>APP_PATH.'/controllers/LoginSimpleController.php'
					));
*/


// set connections from config file
Record::$__CONNECTIONS__ = $connections;


use_helper('I18n');
// set locale from cookie
// TODO move this to autolocale and check cookie,browser there and autoset.
// I18n::setLocale(Flash::getCookie('lng'));
//I18n::setLocale('tr'); 



// profile route 
/*Dispatcher::addRoute(array(
	'/profile/:num/' => '/profile/index/$1',
	'/profile/:num/:any/:any/:any' => '/profile/$2/$1/$3/$4', // use this before second statement because it is more specific
	'/profile/:num/:any/:any' => '/profile/$2/$1/$3', // use this before second statement because it is more specific
	'/profile/:num/:any/' => '/profile/$2/$1/',
	
));*/

Dispatcher::addRoute(array(
	'/wallpaper/:any' => '/index/wallpaper/$1',
	'/download/:any' => '/index/download/$1',
	'/resize/' => '/index/resize/',
	'/tag/' => '/index/tag/',  
	'/tag/:any' => '/index/tag/$1',  
	'/l/' => '/index/indexOrder/l/$1',  
	'/d/' => '/index/indexOrder/d/$1',  
	'/h/' => '/index/indexOrder/h/$1',  
	'/r/' => '/index/indexOrder/r/$1',  
	'/l/:any' => '/index/indexOrder/l/$1',  
	'/d/:any' => '/index/indexOrder/d/$1',  
	'/h/:any' => '/index/indexOrder/h/$1',  
	'/r/:any' => '/index/indexOrder/r/$1'
));


// ready to go 
Dispatcher::dispatch();

	