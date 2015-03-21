<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 15.3.12
 * Time: 01.16
 */

namespace Plugin\Newsletter;


class Service
{
    /**
     * @return Service
     */
    public static function instance()
    {
        return new Service();
    }

    public function getSubscriber($email, $languageCode = null)
    {
        return Model::getSubscriber($email, $languageCode);
    }

    public function subscribe($email, $languageCode = null)
    {
        return Model::save($email, $languageCode);
    }
}
