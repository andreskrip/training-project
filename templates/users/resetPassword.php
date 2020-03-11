<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center;">
        <h1>Сброс пароля</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red;padding: 5px;margin: 15px"><?= $error ?></div>
        <?php endif; ?>
        <form action="/users/reset" method="post">
            <label for="email">Введите ваш Email </label><br>
            <input type="text" name="email" value="<?= $_POST ['email'] ?>">
            <br><br>
            <input type="submit" value="Сбросить пароль">
        </form>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>