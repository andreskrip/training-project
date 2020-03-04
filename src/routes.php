<?php
return [
    //главная
    '~^$~' => [\MyProject\Controllers\MainController::class, 'main'],
    //админка
    '~^account/?$~' => [\MyProject\Controllers\UsersController::class, 'account'],
    '~^account/edit/?$~' => [\MyProject\Controllers\UsersController::class, 'editAccount'],
    //пользователи
    '~^users/register$~' => [\MyProject\Controllers\UsersController::class, 'signUp'],
    '~^users/(\d+)/activate/(.+)$~' => [\MyProject\Controllers\UsersController::class, 'activate'],
    '~^users/login/?$~' => [\MyProject\Controllers\UsersController::class, 'login'],
    '~^users/logout/?$~' => [\MyProject\Controllers\UsersController::class, 'logout'],
    //статьи
    '~^articles/?$~' => [\MyProject\Controllers\ArticlesController::class, 'viewAll'],
    '~^articles/create$~' => [\MyProject\Controllers\ArticlesController::class, 'create'],
    '~^articles/(\d+)/?$~' => [\MyProject\Controllers\ArticlesController::class, 'view'],
    '~^articles/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'edit'],
    '~^articles/(\d+)/delete$~' => [\MyProject\Controllers\ArticlesController::class, 'delete'],
    //комментарии
    '~^articles/(\d+)/comments$~' => [\MyProject\Controllers\ArticlesController::class, 'addComment'],
    '~^.*comments/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'editComment'],
    '~^.*comments/(\d+)/delete$~' => [\MyProject\Controllers\ArticlesController::class, 'deleteComment'],
];