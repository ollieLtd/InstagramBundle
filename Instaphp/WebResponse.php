<?php
namespace Instaphp;

class WebResponse
{
	public $Content;
	public $Info;

	public function __construct($content = null, $info = null)
	{
		$this->Content = $content;
		$this->Info = $info;
	}
}