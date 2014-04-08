<?php

/**
 * Instaphp
 * 
 * Copyright (c) 2011 randy sesser <randy@instaphp.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author randy sesser <randy@instaphp.com>
 * @copyright 2011, randy sesser
 * @license http://www.opensource.org/licenses/mit-license The MIT License
 * @package Instaphp
 * @filesource
 */

namespace Instaphp;
    
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Oh\InstagramBundle\TokenHandler\TokenHandlerInterface;
/**
 * A simple base class used to instantiate the various other API classes
 * @package Instaphp
 * @version 1.0
 * @author randy sesser <randy@instaphp.com>
 */
class Instaphp extends ContainerAware
{

	/**
	 * @var \Instaphp\Instagram\Users
	 * @access public
	 */
	public $Users = null;
	/**
	 * @var \Instaphp\Instagram\Media
	 * @access public
	 */
	public $Media = null;
	/**
	 * @var \Instaphp\Instagram\Tags
	 * @access public
	 */
	public $Tags = null;
	/**
	 * @var \Instaphp\Instagram\Locations
	 */
	public $Locations = null;

	/**
	 * Contains the last API url called
	 *
	 * @var string
	 **/
	public $url = null;
	
	public $configDefaults = array(
      'version'=> 'v1',
      'endpoint'=> 'https://api.instagram.com',
      'endpoint_timeout'=> '10',
      'endpoint_connect_timeout' => 2,
      'client_id'=> null,
      'client_secret'=> null,
      'oauth_path'=> '/oauth/authorize/?client_id={client_id}&amp;response_type=code&amp;redirect_uri={RedirectUri}',
      'oauth_token_path'=> 'oauth/access_token',
      'redirect_route'=> 'OhInstagramBundle_callback',
	);
	
	public $config; 

	private static $instance = null;

	public function __construct($tokenClass = null, $config = array(), Router $router = null)
	{
		if($tokenClass instanceof TokenHandlerInterface) {
			$token = $tokenClass->getToken();
		}else{
			$token = null;
		}

        $config = array_merge($this->configDefaults, $config);
		
		if($router) {
			$config['redirect_uri'] = $router->generate($config['redirect_route'], array(), true);
		}
		
		self::parseConfigArray($config);
		
		$this->config = $config;
		
		$this->Users = new Instagram\Users($token, $config);
		$this->Media = new Instagram\Media($token, $config);
		$this->Tags = new Instagram\Tags($token, $config);
		$this->Locations = new Instagram\Locations($token, $config);
	}
	
	public static function parseConfigArray(&$config)
	{
		$path = $config['oauth_path'];
		$path = str_replace("{client_id}", $config['client_id'], $path);
		$path = str_replace("{redirect_uri}", urlencode($config['redirect_uri']), $path);

		if (!empty($config['scope'])){
			$path .= '&scope=' . $config['scope'];
		}
			
        $config['oauth_uri'] =  $config['endpoint'] . $path;
		
		$config['oauth_token_uri'] = $config['endpoint'] . $config['oauth_token_path'];
	}
	
	public function GetOAuthUri()
	{
		return $this->config['oauth_uri'];
	}
    
    /**
     * Can be used to update the access token
     */
    public function setAccessToken($token)
    {
        $this->Users->setAccessToken($token);
        $this->Media->setAccessToken($token);
        $this->Tags->setAccessToken($token);
        $this->Locations->setAccessToken($token);
    }

}
