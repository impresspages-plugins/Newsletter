<?php
/**
 * Adds administration grid
 *
 * When this plugin is installed, `Newsletter example` panel appears in administration site.
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

    public function posts()
    {
        $this->setSubmenu();
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
            'allowUpdate' => false
        );

        $fields[] = array(
            'label' => 'E-mail subject',
            'field' => 'emailSubject',
        );

        $fields[] = array(
            'label' => 'E-mail text',
            'field' => 'emailText',
        );

        $config = array(
            'title' => 'Posts',
            'table' => 'newsletterPosts',
            'deleteWarning' => 'Are you sure?',
            'sortField' => 'emailSubject',
            'createPosition' => 'top',
            'pageSize' => ipGetOption('Newsletter.adminPageItems'),
            'fields' => $fields
        );

        return $this->gridGateway($config);
    }


    //SUBSCRIBERS

    public function subscribers()
    {
        $this->setSubmenu();
        return $this->showGrid('subscribersGrid');
    }

    public function subscribersGrid()
    {
        $config = array(
            'title' => 'Subscribers',
            'table' => 'newsletterSubscribers',
            'deleteWarning' => 'Are you sure?',
            'sortField' => 'personOrder',
            'createPosition' => 'top',
            'pageSize' => ipGetOption('Newsletter.adminPageItems'),
            'fields' => array(
                array(
                    'label' => 'Email',
                    'field' => 'Email',
                    'validators' => array('Required', 'Email'),
                )
            )
        );
        return $this->gridGateway($config);
    }

    private function checkMenuStatus($aa, $isDefault = false){

        $query = ipRequest()->getQuery('aa');

        if ($aa == $query){
            return true;
        }else if (($query=='Newsletter') && ($isDefault)){
            return true;
        }else{
            return false;
        }

    }

    //GENERAL

    protected function setMenuItem($title, $method, $isDefault = false){

        $menuItem = new \Ip\Menu\Item();
        $menuItem->setTitle($title); //
        $menuItem->setUrl(ipActionUrl(array('aa' => $method)));
        $menuItem->markAsCurrent($this->checkMenuStatus($method, $isDefault));

        return $menuItem;
    }

    protected function setSubmenu()
    {
        $submenu = array();

        $submenu[] = $this->setMenuItem('Posts', 'Newsletter.posts', true);
        $submenu[] = $this->setMenuItem('Subscribers', 'Newsletter.subscribers');

        ipResponse()->setLayoutVariable('submenu', $submenu);
    }

    protected function showGrid($action)
    {
        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');

        $gateway = array('aa' => 'Newsletter.' . $action);

        $variables = array(
            'gateway' => $gateway
        );
        $content = ipView('/Ip/Internal/Grid/view/placeholder.php', $variables)->render();
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
} 