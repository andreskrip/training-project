<?php
// подключение автозагрузки композера
require __DIR__ . '/../vendor/autoload.php';

// настройка роутинга во фронт-контроллере
try {
    // получение роута из GET-параметра
    $route = $_GET['route'] ?? '';

    // подключение конфигурации роутов
    $routes = require __DIR__ . '/../src/routes.php';

    $isRouteFound = false;

    // поиск роута в конфигурации
    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRouteFound = true;
            break;
        }
    }

    if (!$isRouteFound) {
        throw new \MyProject\Exceptions\NotFoundException();
    }
    // удаляем ненужное нулевое совпадение
    unset($matches[0]);

    // присвоение найденному роуту контроллера и экшена
    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];

    $controller = new $controllerName();
    $controller->$actionName(...$matches);
    
    // поимка и вывод исключений на уровне фронт-контроллера
} catch (\MyProject\Exceptions\DbException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('500.php', ['error' => $e->getMessage()], 500);
} catch (\MyProject\Exceptions\NotFoundException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('404.php', ['error' => $e->getMessage()], 404);
} catch (\MyProject\Exceptions\UnauthorizedException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('401.php', ['error' => $e->getMessage()], 401);
} catch (\MyProject\Exceptions\ForbiddenException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('403.php', ['error' => $e->getMessage()], 403);
} catch (\MyProject\Exceptions\ActivationException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/users');
    $view->renderHtml('activationError.php', ['error' => $e->getMessage()]);
}
