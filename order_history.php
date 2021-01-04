<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Item;
use portfolio\lib\Order;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '' , '' , '' , 'o.order_id');

$ses = new Session($db);
$itm = new Item($db);
$order = new Order($db);
$orderGroup = new Order($dbgroup);


// SESSION['id']を元にorder_id(注文ID)を取得
$orderId = $order->getTotalorderId();

// 注文詳細を取得
$orderDetail = $order->getOrderDetail();

// 合計金額の取得
$orderPrice = $orderGroup->getOrderPrice();

// 注文情報を元にitemを取得
$orderItem = $order->getOrderitem();

for ($i = 0; $i < count($orderItem); $i++) {
  $orderItem[$i]['image'] = explode(',', $orderItem[$i]['image']);
}

$context = [];

$context['orderId'] = $orderId;
$context['orderDetail'] = $orderDetail;
$context['orderItem'] = $orderItem;
$context['orderPrice'] = $orderPrice;
$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id']; 

$template = $twig->loadTemplate('order_history.html.twig');
$template->display($context);