$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

// Logout user
$(document).on('click', '.logoutbtn', function () {
  Swal.fire({
    title: "Are you sure?",
    text: "you want to log out!",
    icon: 'warning',
    showCancelButton: true,
    closeOnConfirm: false,
    closeOnCancel: false,
    showLoaderOnConfirm: true,
  }).then((isConfirm) => {
    if (isConfirm.isConfirmed == true) {
      $.ajax({
        type: 'post',
        url: routeUrl + '/admin/logout',
        dataType: 'json',
        success: function (res) {
          location.reload();
        }
      });
    } else {
      return;
    }
  });
});

// Load preview image
function load_preview_image(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#preview_div").show();
      $('#image_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    $("#preview_div").hide();
  }
}

// select checkbox form datetable
$(document).on('change', 'input.dt-checkboxes', function (e) {
  e.preventDefault();
  let rows_selected = $('input.dt-checkboxes').is(':checked');
  if (rows_selected) {
    $('#multiple_delete_btn').prop("disabled", false);
  } else {
    $('#multiple_delete_btn').prop("disabled", true);
  }
});

$(document).on('change', 'tbody,thead input.dt-checkboxes, .dt-checkboxes-select-all', function () {
  var table = $('table').attr('id');
  var rows_selected = $('#' + table).DataTable().column(0).checkboxes.selected();
  if (rows_selected.length) {
    $("#multiple_delete_btn").prop('disabled', false);
  } else {
    $("#multiple_delete_btn").prop('disabled', true);
  }
});
