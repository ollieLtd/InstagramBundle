<?php

namespace Oh\InstagramBundle\TokenHandler;

use Oh\InstagramBundle\TokenHandler\TokenHandlerInterface;

class CookieToken implements TokenHandlerInterface
{

	public function getToken()
	{
		if(array_key_exists('instaphp', $_COOKIE))
		{
			return $_COOKIE['instaphp'];
		}
		
		return null;
	}
	
	public function isLoggedIn()
	{
		return array_key_exists('instaphp', $_COOKIE) && $_COOKIE['instaphp'];
	}
	
	public function logout()
	{
		setcookie('instaphp', null, strtotime('-1 day'), '/');
	}
	
	public function setToken($token)
	{
		setcookie('instaphp', $token, strtotime('30 days'), '/');
	}
	
}
