<?php

namespace portfolio\lib;

class Likes
{
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getLike()
  {
    $table = ' likes ';
    $column = ' count(mem_id) as likes ,item_id ';
    $where = '';
    $arrVal = [];
    $group = ' item_id ';

    $res = $this->db->select($table, $column, $where, $arrVal, $group);

    return $res;
  }

    public function getLikeMember($mem_id = 0)
  {
    $table = ' likes ';
    $column = ' mem_id ,item_id ';
    $where = ' mem_id = ? ';
    $arrVal = [$mem_id];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }

  public function like_exsits($id, $item_id) {
    $table = ' likes ';
    $column =   '';
    $where = ' mem_id = ? AND item_id =? ';
    $arrVal = [$id, $item_id];

    $res = $this->db->select($table, $column, $where, $arrVal);

    $res = ($res !== []) ? true : false;

    return $res;
  }

  public function getLikeUser()
  {
    $table = ' item i JOIN likes l ON i.item_id = l.item_id ';
    $column = ' i.item_id ';
    $where = ' l.mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }
  
  public function getLikeBool()
  {
    $table = ' likes ';
    $column = ' * ';
    $where = ' mem_id = ? && item_id = ? ';
    $arrVal = [];
    $group = ' item_id ';

    $res = $this->db->select($table, $column, $where, $arrVal, $group);

    return $res;
  }

  public function getAjaxLike($mem_id, $item_id)
  {
    $table = ' likes ';
    $column = '';
    $where = ' mem_id = ? AND item_id = ? ';
    $arrVal = [$mem_id, $item_id];
  
    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }

  public function getAjaxLikeCount($item_id)
  {
    $table = ' likes ';
    $column = '';
    $where = ' item_id = ? ';
    $arrVal = [$item_id];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return count($res);
  }

  public function insAjaxLike($mem_id,  $item_id)
  {
    $table = ' likes ';
    $insData = [
      'mem_id' => $mem_id,
      'item_id' => $item_id
    ];

    $this->db->insert($table, $insData);
  }

  public function delAjaxLike($mem_id, $item_id)
  {
    $table = ' likes ';
    $where = ' mem_id = ? AND item_id = ? ';
    $deleteVal = [$mem_id, $item_id];

    $res = $this->db->delete($table, $where, $deleteVal);
  }
}
