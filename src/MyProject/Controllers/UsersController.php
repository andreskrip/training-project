<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ActivationException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UserActivationService;
use MyProject\Models\Users\UserResetService;
use MyProject\Services\EmailSender;
use MyProject\Services\UsersAuthService;

class UsersController extends AbstractController
{

    // регистрация на сайте
    public function signUp(): void
    {
        // проверяем корректность введеных данных
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            // создаем код активации пользователя
            $code = UserActivationService::createActivationCode($user);

            // и отсылаем его на почту пользователя
            EmailSender::send($user, 'Активация', 'userActivation.php', [
                'userId' => $user->getId(),
                'code' => $code
            ]);

            // если все прошло успешно - выводим сообщение об успехе
            $this->view->renderHtml('users/signUpSuccessful.php');
            return;
        }
        $this->view->renderHtml('users/signUp.php');
    }

    // активация учетной записи по email
    public function activate(int $userId, string $activationCode): void
    {
        // проверяем существование пользователя
        $user = User::getById($userId);

        if ($user === null) {
            throw new ActivationException('Пользователь не найден');
        }
        if ($user->getConfirmed()) {
            throw new ActivationException('Пользователь уже подтвержден');
        }

        // проверяем код активации из письма с кодом активации в бд
        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
        if (!$isCodeValid) {
            throw new ActivationException('Неверный код активации');
        }

        // если все проверки пройдены - активируем пользователя
        $user->activate();

        // выводим сообщение об успешной активации
        $this->view->renderHtml('users/activationSuccessful.php');

        // удаляем код активации из бд
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

    // редактирование профиля
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

    // заявка на сброс пароля
    public function resetPassword(): void
    {
        // если пользователь авторизован - перенаправлять на главную
        if ($this->user !== null) {
            header('Location: /');
            exit();
        }

        // проверяем корректность введенного e-mail
        if (!empty($_POST)) {
            try {
                $user = User::validateResetPassword($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/resetPassword.php', ['error' => $e->getMessage()]);
                return;
            }

            // создаем код сброса
            $code = UserResetService::createResetCode($user);

            // отправляем код сброса на почту
            EmailSender::send($user, 'Сброс пароля на ' . $_SERVER['HTTP_HOST'], 'userReset.php', [
                'userId' => $user->getId(),
                'userNickname' => $user->getNickname(),
                'code' => $code
            ]);

            // если все прошло успешно - выводим сообщение с успехом
            $this->view->renderHtml('users/resetSuccessful.php');
            return;
        }
        $this->view->renderHtml('users/resetPassword.php');
    }

    // проверка и подтверждение нового пароля
    public function newPassword(int $userId, string $recoveryCode): void
    {
        // ищем пользователя по id
        $user = User::getById($userId);
        if ($user === null) {
            throw new ActivationException('Пользователь не найден');
        }

        // проверяем код сброса из базы с кодом сброса из письма
        $isCodeValid = UserResetService::checkResetCode($user, $recoveryCode);
        if (!$isCodeValid) {
            throw new ActivationException('Неверный код восстановления');
        }
        // устанавливаем новый пароль
        if (!empty($_POST)) {
            try {
                $user->newPassword($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/newPassword.php', [
                    'error' => $e->getMessage(),
                    'userId' => $userId,
                    'recoveryCode' => $recoveryCode
                ]);
                return;
            }
            // если проверки прошли успешно и пароль установлен - выводим сообщение об успехе
            $this->view->renderHtml('users/newPasswordSuccessful.php');

            // удаляем код сброса из бд
            UserResetService::deleteResetCode($userId);
            return;
        }
        $this->view->renderHtml('users/newPassword.php', [
            'userId' => $userId,
            'recoveryCode' => $recoveryCode
        ]);
    }
}
