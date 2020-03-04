<?php

namespace MyProject\Controllers;

use MyProject\Models\Articles\Article;

class MainController extends AbstractController
{

    public function main(): void
    {
        $this->view->renderHtml('main/main.php', []);
    }
}