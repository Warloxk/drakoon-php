<?
class page_not_found extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->access = 0;
		$this->title = 'Page not found!';
	}

	public function Display()
	{

	}
}