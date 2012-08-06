<?php

namespace Oh\InstagramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InstagramController extends Controller
{

	public function callbackAction(Request $request)
	{
		$code = $request->get('code');

		if (!empty($code))
		{
			//-- Create an Instaphp instance
			/* @var $api Instaphp */
			$api = $this->get('instaphp');

			//-- Authenticate
			$response = $api->Users->Authenticate($code);

			//-- If no errors, grab the access_token (and cookie it, if desired)
			if (empty($response->error))
			{
				$token = $response->auth->access_token;
				$isLoggedIn = $this->get('instaphp_token_handler')->setToken($token);
				$this->get('session')->setFlash('loggedin', 'Thanks for logging in');

				return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_login')));
			}
			else
			{
				$this->createNotFoundException($response->error);
			}
		}

		return new Response(var_dump($response, 1), 200);
	}

	public function instagramOAuthLoginButtonAction()
	{

		$instaphp = $this->get('instaphp');

		$oAuthUrl = $instaphp->GetOAuthUri();

		return $this->render('OhInstagramBundle:Instagram:loginButton.html.twig', array('oAuthUrl' => $oAuthUrl));
	}

	public function userInfoAction()
	{

		$tokenHandler = $this->get('instaphp_token_handler');

		$response = new Response;

		$response->setETag($tokenHandler->getToken());

		if ($response->isNotModified($this->getRequest()))
		{

			return $response;
		}
		else
		{

			$instaphp = $this->get('instaphp');

			$info = $instaphp->Users->info();

			return $this->render('OhInstagramBundle:Instagram:userInfo.html.twig', array('info' => $info), $response);
		}
	}

	public function instagramLoginStatusAction()
	{
		$isLoggedIn = $this->get('instaphp_token_handler')->isLoggedIn();

		if ($isLoggedIn)
		{
			return $this->forward('OhInstagramBundle:Instagram:userInfo');
		}
		else
		{
			return $this->forward('OhInstagramBundle:Instagram:instagramOAuthLoginButton');
		}
	}

	public function logoutAction()
	{
		$isLoggedIn = $this->get('instaphp_token_handler')->logout();
		return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_logout')));
	}

	public function getToken()
	{
		$tokenHandler = $this->get('instaphp_token_handler');
		return $tokenHandler->getToken();
	}
	
	
	/**
     * submit a lat/lng and return a list of locations nearby
     * in the format ?lat=0.0000&lng=0.0000000
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type 
     */
	public function locationSearchAction(Request $request)
	{
		
		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Not authorised');
		}
		
		$lat = $request->query->get('lat', false);
		$lng = $request->query->get('lng', false);
		
		if(!$lat || !$lng) {
			throw $this->createNotFoundException('Not a valid request');
		}
		
		/* @var $api Instaphp */
		$api = $this->get('instaphp');
		
		$locations = $api->Locations->Search(array('lat'=>(float)$lat,'lng'=>(float)$lng));
		
		$response = new Response();                                                
		$response->headers->set('Content-type', 'application/json; charset=utf-8');
		$response->setContent($locations->json);

		return $response;
		
	}

}
