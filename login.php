<?php
require_once("functions.php");
require_once ('connection.php');

#TODO найди причину, по которой в шаблоне ошибки не отрабатывают корректно.

$categories = getCatList();

if ($_POST) {
    $user = $_POST['user'];

    list($foundUser, $errorsFound) = login($user);
    if ($foundUser === true) {
        $_SESSION['user'] = $foundUser;
        $_SESSION["visit_count"] = "Hi";
        setcookie('sessWasStarted', time()+60*60*24*30);
        header('Location: index.php');
        exit;
    } else {
        $errors = $errorsFound;
    }
}

try {
    $templContent = renderTemplate('login', [
        'categories'  => $categories,
        'errors'      => $errors ?? [],
        'email'       => $user['email'] ?? ''
    ]);
} catch (Exception $e)
{
    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
};

$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories'  => $categories,
    'pageName'    => 'Вход - Yeticave']);
//    'isAuth' => empty($_SESSION['user']) ? false : true;

print($layoutContent);