<?php

require_once("functions.php");
require_once("data.php");

date_default_timezone_set("Europe/Moscow");

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

