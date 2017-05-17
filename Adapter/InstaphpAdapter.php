<?php

namespace Oh\InstagramBundle\Adapter;

use Instaphp\Instaphp;
use Instaphp\Instagram\Response;
use Instaphp\Exceptions\InstaphpException;
use Symfony\Component\Routing\RouterInterface;
use Oh\InstagramBundle\TokenHandler\TokenHandlerInterface;
use Monolog\Logger;

class InstaphpAdapter extends Instaphp
{
	/**
	 * @var array Storage for the endpoints
	 */
	protected static $endpoints = array();

	/**
	 * @var array Available endpoints
	 */
	protected static $availableEndpoints = array('media', 'users', 'tags', 'locations', 'subscriptions', 'direct');

	/**
	 * Different construct to integrate better with Symfony2
	 */
	public function __construct($tokenClass = null, $config = array(), RouterInterface $router = null)
	{
        $curlVersion = curl_version();
		$ua = sprintf('Instaphp/2.0; cURL/%s; (+http://instaphp.com)', $curlVersion['version']);

		$defaults = array(
			'client_id'	            => '',
			'client_secret'         => '',
			'access_token'          => '',
			'redirect_uri'          => '',
			'client_ip'             => '',
			'scope'                 => 'comments+relationships+likes',
			'log_enabled'           => true,
			'log_level'             => Logger::DEBUG,
			'api_protocol'          => 'https',
			'api_host'              => 'api.instagram.com',
			'api_version'           => 'v1',
			'http_useragent'        => $ua,
			'http_timeout'          => 6,
			'http_connect_timeout'  => 2,
            'verify'                => true,
			'debug'                 => false,
			'event.before'          => array(),
			'event.after'           => array(),
			'event.error'           => array(),
			'oauth_token_path'      => 'oauth/access_token',
			'redirect_route'        => 'OhInstagramBundle_callback',
        );

		// If a router is passed, generate the redirect url from the route
		if ($router) {
			$config['redirect_uri'] = $router->generate($config['redirect_route'], array(), true);
		}

		$this->config = $config + $defaults;

		// Can't do anything without a client_id...
		if (empty($this->config['client_id'])) {
		    throw new InstaphpException('Invalid client id');
		}

		// Get the token
		if (empty($this->config['access_token'])) {
			if ($tokenClass instanceof TokenHandlerInterface) {
				$token = $tokenClass->getToken();
			} else {
				$token = null;
			}

			$this->setAccessToken($token);
		}
	}
}
