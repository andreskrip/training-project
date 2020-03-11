<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center;">
        <h1>Введите новый пароль</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red;padding: 5px;margin: 15px"><?= $error ?></div>
        <?php endif; ?>
        <form action="/users/<?= $userId ?>/reset/<?= $recoveryCode ?>" method="post">
            <label for="password">Пароль</label><br>
            <input type="password" name="password">
            <br><br>
            <label for="repeatPassword">Повторите пароль</label><br>
            <input type="password" name="repeatPassword">
            <br><br>
            <input type="submit" value="Подтвердить новый пароль">
        </form>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>