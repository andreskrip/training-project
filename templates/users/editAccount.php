<?php include __DIR__ . '/../header.php'; ?>

    <h1>Редактировать аккаунт</h1>

<?php if (!empty($error)): ?>
    <div style="background-color: red; margin-bottom: 20px;"><?= $error ?></div>
<?php endif; ?>
    <form style="text-align: center" action="/account/edit" method="post">
        <label for="nickname">Никнейм</label><br>
        <input type="text" name="nickname" id="nickname" value="<?= $_POST['nickname'] ?? $user->getNickname() ?>"
               size="50"><br><br>
        <label for="email">Е-мейл</label><br>
        <input type="text" name="email" id="email" value="<?= $_POST['email'] ?? $user->getEmail() ?>" size="50"><br>
        <br><span style="font-size: 11px">Если вы не хотите менять текущий пароль, то оставьте следующие поля пустыми</span><br><br>
        <label for="password">Введите текущий пароль</label><br>
        <input type="password" name="password" id="password" size="50"><br><br>
        <label for="newPassword">Введите новый пароль</label><br>
        <input type="password" name="newPassword" id="newPassword" size="50"><br><br>
        <input type="submit" value="Обновить">
    </form>
<?php include __DIR__ . '/../footer.php'; ?>