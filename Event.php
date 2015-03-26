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


    public static function Newsletter_subscriberAdded($info)
    {
        if (ipGetOption('Newsletter.sendWelcomeEmail') && !ipGetOption('Newsletter.confirmSubscribers')) {
            self::sendWelcomeEmail($info);
        }
    }

    public static function Newsletter_subscriberConfirmed($info)
    {
        if (ipGetOption('Newsletter.sendWelcomeEmail')) {
            self::sendWelcomeEmail($info);
        }
    }

    protected static function sendWelcomeEmail($info)
    {

        //send welcome email
        $message = ipGetOption('Newsletter.welcomeEmailText');
        $variables = array("content" => $message);
        $variables = array_merge($variables, $info);
        $emailHtml = ipView('view/welcomeEmail.php', $variables)->render();

        ipSendEmail(
            ipGetOption('Newsletter.fromEmail'),
            ipGetOption('Newsletter.fromName'),
            $info['email'],
            $info['email'],
            ipGetOption('Newsletter.welcomeEmailSubject'),
            $emailHtml
        );
    }

}
