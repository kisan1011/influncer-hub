$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

$(document).ready(function () {

  // Load datatable
  let columns = [
      { data: 'id', name: 'id', orderable: false },
      { data: 'username', name: 'username', orderable: false },
      { data: 'email', name: 'email' },
      { data: 'thumbnail', name: 'thumbnail' },
      { data: 'channel_name', name: 'channel_name' },
      { data: 'category', name: 'category' },
      { data: 'content_category', name: 'content_category' },
      { data: 'subscriber_or_followers_count', name: 'subscriber_or_followers_count' },
      { data: 'view_or_follows_count', name: 'view_or_follows_count' },
      { data: 'video_or_media_count', name: 'video_or_media_count' },
      { data: 'is_verified', name: 'is_verified', orderable: false },
      { data: 'status', name: 'status', orderable: false },
      { data: 'action', name: 'action', orderable: false },
  ];

  // Check if type is 'Instagram' and conditionally add the insta_username column
  if (type === 'instagram') {
      columns.splice(4, 0, { data: 'insta_username', name: 'insta_username' }); // Add at position 4
  }
  $('#influencer-show').DataTable({
    processing: true,
    serverSide: true,
    ajax: routeUrl + '/admin/influencer-network/'+type,
    columnDefs: [
      {
        targets: 0,
        checkboxes: {
          selectRow: true
        }
      }
    ],
    columns: columns,
    rowId: function (a) {
      return a.id;
    },
    order: [0, 'asc']
  });
});


// show influencer details
$(document).on('click', '.show_details', function (e) {
  console.log("lost of most of");

  e.preventDefault();
  $.ajax({
    type: 'get',
    url: $(this).data('action'),
    dataType: 'html',
    success: function (res) {
      if (res != '') {
        $('#modal-xl').modal({ backdrop: 'static', keyboard: false }, 'show');
        $('.show_content').html(res);
        $('.modal-title').html('Influencer details');
      } else {
        toastr.error("Something went wrong. Please try again.");
      }
    },
    error: function (data) {
      console.log(data);
    }
  });
});

// Influencer status change
$(document).on('click', '.change-status-record', function (e) {
  e.preventDefault();
  Swal.fire({
    title: "Are you sure?",
    text: "you want to change status!",
    icon: 'warning',
    confirmButtonText: "Yes, I want change status",
    showCancelButton: true,
    closeOnConfirm: false,
    closeOnCancel: false,
    showLoaderOnConfirm: true,
  }).then((isConfirm) => {
    if (isConfirm.isConfirmed == true) {
      let id = $(this).data('id');
      $.ajax({
        type: 'post',
        url: routeUrl + '/admin/influencer/status-update',
        data: { 'id': id },
        success: (data) => {
          if (data.status == true) {
            toastr.success(data.message, 'Success');
          } else if (data.status == false) {
            toastr.error(data.message);
          }
          var oTable = eval($('#influencer-show').dataTable());
          oTable.fnDraw(false);
        },
        error: function (data) {
          console.log("Error");
        }
      });
    } else {
      var oTable = eval($('#influencer-show').dataTable());
      oTable.fnDraw(false);
    }
  });
});

// Delete single influencer
$(document).on('click', '.delete_data', function (e) {
  e.preventDefault();
  Swal.fire({
    title: "Are you sure?",
    text: "You will not be able to recover this record!",
    icon: 'warning',
    showCancelButton: true,
    closeOnConfirm: false,
    closeOnCancel: false,
    showLoaderOnConfirm: true,
  }).then((isConfirm) => {
    if (isConfirm.isConfirmed == true) {
      $.ajax({
        type: 'delete',
        url: $(this).data('action'),
        success: (data) => {
          if (data.status == true) {
            toastr.success(data.message, 'Success');
            var oTable = eval($('#influencer-show').dataTable());
            oTable.fnDraw(false);
          } else if (data.status == false) {
            toastr.error(data.message);
          }
        },
        error: function (data) {
          console.log("Error");
        }
      });
    } else {
      return;
    }
  });
});

// Delete multiple influencer
$(document).on('click', '#multiple_delete_btn', function (e) {
  var table = $('#influencer-show').dataTable();
  let rows_selected = table.api().columns(0).checkboxes.selected();
  if (rows_selected.length > 0) {
    Swal.fire({
      title: "Are you sure?",
      text: "You will not be able to recover this record!",
      icon: 'warning',
      showCancelButton: true,
      closeOnConfirm: false,
      closeOnCancel: false,
      showLoaderOnConfirm: true,
    }).then((isConfirm) => {
      if (isConfirm.isConfirmed == true) {
        let selected_rows_array = [];
        $.each(rows_selected, function (index, rowId) {
          selected_rows_array.push(rowId);
        });
        $.ajax({
          type: 'POST',
          url: routeUrl + '/admin/delete-influencer',
          data: { 'ids': selected_rows_array[0] },
          success: (data) => {
            if (data.status == false) {
              toastr.error(data.message);
            } else if (data.status == true) {
              toastr.success(data.message, 'Success');
              var oTable = $('#influencer-show').dataTable();
              oTable.fnDraw(false);
            }
            $('#multiple_delete_btn').prop("disabled", true);
          },
          error: function (data) {
            console.log("Error");
          }
        });
      }
    })
  }
});
