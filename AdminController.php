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

    //POSTS

    /**
     * @ipSubmenu Posts
     * @return string
     */
    public function posts()
    {
        return $this->showGrid('postsGrid');
    }

    public function postsGrid()
    {

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
            'pageSize' => ipGetOption('Newsletter.adminPageItems'),
            'fields' => $fields,
            'allowSort' => false

        );

        return $this->gridGateway($config);
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
        return ipGridController($this->subscribersGrid());
        return $this->showGrid('subscribersGrid');
    }

    protected function subscribersGrid()
    {

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
            'pageSize' => ipGetOption('Newsletter.adminPageItems'),
            'fields' => $fields,
            'allowSort' => false
        );
        return $config;
        return $this->gridGateway($config);
    }

    private function checkMenuStatus($aa, $isDefault = false)
    {

        $query = ipRequest()->getQuery('aa');

        if ($aa == $query) {
            return true;
        } else if (($query=='Newsletter') && ($isDefault)) {
            return true;
        } else {
            return false;
        }

    }

    //GENERAL

    protected function setMenuItem($title, $method, $isDefault = false)
    {

        $menuItem = new \Ip\Menu\Item();
        $menuItem->setTitle($title); //
        $menuItem->setUrl(ipActionUrl(array('aa' => $method)));
        $menuItem->markAsCurrent($this->checkMenuStatus($method, $isDefault));

        return $menuItem;
    }



    protected function showGrid($action)
    {
        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');

        $gateway = array('aa' => 'Newsletter.' . $action);

        $variables = array(
            'gateway' => $gateway
        );
        $content = ipView(ipFile('Ip/Internal/Grid/view/placeholder.php'), $variables)->render();
        $previewTemplate = ipView('view/preview.php')->render();
        ipAddJsVariable('newsletterPreviewTemplate', $previewTemplate);
        return $content;
    }

    protected function gridGateway($config)
    {
        $worker = new \Ip\Internal\Grid\Worker($config);
        $result = $worker->handleMethod(ipRequest());

        if (is_array($result) && !empty($result['error']) && !empty($result['errors'])) {
            return new \Ip\Response\Json($result);
        }

        return new \Ip\Response\JsonRpc($result);

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
