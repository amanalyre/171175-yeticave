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

    $parameterList = addLimit($parameterList);
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

/**
 * Установка лимитов для результатов запрос, если лимит использован
 * @param array $parameterList
 */
function addLimit(array $parameterList)
{
    if (!empty($parameterList['limit'])) {
        if ((int)$parameterList['limit']) {
            $parameterList['sql'] .= ' LIMIT ?';
            $parameterList['data'][] = (int)$parameterList['limit'];
        }
    }

    return $parameterList;
}

/** Установка оффсета для результатов запрос, если оффсет использован
 * @param array $parameterList
 */
function addOffset(array &$parameterList)
{
    if (!empty($parameterList['offset'])) {
        if ((int)$parameterList['offset']) {
            $parameterList['sql'] .= ' OFFSET ?';
            $parameterList['data'][] = (int)$parameterList['offset'];
        }
    }

    return;
}

/**
 * Получает список категорий
 * @param int|null $limit необязательное поле лимита для запроса
 * @param null $db Ресурс соединения с ДБ
 * @return array|bool|null
 */
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
              WHERE l.category_id=c.id AND l.id = ?';

    $parametersList = [
        'sql' => $sql,
        'data' => [$lot_id],
        'limit' => 1
    ];
    return processingSqlQuery($parametersList, $db);
}

/**
 * Создает нового юзера
 * @param array $user_data Данные юзера
 * @param array $user_avatar Загруженное изображение
 * @param null $db Подключение к БД
 *
 * @return array|int|string Id добавленного лота или массив ошибок
 */
function saveUser(array $user_data, array $user_avatar, $db = null)
{
    $errors = array_merge(checkFieldsSaveUser($user_data), checkUplImage($user_avatar, 'photo'));

    if (empty($errors)) {
        $config = getConfig();
        if ($imageName = saveImage($user_avatar, $config['avatarDirUpl']))
            $sql = 'INSERT INTO users
                      (us_name, us_email, us_password, create_date, us_image)
                    VALUES
                      (?, ?, ?, NOW(), ?);';
        $parametersList = [
            'sql' => $sql,
            'data' => [
                $user_data['name'],
                $user_data['email'],
                password_hash(trim($user_data['password']), PASSWORD_DEFAULT),
                $imageName
            ],
            'limit' => 1
        ];

        processingSqlQuery($parametersList, $db);

        return true;
    } else {
        return $errors;
    }
}

/**
 * Проверяет обязательые поля в добавлении нового пользователя
 * @param array $user_data
 */
function checkFieldsSaveUser(array $user_data)
{
    $errors = formRequiredFields($user_data,
        [
            'email', 'password', 'password2', 'name', 'message'
        ]); // названия полей в шаблоне

    if (!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите адрес электронной почты';
    } elseif (getUserInfoByEmail($user_data['email'])) {
        $errors['email'] = 'Пользователь с указанным email уже существует';
    };

    if (empty($errors['password']) && $user_data['password'] !== $user_data['password2']) {
        $errors['password'] = 'Введенные пароли не совпадают';
    }

    return $errors;
}

function getUserInfoByEmail(string $email, $limit = 1) // Здесь массив нормальный
{
    if (empty($email)) {
        return false;
    }
    $sql = 'SELECT * FROM users WHERE us_email = ?'; #TODO выпили тут звездочку нафиг!
    $parameterList = [
        'sql' => $sql,
        'data' => [
            $email
        ],
        'limit' => $limit
    ];

    $result = processingSqlQuery($parameterList);
    return $result;
}

/**
 * Логинит пользователя на сайт
 * Не показываем юзеру, что из пары пароль-логин было неправильным.
 * @param array $user_data данные, полученные от юзера
 * @return array
 * todo впилить проверку на количество попыток авторизации (кука?)
 */
function login(array $user_data)
{
    $errors = checkFieldsLogin($user_data);
    $foundUser = getUserInfoByEmail($user_data['email']); // данные юзера. #todo проверь с несуществующим

    if (empty($errors) && $foundUser) { // пустые ошибки
        if (password_verify($user_data['password'], $foundUser['us_password'])) {
            $foundUser['us_password'] = passwordNeedsReHash($foundUser, $user_data['password']);
            return [$foundUser];
        } else {
            $errors['password'] = 'Неправильный логин или пароль';
        }
    } else {
        $errors['email'] = 'Неправильный логин или пароль';
    }

    return [false, $errors];
}

/**
 * Проверяет обязательые поля в авторизации пользователя
 * @param array $user_data
 */
function checkFieldsLogin(array $user_data)
{
    $errors = formRequiredFields($user_data,
        [
            'email', 'password'
        ]);

    if (empty($errors['email']) && (!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL))) {
        $errors['email'] = 'Введите адрес электронной почты';
    }

    return $errors;
}

/**
 * Проверяет, нужно ли пересчитать хеш пароля пользователя, и вызывает его обновление
 * @param array $foundUser
 * @param string $password
 * @return bool|mixed|string
 */
function passwordNeedsReHash(array $foundUser, string $password)
{
    if (password_needs_rehash($foundUser['us_password'], PASSWORD_DEFAULT)) {
        return passwordUpdating($foundUser['id'], $password);
    }

    return $foundUser['us_password'];
}

/**
 * Обновляет у пользователя хеш пароля
 * @param int $userId
 * @param string $password
 * @param null $db
 * @return bool|string
 */
function passwordUpdating(int $userId, string $password, $db = null)
{
    $reHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'UPDATE users SET us_password = ? WHERE id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [
            $reHash,
            $userId
        ],
        'limit' => 1
    ];

    processingSqlQuery($parameterList, $db);

    return $reHash;
}

/**
 * Получает данные сессии
 * @return array данные сессии
 */
function getSession()
{
    static $session = null;

    if ($session === null) {
        $session = $_SESSION;
    }

    return $session;
}

/**
 * Получает данные сессии пользователя
 *
 * @return array|bool данные пользователя из сессии
 */
function getUserSessionData()
{

    return getSession()['user'] ?? false;
}

/**
 * Проверяет, авторизован ли пользователь
 *
 * @return bool Результат
 */
function isAuthorized()
{
    if (!empty(getUserSessionData())) {
//    if (!empty($_SESSION)) {
        return true;
    }

    return false;
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
 * @param array $lot_image Загруженное изображение
 * @param null $db Подключение к БД
 * @param int $limit
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
                      (?, NOW(), ?, ?, ?, ?, ?, 1, ?)';
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
            ]
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