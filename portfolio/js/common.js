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

        $(button).children('span').html(data);
        $(button).children('i').toggleClass('far');
        $(button).children('i').toggleClass('fas');
        $(button).children('i').toggleClass('red');
      },
      function (data) {
        console.log('ajax error');
        $(button).children('span').html(data);
        location.href = 'http://localhost/DT/portfolio/login.php';
      },
    );
  });

  // ハンバーガーメニュー
  // $('.nav_toggle').on('click', function () {
  //   $('.nav_toggle, .nav').toggleClass('show');
  // });

  $('#nav_btn').on('click', function () {
    $('body').toggleClass('nav_open');
  });

  // モーダルウインドウ
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

  $(function () {
    $('.slider').slick({
      infinite: true,
      initialSlide: 0,
      autoplay: false,
      autoplaySpeed: 4000,
      slidesToShow: 5,
      slidesToScroll: 1,
      responsive: [
        {
          breakpoint: 960,
          settings: {
            slidesToShow: 3,
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
          }
        }
      ]
    });
  });

});

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



// $('#address_search').click(function () {

//   let zip1 = $('#zip1').val();
//   let zip2 = $('#zip2').val();

//   let entry_url = $('#entry_url').val();

//   if (zip1.match(/[0-9]{3}/) === null || zip2.match(/[0-9]{4}/) === null) {
//     alert('正確な郵便番号を入力してください');
//     return false;
//   } else {
//     $.ajax({
//       type: "get",
//       url: entry_url + "/postcode_search.php?zip1=" + escape(zip1) + "&zip2=" + escape(zip2),
//     }).then(
//       function (data) {
//         if (data == 'no' || data == '') {
//           alert('該当する郵便番号がありません');
//         } else {
//           $('#address').val(data);
//         }
//       },
//       function (data) {
//         alert('読み込みに失敗しました');
//       },
//     );
//   }
// });