<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\master\initMaster;
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

$errArr = [];

if (isset($_SESSION['user_name']) === false || isset($_SESSION['id']) === false) {
  header('Location: http://localhost/DT/portfolio/login.php');
  exit();
} else {

  $table = ' member ';
  $column = '';
  $where = ' mem_id = ? ';
  $arrVal = [$_SESSION['id']];

  $res = $db->select($table, $column, $where, $arrVal);

  if (count($res) !== 0) {
    $userData = $res[0];
  } else {
    $errArr['id'] = 'データが取得できませんでした';
  }
}

if (isset($_SESSION['id']) === true) {
  $likesItemList = $itm->getlikesItemList();
}

if (isset($_SESSION['id']) === true) {
  $submitItemList = $itm->submitItemList();
}

$sexArr = initMaster::getSex();

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context = [];

$context['userData'] = $userData;
$context['likesItemList'] = $likesItemList;
$context['submitItemList'] = $submitItemList;
$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];


$template = $twig->loadTemplate('mypage.html.twig');
$template->display($context);
