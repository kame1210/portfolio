<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use Exception;
use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Cart;
use portfolio\lib\order;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Refund;

Stripe::setApikey(Bootstrap::STRIPE_API_KEY);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE,);
$ses = new Session($db);
$cart = new Cart($db);
$order = new Order($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$chargeId = null;

if (isset($_POST) && $_POST !== '') {
  $sumPrice = $_POST['sumPrice'];
  $sumNum = $_POST['sumNum'];
  $mem_id = $_SESSION['id'];
}

try {
  $token = $_POST['stripeToken'];
  $charge = Charge::create([
    'amount' => $sumPrice,
    'currency' => 'jpy',
    'description' => 'test',
    'source' => $token,
    'capture' => false,
  ]);

  $chargeId = $charge['id'];

  //order_tbにorder_idをinsert
  $order->insorder();

  $cartData = $cart->getCartData($mem_id);

  // order_detailにdataをinsert
  $order->insorderDetail($cartData);

  // 注文確定後、カート情報削除
  $cart->orderDelCartData($mem_id);

  // 決済確定処理
  $charge->capture();

  header("Location: http://localhost/DT/portfolio/orderComplete.php");
  
} catch (Exception $e) {
  var_dump($e->getMessage());
  if ($chargeId !== null) {
    Refund::create([
      'charge' => $chargeId,
    ]);
  }
  // header("Location: http://localhost/DT/portfolio/top.php");
  exit();
}
