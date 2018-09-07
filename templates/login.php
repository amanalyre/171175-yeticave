<form class="form container <?= count($errors) ?: 'form--invalid' ?>" action="login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>

    <div class="form__item <?php echo (empty($errors['email'])) ? '' : 'form__item--invalid' ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?=$email; ?>">
        <span class="form__error"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="form__item <?php echo (empty($errors['password'])) ? 'form__item--last' : 'form__item--last form__item--invalid' ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="user[password]" placeholder="Введите пароль" >
        <span class="form__error"><?= $errors['password'] ?? '' ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
    <?php if (!empty($errors)): ?>
        <span class="form__error form__error--bottom">Неправильное имя пользователя или пароль. Пожалуйста, проверьте введенные данные и попробуйте еще раз.</span>
    <?php endif?>
    <a class="text-link" href="signUp.php">Создать новый аккаунт</a>
</form>

<!--srtongPassword проверка авторизации
if (isset($_SESSION['user'])) {
        $page_content = include_template('welcome.php', ['username' => $_SESSION['user']['name']]);
    }-->
