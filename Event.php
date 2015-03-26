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
            self::sendWelcomeEmail($info['email']);
        }
    }

    public static function Newsletter_subscriberConfirmed($info)
    {
        if (ipGetOption('Newsletter.sendWelcomeEmail')) {
            self::sendWelcomeEmail($info['email']);
        }
    }

    protected static function sendWelcomeEmail($email)
    {

        //send welcome email
        $message = ipGetOption('Newsletter.welcomeEmailText');
        $emailHtml = ipView('view/welcomeEmail.php', array("content" => $message))->render();

        ipSendEmail(
            ipGetOption('Newsletter.fromEmail'),
            ipGetOption('Newsletter.fromName'),
            $email,
            $email,
            ipGetOption('Newsletter.confirmEmailSubject'),
            $emailHtml
        );
    }

}
