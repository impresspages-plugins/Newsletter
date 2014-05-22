<?php
$routes['newsletterAdmin'] = array(
    'name' => 'Newsletter_admin',
    'plugin' => 'Newsletter',
    'controller' => 'NewsletterAdminController',
    'action' => 'index',
    'environment' => 'admin'
);

$routes['newsletterAdminGrid'] = array(
    'name' => 'Newsletter_admingridgateway',
    'plugin' => 'Newsletter',
    'controller' => 'NewsletterAdminController',
    'action' => 'grid',
    'environment' => 'admin'
);

