<?php
/**
 * Model. Various database and form data operations.
 */
namespace Plugin\Newsletter;


class Model {

    /**
     * Store e-mail in a database
     * @param $email
     */

    public static function save($email, $langCode = 'en') {

        ipDb()->insert('newsletterSubscribers', array('email' => $email, 'isSubscribed' => 1, 'langCode' => $langCode));
    }

    /**
     * Check if e-mail already recorded to database table
     * @param $email
     */
    public static function isRegistered($email){

        $result = ipDb()->selectAll('newsletterSubscribers', '*', array('email' => $email));
        if (count($result)>0){
            return true;
        }else{
            return false;
        }
    }

    public static function createForm()
    {

        // Create a form object
        $form = new \Ip\Form();

        // Add a text field to form object
        $field = new \Ip\Form\Field\Text(
        array(
        'name' => 'email', // HTML "name" attribute
        'label' => 'E-mail' // Field label that will be displayed next to input field
        ));

        $field->addValidator('Email');

        // Add custom validator for checking if e-mail already exists in a table.
        $customValidator = new ValidateSubscriber();
        $field->addValidator($customValidator);

        $form->addField($field);

        // E-mail is submitted to Site controller's `NewsletterRegistration` action `save`.

        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'sa',
        'value' => 'Newsletter.save',
        ));

        $form->addField($field);

        // Add submit button
        $form->addField(new \Ip\Form\Field\Submit(array('value' => 'Save')));

        return $form;
    }

    public static function getSubscribers($langCode = false){
        if (!$langCode){
            $lang = ipDb()->selectAll('newsletterSubscribers', '*');
        }else{
            $lang = ipDb()->selectAll('newsletterSubscribers', '*', array('langCode' => $langCode));
        }
        return $lang;
    }

    private static function getNewsletterLangCode($id){

        $langCode = ipDb()->selectValue('newsletterPosts', 'langCode', array('id' => $id));
        return $langCode;

    }

    public static function send($newsletterId){

        $langCode = self::getNewsletterLangCode($newsletterId);

        $subscribers = self::getSubscribers($langCode);
        $title = self::getNewsletterTitle($newsletterId);
        $text = self::getNewsletterText($newsletterId);

        foreach ($subscribers as $subscriber){
//            ipSendEmail('info@example.com', 'from name', 'subscriber@example.com', 'subscriber@example.com', ' TEMA', ' TEKSTAS');
            ipSendEmail(ipGetOption('Newsletter.fromEmail'), ipGetOption('Newsletter.fromName'), $subscriber['email'], $subscriber['email'], $title, $text);
        }
    }

    public static function getNewsletterTitle($id){
        $postTitle = ipDb()->selectValue('newsletterPosts', 'emailSubject', array('id' => $id));
        return $postTitle;
    }

    public static function getNewsletterText($id){
        $postText = ipDb()->selectValue('newsletterPosts', 'emailText', array('id' => $id));
        return $postText;
    }


    public static function previewEmailText($value, $listValues){


        $html2text = new \Ip\Internal\Text\Html2Text('<html><body>'.$listValues['emailText'].'</body></html>', false);
        $text = esc($html2text->get_text());
        $text = substr($text, 0, 255);

        return $text;
    }


} 