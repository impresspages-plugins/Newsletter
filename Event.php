<?php
/**
 * Load JavaScript file.
 */
namespace Plugin\Newsletter;


class Event
{
    /**
     * This method is launched before loading the controller.
     * Add JS and CSS files here.
     */
    public static function ipBeforeController()
    {
        ipAddJs('assets/newsletter.js');
    }

}
