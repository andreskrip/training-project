<?php

namespace MyProject\Models\Users;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{

    protected $nickname;
    protected $email;
    protected $isConfirmed;
    protected $role;
    protected $passwordHash;
    protected $authToken;
    protected $createdAt;


    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getCreatedAt()
    {
        return $this->getCorrectDateTime()->createdAt;
    }

    private function setNickname($nickname): void
    {
        $this->nickname = $nickname;
    }

    private function setEmail($email): void
    {
        $this->email = $email;
    }

    //привязка класса к таблице БД
    protected static function getTableName(): string
    {
        return 'users';
    }

    //валидация данных регистрации и сохранение их в бд
    public static function signUp(array $userData): User
    {
        if (empty ($userData['nickname'])) {
            throw new InvalidArgumentException('Не передан nickname');
        }
        if (!preg_match('/^[a-zA-Z0-9-]+$/', $userData['nickname'])) {
            throw new InvalidArgumentException('Nickname может состоять только латинских символов и цифр');
        }
        if (empty ($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email не корректен');
        }
        if (empty ($userData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }
        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }
        if (static::findByOneColumn('nickname', $userData['nickname']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
        }
        if (static::findByOneColumn('email', $userData['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже существует');
        }

        $user = new User();
        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->isConfirmed = false;
        $user->role = 'user';
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));

        $user->save();
        return $user;
    }

    public function activate(): void
    {
        $this->isConfirmed = true;
        $this->save();
    }

    public static function login(array $loginData): User
    {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не введен email');
        }
        // if (empty($loginData['password'])) {
        // throw new InvalidArgumentException('Не введен пароль');
        // }

        $user = User::findByOneColumn('email', $loginData['email']);
        if ($user === null) {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }

        if (!password_verify($loginData['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный пароль');
        }

        if (!$user->isConfirmed) {
            throw new InvalidArgumentException('Пользователь не подтвержден');
        }

        $user->refreshAuthToken();
        $user->save();

        return $user;
    }

    // обновление токена авторизации при каждой новой авторизации
    public function refreshAuthToken(): void
    {
        $this->authToken = sha1(random_bytes(100) . sha1(random_bytes(100)));
    }

    public function editAccount(array $userData): User
    {

        if (empty ($userData['nickname'])) {
            throw new InvalidArgumentException('Не передан nickname');
        }
        if (!preg_match('/^[a-zA-Z0-9-]+$/', $userData['nickname'])) {
            throw new InvalidArgumentException('Nickname может состоять только латинских символов и цифр');
        }
        if (empty ($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email не корректен');
        }

        //если поля для смены пароля заполнены, то дополнительно проверяем правильность их ввода
        if (!empty($userData['password'] || $userData['newPassword'])) {

            $user = User::findByOneColumn('id', $this->getId());

            if (!password_verify($userData['password'], $user->getPasswordHash())) {
                throw new InvalidArgumentException('Неправильный пароль');
            }
            if (empty($userData['newPassword'])) {
                throw new InvalidArgumentException('Не введен новый пароль');
            }
            if (mb_strlen($userData['newPassword']) < 8) {
                throw new InvalidArgumentException('Новый пароль должен быть не менее 8 символов');
            }
        }
        $this->setNickname($userData['nickname']);
        $this->setEmail($userData['email']);
        $this->passwordHash = password_hash($userData['newPassword'], PASSWORD_DEFAULT);


        $this->save();

        return $this;
    }
}