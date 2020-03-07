<?php

namespace MyProject\Models\Comments;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;
use MyProject\Models\Articles\Article;

class Comment extends ActiveRecordEntity
{
    protected $userId;
    protected $articleId;
    protected $text;
    protected $createdAt;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAuthor(): string
    {
        $user = User::getById($this->userId);
        return $user->getNickname();
    }

    public function getCreatedAt(): string
    {
        return $this->getCorrectDateTime()->createdAt;
    }

    public function setAuthor(User $user): void
    {
        $this->userId = $user->getId();
    }

    private function setArticleId($articleId): void
    {
        $this->articleId = $articleId;
    }

    public function setText($text): void
    {
        $this->text = $text;
    }

    protected static function getTableName(): string
    {
        return 'comments';
    }

    public static function add(array $fields, User $user): Comment
    {
        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Отсутствует текст комментария');
        }

        $commment = new Comment();
        $commment->setAuthor($user);
        $commment->setArticleId($fields['articleId']);
        $commment->setText($fields['text']);

        $commment->save();

        return $commment;
    }

    public function edit(array $comment): Comment
    {
        if (empty($comment['text'])) {
            throw new InvalidArgumentException('Отсутствует текст комментария');
        }

        $this->setText($comment['text']);

        $this->save();

        return $this;
    }

    public function getArticleName($articleId): string
    {
        return Article::getById($articleId)->getName();
    }

}