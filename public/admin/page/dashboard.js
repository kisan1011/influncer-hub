$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    type: 'get',
    url: routeUrl + '/admin/dashboard',
    cache: false,
    success: (data) => {
      $('.card-content').html('');
      $html = '';
      $.each(data, function (index,card) {
        $html += '<div class="col-lg-3 col-6"> <div class="small-box '+card.class+'">';
        $html += '<div class="inner">';
        $html += '<h3>'+card.count+'</h3><p>'+card.title+'</p></div>';
        $html += '<div class="icon"><i class="'+card.icon+'"></i></div>';
        $html += '<a href="'+card.route+'" class="small-box-footer">View '+card.title+' <i class="fas fa-arrow-circle-right"></i></a>';
        $html += '</div></div>';
      });
      $('.card-content').html($html);
    },
    error: function (data) {
      console.log("Error");
    }
  });







});
