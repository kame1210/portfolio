<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Item;
use portfolio\lib\likes;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$dbLimit = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', 10);

$dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', '', '', 'item_id');

$ses = new Session($db);
$itm = new Item($dbLimit);
$likes = new likes($dbgroup);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

// 検索窓での検索処理
$search = (isset($_GET['search']) === true) ? '%' . $_GET['search'] . '%' : '';

// カテゴリーリストの取得
$cateArr = $itm->getCategoryList();
$subCateArr = $itm->getSubCategoryList();

// アイテムリストの取得
$itemArr = [];
$type = 'category';
$itemArr[0] = $itm->getItemList($type, 1);
$itemArr[1] = $itm->getItemList($type, 2);
$itemArr[2] = $itm->getItemList($type, 3);


// いいねの表示
$likeArr = $likes->getLike();

$context = [];

$context['cateArr'] = $cateArr;
$context['subCateArr'] = $subCateArr;
$context['itemArr'] = $itemArr;
$context['likeArr'] = $likeArr;

// ログインしていた場合のuser_name表示
if (isset($_SESSION['user_name']) === true && $_SESSION['user_name'] !== '') {
  $context['user_name'] = $_SESSION['user_name'];
  $context['id'] = $_SESSION['id'];
}

$template = $twig->loadTemplate('index.html.twig');
$template->display($context);