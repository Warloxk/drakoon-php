<?
class admin extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->access = 100;
		$this->title = 'Admin';
		$this->skin = '_core';
	}

	public function Display()
	{

	}
}