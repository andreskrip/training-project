<?php

namespace MyProject\Models\Users;

use MyProject\Services\Db;

class UserRecoverService
{
    private const TABLE_NAME = 'users_recovery_codes';

    // создание кода активации для пользователя и создание записи в бд
    public static function createRecoveryCode(User $user): string
    {
        // генерируем случайную последовательность символов
        $code = bin2hex(random_bytes(16));

        $db = Db::getInstance();
        $db->query(
            'INSERT INTO `' . self::TABLE_NAME . '` (user_id, code) VALUES (:user_id, :code)',
            [
                ':user_id' => $user->getId(),
                ':code' => $code
            ]
        );
        return $code;
    }

    //проверка кода активации для конкретного пользователя
    public static function checkRecoveryCode(User $user, string $code): bool
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM `' . self::TABLE_NAME . '` WHERE user_id = :user_id AND code = :code',
            [
                ':user_id' => $user->getId(),
                ':code' => $code
            ]
        );
        return !empty($result);

    }

    // удаление кода активации из бд
    public static function deleteRecoveryCode(int $userId): void
    {
        $db = Db::getInstance();
        $delete = $db->query(
            'DELETE FROM `' . self::TABLE_NAME . '` WHERE user_id = :user_id',
            [':user_id' => $userId]
        );
    }
}
