<?
/**
 * drakoon-php base module class
 * @author  Peter Blaho <info@peterblaho.com>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link    https://github.com/Warloxk/drakoon-php
 * @version 1.0
 */

class Module
{
	public $title;
	public $keywords;
	public $pageURL;
	public $description;
	public $siteName;
	public $siteURL;
	public $skin;
	public $access;

	public $view;

	function __construct(  )
	{
		global $drakoon;

		$this->title       = $drakoon->setDefaultTitle;
		$this->keywords    = $drakoon->setDefaultKeywords;
		$this->description = $drakoon->setDefaultDescription;

		$this->siteName = $drakoon->setSiteName;
		$this->skin     = $drakoon->setDefaultSkin;

		$this->access = 0;

		$this->view = new stdClass;
	}

	public function Display()
	{

	}
}