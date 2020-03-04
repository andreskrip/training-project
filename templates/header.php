<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Мой блог' ?></title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<header>
    <div class="title-header"><a href="/">Мой блог</a></div>
    <nav>
        <a href="/articles">Статьи</a>
        <a href="/about-me">Обо мне</a>
        <a href="/about-me">Контакты</a>
    </nav>
    <div class="user-menu">
        <?php
        if (!empty($user)): ?>
            <ul>
                <li>
                    <input type="checkbox" id="user-menu-checkbox" class="user-menu-checkbox">
                    <label for="user-menu-checkbox" class="user-menu-label">Привет, <?= $user->getNickname() ?> ▼</label>
                    <ul class="list">
                        <li><a href="/account">Аккаунт</a></li>
                        <li><a href="/users/logout">Выйти</a></li>
                    </ul>
                </li>
            </ul>
        <?php else: ?>
        <a href="/users/login">Войти</a> | <a href="/users/register">Зарегистрироваться</a>
    </div>
    <?php endif; ?>
</header>
<div class="layout">