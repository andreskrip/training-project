<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center;">
        <h1>Восстановление пароля</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red;padding: 5px;margin: 15px"><?= $error ?></div>
        <?php endif; ?>
        <form action="/users/recover" method="post">
            <label>Email <input type="text" name="email" value="<?= $_POST ['email'] ?>"></label>
            <br><br>
            <input type="submit" value="Восстановить пароль">
        </form>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>