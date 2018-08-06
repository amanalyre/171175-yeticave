<form class="form container <?= !count($errors) ?: 'form--invalid' ?>" action="signUp.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>

    <div class="form__item<?php echo (empty($errors['email'])) ? '' : ' form__item--invalid' ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?=$email; ?>">
        <span class="form__error"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="form__item <?php echo (empty($errors['password'])) ? '' : ' form__item--invalid' ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="user[password]" placeholder="Введите пароль">
        <span class="form__error"><?= $errors['password'] ?? '' ?></span>
    </div>
    <div class="form__item<?php echo (empty($errors['name'])) ? '' : ' form__item--invalid' ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="user[name]" placeholder="Введите имя" value="<?=$name; ?>">
        <span class="form__error"><?= $errors['name'] ?? '' ?></span>
    </div>
    <div class="form__item<?php echo (empty($errors['message'])) ? '' : ' form__item--invalid' ?>">
        <label for="message">Биография*</label>
        <textarea id="message" name="user[message]" placeholder="Напишите пару слов о себе" ><?=$message;?></textarea>
        <span class="form__error"><?= $errors['message'] ?? '' ?></span>
    </div>

    <div class="form__item form__item--file form__item--last <? if (!empty($photo))
    {
        if (empty($errors['photo']))
        {
            echo 'form__item--uploaded';
        }
        else
        {
            echo 'form__item--invalid';
        }} else {echo '';}; ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div>
            <input type="hidden" name="MAX_FILE_SIZE" value="400000" />
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="photo" id="photo" value="<?=$photo ?>">
            <label for="photo">
                <span>+ Добавить</span>
            </label>
        </div>
        <span class="form__error"><?= $errors['photo'] ?? '' ?></span>
    </div>
    <?php if (!empty($errors)): ?>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif?>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
