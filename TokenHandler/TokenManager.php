<?php

namespace Oh\InstagramBundle\TokenHandler;

use Oh\InstagramBundle\TokenHandler\TokenHandlerInterface;

/**
 * If the user is logged in then lets use the UserToken, otherwise use CookieToken
 */
class TokenManager implements TokenHandlerInterface
{

    /**
     * A token handler
     * @var Oh\InstagramBundle\TokenHandler\TokenHandlerInterface
     */
    private $tokenHandler;
    
    
    public function __construct($userHandler, $cookieHandler) {
        if($userHandler->isUserLoggedIn()) {
            $this->tokenHandler = $userHandler;
        }else {
            $this->tokenHandler = $cookieHandler;
        }
    }
    
	public function getToken()
	{
        return $this->tokenHandler->getToken();
	}
	
	public function isLoggedIn()
	{
        return $this->tokenHandler->isLoggedIn();
	}
	
	public function logout()
	{
		return $this->tokenHandler->logout();
	}
	
	public function setToken($token)
	{
		return $this->tokenHandler->setToken($token);
	}
	
}
