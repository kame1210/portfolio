<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

// use PDO;
use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

if (isset($_SESSION['id']) === true) {
  $likesItemList = $itm->getlikesItemList();
}

$context = [];
$context['likesItemList'] = $likesItemList;
// var_dump($likesItemList);

$template = $twig->loadTemplate('mypage.html.twig');
$template->display($context);