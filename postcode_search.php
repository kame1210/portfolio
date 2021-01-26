<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\Bootstrap;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

if (isset($_GET['zip']) === true) {
  $zip = $_GET['zip'];

  $table = ' postcode ';
  $column = ' pref, city, town ';
  $where = ' zip = ? ';
  $arrVal = [$zip];

  $res = $db->select($table, $column, $where, $arrVal);

  echo ($res !== "" && count($res) !== 0) ? $res[0]['pref'] . $res[0]['city'] . $res[0]['town'] : '';
} else {
  echo 'no';
}
