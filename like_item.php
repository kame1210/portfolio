<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;
use portfolio\lib\Item;
use portfolio\lib\Likes;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', '', '', 'item_id');

$function = new \Twig_SimpleFunction('like_exsits', function ($item_id) {
  $dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', '', '', 'item_id');
  $likes = new likes($dbgroup);
  return $likes->like_exsits($_SESSION['id'], $item_id);
});
$twig->addFunction($function);

$common = new Common($db);
$ses = new Session($db);
$itm = new Item($db);
$likes = new Likes($dbgroup);

if (isset($_SESSION['user_name']) === false || isset($_SESSION['id']) === false) {
  header("Location:" . Bootstrap::ENTRY_URL . "login.php");
  exit();
}

if (isset($_SESSION['id']) === true) {
  $likesItemList = $itm->getlikesItemList();
}

$likeArr = $likes->getLike();

$context = [];

$context['id'] = $_SESSION['id'];
$context['user_name'] = $_SESSION['user_name'];
$context['likesItemList'] = $likesItemList;
$context['likeArr'] = $likeArr;

$template = $twig->loadTemplate('like_item.html.twig');
$template->display($context);
