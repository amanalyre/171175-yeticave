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
    if (mysqli_num_rows($res) > 1) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        $result = mysqli_fetch_array($res, MYSQLI_ASSOC);
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
    $sql = 'SELECT `cat_name` FROM categories;';
    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];
    return processingSqlQuery($parameterList, $db);
}

// Здесь получаем список лотов
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

// здесь получаем конкретный лот
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
 * @throws $e;
 */
function renderTemplate (string $templ, $data)
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