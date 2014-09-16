<?php

/**
 * Widget controller
 */

namespace Plugin\Newsletter\Widget\Newsletter;

class Controller extends \Ip\WidgetController
{
    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        $form = \Plugin\Newsletter\Model::createForm();

        // Pass form object to a view file skin/default.php
        $data['form'] = $form;

        if (ipGetOption('Newsletter.confirmSubscribers') == true) {
            $text = 'Please open your mailbox and press the confirmation link to complete the subscription process.';
        } else {
            $text = 'Your e-mail was registered successfully. Thank you very much!';
        }
        $data['thankYouMessage'] = __(
            $text,
            'Newsletter',
            false
        );

        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }
}
