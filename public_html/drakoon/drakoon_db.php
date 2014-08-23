<?
/**
 * drakoon-php database manager
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0b
 */
class DB
{
	public static $dbName = '';
	public static $user = '';
	public static $password = '';
	public static $host = 'localhost';
	public static $port = null;
	public static $encoding = 'utf8';

	public static $drakoonDB = null;

	public static $error_handler = true;
	public static $nonsql_error_handler = null;

	public static $debug = false;

	public static function Test()
	{
		return self::$user;
	}

	public static function GetDrakoonDB()
	{
		$drakoonDB = DB::$drakoonDB;

		if ($drakoonDB === null)
		{
			$drakoonDB = DB::$drakoonDB = new DrakoonDB();
		}

		if ($drakoonDB->error_handler !== DB::$error_handler) $drakoonDB->error_handler = DB::$error_handler;
		if ($drakoonDB->nonsql_error_handler !== DB::$nonsql_error_handler) $drakoonDB->nonsql_error_handler = DB::$nonsql_error_handler;

		return $drakoonDB;
	}

	public static function query()        { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'query' ), $a ); }
	public static function justQuery()    { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'justQuery' ), $a ); }
	public static function getOne()       { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'getOne' ), $a ); }
	public static function getRow()       { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'getRow' ), $a ); }
	public static function queryCount()   { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'queryCount' ), $a ); }
	public static function numRows()      { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'numRows' ), $a ); }
	public static function queryLog()     { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'queryLog' ), $a ); }
	public static function insert()       { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'insert' ), $a ); }
	public static function insertIgnore() { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'insertIgnore' ), $a ); }
	public static function insertUpdate() { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'insertUpdate' ), $a ); }
	public static function update()       { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'update' ), $a ); }
	public static function insertId()     { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'insertId' ), $a ); }
	public static function nonSQLError()  { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'NonSQLError' ), $a ); }
	public static function queryTest()    { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'queryTest' ), $a ); }
	public static function escapeString() { $a = func_get_args(); return call_user_func_array( array( DB::GetDrakoonDB(), 'EscapeString' ), $a ); }

	public static function close()
	{
		$drakoonDB = DB::$drakoonDB;

		if ($drakoonDB !== null)
		{
			$a = func_get_args();
			return call_user_func_array( array( DB::GetDrakoonDB(), 'close' ), $a );
		}
	}
}

class DrakoonDB
{
	public $dbName = '';
	public $user = '';
	public $password = '';
	public $host = 'localhost';
	public $port = null;

	public $i_queryCount = 0;
	public $a_queryLog = array();
	public $i_numRows = 0;
	public $i_insertId = 0;
	public $encoding = 'utf8';

	public $mysqli = null;

	public $error_handler = true;
	public $nonsql_error_handler = null;

	public $debug = true;

	public function __construct( $host = null, $user = null, $password = null, $dbName = null, $port = null, $encoding = null, $debug = null )
	{
		if ($host === null)     $host     = DB::$host;
		if ($user === null)     $user     = DB::$user;
		if ($password === null) $password = DB::$password;
		if ($dbName === null)   $dbName   = DB::$dbName;
		if ($port === null)     $port     = DB::$port;
		if ($encoding === null) $encoding = DB::$encoding;

		$this->host     = $host;
		$this->user     = $user;
		$this->password = $password;
		$this->dbName   = $dbName;
		$this->port     = $port;
		$this->encoding = $encoding;

		$this->debug = true;

		if ( !$this->port )
		{
			$this->port = ini_get( 'mysqli.default_port' );
		}

		$this->mysqli = new mysqli( $this->host, $this->user, $this->password, $this->dbName, $this->port );
		if ($this->mysqli->connect_error)
		{
			$this->NonSQLError('Unable to connect to MySQL server! Error: ' . $this->mysqli->connect_error);
		}

		$this->mysqli->set_charset($this->encoding);
		$this->i_queryCount = 0;
		$this->a_queryLog = array();
	}

	public function query()
	{
		$args = func_get_args();

		$a = array();

		$this->i_numRows = 0;
		$this->i_queryCount++;

		$sql = $this->ParseQueryParams( $args );

		if ( $this->debug ) $this->a_queryLog[] = $sql;

		//echo 'DRAKOON DB::' . $sql . '<br />';

		$result = $this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );

		if ( $result !== false )
		{
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$a[] = $row;

				$this->i_numRows++;
			}
		}

		$result->free();

		return $a;
	}

	public function justQuery()
	{
		$args = func_get_args();

		$a = array();

		$this->i_numRows = 0;
		$this->i_queryCount++;

		$sql = $this->ParseQueryParams( $args );

		if ( $this->debug ) $this->a_queryLog[] = $sql;

		//echo 'DRAKOON DB::' . $sql . '<br />';

		$this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );

		return $a;
	}

	public function queryTest()
	{
		$args = func_get_args();

		$sql = $this->ParseQueryParams( $args );

		var_dump( $sql );
	}

	public function getOne()
	{
		$args = func_get_args();

		$r = '';

		$this->i_numRows = 0;
		$this->i_queryCount++;

		$sql = $this->ParseQueryParams( $args );

		if ( $this->debug ) $this->a_queryLog[] = $sql;

		$result = $this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		if ( $result !== false )
		{
			$r = $result->fetch_row( );
			$this->i_numRows++;
		}

		$result->free();

		return $r[0];
	}

	public function getRow()
	{
		$args = func_get_args();

		$r = array();

		$this->i_numRows = 0;
		$this->i_queryCount++;

		$sql = $this->ParseQueryParams( $args );

		if ( $this->debug ) $this->a_queryLog[] = $sql;

		$result = $this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		if ( $result !== false )
		{
			$r = $result->fetch_array( MYSQLI_ASSOC );
			$this->i_numRows++;
		}

		$result->free();

		return $r;
	}

	/**
	 * close the database connection
	 * @return void
	 */
	public function close(  )
	{
		$this->mysqli->close();
		DB::$drakoonDB = null;
	}

	public function queryCount(  )
	{
		return $this->i_queryCount;
	}

	public function queryLog(  )
	{
		return $this->a_queryLog;
	}

	public function numRows(  )
	{
		return $this->i_numRows;
	}

	public function insert()
	{
		$args = func_get_args();

		if ( is_array( $args[1] ) && !empty( $args[1] ) )
		{
			foreach ( $args[1] as $key => $inp_value )
			{
				if ( is_array( $inp_value ) )
				{
					$args[1][$key] = $this->ParseParams( $inp_value[0], $inp_value[1], $key );
				}
				else
				{
					$args[1][$key] = $this->ParseParams( $inp_value, '%s', $key );
				}
			}
		}

		$sql = 'INSERT INTO ' . $this->TableName( $args[0] ) . ' (' . implode( ',', array_keys( $args[1] ) ) . ') VALUES (' . implode( ',', $args[1] ) . ')';

		$this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		$this->i_insertId = $this->mysqli->insert_id;

		$this->i_queryCount++;
		if ( $this->debug ) $this->a_queryLog[] = $sql;
	}

	public function insertIgnore()
	{
		$args = func_get_args();

		if ( is_array( $args[1] ) && !empty( $args[1] ) )
		{
			foreach ( $args[1] as $key => $inp_value )
			{
				if ( is_array( $inp_value ) )
				{
					$args[1][$key] = $this->ParseParams( $inp_value[0], $inp_value[1], $key );
				}
				else
				{
					$args[1][$key] = $this->ParseParams( $inp_value, '%s', $key );
				}
			}
		}

		$sql = 'INSERT IGNORE INTO ' . $this->TableName( $args[0] ) . ' (' . implode( ',', array_keys( $args[1] ) ) . ') VALUES (' . implode( ',', $args[1] ) . ')';

		$this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		$this->i_insertId = $this->mysqli->insert_id;

		$this->i_queryCount++;
		if ( $this->debug ) $this->a_queryLog[] = $sql;
	}

	public function update()
	{
		$args = func_get_args();

		if ( is_array( $args[1] ) && !empty( $args[1] ) )
		{
			foreach ( $args[1] as $key => $inp_value )
			{
				if ( is_array( $inp_value ) )
				{
					$args[1][$key] = $this->ParseParams( $inp_value[0], $inp_value[1], $key );
				}
				else
				{
					$args[1][$key] = $this->ParseParams( $inp_value, '%s', $key );
				}
			}
		}

		$where = $this->ParseQueryParams( array_merge( (array)$args[2] , array_slice( $args, 3 ) ) );
		//var_dump($where);

		$update = array();

		foreach ( $args[1] as $key => $value )
		{
			$update[] = $key . '=' . $value;
		}

		$sql = 'UPDATE ' . $this->TableName( $args[0] ) . ' SET ' . implode( ',', $update ) . ' WHERE ' . $where;

		$this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		$this->i_insertId = $this->mysqli->insert_id;

		$this->i_queryCount++;
		if ( $this->debug ) $this->a_queryLog[] = $sql;
	}

	public function insertUpdate()
	{
		$args = func_get_args();

		if ( is_array( $args[1] ) && !empty( $args[1] ) )
		{
			foreach ( $args[1] as $key => $inp_value )
			{
				if ( is_array( $inp_value ) )
				{
					$args[1][$key] = $this->ParseParams( $inp_value[0], $inp_value[1], $key );
				}
				else
				{
					$args[1][$key] = $this->ParseParams( $inp_value, '%s', $key );
				}
			}
		}

		$update = array();
		foreach ( $args[1] as $key => $value )
		{
			$update[] = $key . '=' . $value;
		}


		$sql = 'INSERT INTO ' . $this->TableName( $args[0] ) . ' (' . implode( ',', array_keys( $args[1] ) ) . ') VALUES (' . implode( ',', $args[1] ) . ') ON DUPLICATE KEY UPDATE ' . implode( ',', $update );

		$this->mysqli->query( $sql ) or $this->SQLError( $sql, $this->mysqli->error );
		$this->i_insertId = $this->mysqli->insert_id;

		$this->i_queryCount++;
		if ( $this->debug ) $this->a_queryLog[] = $sql;
	}

	public function insertId()
	{
		return $this->i_insertId;
	}



	private function ParseQueryParams( $a_input )
	{
		//var_dump( $a_input );

		$sql = $a_input[0];

		$a_sql = explode( '%', $sql );

		//var_dump($a_sql);

		foreach ($a_sql as $key => $s_sqlPart)
		{
			if ( $key > 0 )
			{
				$arg = $this->ParseParams( $a_input[$key], '%' . substr( $s_sqlPart, 0, 1 ), '' );

				$a_sql[$key] = $arg . substr( $s_sqlPart, 1);
			}
		}

		return implode( '', $a_sql );
	}

	private function ParseParams( $input, $param, $columnName )
	{
		$r = '';

		switch ( $param )
		{
			case '%s': // string
				$r = '"' . $this->EscapeString( $input ) . '"';
				break;

			case '%i': // integer
				$r = intval( $input );
				break;

			case '%d': // decimal/double
				$r = floatval( $input );
				break;

			case '%l': // literal (no escaping or parsing of any kind -- BE CAREFUL)
				$r = $input;
				break;

			case '%t': // table name
				$r = $this->TableName( $input );
				break;

			case '%w': // where
				$r = $this->ParseQueryParams( array_merge( array( $input->sql ), $input->args ) );
				break;

			case '%m': // minus
				$r = $columnName . '-' . intval( $input );
				break;

			case '%p': // plus
				$r = $columnName . '+' . intval( $input );
				break;


			case '%[': // search string (add % to left for use with LIKE)
				$r = '"%' . $this->EscapeString( $input ) . '"';
				break;

			case '%]': // search string (add % to right for use with LIKE)
				$r = '"' . $this->EscapeString( $input ) . '%"';
				break;

			case '%|': // search string (string surrounded with % for use with LIKE)
				$r = '"%' . $this->EscapeString( $input ) . '%"';
				break;

			default:
				$r = '""';
				break;
		}

		return $r;
	}

	private function TableName( $input )
	{
		return '`' . preg_replace( '/[^a-z0-9_]/', '', str_replace( ' ', '_', strtolower( $input ) ) ) . '`';
	}

	public function EscapeString( $input )
	{
		return $this->mysqli->real_escape_string( trim( $input ) );
	}

	private function SQLError( $sql, $error )
	{
		if ( $this->error_handler )
		{
			$error_handler = is_callable( $this->error_handler ) ? $this->error_handler : 'DrakoonDBErrorHandler';

			call_user_func( $error_handler, array(
				'type' => 'sql',
				'query' => $sql,
				'error' => $error
			) );
		}
	}

	private function NonSQLError($message)
	{
		if ( $this->throw_exception_on_nonsql_error )
		{
			$exception = new DrakoonDBException( $message );
			throw $exception;
		}

		$error_handler = is_callable( $this->nonsql_error_handler ) ? $this->nonsql_error_handler : 'DrakoonDBErrorHandler';

		call_user_func( $error_handler, array( 'type' => 'nonsql', 'error' => $message ) );
	}
}



/**
 * DrakoonDBErrorHandler default error handler
 * @param array $params
 */
function DrakoonDBErrorHandler( $params )
{
	if ( isset( $params['query'] ) ) $out[] = "DRAKOON DB - QUERY: " . $params['query'];
	if ( isset( $params['error'] ) ) $out[] = "DRAKOON DB - ERROR: " . $params['error'];

	$out[] = "";

	if ( php_sapi_name() == 'cli' && empty( $_SERVER['REMOTE_ADDR'] ) )
	{
		echo implode("\n", $out);
	}
	else
	{
		echo implode("<br />\n", $out);
	}

	die();
}



/**
 * DrakoonDBException
 */
class DrakoonDBException extends Exception
{
	protected $query = '';

	function __construct( $message = '', $query = '')
	{
		parent::__construct( $message );
		$this->query = $query;
	}

	public function getQuery()
	{
		return $this->query;
	}
}



/**
 * A helper class for building the WHERE part of an SQL string out of pieces.
 */
class Where
{
	public $sql  = '';
	public $args = array();

	public function add()
	{
		$args = func_get_args();
		$sql  = array_shift( $args );

		$this->args = array_merge( $this->args, $args );
		$this->sql .= ' (' . $sql . ')';
	}

	public function addAnd()
	{
		$args = func_get_args();
		$sql  = array_shift( $args );

		$this->args = array_merge( $this->args, $args );
		$this->sql .= ( ( !empty( $this->sql ) ) ? ' AND' : '' ). ' (' . $sql . ')';
	}

	public function addOr()
	{
		$args = func_get_args();
		$sql  = array_shift( $args );

		$this->args = array_merge( $this->args, $args );
		$this->sql .= ( ( !empty( $this->sql ) ) ? ' OR' : '' ). ' (' . $sql . ')';
	}
}