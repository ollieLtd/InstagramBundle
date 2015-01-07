<?php
/**
 * Created by PhpStorm.
 * User: ollie
 * Date: 07/01/2015
 * Time: 13:23
 */

namespace Oh\InstagramBundle\TokenHandler;

/**
 * Interface UserTokenInterface
 *
 * Implement this on your User Entity to save the auth tokens
 *
 * @package Oh\InstagramBundle\TokenHandler
 */
interface UserTokenInterface {

    public function setInstagramAuthCode();

    public function getInstagramAuthCode();
}