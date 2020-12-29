<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// ログイン状態じゃない場合はリダイレクト
if (!isset($_SESSION['user_name'])) {
  header('Location: http://localhost/DT/portfolio/index.php');
}

$context = [];

$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];


$template = $twig->loadTemplate('delete_member.html.twig');
$template->display($context);
