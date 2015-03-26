<?php
/**
 * Adds administration grid
 *
 * When this plugin is installed, `Newsletter` panel appears in administration site.
 *
 */

namespace Plugin\Newsletter;


class AdminController
{


    public function index()
    {
        return $this->posts();
    }

    /**
     * @ipSubmenu Posts
     * @return string
     */
    public function posts()
    {
        $this->init();

        $fields = array();

        $fields[] = array(
            'label' => '',
            'field' => 'id',
            'preview' => function ($id, $record) {
                return '<button type="button" data-id="'.escAttr($record['id']).'"  data-emailSubject="'.escAttr($record['emailSubject']).'" data-emailText="'.escAttr($record['emailText']).'" class="btn btn-default ipsPreview">Preview</button>';
            },
            'allowUpdate' => false,
            'allowCreate' => false,
            'allowSearch' => false
        );



        $fields[] = array(
            'label' => '',
            'field' => 'id',
            'preview' => function ($id, $record) {
                return '<button type="button" data-id="'.escAttr($record['id']).'"  data-emailSubject="'.escAttr($record['emailSubject']).'" data-emailText="'.escAttr($record['emailText']).'" class="btn btn-default ipsSend">Send</button>';
            },
            'allowUpdate' => false,
            'allowCreate' => false,
            'allowSearch' => false
        );

        $fields[] = array(
            'label' => 'E-mail subject',
            'field' => 'emailSubject',
        );

        $fields[] = array(
            'label' => 'E-mail text',
            'field' => 'emailText',
            'type' => 'RichText',
            'preview' => '\Plugin\Newsletter\Model::previewEmailText'
        );


        $languages = self::getLanguages();

        $fields[] = array(
            'label' => 'Language code',
            'field' => 'langCode',
            'type' => 'Select',
            'values' => $languages
        );

        $config = array(
            'title' => 'Posts',
            'table' => 'newsletterPosts',
            'deleteWarning' => 'Are you sure?',
            'sortField' => 'postOrder',
            'createPosition' => 'top',
            'fields' => $fields,
            'allowSort' => false

        );
        return ipGridController($config);
    }



    public function send()
    {

        $newsletterId = ipRequest()->getPost('id');
        if (isset($newsletterId) && is_numeric($newsletterId)) {
            Model::send($newsletterId);
            return new \Ip\Response\Json( array('status' => 'success', 'message' => 'Messages were sent successfully.'));
        } else {
            return new \Ip\Response\Json( array('status' => 'error', 'message' => 'Error occurred.'));
        }



    }

    /**
     * @return string
     * @ipSubmenu Subscribers
     */
    public function subscribers()
    {
        $this->init();
        $fields = Array();

        $fields[] =
            array(
                'label' => __('Email', 'Newsletter-admin', false),
                'field' => 'email',
                'validators' => array('Required', 'Email'),
            );

        $fields[] =
            array(
                'label' => __('Confirmed email', 'Newsletter-admin', false),
                'type' => 'Checkbox',
                'field' => 'isConfirmed'
            );

        $fields[] =
            array(
                'label' => __('Subscribed', 'Newsletter-admin', false),
                'type' => 'Checkbox',
                'field' => 'isSubscribed'
            );

        $languages = self::getLanguages();

        $fields[] = array(
            'label' => __('Language code', 'Newsletter-admin', false),
            'field' => 'langCode',
            'type' => 'Select',
            'values' => $languages
        );




        $config = array(
            'title' => __('Subscribers', 'Newsletter-admin', false),
            'table' => 'newsletterSubscribers',
            'deleteWarning' => 'Are you sure?',
            'sortField' => 'personOrder',
            'createPosition' => 'top',
            'fields' => $fields,
            'allowSort' => false
        );

        return ipGridController($config);
    }




    //GENERAL


    private function init()
    {
        $previewTemplate = ipView('view/preview.php')->render();
        ipAddJsVariable('newsletterPreviewTemplate', $previewTemplate);
    }

    private static function getLanguages()
    {

        $langObjects = ipContent()->getLanguages();
        $languages = Array();

        foreach ($langObjects as $langObject){
            $language = array($langObject->getCode(), $langObject->longDescription);
            $languages[] = $language;
        }

        return $languages;
    }
}
