<?php

namespace portfolio\lib;

class Cart
{
  private $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function insCartData($mem_id, $item_id)
  {
    $table = ' cart ';
    $insData = [
      'mem_id' => $mem_id,
      ' item_id ' => $item_id
    ];

    return $this->db->insert($table, $insData);
  }

  public function updateCartData($mem_id, $item_id, $quantity)
  {
    //mem_idに変更する
    $table = ' cart ';
    $upData = [
      'quantity' => $quantity
    ];
    $where = ' mem_id = ? AND item_id = ? AND delete_flg = ? ';
    $arrWhereVal = [$mem_id, $item_id, 0];

    return $this->db->update($table, $upData, $where, $arrWhereVal);
  }

  public function getCartData($mem_id)
  {
    $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
    $column = ' c.crt_id, c.quantity, i.item_id, i.item_name, i.detail, i.price, i.image ';
    $where = ' c.mem_id = ? AND c.delete_flg = ? ';
    $arrVal = [$mem_id, 0];

    $res = $this->db->select($table, $column, $where, $arrVal);

    for ($i = 0; $i < count($res); $i++) {
      $res[$i]['image'] = explode(',', $res[$i]['image']);
    }
    
    return $res;
  }

  public function getCartquantity($mem_id, $item_id)
  {
    $table = ' cart ';
    $column = ' quantity ';
    $where = ' mem_id = ? AND item_id = ? AND delete_flg = ? ';
    $arrVal = [$mem_id, $item_id, 0];

    return $this->db->select($table, $column, $where, $arrVal);
  }

  public function delCartData($crt_id)
  {
    $table = ' cart ';
    $insData = [' delete_flg ' => 1];
    $where = ' crt_id = ? ';
    $arrWhereVal = [$crt_id];

    return $this->db->update($table, $insData, $where, $arrWhereVal);
  }

  public function orderDelCartData($mem_id)
  {
    $table = ' cart ';
    $insData = [' delete_flg ' => 1];
    $where = ' mem_id = ? && delete_flg = ? ';
    $arrWhereVal = [$mem_id, 0];

    return $this->db->update($table, $insData, $where, $arrWhereVal);
  }

  public function getItemAndSumPrice($mem_id)
  {
    //合計金額
    $table = " cart c LEFT JOIN item i ON c.item_id = i.item_id ";
    $column = " SUM( i.price * c.quantity ) AS totalPrice ";
    $where = ' c.mem_id = ? AND c.delete_flg = ? ';
    $arrWhereVal = [$mem_id, 0];

    $res = $this->db->select($table, $column, $where, $arrWhereVal);

    $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;

    //アイテム数
    $table = ' cart c ';
    $column = ' SUM( quantity ) AS num ';
    // $column = ' SUM( num ) AS num ';
    $res = $this->db->select($table, $column, $where, $arrWhereVal);

    $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
    return [$num, $price];
  }
}
