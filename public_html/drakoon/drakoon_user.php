<?
/**
 * drakoon-php user manager
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0b
 */
class User
{
	public static $id;
	public static $nick;
	public static $password;
	public static $rank;
	public static $rankName;
	public static $status;
	public static $email;
	public static $avatar;
	public static $messagesNum;

	public static function Init()
	{
		if ( isset( $_SESSION['nick'] ) && !empty( $_SESSION['nick'] ) )
		{
			global $drakoon;

			$row = DB::getRow('SELECT id, nick, password, rank, email, status FROM users WHERE nick = %s AND password = %s', $_SESSION['nick'], $_SESSION['password']);

			self::$id          = $row['id'];
			self::$nick        = $row['nick'];
			self::$password    = $row['password'];
			self::$rank        = $row['rank'];
			self::$email       = $row['email'];
			self::$status      = $row['status'];
			if ( isset( $drakoon->ranks[ $row['rank'] ] ) )
			{
				self::$rankName = $drakoon->ranks[ $row['rank'] ]['name'];
			}
			else
			{
				self::$rankName = 'UNDEFINED RANK NAME';
			}
			self::$avatar      = $drakoon->setDefaultAvatar;
			self::$messagesNum = 0;


			if ( mb_strtolower( self::$nick, 'UTF-8' ) == mb_strtolower( $_SESSION['nick'], 'UTF-8' ) && self::$password == $_SESSION['password'])
			{
				$_SESSION['id']       = self::$id;
				$_SESSION['nick']     = self::$nick;
				$_SESSION['password'] = self::$password;

				self::$messagesNum = DB::getOne( 'SELECT COUNT(id) AS cnt FROM messages WHERE to_user_id = %i AND to_status = 1', self::$id );

				$file = $drakoon->setAvatarDir . '/' . $drakoon->DirectorySplitter( self::$id ) . '/' . self::$id . '.jpg';
				if ( file_exists( $file ) )
				{
					self::$avatar = '/' . $file;
				}

				return $row['status'];
			}
			else
			{

				$_SESSION['id'] = '';
				$_SESSION['nick'] = '';
				$_SESSION['password'] = '';

				self::$rank = 0;
				self::$id = 0;
				self::$nick = 'Guest';
			}
		}
		else
		{
			$_SESSION['id'] = '';
			$_SESSION['nick'] = '';
			$_SESSION['password'] = '';
			self::$id = 0;
			self::$rank = 0;
			self::$nick = 'Guest';
		}

		return false;
	}

	public static function SessionCookie()
	{
		global $drakoon;

		if ( !isset( $_SESSION['guid'] ) || empty( $_SESSION['guid'] ) )
		{
			$_SESSION['guid'] = md5( uniqid( rand(  ), true ) );
		}

		if ( !isset( $_COOKIE['guid'] ) || empty( $_COOKIE['guid'] ) )
		{
			setcookie( 'guid', $_SESSION['guid'], time() + 2592000, '/', $drakoon->setDomain );
		}
		else
		{
			setcookie( 'guid', $_COOKIE['guid'], time() + 2592000, '/', $drakoon->setDomain );
		}

		if ( isset( $_SESSION['guid'] ) && !empty( $_SESSION['guid'] ) )
		{
			$session = DB::getRow( 'SELECT user_id FROM session WHERE guid = %s', $_SESSION['guid'] );

			$values = array();
			$values['user_id'] = 0;
			$values['time'] = array( 'NOW()', '%l' );
			$values['guid'] = $_SESSION['guid'];
			$values['ip'] = $_SERVER['REMOTE_ADDR'];
			$values['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

			if ( $_SESSION['id'] > 0 )
			{
				$values['user_id'] = $_SESSION['id'];
			}

			if ( $session === null )
			{
				DB::insert( 'session', $values );
			}
			else
			{
				DB::update( 'session', $values, 'guid = %s', $_SESSION['guid'] );
			}
		}
	}

	public static function Login( $inp_user_name = '' )
	{
		$result = self::Init();

		if ( $result !== false )
		{
			DB::justQuery( 'UPDATE users SET login_date = NOW() WHERE id = %i', $_SESSION['id'] );
		}

		self::LogLoginAttempt( $_SESSION['id'], $_SESSION['guid'], $inp_user_name );

		return $result;
	}

	public static function LogLoginAttempt( $user_id, $guid, $inp_user_name )
	{
		$values = array();

		$values['date'] = array( 'NOW()', '%l' );
		$values['user_id'] = array( $user_id, '%i' );
		$values['ip'] = array( $_SERVER['REMOTE_ADDR'], '%s' );
		$values['guid'] = $guid;
		$values['inp_user_name'] = $inp_user_name;

		DB::insert( 'log_login_attempt', $values );
	}

	public static function Logout()
	{
		$_SESSION['id'] = '';
		$_SESSION['nick'] = '';
		$_SESSION['password'] = '';

		self::$rank = 0;
		self::$id = 0;
		self::$nick = 'Guest';
		self::$password = '';
		self::$email = '';
	}

	public static function DeleteSession()
	{
		DB::justQuery( 'DELETE FROM session WHERE time <= %s', date( 'Y-m-d H:i:s', ( time() - 180 ) )  );
	}



	public static function GeneratePassword( $length = 8 )
	{
		$pattern = '123456789abcdefghijkmnrstuvwxyz';
		$key = '';

		for ( $i = 0; $i < $length; $i++ )
		{
			$key .= $pattern{ rand( 0 , 30 ) };
		}

		return $key;
	}

	public static function LostPassword()
	{
		global $drakoon, $emailContent;

		$nick = mb_strtolower( $_POST['nick'], 'UTF-8' );
		$email = mb_strtolower( $_POST['email'], 'UTF-8' );

		if ( empty( $nick ) || empty( $email ) )
		{
			return false;
		}

		$result = DB::getRow( 'SELECT id, nick, email FROM users WHERE nick = %s AND email = %s', $nick, $email );

		if ( $result != null )
		{
			$newPassword = self::GeneratePassword();

			$values = array();
			$values['password'] = md5($newPassword);
			$values['status'] = 2;
			DB::update( 'users', $values, 'id = %i', $result['id'] );

			$search = array( '%USERNAME%', '%PASSWORD%' );
			$replace = array( $result['nick'], $newPassword );

			$subject = $emailContent['lost_password']['subject'];
			$content = str_replace( $search, $replace, $emailContent['lost_password']['content'] );
			$from = $emailContent['lost_password']['from'];
			$to = $result['email'];

			$drakoon->SendHtmlEmail( $content, $from, $to, $subject, $emailContent['lost_password']['from_name'] );

			return true;
		}

		return false;
	}

	public static function Registration()
	{
		global $drakoon, $emailContent;

		//$nick = mb_strtolower( trim( $_POST['nick'] ), 'UTF-8' );
		$nick = trim( $_POST['nick'] );
		$email = mb_strtolower( trim( $_POST['email'] ), 'UTF-8' );
		$email_again = mb_strtolower( trim( $_POST['email_again'] ), 'UTF-8' );
		$password = $_POST['password'];
		$password_again = $_POST['password_again'];

		if ( empty( $nick ) )
		{
			return 6;
		}

		if ( empty( $email ) )
		{
			return 2;
		}

		if ( empty( $password ) )
		{
			return 8;
		}

		if ( $email != $email_again )
		{
			return 3;
		}

		if ( $password != $password_again )
		{
			return 7;
		}

		$result = DB::getRow( 'SELECT nick FROM users WHERE nick = %s', $nick );
		if ( $result == null )
		{
			$result = DB::getRow( 'SELECT email FROM users WHERE email = %s', $email );
			if ( $result == null )
			{
				$values = array();
				$values['password'] = md5( $password );
				$values['nick'] = $nick;
				$values['email'] = $email;
				$values['language'] = 'hu';
				$values['status'] = 0;
				$values['rank'] = 100;
				$values['registration_date'] = array( 'NOW()', '%l' );
				$values['guid'] = $drakoon->GenGUID();

				DB::insert( 'users', $values );

				$activationLink = 'http://piroszona.hu/user_activate/u-' . $values['guid'];

				$search = array( '%USERNAME%', '%PASSWORD%', '%ACTIVATION_LINK%' );
				$replace = array( $nick, $password, $activationLink );

				$subject = $emailContent['registration']['subject'];
				$content = str_replace( $search, $replace, $emailContent['registration']['content'] );
				$from = $emailContent['registration']['from'];
				$to = $email;

				$drakoon->SendHtmlEmail( $content, $from, $to, $subject, $emailContent['registration']['from_name'] );

				$userFn = 'RegistrationComplete';

				if ( is_callable( $userFn ) )
				{
					call_user_func( $userFn, $nick );
				}

				return 1;
			}
			else
			{
				return 5; // email registered
			}
		}
		else
		{
			return 4; // nick registered
		}

		return false;
	}

	/**
	 * RegistrationForm generate the registration form
	 * for validation use the head include tpl file
	 * @param string $action action for form post
	 */
	public static function RegistrationForm( $action, $nick = '', $email = '' )
	{
		$form[] = '<form id="registration_form" name="registration_form" class="myForm" action="' . $action . '" method="post">';
			$form[] = array( 'label' => 'Felhasználónév', 'input' => '<input type="text" id="nick" name="nick" value="' . $nick . '" minlength="3" length="30" class="required username autoDisable" style="width:180px;" />' );
			$form[] = array( 'label' => '', 'input' => '<p id="suggestions"></p>' );

			$form[] = array( 'label' => 'Jelszó', 'input' => '<input type="password" id="password" name="password" length="150" minlength="6" class="required password" style="width:150px;" />' );
			$form[] = array( 'label' => 'Jelszó mégegyszer', 'input' => '<input type="password" id="password_again" value="" minlength="6" name="password_again" length="150" class="required password" style="width:150px;" />' );

			$form[] = array( 'label' => '', 'input' => '&nbsp;' );

			$form[] = array( 'label' => 'email', 'input' => '<input type="text" id="email" name="email" value="' . $email . '" length="150" class="required email" />' );
			$form[] = array( 'label' => 'email mégegyszer', 'input' => '<input type="text" id="email_again" value="" name="email_again" length="150" class="required email" /><div id="email_error" class="error"></div>' );

			$form[] = array( 'label' => '', 'input' => '<input type="hidden" name="post" value="1" />' );

			$form[] = array( 'label' => '', 'input' => '<a href="javascript:void(0);" onclick="javascript:Register();" class="btn ui_blue ui_text_white">Regisztráció</a>' );
		$form[] = '</form>';

		return $form;
	}

	public static function ChangePasswordForm( $action )
	{
		$form[] = '<form id="change_password_form" name="change_password_form" class="myForm" action="' . $action . '" method="post">';
			$form[] = array( 'label' => 'Jelenlegi jelszó', 'input' => '<input type="password" id="current_password" name="current_password" class="required" />' );
			$form[] = array( 'label' => '', 'input' => '' );
			$form[] = array( 'label' => 'Új jelszó', 'input' => '<input type="password" id="new_password" name="new_password" minlength="6" class="required" />' );
			$form[] = array( 'label' => 'Új jelszó mégegyszer', 'input' => '<input type="password" id="new_password_again" name="new_password_again" minlength="6" class="required" />' );

			$form[] = array( 'label' => '', 'input' => '<input type="submit" id="post" name="post" value="Mentés" />' );
		$form[] = '</form>';

		return $form;
	}

	public static function ChangePassword()
	{
		$_POST['new_password'] = trim( $_POST['new_password'] );

		$result = DB::getOne( 'SELECT id FROM users WHERE id = %i AND password = %s', User::$id, md5( $_POST['current_password'] ) );

		if ( $result != null )
		{
			if ( !empty( $_POST['new_password'] ) )
			{
				if ( $_POST['new_password'] == $_POST['new_password_again'] )
				{
					if ( strlen( $_POST['new_password'] ) >= 3 )
					{
						DB::justQuery( 'UPDATE users SET password = %s, status = 1 WHERE id = %i', md5( $_POST['new_password'] ), User::$id );

						$_SESSION['password'] = md5( $_POST['new_password'] );

						return 1;
					}
					else
					{
						return 5; // passwords too short
					}
				}
				else
				{
					return 4; // passwords not match
				}
			}
			else
			{
				return 3; // password are empty
			}
		}
		else
		{
			return 2; // invalid current password
		}

		return $form;
	}

	public static function CheckNick( $nick )
	{
		$json = array( 'result' => 0 );

		$nick = trim( $nick );

		$result = DB::getOne( 'SELECT id FROM users WHERE nick = %s', $nick );
		if ( $result == null )
		{
			$json['result'] = 1;
			$json['html'] = '<font class="color_green">Szabad becenév</font>';
		}
		else
		{
			$json['result'] = 2;
			$json['html'] = '<font class="color_red">Ez a becenév foglalt</font><br />';

			$suggestions = array();

			for ( $i = 1; $i <= 10; $i++ )
			{
				$suggestions[] = $nick . $i;
			}

			$rows = DB::query( 'SELECT nick FROM users WHERE nick IN ("%l")', implode( '","', $suggestions ) );
			if ( $rows != null )
			{
				foreach ($rows as $row)
				{
					$key = array_search( $row['nick'], $suggestions );

					if ( $key !== false )
					{
						unset($suggestions[$key]);
					}
				}
			}

			if ( is_array( $suggestions ) && !empty( $suggestions ) )
			{
				$json['html'] .= 'Javaslatok:<br />';

				foreach ( $suggestions as $suggestion )
				{
					$json['html'] .= '<input type="radio" name="suggestion_radio" value="' . $suggestion . '" onclick="javascript:SetSuggestion(\'' . $suggestion . '\')" />' . $suggestion . '</a><br />';
				}
			}

		}

		return json_encode( $json );
	}

	public static function CheckEmail( $email )
	{
		$json = array( 'result' => 0 );

		$email = trim( $email );

		$result = DB::getOne( 'SELECT id FROM users WHERE email = %s', $email );
		if ( $result == null )
		{
			$json['result'] = 1;
			$json['html'] = '';
		}
		else
		{
			$json['result'] = 2;
			$json['html'] = '<font class="color_red">Hiba, ezzel az email címmel már regisztrált valaki, ha ez a Te email címed, akkor <a href="javascript:void(0);" onclick="javascript:Belepes(1);">itt tudsz jelszó emlékeztetőt kérni</a>.</font>';
		}

		return json_encode( $json );
	}

	public static function UserBar()
	{
		$bellActive = '';

		if ( self::$messagesNum > 0 || self::$elmenyindexNum > 0 )
		{
			$bellActive = 'active';
		}

		$ret = '<div id="userBar">';
			if ( self::$id > 0 )
			{
				$ret .= '<ul class="bar">';
					if ( self::$aktivHirdetoSzam > 1 )
					{
						$ret .= '<li><a href="javascript:void(0);" onclick="javascript:UserHirdetesWindowShow();" class="userBtn hirdetesValtasGomb tooltip" title="Jelenleg ' . self::$aktivHirdetok[self::$aliasHirdetoId]['nev'] . '</b> hirdetőként böngészed az oldalt."><i class="icon-arrowDown icon-white"></i>' . self::$aktivHirdetok[self::$aliasHirdetoId]['nev'] . '</a></li>';
					}

					if ( self::$rank >= 240 )
					{
						$ret .= '<li><a href="/admin" class="userBtn tooltip" title="Admin felület"><i class="icon-gear icon-white"></i>Admin</a></li>';
					}
					$ret .= '<li><a href="javascript:void(0);" onclick="javascript:UserWindowShow();" class="userBtn tooltip" title="Saját menü"><i class="icon-arrowDown icon-white"></i>' . self::$nick . '</a></li>
					<li><a href="javascript:void(0);" onclick="javascript:UserNotificationWindowShow();" id="userBell" class="userBell ' . $bellActive . ' tooltip" title="Értesítések"><i class="icon-bell icon-white"></i></a></li>
					<li><a href="javascript:void(0);" onclick="javascript:UserWindowShow();" class="tooltip" title="Saját menü"><img src="' . self::$avatar . '" style="width:30px;height:30px;"></a></li>
				</ul>';

				if ( self::$aktivHirdetoSzam > 1 )
				{
					$ret .= '<div id="userHirdetesWindow" style="display:none;">
						<button type="button" class="close tooltip" title="Bezár" onclick="javascript:MessageClose(this)">×</button>
						<h3>Váltás a hirdetéseid között</h3>

						<ul class="userWindowMenu">';


							foreach ( self::$aktivHirdetok as $hirdeto )
							{
								$ret .= '<li><a href="/hirdetes_valtas/hid-' . $hirdeto['id'] . '/post-1" class="tooltip" title="' . $hirdeto['nev'] . ' (' . $hirdeto['telefon'] . ')"><i class="icon-heart"></i>' . $hirdeto['nev'] . '</a></li>';
							}

						$ret .= '</ul>
					</div>';
				}

				$ret .= '<div id="userNotificationWindow" style="display:none;">
					<button type="button" class="close tooltip" title="Bezár" onclick="javascript:MessageClose(this)">×</button>
					<h3>Értesítések</h3>

					<ul class="userWindowMenu">';
						$haveNotification = false;
						if ( self::$messagesNum > 0 )
						{
							$ret .= '<li><a href="/privat_uzenetek"><i class="icon-message"></i>Privát üzenetek <em>' . self::$messagesNum . '</em></a></li>';
							$haveNotification = true;
						}

						if ( self::$elmenyindexNum > 0 )
						{
							$ret .= '<li><a href="/felhasznalo_elmenyindexei"><i class="icon-edit"></i>Élményindexeid <em>' . self::$elmenyindexNum . '</em></a></li>';
							$haveNotification = true;
						}
						if ( !$haveNotification )
						{
							$ret .= '<li style="font-style: italic;">Nincs új értesítésed</li>';
						}
					$ret .= '</ul>
				</div>

				<div id="userWindow" style="display:none;">
					<button type="button" class="close tooltip" title="Bezár" onclick="javascript:MessageClose(this)">×</button>
					<h3>' . self::$nick . '</h3>

					<div class="userWindowLeft">
						<img src="' . self::$avatar . '" style="width:100;height:100px;" /><p>' . self::$rankName . '<br />' . self::$points . ' pont' . ( self::$credit > 0 ? '<br />' . self::$credit .' kredit' : '' ) . '</p>
					</div>

					<div class="userWindowRight">
						<ul class="userWindowMenu">';
							if ( self::$rank >= 240 ) // admin
							{
								$ret .= '<li><a href="/admin"><i class="icon-gear"></i>Admin felület</a></li>';
							}
							if ( self::$rank >= 200 ) // hirdeto
							{
								$ret .= '<li><a href="/hirdetes_feladas"><i class="icon-heart"></i>Hirdetéseid</a></li>';
							}
							$ret .= '<li><a href="/sajat_adatlap"><i class="icon-user"></i>Saját adatlap</a></li>
							<li><a href="/privat_uzenetek"><i class="icon-message"></i>Privát üzenetek' . ( self::$messagesNum > 0 ? ' <em>' . self::$messagesNum . '</em>' : '' ) . '</a></li>
							<li><a href="/felhasznalo_elmenyindexei"><i class="icon-edit"></i>Élményindexeid' . ( self::$elmenyindexNum > 0 ? ' <em>' . self::$elmenyindexNum . '</em>' : '' ) . '</a></li>
							<li><a href="/felhasznalo_beallitasok"><i class="icon-wrench"></i>Beállítások</a></li>
						</ul>
					</div>

					<div class="userWindowBottom">
						<a href="/kilepes/post-1" class="myBtn">Kilépés</a>
					</div>
				</div>';
			}
			else
			{
				$ret .= '<ul class="bar">
					<li><a href="javascript:void(0);" onclick="javascript:Belepes(1);" class="userBtn tooltip" title="Kattints ide a belépéshez"><i class="icon-padlock icon-white"></i>Belépés</a></li>
					<li><a href="/regisztracio" class="userBtn tooltip" title="Ha még nem rendelkezel felhasználóval, akkor ide kattintva tudod regisztrálni magad."><i class="icon-form icon-white"></i>Regisztráció</a></li>
					<li><a href="javascript:void(0);" onclick="javascript:Belepes(1);" class="userBtn tooltip" title="Ha elfelejtetted a jelszavad, akkor ide kattintva kérhetsz újat."><i class="icon-key icon-white"></i>Elfelejtett jelszó</a></li>
				</ul>';
			}
		$ret .= '</div>';

		return $ret;
	}
}