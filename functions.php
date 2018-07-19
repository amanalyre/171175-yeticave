<?php

require_once ('mysql_helper.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// получаем коннект к базе
function connectToDb()
{
    static $db;
    if ($db === null) {
        $config = getConfig();
        //$db = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_database']);
        $db = mysqli_connect('localhost', 'root', '', 'YetiCave');
        mysqli_set_charset($db, 'utf8');
        if (!$db) {
            print('Ошибка: Невозможно подключиться к MySQL  ' .mysqli_connect_error());
            die();
        }
    }
    return $db;
}

// используем данные для доступа к БД
function getConfig()
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/connection.php';
    }
    return ($config);
}

// Здесь подготавливаются выражения
function processingSqlQuery(array $parameterList, $db = null)
{
    if ($db === null) {
        $db = connectToDb();
    }
    addLimit($parameterList);
    $stmt = db_get_prepare_stmt($db, $parameterList['sql'], $parameterList['data']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = true;
    if ($res != false) {
        if (mysqli_num_rows($res) > 1) {
            $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $result = mysqli_fetch_array($res, MYSQLI_ASSOC);
        }
    }
    return $result;
}

// Здесь задаются лимиты для результатов
function addLimit(array $parameterList)
{
    if ( (int) $parameterList['limit']) {
        $parameterList['sql'] .= ' LIMIT ?';
        $parameterList['data'][] = (int) $parameterList['limit'];
    }
    return;
}
// Здесь получается список категорий
function getCatList(int $limit = null, $db = null) {
    $sql = 'SELECT `cat_name`, `id` FROM categories;';
    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];
    return processingSqlQuery($parameterList, $db);
}

/**
 * Получение списка последних лотов
 * @param int|null $limit количество получаемых лотов
 * @param null $db подключение к ДБ
 *
 * @return mixed данные лота
 */
function getLotsList(int $limit = null, $db = null)
{
    $sql = 'SELECT l.lot_name, l.start_price, l.img_url, l.id, MAX(b.bid_price) AS cur_price, cat.cat_name, COUNT(b.lot_id) AS bids_qty
              FROM lots l
              LEFT JOIN bids b ON b.lot_id=l.id
              LEFT JOIN categories cat ON cat.id=l.category_id
              WHERE winner_id IS NULL
              GROUP BY l.id
              ORDER BY l.create_date DESC';
    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];
    return processingSqlQuery($parameterList, $db);
}

/**
 * Получение данных об одном лоте по его id
 * @param int $lot_id Цена товара точная
 * @param null $db подключение к ДБ
 *
 * @return mixed данные лота
 */
function getLot(int $lot_id, $db = null)
{
    $sql = 'SELECT l.lot_name, l.start_price, c.cat_name, l.id, l.img_url, l.lot_description
              FROM lots l, categories c
              WHERE l.category_id=c.id AND l.id = ?;';

    $parametersList = [
        'sql' => $sql,
        'data' => [$lot_id],
        'limit' => 1
    ];
    return processingSqlQuery($parametersList, $db);
}

/**
 * Округляет до целого цену лота
 * @param int $price Цена товара точная
 *
 * @return string $price_formatted округленная цена
 */
function price_round($price)
{
    htmlspecialchars($price);
    if ($price < 1000)
    {
        $price_round = $price;
    } else
    {
        $price_round = number_format(ceil($price), '0', '0', '&thinsp;');
    }
    return $price_formatted = $price_round . ' ₽';
};

/**
 * Сохраняет данные лота
 * @param array $lot_data Данные лота
 * @param array $lot_image Данные изображения
 * @param null $db Подключение к БД
 *
 * @return array|int|string Id добавленного лота или массив ошибок
 */
function saveLot(array $lot_data, array $lot_image, $db = null)
{
    $errors = array_merge(checkFieldsSaveLot($lot_data), checkUplImage($lot_image, 'photo'));

    if (empty($errors)) {
        $config = getConfig();
        if ($imageName = saveImage($lot_image, $config['imgDirUpl']))
            $sql = 'INSERT INTO lots
                      (lot_name, create_date, category_id, start_price, bid_step, img_url, lot_description, author_id, finish_date)
                    VALUES 
                      (?, NOW(), ?, ?, ?, ?, ?, 1, ?);';
        $parametersList = [
            'sql' => $sql,
            'data' => [
                $lot_data['name'],
                $lot_data['category'],
                $lot_data['start_price'],
                $lot_data['step'],
                $imageName,
                $lot_data['description'],
                $lot_data['finish_date']
            ],
            'limit' => 1
        ];

        processingSqlQuery($parametersList, $db);

        return mysqli_insert_id(connectToDb());
    } else {
        return $errors;
    }
};

/**
 * Проверяет поля формы на соответствие заданному ограничению
 * @param array $lot_data Данные лота
 *
 * @return array массив ошибок
 */
function checkFieldsSaveLot(array $lot_data) // #TODO удостовериться, что поля в форме совпадают по названию
{
    $errors = formRequiredFields($lot_data,
        [
            'name', 'category', 'description', 'start_price', 'step', 'finish_date'
        ]); // названия полей в шаблоне

    if (!getLot($lot_data['category'])) {
        $errors['category'] = 'Выберите категорию';
    }

    if (!filter_var($lot_data['start_price'], FILTER_VALIDATE_INT) && empty($errors['start_price'])) {
        $errors['start_price'] = 'Введите цену продажи';
    } else {
        if ($lot_data['start_price'] < 0) {$errors['start_price'] = 'Цена продажи должна быть больше нуля';}
    }

    if (!filter_var($lot_data['step'], FILTER_VALIDATE_INT) && empty($errors['step'])) {
        $errors['step'] = 'Введите шаг ставки';
    } else {
        if ($lot_data['step'] < 1) {$errors['step'] = 'Шаг ставки должен быть больше единицы';}
    }

    if (is_numeric(strtotime($lot_data['finish_date']))) {
        if (strtotime($lot_data['finish_date']) < time()) {
            $errors['finish_date'] = 'Дата окончания торгов не может быть в прошлом';
        }
    } else {
        $errors['finish_date'] = 'Выберите дату';
    }

    return $errors;
};

/**
 * Формирует список проверяемых полей и проверяет их заполненность
 * @param array $form Данные формы
 * @param array $fields Список обязательных полей
 *
 * @return array массив ошибок
 */
function formRequiredFields(array $form, array $fields)
{
    $errors = [];

    foreach ($fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    return $errors;
}

/**
 * Проверка загружаемого изображения
 * @param array $image Изображение
 * @param string $key Название поля для возврата ошибки
 *
 * @return array $error массив ошибок
 */
function checkUplImage(array $image, string $key)
{
    $error = [];

    if (empty($image['size']) or $_FILES["pictures"]["error"] != UPLOAD_ERR_OK) {  //тут потенциально может быть хрень
        $error[$key] = 'Выберите изображение';
    } elseif ($image['size'] > 5e+6) {
        $error[$key] = 'Изображение не должно быть более 5Мб'; // #TODO проверить размер файла
    } else {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $image['tmp_name']); // вытаскиваем тип файла

        $fileFormat = ['image/jpeg', 'image/jpg', 'image/png'];

        if (!in_array($fileType, $fileFormat)) {
            $error[$key] = 'Выберите фотографию формата JPEG, JPG или PNG';
        }
    }

    return $error;
};

/**
 * Сохраняет изображение на сервер
 * @param array $image изображение для сохранения
 * @param string $dir папка для сохранения
 *
 * @return bool|string Результат загрузки
 */
function saveImage(array $image, string $dir)
{
    $uploadDir = __DIR__ ; // в корне проекта
    $name = basename($image["name"]); // здесь только имя.расширение файла
    $uploadFile = "$uploadDir\\$dir\\$name";

    if (move_uploaded_file($image['tmp_name'], "$uploadFile")) {
        return "$dir/$name";
    } else {
        return false;
    }
}

/**
 * Рендерит указанный шаблон
 * @param string templ название шаблона,
 * @return string готовый для вставки шаблон
 * @throws $e;
 */
function renderTemplate(string $templ, $data)
{
    $filePath = __DIR__ . '/templates/' . $templ . '.php';
    if (!file_exists($filePath)) {
        return 'Template '. $templ. '.php doesn\'t exist at ' . $filePath;
    }

    extract($data);
    ob_start();
    try {
        include ($filePath);
    } catch (Throwable $e) {
        ob_end_clean();
        throw $e;
    }
    return ob_get_clean();
}