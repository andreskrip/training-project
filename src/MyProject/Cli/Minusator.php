<?php

namespace MyProject\Cli;

use MyProject\Exceptions\CliException;

class Minusator extends AbstractCommand
{

    // проверяем существование нужных параметров
    protected function checkParams(): void
    {
        $this->ensureParamExists('x');
        $this->ensureParamExists('y');
    }

    public function execute(): void
    {
        echo $this->getParam('x') - $this->getParam('y');
    }
}