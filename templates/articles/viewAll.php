<?php include __DIR__ . '/../header.php'; ?>
    <h1>Все статьи</h1>
    <div class="articles">
        <?php foreach ($articles as $article): ?>
            <div class="short-article">
                <h2><a href="/articles/<?= $article->getId() ?>"><?= $article->getName() ?></a></h2>
                <p><?= $article->getShortText() ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>