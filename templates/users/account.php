<?php include __DIR__ . '/../header.php'; ?>

    <h1>Аккаунт</h1>

    <span class="greetings-block">Привет, <?= $user->getNickname() ?></span>

    <a href="/account/edit">Редактировать аккаунт</a>

    <p>Твой e-mail: <?= $user->getEmail() ?></p>
    <p>Ты на сайте с <?= $user->getCreatedAt() ?></p>


<?php if ($user->isAdmin()): ?>
    <div style="display: flex; justify-content: center; align-items: center;">
        <a class="button" href="/articles/create">Создать новую статью</a>
    </div>
    <table class="admin-table" border="1" style="width: 100%">
        <caption><h3>Скучные админские новости</h3></caption>
        <tr>
            <th><h4>Последние статьи</h4></th>
            <th><h4>Последние комментарии</h4></th>
        </tr>
        <tr>
            <td>
                <?php
                $i = 0;
                foreach (array_reverse($articles) as $article):
                    if ($i >= 3):
                        break;
                    endif; ?>
                    <div class="short-article">
                        <div class="article-header">
                            <h3 style="display: inline"><a
                                        href="/articles/<?= $article->getId() ?>"><?= $article->getName() ?></a></h3>
                            <a class="icon icon-delete"
                               href="/articles/<?= $article->getId() ?>/delete"
                               title="Удалить">🞮</a>
                            <a class="icon icon-edit"
                               href="/articles/<?= $article->getId() ?>/edit"
                               title="Редактировать">🖉</a>
                        </div>
                        <span><?= $article->getCreatedAt() ?></span>
                        <p><?= $article->getShortText() ?></p>
                        <hr>
                    </div>
                    <?php
                    $i++;
                endforeach; ?>
            </td>
            <td>
                <?php
                $i = 0;
                if (!empty($allComments)):
                    foreach ($allComments as $comment): if ($i >= 5): break; endif; ?>
                        <div id="comment<?= $comment->getId() ?>" class="last-comments comment">
                            <div class="comment-header">
                    <span class="comment-author">В статье <a
                                href="/articles/<?= $comment->getArticleId() ?>"><?= $comment->getArticleName($comment->getArticleId()) ?></a></span>
                                <span><?= $comment->getCreatedAt() ?></span>
                                <?php if ($user->getId() === $comment->getUserId() || $user->isAdmin()): ?>
                                    <a class="icon icon-delete"
                                       href="/articles/<?= $comment->getArticleId() ?>/comments/<?= $comment->getId() ?>/delete"
                                       title="Удалить">🞮</a>
                                    <a class="icon icon-edit"
                                       href="/articles/<?= $comment->getArticleId() ?>/comments/<?= $comment->getId() ?>/edit"
                                       title="Редактировать">🖉</a>
                                <?php endif; ?>
                            </div>
                            <p><?= $comment->getText() ?></p>
                        </div>
                        <?php
                        $i++;
                    endforeach;
                else: ?>
                    <p style="text-align: center">Пусто!</p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php endif; ?>

    <div id="comments" class="comments">
        <h4>Твои последние комментарии:</h4>
        <?php
        if (!empty($myComments)):
            $i = 0;
            foreach ($myComments as $comment): if ($i >= 5): break; endif; ?>
                <div id="comment<?= $comment->getId() ?>" class="comment">
                    <div class="comment-header">
                    <span class="comment-author">В статье <a
                                href="/articles/<?= $comment->getArticleId() ?>"><?= $comment->getArticleName($comment->getArticleId()) ?></a></span>
                        <span><?= $comment->getCreatedAt($comment) ?></span>
                        <?php if ($user->getId() === $comment->getUserId() || $user->isAdmin()): ?>
                            <a class="icon icon-delete"
                               href="/articles/<?= $comment->getArticleId() ?>/comments/<?= $comment->getId() ?>/delete"
                               title="Удалить">🞮</a>
                            <a class="icon icon-edit"
                               href="/articles/<?= $comment->getArticleId() ?>/comments/<?= $comment->getId() ?>/edit"
                               title="Редактировать">🖉</a>
                        <?php endif; ?>
                    </div>
                    <p><?= $comment->getText() ?></p>
                </div>
                <?php
                $i++;
            endforeach;
        else: ?>
            <p>Пусто!</p>
        <?php endif; ?>
    </div>

<?php include __DIR__ . '/../footer.php'; ?>