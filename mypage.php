<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);

$errArr = [];

if (isset($_SESSION['user_name']) === false || isset($_SESSION['id']) === false) {
  header('Location: http://localhost/DT/portfolio/login.php');
  exit();
}

$context = [];

$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];


$template = $twig->loadTemplate('mypage.html.twig');
$template->display($context);
