<?php

namespace MyProject\Cli;

use MyProject\Services\Db;

class TableBuilder extends AbstractCommand
{
    public function createUsersTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL,
            `nickname` varchar(128) NOT NULL,
            `email` varchar(255) NOT NULL,
            `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
            `role` enum('admin','user') NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `auth_token` varchar(255) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            ALTER TABLE `users`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `nickname` (`nickname`),
            ADD UNIQUE KEY `email` (`email`);
            
            ALTER TABLE `users`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ";
        $db = Db::getInstance();
        return $db->query($sql, [], static::class);
    }

    public function createArticlesTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `articles` (
            `id` int(11) NOT NULL,
            `author_id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `text` text NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            ALTER TABLE `articles`
            ADD PRIMARY KEY (`id`);
            
            ALTER TABLE `articles`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ";
        $db = Db::getInstance();
        return $db->query($sql, [], static::class);
    }

    protected function checkParams(): void
    {
        $this->ensureParamExists('table');
    }

    public function execute(): void
    {
        $tableName = $this->getParam('table');
        $method = 'create' . $tableName . 'Table';
        $result = $this->$method();
        if ($result === null) {
            echo 'Ошибка!' . PHP_EOL;
        } else {
            echo 'Успешно!' . PHP_EOL;
        }
    }


}