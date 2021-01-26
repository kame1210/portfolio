<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;
use portfolio\lib\Item;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common($db);
$ses = new Session($db);
$itm = new Item($db);


if (isset($_SESSION['user_name']) === false || isset($_SESSION['id']) === false) {
  header('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
  exit();
} 

if (isset($_SESSION['id']) === true) {
  $submitItemList = $itm->submitItemList();
}

$context = [];

$context['submitItemList'] = $submitItemList;
$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id']; 

$template = $twig->loadTemplate('submit_Item.html.twig');
$template->display($context);