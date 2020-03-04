<?php
try {
    require __DIR__ . '/../vendor/autoload.php';

    unset($argv[0]);

    // Составляем полное имя класса, добавив нэймспейс
    $className = '\\MyProject\\Cli\\' . array_shift($argv);
    if (!class_exists($className)) {
        throw new \MyProject\Exceptions\CliException('Класс "' . $className . '" не найден');
    }

    // Проверяем, является ли класс подклассом AbstractCommand
    $obj = new ReflectionClass($className);
    if (!$obj->isSubclassOf(\MyProject\Cli\AbstractCommand::class)) {
        throw new \MyProject\Exceptions\CliException($className . ' не наследник класса AbstractCommand');
    }

    // Подготавливаем список аргументов
    $params = [];
    foreach ($argv as $argument) {
        preg_match('/^-(.+)=(.+)$/', $argument, $matches);
        if (!empty($matches)) {
            $paramName = $matches[1];
            $paramValue = $matches[2];

            $params[$paramName] = $paramValue;
        }
    }

    // Создаём экземпляр класса, передав параметры и вызываем метод execute()
    $entity = new $className($params);
    $entity->execute();
} catch (\MyProject\Exceptions\CliException $e) {
    echo 'Error: ' . $e->getMessage();
}
