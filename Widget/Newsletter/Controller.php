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
            $text = __('Please open your mailbox and press the confirmation link to complete the subscription process.', 'Newsletter', false);
        } else {
            $text = __('You have successfully subscribed to our newsletter.', 'Newsletter', false);
        }
        $data['thankYouMessage'] = __(
            $text,
            'Newsletter',
            false
        );

        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }
}
