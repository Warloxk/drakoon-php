<?
/**
 * drakoon-php config
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0
 */
$drakoon->setSiteName = 'drakoon-php';

$drakoon->setDefaultTitle = 'Default Title';
$drakoon->setDefaultKeywords = 'drakoon-php, framework';
$drakoon->setDefaultDescription = 'drakoon-php framework';
$drakoon->setDefaultAuthor = 'Peter Blaho';

$drakoon->setSiteVersion = '1.0';

$drakoon->setSiteMaintenance = false;
$drakoon->setSiteMaintenanceException = [ '127.0.0.1' ];
$drakoon->setDebug = false;
$drakoon->setCacheDir = 'cache';
$drakoon->setImageDir = 'public/images';
$drakoon->setDefaultModule = 'index';
$drakoon->setDefaultSkin = '_core';
$drakoon->setDomain = 'drakoon-php';
$drakoon->setTimeZone = 'Europe/Berlin';
$drakoon->setTemplatesDir = 'templates';
$drakoon->setPageNotFoundModule = 'page_not_found';
$drakoon->setAccessDeniedModule = 'access_denied';



/**
 * image extension
 */
$drakoon->extImage = false;



/**
 * banners extension
 */
$drakoon->extBanners = false;



/**
 * comments extension
 */
$drakoon->extComments = false;



/**
 * User extension settings
 */
$drakoon->setDefaultAvatar = 'public/drakoon/media/no_image/user.png';
$drakoon->setAvatarDir = 'public/images/user/avatar';
/** user ranks */
$drakoon->ranks[255] = [ 'name' => 'Developer' ];
$drakoon->ranks[250] = [ 'name' => 'Admin' ];
$drakoon->ranks[100] = [ 'name' => 'User' ];
$drakoon->ranks[0]   = [ 'name' => 'Banned' ];