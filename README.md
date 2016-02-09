# OhInstagramBundle

Uses [Instaphp][instaphp] by [sesser][sesser] as a Symfony2 service. Adapted to
use Instaphp v2 as a dependency.

## Installation

Install this bundle as usual by adding to composer.json:

```json
"ollieltd/instagram-bundle": "dev-master"
```

After installing above module you need to update your composer

```bash
composer update
```

Register the bundle in `app/AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    return [
        // ...
        new Oh\InstagramBundle\OhInstagramBundle(),
    ];
}
```

Add the following to `app/config/config.yml`:

```yaml
imports:
    - { resource: '@OhInstagramBundle/Resources/config/services.yml' }

oh_instagram:
    instaphp:
        config:
            client_id: %instagram_api_client_id%
            client_secret: %instagram_api_client_secret%
```

And add these parameters:

```yaml
instagram_api_client_id: YOUR_API_ID
instagram_api_client_secret: YOUR_API_SECRET
```

Most of the Instaphp configuration keys can be set in
`oh_instagram.instaphp.config`. See
[Configuring Instaphp](Resources/docs/Instaphp.md)

And if you're OK with the provided routes, add these to
`app/config/routing.yml`:

```yaml
OhInstagramBundle:
    resource: "@OhInstagramBundle/Resources/config/routing.yml"
    prefix:   /
```

## Usage (Controller)

```php
$query = "snoopdogg";
	
/**
 * @var InstaphpAdapter
 */
$api = $this->get('instaphp');

$userId = $api->Users->FindId($query);
$media = $api->Users->Recent($userId);
```

You can also test if a user is logged in:

```php
//is a user logged in?
$loggedIn = $this->get('instaphp_token_handler')->isLoggedIn();
```

## Usage (Twig)

You should set up your [Instagram API account][instagram_clients] to callback to
the `OhInstagramBundle_callback` route, which you can set yourself, or use the
one provided which is `http://yourserver.com/instaphp/callback`.

For quick testing purposes you can add this to your routing:

```yaml
OhInstagramBundle_check:
    pattern: /checkInstagram
    defaults: { _controller: OhInstagramBundle:Instagram:instagramLoginStatus }
```

Then navigate to `/checkInstagram` to try out the login button.

There's a login button included in the views. Just implement this line in your
Twig template:

```twig
{{ render(controller('OhInstagramBundle:Instagram:instagramLoginStatus')) }}
```

## Instagram Auth Token

There are 2 TokenHandlers included:

### CookieToken 

The Instagram auth code is stored in a cookie

```yaml
services:
    instaphp_token_handler:
        class: Oh\InstagramBundle\TokenHandler\CookieToken
```
 
### UserToken
 
The Instagram auth code is stored in the User Entity. The methods
`setInstagramAuthCode()` and `getInstagramAuthCode()` must be implemented on
your User. When the login call is returned from Instagram, the code is set and
the user is persisted and flushed in the Handler. There is an interface which is
recommended that you use on your entity:
`Oh\InstagramBundle\TokenHandler\UserTokenInterface`

```yaml
services:
    instaphp_token_handler:
        class: Oh\InstagramBundle\TokenHandler\UserToken
            arguments: ['@security.context', '@doctrine.orm.default_entity_manager']
```

### Both

This will look to see if the user can be retrieved from the context and if it
can't it will store the auth code in a cookie.

```yaml
services:
    instaphp_user_token_handler:
        class: Oh\InstagramBundle\TokenHandler\UserToken
        arguments: ['@security.context', '@doctrine.orm.default_entity_manager']
    instaphp_cookie_token_handler:
        class: Oh\InstagramBundle\TokenHandler\CookieToken
    instaphp_token_handler:
        class: Oh\InstagramBundle\TokenHandler\TokenManager
        arguments: ['@instaphp_user_token_handler', '@instaphp_cookie_token_handler']
```

You can also implement your own TokenHandlerInterface to store the auth code
somewhere else, for example in the session.

## Tests

@todo

## Credits

* Ollie Harridge (ollietb) as the author.
* Randy (sesser) for writing [Instaphp][instaphp]

[instaphp]: https://github.com/sesser/instaphp
[sesser]: https://github.com/sesser
[instagram_clients]: http://instagram.com/developer/clients/manage
