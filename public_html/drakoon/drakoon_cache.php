<?
/**
 * drakoon-php cache manager
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0b
 */
class Cache
{
	public static $cache_root = '';
	public static $cache_dir = 'cache';

	public static $drakoonCache = null;

	public static function GetDrakoonCache()
	{
		$drakoonCache = Cache::$drakoonCache;

		if ($drakoonCache === null)
		{
			$drakoonCache = Cache::$drakoonCache = new DrakoonCache();
		}

		return $drakoonCache;
	}

	public static function get()         { $a = func_get_args(); return call_user_func_array( array( Cache::GetDrakoonCache(), 'get' ), $a ); }
	public static function set()         { $a = func_get_args(); return call_user_func_array( array( Cache::GetDrakoonCache(), 'set' ), $a ); }
	public static function delete()      { $a = func_get_args(); return call_user_func_array( array( Cache::GetDrakoonCache(), 'delete' ), $a ); }
	public static function deleteGroup() { $a = func_get_args(); return call_user_func_array( array( Cache::GetDrakoonCache(), 'deleteGroup' ), $a ); }
}

class DrakoonCache
{
	var $cache_root = '';
	var $cache_dir = 'cache';

	private $mode = 0777;
	private $betuSzam = 6; // ha a keyben nincs szam, akkor az elso x betu a csoportosito

	public function __construct(  )
	{
		$this->cache_root = ROOT_DIR . '/' . SET_CACHE_DIR;
	}

	public function printCurrentDir(  )
	{
		echo '<pre>';
			echo $this->cache_root . '<hr />';
			echo getcwd(  );
			print_r( $_SERVER );
		echo '</pre>';
	}

	public function get( $key, $group = '' )
	{
		return $this->getcache(  $key, $group  );
	}

	public function set( $key, $value, $group = '', $i_time = 3600 )
	{
		if ( !empty( $key ) )
		{
			$this->savecache( $key, $value, $group, $i_time );
		}
	}

	public function deleteGroup( $group )
	{
		$dir = $this->cache_root;

		$group = explode( '/', $group );

		if ( isset( $group ) && is_array( $group ) )
		{
			foreach ( $group as $value )
			{

				$dir .= '/__g__' . $value;

				if ( !is_dir( $dir ) )
				{
					mkdir( $dir );
				}
			}
		}

		if ( strpos( $dir, $this->cache_dir ) !== false )
		{
			$this->RemoveDirectory( $dir, true );
		}
	}

	public function delete( $key, $group = '' )
	{
		$dir = $this->directory( $key, $group );

		$cachefile = $dir . '/' . $key . '.php';

		if ( is_file( $cachefile ) )
		{
			unlink( $cachefile );
		}
	}


	private function szamok( $s_string )
	{
		$number = preg_replace( "/[^0-9]/", '', $s_string );

		if ( is_numeric( $number ) && $number > 0 )
		{
			return $number;
		}
		else
		{
			return false;
		}
	}

	private function betuk( $s_string )
	{
		$betuk = preg_replace( "/[^a-z]/", '', strtolower( $s_string ) );

		if ( strlen( $betuk ) >= $this->betuSzam )
		{
			$betuk = substr( $betuk, 0, $this->betuSzam );

			return $betuk;
		}
		else
		{
			return false;
		}
	}

	private function directory(  $key, $group = '', $b_makeDir = false )
	{
		umask( 002 );

		$dir = $this->cache_root;
		if ( !is_dir( $dir ) && $b_makeDir )
		{
			mkdir( $dir, $this->mode, true );
		}

		$group = explode( '/', $group );

		if ( isset( $group ) && is_array( $group ) )
		{
			foreach ( $group as $value )
			{

				$dir .= '/__g__' . $value;

				if ( !is_dir( $dir ) && $b_makeDir )
				{
					mkdir( $dir, $this->mode, true );
				}
			}
		}


		$i_num = $this->szamok( $key );

		if ( is_numeric( $i_num ) )
		{
			$dir .= '/' . $this->directorySplit( $i_num );
		}
		else
		{
			$dir .= '/' . $this->betuk( $key );
		}

		if ( !is_dir( $dir ) && $b_makeDir )
		{
			mkdir( $dir, $this->mode, true );
		}

		return $dir;
	}


	private function directorySplit( $i_num, $i_limit = 500 )
	{
		if ( is_numeric( $i_num ) && is_numeric( $i_limit ) )
		{
			return ceil( $i_num / $i_limit ) * $i_limit;
		}
	}


	private function savecache( $key, $value, $group, $i_expire )
	{
		umask( 002 );

		$dir = $this->directory( $key, $group, true );

		if ( !is_dir( $dir ) )
		{
			return false;
		}

		$cachefile = $dir . '/' . $key . '.php';

		$content = '';

		ob_start(  );
			var_export( $value );

			if ( is_array( $value ) )
			{
				$content = substr_replace( ob_get_contents(  ), '$a__data = array', 0, 5 ) . ';';
			}
			else
			{
				$content = '$a__data = ' . ob_get_contents(  ) . ';';
			}
		ob_end_clean(  );

		$content .= "\n" . '$i__expire = ' . intval( $i_expire ) . ';';


		if ( $content != '' )
		{
			$afile = fopen( $cachefile, "a" );
			$ujcontent = "<?php\n" . $content;
			if ( flock( $afile, LOCK_EX ) )
			{
				// do an exclusive lock
				ftruncate(  $afile, 0  );
				fwrite( $afile, $ujcontent );
				flock( $afile, LOCK_UN );
				// release the lock
			}

			fclose( $afile );
		}

	}

	private function getcache( $key, $group )
	{
		$dir = $this->directory( $key, $group );

		$cachefile = $dir . '/' . $key . '.php';

		if ( is_file( $cachefile ) )
		{
			include( $cachefile );

			$modtime = filemtime( $cachefile );

			if ( isset( $i__expire ) && $i__expire != -1 )
			{
				$curtime = time(  ) - $i__expire;
				if ( $modtime < $curtime )
				{
					return false;
				}
			}

			if ( isset( $a__data ) )
			{
				return $a__data;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}




	private function RemoveDirectory( $directory, $empty = FALSE )
	{
	    if( substr( $directory,-1 ) == '/' )
	    {
	        $directory = substr( $directory,0,-1 );
	    }
	    if( !file_exists( $directory ) || !is_dir( $directory ) )
	    {
	        return FALSE;
	    }elseif( is_readable( $directory ) )
	    {
	        $handle = opendir( $directory );
	        while ( FALSE !== ( $item = readdir( $handle ) ) )
	        {
	            if( $item != '.' && $item != '..' )
	            {
	                $path = $directory.'/'.$item;
	                if( is_dir( $path ) )
	                {
	                    $this->RemoveDirectory( $path );
	                }else{
	                    unlink( $path );
	                }
	            }
	        }
	        closedir( $handle );
	        if( $empty == FALSE )
	        {
	            if( !rmdir( $directory ) )
	            {
	                return FALSE;
	            }
	        }
	    }
	    return TRUE;
	}
}