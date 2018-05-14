<?php
require_once("functions.php");
require_once("data.php");

date_default_timezone_set("Europe/Moscow");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = mysqli_connect('localhost', 'root', '', 'YetiCave');

if ($db == false)
{
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

$sql_cat = 'SELECT `cat_name` FROM categories';
$result = mysqli_query($db, $sql_cat) or die('Error '.mysqli_error($db));
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql_lot = 'SELECT l.lot_name, l.start_price, l.img_url, MAX(b.bid_price) AS cur_price, cat.cat_name, COUNT(b.lot_id) AS bids_qty
  FROM lots l
  LEFT JOIN bids b ON b.lot_id=l.id
  LEFT JOIN categories cat ON cat.id=l.category_id
  WHERE winner_id IS NULL
  GROUP BY l.id
  ORDER BY l.create_date DESC
  LIMIT 6';
$result = mysqli_query($db, $sql_lot) or die('Error '.mysqli_error($db));
$ads = mysqli_fetch_all($result, MYSQLI_ASSOC);

$left = strtotime("tomorrow 00:00:00")- time();
$hours = (int) ($left / 60 / 60);
$minutes = (int) ($left / 60) - $hours * 60;
$timer = ($hours == 0 ? "00":$hours) . ":" . ($minutes == 0 ? "00":($minutes < 10? "0".$minutes:$minutes));

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

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

$templContent = renderTemplate('templates/index.php', [
    'ads' => $ads]);

$layoutContent = renderTemplate('templates/layout.php', [
    'pageContent' => $templContent,
    'categories' => $categories,
    'pageName' => 'Main - YetiCave']);

print($layoutContent);
