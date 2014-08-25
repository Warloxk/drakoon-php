<?
/**
 * drakoon-php core
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0b
 */
// load timer initialization
$start_loadtime = explode(" ", microtime());
$start_loadtime = $start_loadtime[1] + $start_loadtime[0];



// STATIC settings
require_once dirname( dirname( __FILE__ ) ) . '/app/config/base_config.php';



// set the include path
set_include_path( SET_INCLUDE_PATH );



// Database initialization
require_once 'drakoon/drakoon_db.php';

DB::$host     = DB_HOST;
DB::$user     = DB_USER;
DB::$password = DB_PASS;
DB::$dbName   = DB_DBNAME;
DB::$port     = DB_PORT;
DB::$encoding = DB_ENCODING;



// include drakoon main class
require_once 'drakoon/drakoon.php';
$drakoon = new Drakoon();



// include the main config
require_once 'app/config/config.php';



// drakoo initialization
$drakoon->Init();



// URL
$module = $drakoon->ParseURL();



// check if SiteMaintenance mode is enabled
$drakoon->SiteMaintenance();



// check if debug mode is enabled
$drakoon->Debug();



// drakoon-php cache extension
require_once 'drakoon/drakoon_cache.php';



// include the template snippet
require_once $drakoon->viewDir . '/snippet/default.php';



// drakoon-php comments extension
if ( $drakoon->extComments )
{
	require_once 'drakoon/drakoon_comment.php';
	$comments = new DrakoonComment( $commentsSettings );
}



// drakoon-php banners extension
if ( $drakoon->extComments )
{
	require_once 'drakoon/drakoon_banner.php';
	$c_banner = new DrakoonBanner();
}



/**
 * User class and session initialization
 */
session_start();
require_once 'drakoon/drakoon_user.php';
$result = User::Init();
User::SessionCookie();

if ( User::$id == 1 )
{
	ini_set( 'display_errors', '1' );
}

if ( $result === 0 && $module != 'sajat_adatlap' )
{
	$drakoon->Redirect( '/sajat_adatlap' );
}

if ( $result === 2 && $module != 'felhasznalo_beallitasok' )
{
	$drakoon->Redirect( '/felhasznalo_beallitasok' );
}



/**
 * include the core includer
 */
require_once 'app/includes/core_includes.php';



/**
 * include the base modul class
 */
require 'app/module/_module.php';



/**
 * page settings check
 */
if (is_file('app/module/' . $module . '.php'))
{
	include('app/module/' . $module . '.php');

	$m = new $module(  );
}
else
{
	unset( $_REQUEST['post'], $_POST['post'], $_GET['post'] );
	$drakoon->Redirect( $drakoon->setPageNotFoundModule );
}



// access check
if ($m->access > User::$rank)
{
	unset( $_REQUEST['post'], $_POST['post'], $_GET['post'] );

	$drakoon->Redirect( $drakoon->setAccessDeniedModule );
}



// post check
if ( isset( $_REQUEST['post'] ) && !empty( $_REQUEST['post'] ) )
{
	if ( method_exists( $m, 'FormPost' ) )
	{
		$m->FormPost();
	}
}



// call the module Display function
$m->Display();



/**
 * set the page header
 * @todo this must be dynamic, for xml or other type of documents
 */
header( 'Content-Type: text/html; charset:UTF-8' );



// includes and render the page
include $drakoon->_view( $m->skin . '.tpl' );



// unset error messages
unset( $_SESSION['info_message'], $_SESSION['error_message'] );



// close the database connection
DB::Close();