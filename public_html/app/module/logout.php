<?
class logout extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->access = 0;
		$this->title = 'Logout';
	}

	public function Display()
	{

	}

	public function FormPost()
	{
		global $drakoon;

		User::Logout();

		$drakoon->Redirect( '/index' );
	}
}