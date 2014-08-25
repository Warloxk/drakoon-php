<?
class login extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->access = 0;
		$this->title = 'Login';
	}

	public function Display()
	{
	}

	public function FormPost()
	{
		global $drakoon;

		$_SESSION['nick'] = $_POST['username'];
		$_SESSION['password'] = md5( $_POST['password'] );
		$result = User::Login( $_POST['username'] );

		if ( $result !== false )
		{
			$drakoon->Redirect('/');
		}
		else
		{
			$drakoon->Redirect('/login', '', 'Login failed!');
		}
	}
}