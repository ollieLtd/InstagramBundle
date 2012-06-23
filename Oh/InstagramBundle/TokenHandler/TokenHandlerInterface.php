<?php

namespace Oh\InstagramBundle\TokenHandler;

interface TokenHandlerInterface
{
	
	/**
	 * return the token however you want
	 */
	public function getToken();
	
	
	/**
	 * check to see if this user logged in
	 * @return boolean
	 */
	public function isLoggedIn();
	
	/**
	 * delete the token
	 */
	public function logout();
	
	/**
	 * set a new token
	 */
	public function setToken($token);
	
}
