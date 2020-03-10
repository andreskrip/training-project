<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center;">
        <h1>Введите новый пароль</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red;padding: 5px;margin: 15px"><?= $error ?></div>
        <?php endif; ?>
        <form action="/users/<?= $userId ?>/newpassword/<?= $recoveryCode ?>" method="post">
            <label>Пароль <input type="password" name="password"></label>
            <br><br>
            <label>Повторите пароль <input type="password" name="repeatPassword"></label>
            <br><br>
            <input type="submit" value="Подтвердить новый пароль">
        </form>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>