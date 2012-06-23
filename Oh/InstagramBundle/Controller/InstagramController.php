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

				return $this->redirect($this->generateUrl('homepage'));
			}
			else
			{
				throw new Exception($response->error);
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
		return $this->redirect($this->get('router')->generate('homepage'));
	}

	public function getToken()
	{
		$tokenHandler = $this->get('instaphp_token_handler');
		return $tokenHandler->getToken();
	}

}
