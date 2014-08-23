<?
/**
 * drakoon-php config
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0
 */
define( 'SET_SITE_MAINTENANCE', '0' );

$a_setSiteMaintenanceException = array('127.0.0.1');

define( 'SET_DEBUG', 0 );

define( 'SET_CACHE_DIR', 'cache' );

define( 'SET_IMAGE_DIR', 'public/images' );

define( 'SET_DEFAULT_MODUL', 'index' );

define( 'SET_DEFAULT_SKIN', '_core' );


define( 'SET_VERSION', '1.0' );

define( 'SET_SITE_NAME', 'drakoon-php' );

define( 'SET_SITE_URL', 'http://drakoon-php' );

define( 'SET_DOMAIN', 'drakoon-php' );

define( 'SET_TIMEZONE', 'Europe/Berlin' );

define( 'SET_DEFAULT_LANGUAGE', 'hu' );



define( 'SET_DEFAULT_KEYWORDS', '' );
define( 'SET_DEFAULT_DESCRIPTION', '' );


define ( 'SET_TEMPLATES_DIR', 'templates' );
define ( 'SET_PAGE_NOT_FOUND_MODULE', 'page_not_found' );
define ( 'SET_ACCESS_DENIED_MODULE', 'access_denied' );



define( 'SET_DEFAULT_AVATAR', 'public/drakoon/media/no_image/user.png' );
define( 'SET_AVATAR_DIR',     'public/images/user/avatar/' );
$ranks[255] = [ 'name' => 'Developer' ];
$ranks[250] = [ 'name' => 'Admin' ];
$ranks[100] = [ 'name' => 'User' ];
$ranks[0]   = [ 'name' => 'Banned' ];
///////////////////////////////////////////////////////////