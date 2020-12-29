<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Cart;
use Stripe\Stripe;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE,);
$ses = new Session($db);
$cart = new Cart($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

if (isset($_POST) && $_POST !== '') {
  $sumPrice = $_POST['sumPrice'];
  $sumNum = $_POST['sumNum'];
  $mem_id = $_SESSION['id'];
}

$dataArr = $cart->getCartData($mem_id);

$template = '';
$errMsg = '';

if ($dataArr !== []) {
  $template = 'payment.html.twig';
} else {
  $template = 'cart.html.twig';
  $errMsg = 'カートに商品が入っていません';
}

$context = [];

$context['sumPrice'] = floor($sumPrice);
$context['sumNum'] = $sumNum;
$context['dataArr'] = $dataArr;
$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id']; 

$context['errMsg'] = ($errMsg !== '') ? $errMsg : '';

$template = $twig->loadTemplate($template);
$template->display($context);