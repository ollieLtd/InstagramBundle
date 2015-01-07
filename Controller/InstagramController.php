<?php

namespace Oh\InstagramBundle\Controller;

use Oh\InstagramBundle\Adapter\InstaphpAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InstagramController extends Controller
{

	/**
	 * This Action saves the OAuth token by passing the it to the service `instaphp_token_handler`
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function callbackAction(Request $request)
	{

		$code = $request->get('code');

		if (!empty($code))
		{
			//-- Create an Instaphp instance
			/* @var $api InstaphpAdaptor */
			$api = $this->get('instaphp');

			//-- Authenticate
			$success = $api->Users->Authorize($code);

			if ($success)
			{
				$token = $api->getAccessToken();

				$isLoggedIn = $this->get('instaphp_token_handler')->setToken($token);

				$this->get('session')->getFlashBag()->add('loggedin', 'Thanks for logging in');

				return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_login')));
			}
			else
			{
				throw $this->createNotFoundException();
			}
		}

		throw $this->createNotFoundException('Invalid Request');
	}

	/**
	 * Action which shows the login button
	 *
	 * @return Response
	 */
	public function instagramOAuthLoginButtonAction()
	{

		$instaphp = $this->get('instaphp');

		$oAuthUrl = $instaphp->getOauthUrl();

		return $this->render('OhInstagramBundle:Instagram:loginButton.html.twig', array('oAuthUrl' => $oAuthUrl));
	}

	/**
	 * This Action displays the user info (name and profile pic)
	 *
	 * @return Response
	 */
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

			$info = $instaphp->Users->Info('self');

			return $this->render('OhInstagramBundle:Instagram:userInfo.html.twig', array('info' => $info), $response);
		}
	}

	/**
	 * Checks whether the user is logged in and if they are not, it shows the login button
	 *
	 * @return Response
	 */
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

	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function logoutAction()
	{
		$isLoggedIn = $this->get('instaphp_token_handler')->logout();
		return $this->redirect($this->generateUrl($this->container->getParameter('instaphp.redirect_route_logout')));
	}

	/**
	 * @return string|null
	 */
	public function getToken()
	{
		$tokenHandler = $this->get('instaphp_token_handler');
		return $tokenHandler->getToken();
	}
	
	
	/**
     * submit a lat/lng and return a list of locations nearby
     * in the format ?lat=0.0000&lng=0.0000000
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
	public function locationSearchAction(Request $request)
	{
		
		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Not authorised');
		}
		
		$lat = $request->query->get('lat', false);
		$lng = $request->query->get('lng', false);
		
		if($lat === false || $lng === false) {
             throw $this->createNotFoundException('Not a valid request');
		}
		
		/* @var $api \Instaphp\Instaphp */
		$api = $this->get('instaphp');
		
		$locations = $api->Locations->Search(array('lat'=>(float)$lat,'lng'=>(float)$lng));
                
		$response = new Response();                                                
		$response->headers->set('Content-type', 'application/json; charset=utf-8');
		$response->setContent($locations->json);

		return $response;
		
	}
    
    /**
     * Follow a user using their ID (more efficient) or username
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function followAction(Request $request)
    {
        
		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Not authorised');
		}
        
		/* @var $api InstaphpAdapter */
		$api = $this->get('instaphp');
        
        $userId = $request->request->get('userId');
        
        // if its a username rather than user id
        // if possible always use the ID
        if(!is_numeric($userId))
        {
            $return = $api->Users->Find($userId);
            $user = json_decode($return->json);
            
            // if the username isn't found or there's an error
            if($user->meta->code != 200 || count($user->data) == 0) {
                $response = new Response($return->json, $user->meta->code);                                                
                $response->headers->set('Content-type', 'application/json; charset=utf-8');
                return $response;
            }
            
            // return an error if there is more than one result
            if(count($user->data) > 1) {
                $response = new Response($return->json, 500);                                              
                $response->headers->set('Content-type', 'application/json; charset=utf-8');
                return $response;
            }
            
            $userId = $user->data[0]->id;
        }
        
        $return = $api->Users->Follow($userId);

        return $this->returnInstagramResponse($return);
        
    }
    
    /**
	 * Ajax method to like a mediaId
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function likeAction(Request $request)
    {

		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Not authorised');
		}
        
		/* @var $api \Instaphp\Instaphp */
		$api = $this->get('instaphp');
        
        $mediaId = $request->request->get('mediaId');
        
        $return = $api->Media->Like($mediaId);

        return $this->returnInstagramResponse($return);
    }
    
    /**
     * Ajax method to submit a comment on a photo
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @todo Instagram are currently reviewing applications to comment
     * in an attempt to cut down spam. If this method does not work for you,
     * check the message in the response.
     */
    public function commentAction(Request $request)
    {
		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Not authorised');
		}
        
		/* @var $api \Instaphp\Instaphp */
		$api = $this->get('instaphp');
        
        $mediaId = $request->request->get('mediaId');
        $comment = $request->request->get('comment');
        
        $return = $api->Media->Comment($mediaId, $comment);
        
        return $this->returnInstagramResponse($return);
        
    }
    
    /**
     * Returns the Instagram response
	 *
     * @param type $instagramResponse
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function returnInstagramResponse($instagramResponse)
    {
        try {
            $metaCode = $instagramResponse->meta['code'];
            $json = $instagramResponse->json;
            if($metaCode == 200){
                $response = new Response();                                                
                $response->headers->set('Content-type', 'application/json; charset=utf-8');
                $response->setContent(json_encode($json));

                return $response;
            }
        }catch(\Exception $e) {
            $metaCode = 500;
            $json = json_encode(array('response'=>$instagramResponse, 'message'=>$e->getMessage()));
        }

        $response = new Response($json, $metaCode);                                                
        $response->headers->set('Content-type', 'application/json; charset=utf-8');
        return $response;

        
    }

}
