<?php
/**
 * Model. Various database and form data operations.
 */
namespace Plugin\Newsletter;


class Model
{

    /**
     * Store e-mail in a database
     * @param $email
     */

    public static function save($email, $langCode = null)
    {

        if ($langCode == null) {
            $langCode = ipContent()->getCurrentLanguage()->getCode();
        }
        $activationkey = '';
        if (ipGetOption('Newsletter.confirmSubscribers') == true) {
            // Generate activation key
            $length = 32;
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            for ($p = 0; $p < $length; $p++) {
                $activationkey .= $characters[mt_rand(0, strlen($characters - 1))];
            }

            $confirmLink = ipRouteUrl('Newsletter_Confirm', array('hash' => $activationkey));
            $message = ipGetOption('Newsletter.confirmEmailMessage');
            $message = str_replace("{{link}}", "<a href=\"" . $confirmLink . "\">" . $confirmLink . "</a>", $message); //old syntax
            $message = str_replace("{link}", "<a href=\"" . $confirmLink . "\">" . $confirmLink . "</a>", $message); //new syntax

            ipSendEmail(
                ipGetOption('Newsletter.fromEmail'),
                ipGetOption('Newsletter.fromName'),
                $email,
                $email,
                ipGetOption('Newsletter.confirmEmailSubject'),
                ipEmailTemplate(array("content" => $message))
            );
        }

        $data =             array(
            'email' => $email,
            'isSubscribed' => 1,
            'isConfirmed' => 0,
            'langCode' => $langCode,
            'hash' => $activationkey
        );

        $id = ipDb()->insert(
            'newsletterSubscribers',
            $data
        );
        $data['id'] = $id;

        ipEvent('Newsletter_subscriberAdded', $data);
    }

    public static function updateFormData($table, array $data, $hash)
    {
        return ipDb()->update($table, $data, array('hash' => $hash));
    }

    /**
     * Check if e-mail already recorded to database table
     * @param $email
     */
    public static function isRegistered($email)
    {

        $result = ipDb()->selectAll('newsletterSubscribers', '*', array('email' => $email));
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSubscriber($email, $languageCode = null)
    {
        $params = array(
            'email' => $email
        );
        if ($languageCode !== null) {
            $params['languageCode'] = $languageCode;
        }
        $result = ipDb()->selectRow('newsletterSubscribers', '*', $params, ' ORDER BY isSubscribed DESC, isConfirmed DESC');
        return $result;
    }



    public static function getSubscriberByHash($hash)
    {
        $params = array();
        $params['hash'] = $hash;
        $result = ipDb()->selectRow('newsletterSubscribers', '*', $params, ' ORDER BY id DESC');
        return $result;
    }


    public static function createForm()
    {

        // Create a form object
        $form = new \Ip\Form();

        $form->setEnvironment(\Ip\Form::ENVIRONMENT_PUBLIC);

        // Add a text field to form object
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'email', // HTML "name" attribute
                'label' => __('E-mail', 'Newsletter', false) // Field label that will be displayed next to input field
            )
        );

        $field->addValidator('Email');
        $field->addValidator('Required');

        // Add custom validator for checking if e-mail already exists in a table.
        $customValidator = new ValidateSubscriber();
        $field->addValidator($customValidator);

        $form->addField($field);

        // E-mail is submitted to Site controller's `NewsletterRegistration` action `save`.

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'sa',
                'value' => 'Newsletter.save',
            )
        );

        $form->addField($field);

        // Add submit button
        $form->addField(new \Ip\Form\Field\Submit(array('value' => __('Subscribe', 'Newsletter', false))));

        return $form;
    }

    public static function getSubscribers($langCode = false)
    {
        if (!$langCode) {
            $lang = ipDb()->selectAll('newsletterSubscribers', '*', array('isSubscribed' => true));
        } else {
            $lang = ipDb()->selectAll(
                'newsletterSubscribers',
                '*',
                array('langCode' => $langCode, 'isSubscribed' => true)
            );
        }
        return $lang;
    }

    public static function getConfirmedSubscribers($langCode = false)
    {
        if (!$langCode) {
            $lang = ipDb()->selectAll(
                'newsletterSubscribers',
                '*',
                array('isSubscribed' => true, 'isConfirmed' => true)
            );
        } else {
            $lang = ipDb()->selectAll(
                'newsletterSubscribers',
                '*',
                array('langCode' => $langCode, 'isSubscribed' => true, 'isConfirmed' => true)
            );
        }
        return $lang;
    }

    private static function getNewsletterLangCode($id)
    {

        $langCode = ipDb()->selectValue('newsletterPosts', 'langCode', array('id' => $id));
        return $langCode;

    }

    public static function send($newsletterId)
    {

        $langCode = self::getNewsletterLangCode($newsletterId);

        if (ipGetOption('Newsletter.confirmSubscribers') == true) {
            $subscribers = self::getConfirmedSubscribers($langCode);
        } else {
            $subscribers = self::getSubscribers($langCode);
        }
        $title = self::getNewsletterTitle($newsletterId);
        $text = self::getNewsletterText($newsletterId);


        foreach ($subscribers as $subscriber) {
            ipSendEmail(
                ipGetOption('Newsletter.fromEmail'),
                ipGetOption('Newsletter.fromName'),
                $subscriber['email'],
                $subscriber['email'],
                $title,
                $text
            );
        }
    }

    public static function getNewsletterTitle($id)
    {
        $postTitle = ipDb()->selectValue('newsletterPosts', 'emailSubject', array('id' => $id));
        return $postTitle;
    }

    public static function getNewsletterText($id)
    {
        $postText = ipDb()->selectValue('newsletterPosts', 'emailText', array('id' => $id));
        return $postText;
    }


    public static function previewEmailText($value, $listValues)
    {


        try {
            $text = Html2Text::convert('<html><body>' . $listValues['emailText'] . '</body></html>');
        } catch (Html2TextException $e) {
            $text = '';
        }
        $text = esc($text);
        $text = substr($text, 0, 255);

        return $text;
    }


}
