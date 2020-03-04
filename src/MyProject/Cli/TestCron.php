<?php

namespace MyProject\Cli;

class TestCron extends AbstractCommand
{
    protected function checkParams(): void
    {
        $this->ensureParamExists('x');
        $this->ensureParamExists('y');
    }

    public function execute(): void
    {
        // чтобы проверить работу скрипта, будем записывать в файлик 1.log текущую дату и время
        file_put_contents('C:\\OSPanel\\1.log', date(DATE_ISO8601) . PHP_EOL, FILE_APPEND);
    }
}
