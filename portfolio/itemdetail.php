<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

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

$ses->checkSession();

$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';

if ($item_id === '') {
  header('Location: ' . Bootstrap::ENTRY_URL . 'list.php');
}

$cateArr = $itm->getCategoryList();

$itemData = $itm->getItemDetailData($item_id);

$context = [];

$context['cateArr'] = $cateArr;
$context['itemData'] = $itemData;

if (isset($_SESSION['user_name']) === true && $_SESSION['user_name'] !== '') {
  $context['user_name'] = $_SESSION['user_name'];
  $context['id'] = $_SESSION['id'];
}


$template = $twig->loadTemplate('item_detail.html.twig');
$template->display($context);