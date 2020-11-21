<?php

namespace portfolio\lib;

class Page2
{
  private $maxViewItem;
  private $defaultPage;
  private $pageLink;

  public function __construct()
  {
    $this->setMaxViewItem(5);
    $this->setDefaultPage(1);
    $this->setPageLink(5); //奇数
  }

  public function setMaxViewItem($number)
  {
    $this->maxViewItem = $number < 1 ? 1 : $number;
  }

  public function setDefaultPage($number)
  {
    $this->defaultPage = $number < 1 ? 1 : $number;
  }

  public function setPageLink($number)
  {
    $this->pageLink = $number < 1 ? 1 : $number;
  }

  public function getPageData($page = null, $itemCount)
  {
    //DBから取得したitemデータの数
    $itemCount = $itemCount < 1 ? 1 : $itemCount;

    $data = [];
    // 1ページの表示件数
    $data['limit'] = $this->maxViewItem;
    // 最大ページ数の作成
    $data['maxPage'] = (int)ceil(intval($itemCount) / $this->maxViewItem);

    if (is_null($page) || $page === 0 || $data['maxPage'] < $page) {
      $data['currentPage'] = $this->defaultPage;
      $data['offset'] = 0;
    } else {
      $data['currentPage'] = $page;
      $data['offset'] = ($page - 1) * $this->maxViewItem;
    }

    $data['pageLink'] = $this->pageLink;
    // もし現在のページが最大ページ ー ページリンク / 2 より小さかったら、
    // スタートナンバーを 現在のページ - ページリンク / 2 にする
    if ($data['currentPage'] < $data['maxPage'] - ($data['pageLink'] / 2)) {
      $startNumber = $data['currentPage'] - ($data['pageLink'] / 2);
    } else {
      $startNumber = $data['maxPage'] - $data['pageLink'] + 1;
    }
    $data['startPage'] = ($startNumber < $this->defaultPage) ? $this->defaultPage : $startNumber;

    $data['prePage'] = ($data['startPage'] > 1) ? $data['startPage'] - 1 : null;
    $endNumber = $data['startPage'] + $data['pageLink'] - 1;
    $data['nextPage'] = ($endNumber < $data['maxPage']) ? $endNumber + 1 : null;

    return $data;
  }

  public function getPageLink()
  {
    $arr = [];

    parse_str($_SERVER['QUERY_STRING'], $arr);
    unset($arr['page']);

    $data = '';
    foreach ($arr as $key => $value) {
      $data .=  $key . '=' . $value . '&';
    }

    return $data;
  }
}
