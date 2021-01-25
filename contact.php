<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';


use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common($db);
$ses = new Session($db);

$context = [];

if (isset($_SESSION['id'])) {
  $context['id'] = $_SESSION['id'];
  $context['user_name'] = $_SESSION['user_name'];
}

$template = $twig->loadTemplate('contact.html.twig');
$template->display($context);
