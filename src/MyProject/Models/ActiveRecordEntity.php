<?php

namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity implements \JsonSerializable
{
    protected $id;

    // получение id нужного объекта
    public function getId(): int
    {
        return $this->id;
    }

    // имя таблицы бд, соответствующей нужному объекту
    abstract protected static function getTableName(): string;

    // переименование имен_столбцов в именаСвойств объектов при экспорте из бд
    public function __set(string $name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    // переименование camelCase в нижнее_подчеркивание
    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }

    // переименование нижнего_подчеркивания в camelCase
    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    // вывод содержимого таблицы соответствующего объекта
    public static function findAll(): ?array
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    // вывод полной записи из таблицы соответствующего объекта по id
    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    // вывод всех комментариев, относящихся к определенному article_id
    public static function getCommentsByArticleId(int $articleId): ?array
    {
        $db = Db::getInstance();
        $entries = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE article_id=:articleId;',
            [':articleId' => $articleId],
            static::class
        );
        return $entries ? $entries : null;
    }

    // вывод всех комментариев определенного пользователя
    public static function getCommentsByUserId(int $userId): ?array
    {
        $db = Db::getInstance();
        $entries = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE user_id=:userId ORDER BY `created_at` DESC;',
            [':userId' => $userId],
            static::class
        );
        return $entries ? $entries : null;
    }

    // получение всех имен свойств объекта и приведение их в нужный формат для добавления/обновления данных в бд
    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();
        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }
        return $mappedProperties;
    }

    // выбор операции для сохранения данных в бд
    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
    }

    // подготовка данных для обновления в бд
    private function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index;
            $columns2params[] = $column . ' = ' . $param;
            $params2values[$param] = $value;
            $index++;
        }
        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id=' . $this->id;
        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);
    }

    // подготовка данных для добавления новой записи в таблицу бд
    private function insert(array $mappedProperties): void
    {
        // фильтруем массив от null-значений, которые автоматически сгенерируются в бд
        $filterProperties = array_filter($mappedProperties);
        // создаем пустые массивы для столбцов, их значений и бинд параметров PDO
        $columns = [];
        $values = [];
        $params = [];

        // разбиваем свойства объектов на столбцы, значения и бинд-параметры
        foreach ($filterProperties as $column => $value) {
            $columns [] = '`' . $column . '`';
            $valueName = ':' . $column;
            $values[] = $valueName;
            $params[$valueName] = $value;
        }

        // превращаем массив столбцов и значений в список через запятую для SQL-запроса
        $columnsViaCommas = implode(', ', $columns);
        $valuesViaCommas = implode(', ', $values);

        // формируем SQL-запрос
        $sql = 'INSERT INTO `' . static::getTableName() . '` (' . $columnsViaCommas . ') VALUES (' . $valuesViaCommas . ');';
        $db = Db::getInstance();
        $db->query($sql, $params, static::class);
        $this->id = $db->getLastInsertId();
        $this->refresh();
    }

    // заполнение недостающих свойств нового объекта при добавлении
    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        foreach ($objectFromDb as $property => $value) {
            $this->$property = $value;
        }
    }

    // подготовка запроса на удаление строки из бд по id
    public function delete(): void
    {
        $db = Db::getInstance();
        $db->query('DELETE FROM `' . static::getTableName() . '` WHERE id = :id;', [':id' => $this->id]);
        $this->id = null;
    }

    // удаление всех комментариев, относящихся к определенной статье
    public static function deleteComments($articleId): void
    {
        $db = Db::getInstance();
        $db->query('DELETE FROM `' . static::getTableName() . '` WHERE article_id = :article_id;', [':article_id' => $articleId]);
    }

    // поиск и вывод записи с определенным значением отпределенного столбца
    public static function findByOneColumn(string $columnName, $value): ?self
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1;',
            [':value' => $value],
            static::class
        );
        if ($result === []) {
            return null;
        }
        return $result[0];
    }

    // форматирование времени из бд в корректный вид
    protected function getCorrectDateTime(string $columnName = 'created_at'): self
    {
        $sql = "SET lc_time_names = 'ru_RU'";
        $sql2 = 'SELECT DATE_FORMAT(' . $columnName . ',"%d %M %Y в %H:%i") AS ' . $columnName . '
                FROM `' . static::getTableName() . '`
                WHERE id =:id';

        $db = Db::getInstance();
        $db->query($sql, [], static::class);
        $correctDateTime = $db->query($sql2, [':id' => $this->getId()], static::class);
        return $correctDateTime[0];
    }

    // преобразование объектов в JSON формат
    public function jsonSerialize()
    {
        return $this->mapPropertiesToDbFormat();
    }
}