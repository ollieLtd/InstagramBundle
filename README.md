OhInstagramBundle
=================

Uses [Instaphp](https://github.com/sesser/Instaphp) by [sesser](https://github.com/sesser) as a Symfony2 service. Adapted to use Instaphp v2 as a dependency.

Installation
------------

Install this bundle as usual by adding to composer.json:

    "ollieltd/instagram-bundle": "dev-master"
    
After installing above module you need to update your composer

    run the command “php composer.phar update”

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Oh\InstagramBundle\OhInstagramBundle(),
        );
    }

Add the following line to `app/config/config.yml`:

	imports:
		- { resource: @OhInstagramBundle/Resources/config/services.yml }

Add the following config to your parameters.yml:

    instagram_api_client_id: YOUR_API_ID
    instagram_api_client_secret: YOUR_API_SECRET
    instagram_timeout: 30
    instagram_connect_timeout: 8

And if you're OK with the provided routes, add these to `app/config/routing.yml`

    OhInstagramBundle:
        resource: "@OhInstagramBundle/Resources/config/routing.yml"
        prefix:   /

Usage (Controller)
------------

	$query = "snoopdogg";
	
	/**
	 * @var InstaphpAdapter
	 */
	$api = $this->get('instaphp');

	$userId = $api->Users->FindId($query);

	$media = $api->Users->Recent($userId);


// NB: pagination no longer compatible

You can also test if a user is logged in.

    //is a user logged in?
    $loggedIn = $this->get('instaphp_token_handler')->isLoggedIn();

Usage (Twig)
------------

You should set up your [Instagram API account](http://instagram.com/developer/clients/manage/) to callback to the
"OhInstagramBundle_callback" route, which you can set yourself, or use the one
provided which is "http://yourserver.com/instaphp/callback".

For quick testing purposes you can add this to your routing

    OhInstagramBundle_check:
        pattern: /checkInstagram
        defaults: { _controller: OhInstagramBundle:Instagram:instagramLoginStatus }

Then navigate to /checkInstagram to try out the login button

Theres a login button included in the views. Just implement this line in your
Twig template

    {{ render(controller('OhInstagramBundle:Instagram:instagramLoginStatus')) }}



Instagram Auth Token
-----------

There are 2 TokenHandlers included.

1.  CookieToken - The Instagram auth code is stored in a cookie

        services:
            instaphp_token_handler:
                class:            Oh\InstagramBundle\TokenHandler\CookieToken
 
2.  UserToken - The Instagram auth code is stored in the User Entity. The methods 
`setInstagramAuthCode()` and `getInstagramAuthCode()` must be implemented on your 
User. When the login call is returned from Instagram, the code is set and the 
user is persisted and flushed in the Handler. There is an interface which is
recommended that you use on your Entity `Oh\InstagramBundle\TokenHandler\UserTokenInterface`

        services:
            instaphp_token_handler:
                class:            Oh\InstagramBundle\TokenHandler\UserToken
                arguments:        [@security.context, @doctrine.orm.default_entity_manager]

3.  Both - This will look to see if the user can be retrieved from the context
and if it can't it will store the auth code in a cookie.

        services:
            instaphp_user_token_handler:
                class:            Oh\InstagramBundle\TokenHandler\UserToken
                arguments:        [@security.context, @doctrine.orm.default_entity_manager]
            instaphp_cookie_token_handler:
                class:            Oh\InstagramBundle\TokenHandler\CookieToken
            instaphp_token_handler:
                class:            Oh\InstagramBundle\TokenHandler\TokenManager
                arguments:        [@instaphp_user_token_handler, @instaphp_cookie_token_handler]

You can also implement your own TokenHandlerInterface to store the auth code
somewhere else, like the session etc.

Tests
-------

@todo

Credits
-------

* Ollie Harridge (ollietb) as the author.
* Randy (sesser) for writing the Instaphp script at [https://github.com/sesser/Instaphp]
