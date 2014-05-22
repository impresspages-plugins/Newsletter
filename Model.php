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

    public static function save($email) {

        ipDb()->insert('newsletterSubscribers', array('Email' => $email));
    }

    /**
     * Check if e-mail already recorded to database table
     * @param $email
     */
    public static function isRegistered($email){

        $result = ipDb()->selectAll('newsletterSubscribers', '*', array('Email' => $email));
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

} 