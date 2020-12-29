<?php

namespace portfolio\lib;

class PDODatabase
{
  private $dbh = '';
  private $db_host = '';
  private $db_user = '';
  private $db_pass = '';
  private $db_name = '';
  private $db_type = '';
  private $order = '';
  private $limit = '';
  private $offset = '';
  private $groupby = '';

  //実引数はBootstrap
  public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type, $order = '', $limit = '', $offset = '', $groupby = '')
  {
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    $this->setOrder($order);
    $this->setLimitOffset($limit, $offset);
    $this->setGroupBy($groupby);
  }

  //実引数はコンストラクタの引数(Bootstrap)
  public function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    try {
      switch ($db_type) {
        case 'mysql':
          $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;

          $dbh = new \PDO($dsn, $db_user, $db_pass);
          $dbh->query('SET NAMES utf8');
          break;

        case 'pgsql';
          $dsn = 'pgsql:name=' . $db_name . ';host=' . $db_host . 'port=5432';
          $dbh = new \PDO($dsn, $db_user, $db_pass);
          break;
      }
    } catch (\PDOException $e) {
      var_dump($e->getMessage());
      exit;
    }
    return $dbh;
  }

  //selectの構文作成から、実行まで＋ログ残し
  public function select($table, $column = '', $where = '', $arrVal = [])
  {
    //セレクト文作成
    $sql = $this->getSql('select', $table, $where, $column);

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $data = [];
    while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data, $result);
    }
    return $data;
  }
  
  public function selectLimit($table, $column = '', $where = '', $arrVal = [], $limit ='', $offset ='')
  {
    //セレクト文作成
    $sql = $this->getSqlLimit('select', $table, $where, $column, $limit, $offset);

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $data = [];
    while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data, $result);
    }
    return $data;
  }


  public function selectlike($table, $column = '', $where = '', $like = '', $arrVal = [])
  {
    $sql = $this->getSql('select', $table, $where, $column, $like);

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $data = [];
    while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data, $result);
    }
    return $data;
  }

  public function getSql($type, $table, $where = '', $column = '', $like = '')
  {
    switch ($type) {
      case 'select':
        $columnkey = ($column !== '') ? $column : '*';
        break;

      case 'count':
        $columnkey = 'COUNT(*) AS NUM';
        break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';

    $likeSQL = ($like !== '') ? ' like ' . $like : '';

    $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;

    $sql = ' SELECT ' . $columnkey . ' FROM ' . $table . $whereSQL . $likeSQL . $other;

    return $sql;
  }

  public function getSqlLimit($type, $table, $where = '', $column = '', $limit = '', $offset = '')
  {
    switch ($type) {
      case 'select':
        $columnkey = ($column !== '') ? $column : '*';
        break;

      case 'count':
        $columnkey = 'COUNT(*) AS NUM';
        break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';

    // $likeSQL = ($like !== '') ? ' like ' . $like : '';

    // $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;

    $limitSQL = ($limit !== '') ? ' LIMIT ' . $limit : '';

    $offsetSQL = ($offset !== '') ? ' OFFSET ' . $offset : '';

    $sql = ' SELECT ' . $columnkey . ' FROM ' . $table . $whereSQL . $limitSQL . $offsetSQL;

    return $sql;
  }


  public function count($table, $where = '', $arrVal = [])
  {
    $sql = $this->getSql('count', $table, $where);

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return intval($result['NUM']);
  }

  public function insert($table, $insData = [])
  {
    $insDatakey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = '';
    $prest = '';

    foreach ($insData as $col => $val) {
      $insDatakey[] = $col;
      $insDataVal[] = $val;
      $preCnt[] = ' ? ';
    }

    $columns = implode(",", $insDatakey);
    $prest = implode(',', $preCnt);

    $sql = " INSERT INTO "
      . $table
      . " ( "
      . $columns
      . ", regist_date"
      . " ) VALUES ( "
      . $prest
      . ", NOW() "
      . " ) ";

    $this->sqlLogInfo($sql, $insDataVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($insDataVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  public function delete($table, $where, $deleteDataVal)
  {
    $sql = " delete from "
      . $table // likes
      . " WHERE " . $where;

    $this->sqlLogInfo($sql, $deleteDataVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($deleteDataVal);


    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  //一旦ストップ
  public function insertMember($table, $insData = [])
  {
    $insDatakey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = '';
    $prest = '';

    foreach ($insData as $col => $val) {
      $insDatakey[] = $col;
      $insDataVal[] = $val;
      $preCnt[] = ' ? ';
    }

    $columns = implode(",", $insDatakey);
    $prest = implode(',', $preCnt);

    $sql = " INSERT INTO "
      . $table
      . " ( "
      . $columns
      . "regist_date"
      . " ) VALUES ( "
      . $prest
      . " ) ";

    $this->sqlLogInfo($sql, $insDataVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($insDataVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  public function update($table, $insData = [], $where, $arrWhereVal = [])
  {
    $arrPrest = [];

    foreach ($insData as $col => $val) {
      $arrPrest[] = $col . ' = ? ';
    }

    $preSt = implode(",", $arrPrest);

    $sql = " UPDATE "
      . $table
      . " SET "
      . $preSt // family_name = ?, fist_name = ?
      . "WHERE"
      . $where; // 

    $updateData = array_merge(array_values($insData), $arrWhereVal);

    $this->sqlLogInfo($sql, $updateData);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($updateData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  public function getLastId()
  {
    return $this->dbh->lastInsertId();
  }

  private function catchError($errArr = [])
  {
    $errMsg = (!empty($errArr[2])) ? $errArr[2] : '';
    var_dump($errArr[2]);
    die('SQLエラーが発生しました' . $errMsg);
  }

  private function sqlLogInfo($str, $arrVal = [])
  {
    $logPath = $this->makeLogFile();
    $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));
    error_log($logData, 3, $logPath);
  }

  public function makeLogFile()
  {
    $logDir = dirname(__DIR__) . '/logs';
    if (!file_exists($logDir)) {
      mkdir($logDir, 777);
    }

    $logPath = $logDir . '/portfolio.log';
    if (!file_exists($logPath)) {
      touch($logPath);
    }

    return $logPath;
  }

  public function setOrder($order = '')
  {
    if ($order !== '') {
      $this->order = ' ORDER BY ' . $order;
    }
  }

  public function setLimitOffset($limit = '', $offset = '')
  {
    if ($limit !== "") {
      $this->limit = " LIMIT " . $limit;
    }
    if ($offset !== "") {
      $this->offset = " OFFSET " . $offset;
    }
  }

  public function setGroupBy($groupby = '')
  {
    if ($groupby !== "") {
      $this->groupby = ' GROUP BY ' . $groupby;
    }
  }
}


  // public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type)
  // {
  //   $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
  //   $this->db_host = $db_host;
  //   $this->db_user = $db_user;
  //   $this->db_pass = $db_pass;
  //   $this->db_name = $db_name;
  // }

   // public function getSql($type, $table, $where = '', $column = '')
  // {
  //   switch ($type) {
  //     case 'select':
  //       $columnkey = ($column !== '') ? $column : '*';
  //       break;

  //     case 'count':
  //       $columnkey = 'COUNT(*) AS NUM';
  //       break;
  //   }

  //   $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';

  //   $sql = ' SELECT ' . $columnkey . ' FROM ' . $table . $whereSQL .  $likeSQL . $group . $limit . $offset . $order;

  //   return $sql;
  // }

   // public function select($table, $column = '', $where = '', $arrVal = [], $group = '', $limit = '', $offset = '', $order = '')
  // {
  //   //セレクト文作成
  //   $sql = $this->getSql('select', $table, $where, $column, '', $group, $limit, $offset, $order);

  //   //log残し
  //   $this->sqlLogInfo($sql, $arrVal);

  //   //？入りの実行文の準備
  //   $stmt = $this->dbh->prepare($sql);

  //   //実行
  //   $res = $stmt->execute($arrVal);
  //   //executeの引数は配列arrayである必要あり。複数ある可能性があるから。

  //   if ($res === false) {
  //     $this->catchError($stmt->errorInfo());
  //   }

  //   $data = [];
  //   while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
  //     array_push($data, $result);
  //   }
  //   return $data;
  // }