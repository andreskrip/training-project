<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;

class ArticlesController extends AbstractController
{

    public function viewAll(): void
    {
        $articles = Article::findAll();
        $this->view->renderHtml('articles/viewAll.php', ['articles' => $articles]);
    }

    public function view(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }
        //вывод всех комментариев относящихся к определенной статье
        $comments = Comment::getCommentsByArticleId($articleId);

        $this->view->renderHtml('articles/view.php', ['article' => $article, 'comments' => $comments]);
    }

    public function create(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $article = Article::createArticle($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/create.php', ['error' => $e->getMessage()]);
                return;
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('articles/create.php');
        return;
    }

    public function edit(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if (!$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $article->editArticle($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/edit.php', [
                    'error' => $e->getMessage(),
                    'article' => $article
                ]);
                return;
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function delete(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        //удаление всех комментариев относящихся к определенной статье
        Comment::deleteComments($articleId);

        $article->delete();

        header('Location: /', true, 302);
        exit();

    }

    public function addComment(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!empty($_POST)) {
            try {
                $comment = Comment::add($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->setVar('error', $e->getMessage());
                $this->view($_POST['articleId']);
                return;
            }
            header('Location: /articles/' . $_POST['articleId'] . '#comment' . $comment->getId(), true, 302);
            exit();
        }
    }

    public function editComment(int $commentId): void
    {
        $comment = Comment::getById($commentId);

        if ($comment === null) {
            throw new NotFoundException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        // редактировать можно либо автору комментария, либо админу
        if ($this->user->getId() !== $comment->getUserId() && !$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $comment->edit($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('comments/edit.php', [
                    'error' => $e->getMessage(),
                    'comment' => $comment
                ]);
                return;
            }
            header('Location: /articles/' . $comment->getArticleId() . '#comment' . $comment->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('comments/edit.php', ['comment' => $comment]);
    }

    public function deleteComment(int $commentId): void
    {

        $comment = Comment::getById($commentId);

        if ($comment === null) {
            throw new NotFoundException();
        }
        //удалять комментарии может либо автор комментария либо админ
        if ($this->user->getId() !== $comment->getUserId() && !$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        $comment->delete();

        header('Location: ' . $_SERVER['HTTP_REFERER'] . '#comments', true, 302);
        exit();
    }
}
