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
     * @return Model
     */
    public static function instance()
    {
        return new Model();
    }

    public function getSubscriber($email, $languageCode = null)
    {
        return Model::getSubscriber($email, $languageCode);
    }
}
