<?php

namespace Instaphp;

class WebRequestManager
{
	private $key;
	private $request;

	public function __construct($key, $config)
	{
		$this->key = $key;
		$this->request = WebRequest::Instance($config);
	}

	public function __get($name)
	{
		$response = $this->request->GetResponse($this->key);
		return $response->{$name};
	}
}