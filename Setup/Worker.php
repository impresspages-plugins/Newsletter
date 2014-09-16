<?php

namespace Plugin\Newsletter\Setup;

class Worker extends \Ip\SetupWorker
{

    /**
     * Create SQL table on plugin activation
     */
    public function activate()
    {
        $sql = '
        CREATE TABLE IF NOT EXISTS
           ' . ipTable('newsletterSubscribers') . '
        (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `personOrder` double,
        `email` varchar(255),
        `langCode` varchar(5),
        `isSubscribed` boolean,
        `isConfirmed` boolean,
        `hash` varchar(32),
        PRIMARY KEY (`id`)
        )';
        ipDb()->execute($sql);


        $sql = '
        CREATE TABLE IF NOT EXISTS
           ' . ipTable('newsletterPosts') . '
        (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `postOrder` double,
        `emailSubject` varchar(255),
        `emailText` text,
        `langCode` varchar(5),
        PRIMARY KEY (`id`)
        )';
        ipDb()->execute($sql);

        ipSetOption('Newsletter.fromEmail', ipGetOptionLang('Config.websiteEmail'));

    }

    public function deactivate()
    {
    }

    public function remove()
    {
    }

}

