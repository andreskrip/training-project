<?php include __DIR__ . '/../header.php'; ?>
<h1><?= $article->getName() ?></h1>
<h3>Автор: <?= $article->getAuthor()->getNickname() ?></h3>
<span><?= $article->getCreatedAt() ?></span><br>
<?php if ($user !== null && $user->isAdmin()): ?>
    <a href="/articles/<?= $article->getId() ?>/edit">Редактировать</a> |
    <a href="/articles/<?= $article->getId() ?>/delete">Удалить</a>
<?php endif; ?>
<hr>
<p><?= $article->getText() ?></p>
<hr>
<h3>Комментарии</h3>
<?php if (!empty($error)): ?>
    <div style="background-color: tomato;"><?= $error ?></div>
<?php endif; ?>
<?php if ($user !== null): //если неавторизованный пользователь - скрываем поле ввода комментария ?>
    <form action="/articles/<?= $article->getId() ?>/comments" method="post">
        <textarea name="text" id="text" rows="10" cols="100" style="width: 100%"></textarea>
        <br>
        <input type="hidden" name="articleId" value="<?= $article->getId() ?>">
        <input type="submit" value="Отправить">
    </form>
<?php endif; ?>
<?php if ($comments !== null): //если комментарии пустые - не выводим блок с ними?>
    <div id="comments" class="comments">
        <?php foreach ($comments as $comment): ?>
            <div id="comment<?= $comment->getId() ?>" class="comment">
                <div class="comment-header">
                    <span class="comment-author"><?= $comment->getAuthor() ?></span>
                    <span><?= $comment->getCreatedAt($comment) ?></span>
                    <?php if ($user !== null && ($user->getId() === $comment->getUserId() || $user->isAdmin())): ?>
                        <a class="icon icon-delete"
                           href="/articles/<?= $article->getId() ?>/comments/<?= $comment->getId() ?>/delete"
                           title="Удалить">🞮</a>
                        <a class="icon icon-edit"
                           href="/articles/<?= $article->getId() ?>/comments/<?= $comment->getId() ?>/edit"
                           title="Редактировать">🖉</a>
                    <?php endif; ?>
                </div>
                <p><?= $comment->getText() ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../footer.php'; ?>
