<?
/**
 * drakoon-php framework main class
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0b
 */
class Drakoon
{
	protected $home;

	// settings from the config file
	public $setSiteName;

	public $setDefaultTitle;
	public $setDefaultKeywords;
	public $setDefaultDescription;
	public $setDefaultAuthor;

	public $setSiteVersion;

	public $setSiteMaintenance;
	public $setSiteMaintenanceException;
	public $setDebug;
	public $setCacheDir;
	public $setImageDir;
	public $setDefaultModule;
	public $setDefaultSkin;
	public $setSiteURL;
	public $setDomain;
	public $setTimeZone;
	public $setTemplatesDir;
	public $setPageNotFoundModule;
	public $setAccessDeniedModule;
	public $extBanners;
	public $extComments;
	public $setDefaultAvatar;
	public $setAvatarDir;
	public $ranks;

	//
	public $viewDir;
	public $vars = array();
	public $now;
	public $currentHour;
	public $thisMonday;
	public $rootDir;

	public function Init( $template = 'default' )
	{
		$rootDir = dirname( dirname( __FILE__ ) );

		// set the timezone
		date_default_timezone_set( $this->setTimeZone );

		// need some times
		$this->now         = date( 'Y-m-d H:i:s', time() ) ;
		$this->currentHour = date( 'Y-m-d H', time() ) . ':00:00';
		$this->thisMonday  = date( 'Y-m-d', time() + ( 1 - date('w') ) * 86400 ) . ' 00:00:00';

		$this->home = $this->setSiteURL;
		$this->viewDir = 'public/template/' . $template;
	}

	/*public function __set( $name, $value )
	{
		$this->vars[ $name ] = $value;
	}

	public function __get( $name )
	{
		return $this->vars[ $name ];
	}*/

	public function VarGet( $name, $value = 0 )
	{
		if ( isset( $this->vars[ $name ] ) )
		{
			return $this->vars[ $name ];
		}

		return $value;
	}

	public function VarSet( $name, $value )
	{
		$this->vars[ $name ] = $value;
	}

	public function ParseURL()
	{
		$s_url = $_SERVER['REQUEST_URI'];
		$a_url = explode( '/', substr( $s_url, 1 ) );
		//$a_excludes = array( 'exclude_me' );
		//$module = $a_url[0];
		//$b_isModal = false;

		/*if ( in_array( $module, $a_excludes ) )
		{
			die( 'exclude_me' );
			$key = $drakoon->txtEscape( $a_url[1] );
		}
		else
		{*/
			foreach ( $a_url as $key => $value )
			{
				if ( !empty( $value ) && $key > 0 )
				{
					$data = explode( '-', $value, 2 );

					if ( isset( $data[1] ) )
					{
						$_GET[ $data[0] ] = $data[1];

						/*if ( $data[0] == 'post' )
						{
							$_REQUEST['post'] = 1;
						}
						else
						{
							$drakoon->VarSet( $data[0], $data[1] );
						}*/
					}
				}
			}
		//}

		if ( empty( $a_url[0] ) )
		{
			return $this->setDefaultModule;
		}

		return $a_url[0];
	}

	public function SiteMaintenance()
	{
		if ( $this->setSiteMaintenance )
		{
			if ( is_array( $this->setSiteMaintenanceException ) && !empty( $this->setSiteMaintenanceException ) && in_array( $_SERVER['REMOTE_ADDR'], $this->setSiteMaintenanceException ) )
			{
				require $this->viewDir . '_siteMaintenanceOn.tpl';
			}
			else
			{
				require_once $this->rootDir . '/errordocs/maintenance.php';
				die();
			}
		}
	}

	public function Debug()
	{
		if ( $this->setDebug )
		{
			ini_set( 'display_errors', 1 );
			error_reporting( E_ALL );

			require $this->viewDir . '_debugModeOn.tpl';
		}
	}

	public function _view( $s_view = '' )
	{
		global $module;

		if ( empty( $s_view ) )
		{
			return $this->viewDir . '/' . $module . '.tpl';
		}
		else
		{
			return $this->viewDir . '/' . $s_view;
		}
	}

	public function _head()
	{
		global $module;

		$file = $this->viewDir . '/head/' . $module . '.tpl';

		if ( file_exists( $file ) )
		{
			return $file;
		}
		else
		{
			return $this->viewDir . '/head/_default.tpl';
		}
	}

	public function _block( $s_blockName )
	{
		require_once 'app/block/' . $s_blockName . '.php';

		require_once $this->viewDir . '/block/' . $s_blockName . '.tpl';
	}

	public function _snippet(  )
	{
		$args = func_get_args();

		return call_user_func_array( 'snippet_' . $args[0], array_slice( $args, 1 ) );
	}



	public function HTMLCache( $cacheKey, $start = '', $expire = 3600, $debug = false )
	{
		global $module;

		if ( $debug )
		{
			return false;
		}

		$cacheGroup = 'html/' . $module;

		if ( $start != 'end' )
		{
			if ( ( $r = Cache::get( $cacheKey, $cacheGroup ) ) === false )
			{
				ob_start();
				return false;
			}
			else
			{
				return $r;
			}
		}
		else
		{
			$content = ob_get_flush();
			Cache::set( $cacheKey, $content, $cacheGroup, $expire );
		}
	}



	public function ScrollTo()
	{
		$ret = '';

		if ( isset( $this->vars['scroll'] ) && !empty( $this->vars['scroll'] ) )
		{
			$ret = '<script type="text/javascript">
				$(document).ready(function() {
					ScrollTo("#' . $this->vars['scroll'] . '");
				});
			</script>';
		}

		return $ret;
	}









	public function NumberFormat( $num )
	{
		return number_format( $num, 0, ".", " " );
	}















	public function GetMonday( $plusWeek = 0, $addTime = false )
	{
		return date( 'Y-m-d', time() + ( ( 1 + ( $plusWeek * 7 ) ) - date( 'N' ) ) * 86400 ) . ( $addTime ? ' 00:00:00' : '' );
	}

	public function ForeachTd( $inp, $cols = 3, $width = 0 )
	{
		if ( isset( $inp ) && is_array( $inp ) )
		{
			if ( empty( $inp ) )
			{
				return '<tr><td' . ( $width > 0 ? ' width="' . $width . '%"' : '' ) . '></td></tr>';
			}

			$r = '<tr>';
			$cnt = 0;

			foreach ( $inp as $value )
			{
				if ( $cnt == $cols )
				{
					$cnt = 0;
					$r .= '</tr><tr>';
				}

				$r .= '<td' . ( $width > 0 ? ' width="' . $width . '%"' : '' ) . '>' . $value . '</td>';

				$cnt++;
			}

			$r .= str_repeat( '<td></td>', ( $cols - $cnt ) ) . '</tr>';

			return $r;
		}
	}







	public function LoadTimerEnd()
	{
		global $start_loadtime, $end_loadtime;

		$end_loadtime = explode( ' ', microtime() );
		$end_loadtime = $end_loadtime[1] + $end_loadtime[0];
		$total_loadtime = ( $end_loadtime - $start_loadtime );
		$s_total_loadtime = round( $total_loadtime, 4);

		return $s_total_loadtime;
	}

	public function MemoryUsage()
	{
		if ( substr( PHP_OS, 0, 3 ) != 'WIN' )
		{
			$pid = getmypid();
			exec("ps -eo%mem,rss,pid | grep $pid", $output);
			$output = explode("  ", $output[0]);
			return $output[1] . ' kB';
		}
	}

	public function Redirect( $s_link = '', $s_info_message = '', $s_error_message = '', $a_values = '' )
	{
		if ( empty( $s_link ) )
		{
			header( 'location: /' . $this->setDefaultModule );
			exit();
		}

		$s_location = $s_link;

		$_SESSION['info_message'] = $s_info_message;
		$_SESSION['error_message'] = $s_error_message;

		if ( is_array( $a_values ) && !empty( $a_values ) )
		{
			foreach ( $a_values as $key => $value )
			{
				$s_location .= '/' . $key . '-' . $value;
			}
		}

		header( 'location: ' . $s_location );
		exit();
	}

	public function RedirectJS( $s_link, $s_info_message = '', $s_error_message = '', $a_values = '' )
	{
		if ( empty( $s_link ) )
		{
			echo '<script type="text/javascript">
					window.location = "/' . $this->setDefaultModule . '";
				</script>';
			exit();
		}

		$s_location = $s_link;

		$_SESSION['info_message'] = $s_info_message;
		$_SESSION['error_message'] = $s_error_message;

		if ( is_array( $a_values ) && !empty( $a_values ) )
		{
			foreach ( $a_values as $key => $value )
			{
				$s_location .= '/' . $key . '-' . $value;
			}
		}

		echo '<script type="text/javascript">
				window.location = "' . $s_location . '";
			</script>';
		exit();
	}

	/**
	 * Pageing function for generating the pager
	 * @param integer $recordsNum     number of the records
	 * @param integer $page           number of the current page
	 * @param integer $recordsPerPage record per page
	 * @param array   $passGet        array variable to pass $_GET variables
	 * @param string  $options        options for the first_page, last_page, previous_page, next_page caption
	 */
	public function Pageing( $recordsNum, $page, $recordsPerPage = 20, $passGet = '', $options = '' )
	{
		global $module;

		if ( !is_array( $options ) )
		{
			$options['first_page']    = '&lt;&lt;';
			$options['last_page']     = '&gt;&gt;';
			$options['previous_page'] = '&lt;';
			$options['next_page']     = '&gt;';
		}

		$page = intval( $page );

		if ( $page < 1 )
		{
			$page = 1;
		}

		$offset = ($page - 1) * $recordsPerPage;
		$max_page = intval( ceil( $recordsNum / $recordsPerPage ) );

		if ( $page >= 2 )
		{
			$prev_page = $page - 1;
		}
		else
		{
			$prev_page = 1;
		}

		if ( $page > $max_page )
		{
			$page = $max_page;
		}

		if ( $page != $max_page )
		{
			$next_page = $page + 1;
		}

		else
		{
			$next_page = $max_page;
		}

		$last_page = $max_page;

		//1 7 8 9 10 11 12
		//1 2 3 4
		//1 3 4 5 6 7 8

		$s_pass = '';
		if ( is_array( $passGet ) && !empty( $passGet ) )
		{
			foreach ( $passGet as $key => $value )
			{
				if ( !empty( $value ) )
				{
					$s_pass .= '/' . $key . '-' . $value;
				}
			}
		}

		$html = '<div class="pagination"><ul>';

		if ($page > 3)
		{
			$html .= '<li><a href="/' . $module . '/p-1' . $s_pass . '">' . $options['first_page'] . '</a></li>
			<li><a href="/' . $module . '/p-' . $prev_page . '' . $s_pass . '">' . $options['previous_page'] . '</a></li>
			<li><a href="/' . $module . '/p-1' . $s_pass . '">1</a></li>';
		}
		elseif ( $page > 2)
		{
			$html .= '<li><a href="/' . $module . '/p-1' . $s_pass . '">' . $options['first_page'] . '</a></li>
			<li><a href="/' . $module . '/p-' . $prev_page . '' . $s_pass . '">' . $options['previous_page'] . '</a></li>
			<li><a href="/' . $module . '/p-1' . $s_pass . '">1</a></li>';
		}
		elseif ( $page > 1 )
		{
			$html .= '<li><a href="/' . $module . '/p-1' . $s_pass . '">' . $options['first_page'] . '</a><li><a href="/' . $module . '/p-' . $prev_page . '' . $s_pass . '">' . $options['previous_page'] . '</a></li></li>';
		}


		$start = 0;
		if ( $page > 1 )
		{
			$start = -1;
		}

		$max = $start + 4;

		for ( $i = $start; $i <= $max; $i++ )
		{
			if ( ( $page + $i ) > $max_page )
			{
				break;
			}

			$html .= '<li' . ( $i == 0 ? ' class="current"' : '' ) . '><a href="/' . $module . '/p-'. ( $page + $i ) . '' . $s_pass . '">'. ( $page + $i ) . '</a></li>';
		}

		if ( $max_page > $page )
		{
			$html .= '<li><a href="/' . $module . '/p-' . $next_page . '' . $s_pass . '" class="prev">' . $options['next_page'] . '</a></li><li><a href="/' . $module . '/p-' . $last_page . '' . $s_pass . '" class="prev">' . $options['last_page'] . '</a></li>';
		}

		$html .= '</ul></div>';

		return array( 'html' => $html, 'from' => ( ( $page - 1 ) * $recordsPerPage ), 'limit' => $recordsPerPage );
	}


	/**
	 * Generate unique ID
	 * length = 16 characters
	 * filesystem safe
	 */
	public function UniqueId()
	{
		$uni_id = sprintf( '%08x%04x%04x%02x%02x%012x', mt_rand(), mt_rand( 0, 65535 ), bindec( substr_replace ( sprintf( '%016b', mt_rand( 0, 65535 ) ), '0100', 11, 4 ) ), bindec( substr_replace( sprintf( '%08b', mt_rand( 0, 255 ) ), '01', 5, 2 ) ), mt_rand( 0, 255 ), mt_rand() );

		return substr( $uni_id, 0, 12 ) . date( 'is', time() );
	}

	/**
	 * Generate GUID
	 * length = 36 characters
	 */
	public function GenGUID()
	{
		$s = md5( uniqid( mt_rand(  ), true ) );
		$guidText = substr( $s, 0, 8 ) . '-' . substr( $s, 8, 4 ) . '-' . substr( $s, 12, 4 ) . '-' . substr( $s, 16, 4 ) . '-' . substr( $s, 20 );
		return $guidText;
	}

	public function DateToString( $datum = '1970-01-01', $fulldate = true )
	{
		$eng = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		$hun = array( 'január', 'február', 'március', 'április', 'május', 'június', 'július', 'augusztus', 'szeptember', 'október', 'november', 'december', 'hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat', 'vasárnap' );

		if ( $fulldate == false )
		{
			if ( date( 'Y', strtotime( $datum ) ) < date( 'Y' ) )
			{
				$datum = date( 'Y. F j.', strtotime( $datum ) );
			}
			else
			{
				$datum = date( 'F j.', strtotime( $datum ) );
			}
		}
		else
		{
			$datum = date( 'Y. F j., l H:i', strtotime( $datum ) );
		}

		$datum = str_ireplace( $eng, $hun, $datum );

		return $datum;
	}

	public function Ago( $time, $language = 'en' )
	{
		$periods['en'] = array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' );
		$periods['hu'] = array( 'másodperce', 'perce', 'órája', 'napja', 'hete', 'month', 'year', 'decade' );
		$lengths = array( '60', '60', '24', '7', '4.35', '12', '10' );

		$now = time();

		$difference = $now - $time;

		$tense = ' ago';

		for ( $j = 0; $difference >= $lengths[$j] && $j < count( $lengths ) - 1; $j++ )
		{
			$difference /= $lengths[$j];
		}

		$difference = round( $difference );

		if ( $difference != 1 && $language == 'en' )
		{
			$periods[$language][$j] .= 's';
		}

		if ( $language == 'en' )
		{
			return $difference . ' ' . $periods[$language][$j] . $tense;
		}
		else
		{
			return $difference . ' ' . $periods[$language][$j];
		}
	}



	public function PhoneFormat( $s_telefonszam )
	{
		if ( strlen( $s_telefonszam ) == 7 )
		{
			return "+36 1 / " . substr( $s_telefonszam, 0, 3 ) . "-" . substr( $s_telefonszam, 3, 4 );
		}
		elseif ( strlen( $s_telefonszam ) == 8 )
		{
			return "+36 1 / " . substr( $s_telefonszam, 1, 3 ) . "-" . substr( $s_telefonszam, 4, 4 );
		}
		else
		{
			return "+36 " . substr( $s_telefonszam, 0, 2 ) . " / " . substr( $s_telefonszam, 2, 3 ) . "-" . substr( $s_telefonszam, 5, 4 );
		}
	}

	public function PhoneSave( $s_telefonszam )
	{
		$telefon = preg_replace( '/[^0-9]/', '', $s_telefonszam );

		if ( substr( $telefon, 0, 2 ) == '36' || substr( $telefon, 0, 2 ) == '06' )
		{
			$telefon = substr( $telefon, 2 );
		}

		return $telefon;
	}

	public function SendHtmlEmail( $body, $from, $to, $subject, $from_name = '' )
	{
		if ( empty( $from_name ) )
		{
			$from_name = $from;
		}

		$subject = "=?utf-8?b?".base64_encode($subject)."?=";

	    $headers  = "MIME-Version: 1.0\r\n";
	    $headers .= "From: =?utf-8?b?".base64_encode($from_name)."?= <".$from.">\r\n";
	    $headers .= "Content-Type: text/html;charset=utf-8\r\n";
	    $headers .= "Reply-To: $from\r\n";
	    $headers .= "X-Mailer: PHP/" . phpversion();
	    mail( $to, $subject, $body, $headers );
	}

	public function UcFirst( $string, $e = 'utf-8' )
	{
		if ( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) && !empty( $string ) )
		{
			$string = mb_strtolower( $string, $e );
			$upper = mb_strtoupper( $string, $e );
			preg_match( '#(.)#us', $upper, $matches );
			$string = $matches[1] . mb_substr( $string, 1, mb_strlen( $string, $e ), $e );
		}
		else
		{
			$string = ucfirst( $string );
		}
		return $string;
	}

	public function InsertFlash( $s_file, $i_width, $i_height, $s_falshVars = '' )
	{
		$ret = '<object type="application/x-shockwave-flash" data="' . $s_file . '" width="' . $i_width . '" height="' . $i_height . '">';
		$ret .= '<param name="wmode" value="transparent" />';
		$ret .= '<param name="movie" value="' . $s_file . '" />';
		if ( !empty( $s_falshVars ) )
		{
			$ret .= '<param name="FlashVars" value="' . $s_falshVars . '" />';
		}
		$ret .= '</object>';

		return $ret;
	}

	public function DirectorySplitter( $i_num, $i_limit = 5000 )
	{
		if ( is_numeric( $i_num ) && is_numeric( $i_limit ) )
		{
			return ( ceil( $i_num / $i_limit ) * $i_limit );
		}

		return '';
	}

	public function GetScriptName()
	{
		if ( strpos( $_SERVER['REQUEST_URI'], '?' ) != '' )
		{
			return basename( substr( $_SERVER['REQUEST_URI'], 0, strpos( $_SERVER['REQUEST_URI'], '?' ) ) );
		}
		else
		{
			return basename( $_SERVER['REQUEST_URI'] );
		}
	}

	public function txt2db( $text, $allowHtmlTags = false )
	{
		if ( !$allowHtmlTags )
		{
			$text = html_entity_decode( $text );
			$text = strip_tags( $text );
			$text = htmlspecialchars( $text, ENT_QUOTES );
		}

		$text = trim( $text );
		$text = str_replace( chr(13) . chr(10), '[br/]', $text );

		return $text;
	}

	public function db2txt( $text )
	{
		return str_replace( '[br/]', chr(13) . chr(10), $text );
	}

	public function db2html( $text )
	{
		return str_replace( '[br/]', '<br />', $text );
	}

	public function LinkSafeText($s_string, $b_onlyLettersAndNumbers = false)
	{
		$src = array('á', 'ä', 'å', 'Á', 'Ä', 'Å', 'é', 'ë', 'ĕ', 'É', 'Ë', 'Ĕ', 'í', 'ï', 'ĭ', 'Í', 'Ï', 'Ĭ', 'ö', 'ó', 'ő', 'ŏ', 'Ö', 'Ó', 'Ő', 'Ŏ', 'ü', 'ú', 'ű', 'ŭ', 'Ü', 'Ú', 'Ű', 'Ŭ', ' ');
		$rep = array('a', 'a', 'a', 'A', 'A', 'A', 'e', 'e', 'e', 'E', 'E', 'E', 'i', 'i', 'i', 'I', 'I', 'I', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', '_');

		$s_string = str_replace($src, $rep, $s_string);

		$regexp = '/[^A-Za-z0-9_]/';

		if ($b_onlyLettersAndNumbers)
		{
			$regexp = '/[^A-Za-z0-9]/';
		}

		return strtolower( trim( preg_replace($regexp, '', $s_string) ) );
	}



	////////////////////////////////////////////////////////////////////////////////////////
	// filter
	public function FilterAdd($s_filterName)
	{
		if (isset($_POST['filter_post']) && !empty($_POST['filter_post']) && isset($_POST[$s_filterName]) && !empty($_POST[$s_filterName]))
		{
			$_SESSION['filter'][$s_filterName] = $_POST[$s_filterName];
		}
	}

	public function FilterGet($s_filterName)
	{
		if (isset($_SESSION['filter'][$s_filterName]) && !empty($_SESSION['filter'][$s_filterName]))
		{
			return $_SESSION['filter'][$s_filterName];
		}

		return null;
	}

	public function FilterLink( $s_filterName )
	{
		return $_SERVER['REQUEST_URI'] . '/filter_name-' . $s_filterName . '/filter_post-1';
	}
	////////////////////////////////////////////////////////////////////////////////////////
}