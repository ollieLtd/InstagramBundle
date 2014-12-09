<?php

namespace Oh\InstagramBundle\TokenHandler;

use Oh\InstagramBundle\TokenHandler\TokenHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserToken implements TokenHandlerInterface
{

    /**
     * The current security context (usually @security.context)
     * @var Symfony\Component\Security\Core\SecurityContextInterface 
     */
    private $securityContext;
    
    /**
     * The currently logged in user
     * @var User 
     */
    private $user = false;
    
    /**
     * The Doctrine entity manager (usually @doctrine.orm.default_entity_manager)
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    
    public function __construct($securityContext, $em) {
        $this->securityContext = $securityContext;
        $token = $securityContext->getToken();
        if($token instanceof TokenInterface) {
            $this->user = $securityContext->getToken()->getUser();
        }
        $this->em = $em;
    }
    
    public function isUserLoggedIn()
    {
        return is_object($this->user);
    }
    
    /**
     * you can set your user manually here
     * @param type $user
     */
    public function setUser($user) {
        $this->user = $user;
    }
    
    /**
     * Gets the token from the user entity
     * @return null
     */
	public function getToken()
	{
		if($this->user && $token = $this->user->getInstagramAuthCode()) {
            return $token;
        }
		
		return null;
	}
	
	public function isLoggedIn()
	{
        if(!is_null($this->getToken()))
        {
            return true;
        }
        return false;
	}
	
	public function logout()
	{
        $this->user->setInstagramAuthCode(null);
        $this->em->persist($this->user);
        $this->em->flush($this->user);
	}
	
	public function setToken($token)
	{
		$this->user->setInstagramAuthCode($token);
        $this->em->persist($this->user);
        $this->em->flush($this->user);
	}
	
}
