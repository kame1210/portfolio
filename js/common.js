$(function () {
  // 郵便番号からの住所検索
  $('#address_search').click(function () {

    let zip = $('#zip').val();

    let entry_url = $('#entry_url').val();

    if (zip.match(/[0-9]{7}/) === null) {
      alert('正確な郵便番号を入力してください');
      return false;
    } else {
      $.ajax({
        type: "get",
        url: entry_url + "/postcode_search.php?zip=" + escape(zip),
      }).then(
        function (data) {
          if (data == 'no' || data == '') {
            alert('該当する郵便番号がありません');
          } else {
            $('#address').val(data);
          }
        },
        function (data) {
          alert('読み込みに失敗しました');
        },
      );
    }
  });

  // いいねボタン実装
  $('.btn-like').on('click', function () {
    let item_id = $(this).data('item-id');
    let button = this;

    console.log(item_id);

    $.ajax({
      type: 'POST',
      url: 'ajaxlikes.php',
      data: { item_id: item_id }
    }).then(
      function (data) {
        console.log('ajax success');

        $(button).find('span').html(data);
        $(button).children('i').toggleClass('far');
        $(button).children('i').toggleClass('fas');
        $(button).children('i').toggleClass('red');
      },
      function (data) {
        console.log('ajax error');
        $(button).children('span').html(data);
        location.href = 'http://hobbykatsu.work/portfolio/login.php';
      },
    );
  });

});

// モーダルウインドウ
$(function () {
  $(document).on('open', '.remodal', function () {
    var modal = $(this);
  });
  $(document).on('opened', '.remodal', function () {
    var modal = $(this);
  });
  $(document).on('close', '.remodal', function () {
    var modal = $(this);
  });
  $(document).on('closed', '.remodal', function () {
    var modal = $(this);
  });
  $(document).on('confirm', '.remodal', function () {
    var modal = $(this);
  });
  $(document).on('cancel', '.remodal', function () {
    var modal = $(this);
  });
});

// item_detail カーセルスライダー
$(function () {
  $('.slider').slick({
    infinite: true,
    initialSlide: 0,
    slidesToShow: 1,
    arrows: true,
    slidesToScroll: 1,
    swipe: true,
    asNavFor: '.slider-nav',
  });
  $('.slider-nav').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: '.slider',
    arrows: false,
    dots: true,
    focusOnSelect: true
  });
});

// カートイン機能
$(function () {
  var entry_url = $('#entry_url').val();

  $("#cart-btn").click(function () {
    var item_id = $("#item_id").val();
    location.href = entry_url + 'cart.php?item_id=' + item_id;
  });
});

// password表示切替
$(function () {
  $('#passcheck').change(function () {
    if ($(this).prop('checked')) {
      $('#password').attr('type', 'text');
    } else {
      $('#password').attr('type', 'password');
    }
  });
});

// マイページの商品削除コメント
function deleteItem() {
  if (confirm('削除してよろしいですか？')) {
    alert('商品を削除しました');
    return true;
  } else {
    alert('商品の削除をキャンセルしました');
    return false;
  }
}

// navレスポンシブ
$(function () {
  $('#nav_btn').on('click', function () {
    $('body').toggleClass('nav_open');
  });
});
