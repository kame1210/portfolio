<?php

namespace portfolio\lib;

class Member
{
  private $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  // セッションにidとuser_nameを保存する。
  public function login($dataArr)
  {
    $loginData = $this->loginMember($dataArr['email']);

    if ($loginData !== '') {
      if ($loginData['email'] === $dataArr['email'] && password_verify($dataArr['password'], $loginData['password'])) {
        $_SESSION['id'] = $loginData['mem_id'];
        $_SESSION['user_name'] = $loginData['family_name'] . $loginData['first_name'];
        $_SESSION['email'] = $loginData['email'];

        header('Location: http://localhost/DT/portfolio/index.php');
      } else {
        $errMsg = 'パスワードかメールアドレスが違います';
        return  $errMsg;
      }
    } else {
      $errMsg = 'メールアドレスが違うかユーザー登録ができていません';
      return  $errMsg;
    }
  }

  public function loginMember($email)
  {
    $table = ' member ';
    $column = '';
    $where = ' email = ? AND delete_flg = ? ';
    $arrVal = [$email, 0];

    $res = $this->db->select($table, $column, $where, $arrVal);

    $res = ($res !== []) ? $res[0] : '';

    return $res;
  }

  public function deleteMemberCheck($dataArr){
    $memData = $this->loginMember($dataArr['email']);

    if ($memData !== '') {
      if ($memData['email'] === $dataArr['email'] && password_verify($dataArr['password'], $memData['password'])) {
        return '';
      } else {
        $errMsg = 'パスワードが間違っています。';
        return $errMsg;
      }
    } else {
      $errMsg = 'ユーザー情報が間違っています。';
      return $errMsg;
    }

  }

  public function memberCheck($mem_id)
  {
    $table = ' member ';
    $column = '';
    $where = ' mem_id = ? ';
    $arrVal = [$mem_id];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }

  public function memberUpdate($updateData, $mem_id)
  {
    $updateData['password'] = password_hash($updateData['password'], PASSWORD_DEFAULT);

    $table = ' member ';
    $arrVal = $updateData;
    $where = ' mem_id = ? ';
    $arrWhereVal = [$mem_id];

    $res = $this->db->update($table, $arrVal, $where, $arrWhereVal);

    return $res;
  }

  public function memberDelete($dataArr){
    $table = ' member ';
    $insData = ['delete_flg' => '1'];
    $where = ' email = ? ';
    $arrWhereVal = [$dataArr['email']];

    $res = $this->db->update($table, $insData, $where, $arrWhereVal);

    return $res;
  }

  public function memberRegist($dataArr){
    $dataArr['password'] = password_hash($dataArr['password'], PASSWORD_DEFAULT);

    $table = ' member ';

    $res = $this->db->insert($table, $dataArr);

    return $res;
  }

  public function logout()
  {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }

    session_destroy();

    header('Location: http://localhost/DT/portfolio/index.php');
  }
}
