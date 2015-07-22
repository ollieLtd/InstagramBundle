Configuring Instaphp
====================

[Instaphp's configuration][instaphp-config] is manageable from the
`oh_instagram.instaphp.config` configuration object.

InstaphpAdapter's configuration keys are the same as the keys used in Instaphp.
There are a number of keys not exposed by this bundle (not all intentionally -
feel free to make a PR):

*   `access_token` - InstaphpAdapter will call `setAccessToken()` if a
    TokenHandler is passed to the constructor,
*   `redirect_uri` - generated from `redirect_route`,
*   `client_ip`,
*   `log_enabled` - but is set to `true` by default,
*   `log_level` - but is set to `Monolog\Logger::DEBUG` by default,
*   `http_useragent` - set to "Instaphp/2.0; cURL/$CURLVERSION; (+http://instaphp.com)",
*   `debug` - set to `false` by default,
*   `event.before`, `event.after`, `event.error`.

[instaphp-config]: https://github.com/sesser/instaphp#configuration
