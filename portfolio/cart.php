<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\master\initMaster;
use portfolio\lib\Session;
use portfolio\lib\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE,);
$ses = new Session($db);
$cart = new Cart($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// セッションチェック
$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

// sessionでmem_id取得
$mem_id = $_SESSION['id'];
$user_name = $_SESSION['user_name'];


// GETでitem_id取得
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';

// GETでitem_id取得
$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/', $_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';

// カートデータ取得
$dataArr = $cart->getCartData($mem_id);
$quantity = $cart->getCartquantity($mem_id, $item_id);

// $dataArrのitem_idを取得 $item_idと$resultで同じ値があるかどうかの判定。無ければカートに挿入。
$result = (array_column($dataArr, 'item_id'));
$cartItem = in_array($item_id, $result);

// $item_idがあればcartテーブルにinsert
if ($item_id !== '' && $cartItem === false) {
  $res = $cart->insCartData($mem_id, $item_id);
  header("Location: http://localhost/DT/portfolio/cart.php");
  if ($res === false) {
    echo '商品購入に失敗しました。';
    exit();
  }
} elseif ($item_id !== '' && $cartItem === true) {
  $num = intval($quantity[0]['quantity']) + 1;
  $res = $cart->updateCartData($mem_id, $item_id, $num);
  if ($res === true) {
    header("Location: http://localhost/DT/portfolio/cart.php");
  }
}

// $crt_idがあれば(削除ボタンが押されたら) insert時autoincrement
if ($crt_id !== '') {
  $res = $cart->delCartData($crt_id);
}

// 個数セレクトボックスの数作成
$quantityArr = initMaster::getquantity();

// カートの個数変更処理
$quantity = '';
$item_id = '';

if (isset($_POST['change']) === true) {
  $quantity = $_POST['quantity'];
  $item_id = $_POST['item_id'];

  $cart->updateCartData($mem_id, $item_id, $quantity);
}

$dataArr = $cart->getCartData($mem_id);

// 合計個数と合計金額を取得
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($mem_id);

$context = [];
$context['user_name'] = $user_name;
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$context['quantityArr'] = $quantityArr;


$template = $twig->loadTemplate('cart.html.twig');
$template->display($context);
