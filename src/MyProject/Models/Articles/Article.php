<?php

namespace MyProject\Models\Articles;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;

class Article extends ActiveRecordEntity
{

    protected $name;

    protected $text;

    protected $authorId;

    protected $createdAt;


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getText(): string
    {
        $parser = new \Parsedown();
        return $parser->text($this->text);
    }

    public function getShortText(): string
    {
        $parser = new \Parsedown();
        return $parser->text(mb_substr($this->text, 0, 200));
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    public function setAuthorId($authorId): void
    {
        $this->authorId = $authorId;
    }

    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }

    public function getCreatedAt(): string
    {
       return $this->getCorrectDateTime()->createdAt;
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }

    public static function createArticle(array $fields, User $author): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }

        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $article = new Article();

        $article->setAuthor($author);
        $article->setName($fields['name']);
        $article->setText($fields['text']);

        $article->save();

        return $article;
    }

    public function editArticle(array $article): Article
    {
        if (empty($article['name'])) {
            throw new InvalidArgumentException('Отсутствует заголовок статьи');
        }
        if (empty($article['text'])) {
            throw new InvalidArgumentException('Отсутствует текст статьи');
        }
        $this->setName($article['name']);
        $this->setText($article['text']);

        $this->save();

        return $this;
    }
}
