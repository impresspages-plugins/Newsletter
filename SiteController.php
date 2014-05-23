<?php
/**
 * Plugin's site controller. Validates an e-mail address submitted from Newsletter widget, and stores it to database.
 */
namespace Plugin\Newsletter;


class SiteController extends \Ip\Controller {

    public function save(){
        // Initialize the same form object as it was used to render a form
        $form = new \Ip\Form();

        // Add text input field field
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'email', //html "name" attribute
            ));

        // Validate e-mail
        $field->addValidator('Email');

        // Add custom validator, which checks if e-mail already registered
        $customValidator = new ValidateSubscriber();
        $field->addValidator($customValidator);

        $form->addField($field);

        $postData = ipRequest()->getPost();
        $errors = $form->validate($postData);

        if ($errors) {
            $status = array('status' => 'error', 'errors' => $errors); //success
        } else {
            Model::save(ipRequest()->getPost('email'), ipContent()->getCurrentLanguage()->getCode());
            $status = array('status' => 'ok'); //success
        }

        return new \Ip\Response\Json( $status);
    }

} 