<?
class access_denied extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->access = 0;
		$this->title = 'Access Denied!';
	}

	public function Display()
	{

	}
}