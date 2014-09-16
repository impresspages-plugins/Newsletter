<?php
/**
 * Custom validator which prevents duplicate subscriptions
 */
namespace Plugin\Newsletter;

class ValidateSubscriber extends \Ip\Form\Validator {

    public function getError($values, $valueKey, $environment) {

        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];

        if (Model::isRegistered($value)){
            $errorText = __('E-mail already subscribed.', 'Newsletter');
            return $errorText;
        } else {
            return false;
        }
    }

}