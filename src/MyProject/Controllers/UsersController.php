<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ActivationException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UserActivationService;
use MyProject\Services\EmailSender;
use MyProject\Services\UsersAuthService;

class UsersController extends AbstractController
{

    // регистрация на сайте
    public function signUp(): void
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);

                EmailSender::send($user, 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(),
                    'code' => $code
                ]);
                $this->view->renderHtml('users/signUpSuccessful.php');
                return;
            }
        }
        $this->view->renderHtml('users/signUp.php');
    }

    // активация учетной записи по email
    public function activate(int $userId, string $activationCode): void
    {
        $user = User::getById($userId);

        if ($user === null) {
            throw new ActivationException('Пользователь не найден');
        }
        if ($user->getConfirmed()) {
            throw new ActivationException('Пользователь уже подтвержден');
        }

        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);

        if (!$isCodeValid) {
            throw new ActivationException('Неверный код активации');
        }
        $user->activate();
        $this->view->renderHtml('users/activationSuccessful.php');
        UserActivationService::deleteActivationCode($userId);
    }

    // вход в учетную запись
    public function login(): void
    {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UsersAuthService::createAuthToken($user);
                header('Location: ' . $_COOKIE['loginReferer']);
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        setcookie('loginReferer', $_SERVER['HTTP_REFERER'], 0, '/', '', false, true);
        $this->view->renderHtml('users/login.php');
    }

    // выход из учетной записи
    public function logout(): void
    {
        setcookie('token', '', 1, '/');
        header('Location: /');
    }

    // личный кабинет пользователя
    public function account(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        //передаем обратный порядок комментариев и статей, чтобы первыми показывались самые свежие
        if (Comment::getCommentsByUserId($this->user->getId()) !== null) {
            $this->view->setVar('myComments', array_reverse(Comment::getCommentsByUserId($this->user->getId())));
        }
        if (Comment::findAll() !== null) {
            $this->view->setVar('allComments', array_reverse(Comment::findAll()));
        }
        $this->view->setVar('articles', array_reverse(Article::findAll()));
        $this->view->renderHtml('users/account.php', []);
    }

    public function editAccount(): void
    {

        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if (!empty($_POST)) {
            try {
                $this->user->editAccount($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/editAccount.php', ['error' => $e->getMessage(),]);
                return;
            }
        }
        $this->view->renderHtml('users/editAccount.php', []);
    }
}
