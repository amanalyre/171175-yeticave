<form class="form form--add-lot container <?= !count($errors) ?: 'form--invalid' ?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">

        <div class="form__item<?php echo (empty($errors['name'])) ? '' : ' form__item--invalid' ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[name]" value="<?=$name; ?>" placeholder="Введите наименование лота" >
            <span class="form__error"><?= $errors['name'] ?? '' ?></span>
        </div>

        <div class="<?php echo (empty($errors['category'])) ? "form__item" : "form__item form__item--invalid" ?>">
            <label for="category">Категория</label>
            <select id="category" name="lot[category]" >
                <option value="0">Выберите категорию</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?=$cat['id']; ?>" <?=$category == $cat['id'] ? 'selected' : ''; ?>> <?=$cat['cat_name']; ?></option>
                <? endforeach; ?>
            </select>
            </select>
            <span class="form__error"><?= $errors['category'] ?? '' ?></span>
        </div>
    </div>
    <div class="form__item <?php echo (empty($errors['description'])) ? "form__item--wide" : "form__item form__item--wide form__item--invalid" ?>">
        <label for="description">Описание</label>
        <textarea id="description" name="lot[description]" placeholder="Укажите описание лота" ><?=$description;?></textarea>
        <span class="form__error"><?= $errors['description'] ?? '' ?></span>
    </div>

    <div class="form__item form__item--file <? if (!empty($photo))
    {
        if (empty($errors['photo']))
        {
            echo 'form__item--uploaded';
        }
        else
        {
            echo 'form__item--invalid';
        }} else {echo '';}; ?>">  <!-- form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
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

    <div class="form__container-three">
        <div class="form__item <?php echo (empty($errors['start_price'])) ? " form__item--small" : "form__item--small form__item--invalid" ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="lot[start_price]" value="<?=$start_price; ?>" placeholder="0">
            <span class="form__error"><?= $errors['start_price'] ?? '' ?></span>
        </div>
        <div class="form__item <?php echo (empty($errors['step'])) ? " form__item--small" : "form__item--small form__item--invalid" ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="lot[step]" value="<?=$step; ?>" placeholder="0" >
            <span class="form__error"><?= $errors['step'] ?? '' ?></span>
        </div>
        <div class="<?php echo (empty($errors['finish_date'])) ? "form__item" : "form__item form__item--invalid" ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="lot[finish_date]" value="<?=$finish_date; ?>">
            <span class="form__error"><?= $errors['finish_date'] ?? '' ?></span>
        </div>
    </div>
    <?php if (!empty($errors)): ?>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif?>
    <button type="submit" class="button">Добавить лот</button>
</form>