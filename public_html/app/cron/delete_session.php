<?
/**
 * drakoon-php delete user sessions
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0
 */
require_once dirname( dirname( __FILE__ ) ) . '../../drakoon/core_ajax.php';

User::DeleteSession();