<?php

namespace MyProject\Cli;

use MyProject\Exceptions\CliException;

abstract class AbstractCommand
{
    private $params;

    // принимаем список параметров и проверяем их
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->checkParams();
    }

    abstract public function execute(): void;

    // проверяем существование нужных параметров
    abstract protected function checkParams(): void;

    protected function getParam(string $paramName): ?string
    {
        return $this->params[$paramName] ?? null;
    }

    protected function ensureParamExists(string $paramName): void
    {
        if (!isset($this->params[$paramName])) {
            throw new CliException('Param with name "' . $paramName . '" is not set');
        }
    }
}