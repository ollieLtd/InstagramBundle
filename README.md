OhInstagramBundle
=================

Uses [Instaphp](https://github.com/sesser/Instaphp) by [sesser](https://github.com/sesser) as a Symfony2 service

Installation
------------

Install this bundle as usual by adding to deps:

	// /deps
	[OhEmojiBundle]
	   git=https://github.com/ollietb/OhInstagramBundle
	   target=/bundles/Oh/InstagramBundle

and running the vendors script

    php bin/vendors install

Register the namespace in `app/autoload.php`:

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Oh' => __DIR__.'/../vendor/bundles',
    ));

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

And if you're OK with the provided routes, add these to `app/config/routing.yml`

    OhInstagramBundle:
        resource: "@OhInstagramBundle/Resources/config/routing.yml"
        prefix:   /

Usage (Controller)
------------

    //finding a user
    $query = "snoopdogg";

    $api = $this->get('instaphp');
		
    $response = $api->Users->Find($query);

    $userInfo = $response->data[0];

You can also test if a user is logged in by implementing the 
TokenHandlerInterface. A cookie method is provided, but you can use sessions or 
a database by putting your own class into the parameter %instaphp.token_class%

    //is a user logged in?
    $loggedIn = $this->get('instaphp_token_handler')->isLoggedIn();

Usage (Twig)
------------

Theres a login button included in the views. Just implement this line in your
Twig template

    {% render "OhInstagramBundle:Instagram:instagramLoginStatus" %}

You should set up your Instagram API account to callback to the
"OhInstagramBundle_callback" route, which you can set yourself, or use the ones
provided - "/instaphp/callback".

Tests
-------

@todo

Credits
-------

* Ollie Harridge (ollietb) as the author.
* Randy (sesser) for writing the Instaphp script [https://github.com/sesser/Instaphp]