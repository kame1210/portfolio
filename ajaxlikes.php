<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Likes;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$likes = new Likes($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);


if (isset($_POST['item_id']) && isset($_SESSION['id'])) {
  $mem_id = $_SESSION['id'];
  $item_id = $_POST['item_id'];

  // mem_id, item_id を元にselect
  $selectData = $likes->getAjaxLike($mem_id, $item_id);


  if (!empty($selectData)) {
    // DBにデータが存在してる場合の処理

    // デリート処理
    $likes->delAjaxLike($mem_id, $item_id);

    // カウント処理
    $likeCount = $likes->getAjaxLikeCount($item_id);

    // ajaxでいいねの総数表示
    echo $likeCount;
  } else {
    // DBにデータが存在しない場合の処理

    // データインサート処理
    $likes->insAjaxLike($mem_id, $item_id);

    // カウント処理
    $likeCount = $likes->getAjaxLikeCount($item_id);

    // ajaxでいいねの総数表示
    echo $likeCount;
  }
} else {
  echo 'ログインしてください';
}
