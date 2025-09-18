$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

$(document).ready(function () {

  // Load datatable
  $('#business-show').DataTable({
    processing: true,
    serverSide: true,
    ajax: routeUrl + '/admin/business',
    columnDefs: [
      {
        targets: 0,
        checkboxes: {
          selectRow: true
        }
      }
    ],
    columns: [
      { data: 'id', name: 'id', orderable: false },
      { data: 'profile', name: 'profile', orderable: false },
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'type', name: 'type', orderable: false },
      { data: 'verified', name: 'verified', orderable: false },
      { data: 'status', name: 'status', orderable: false },
      { data: 'action', name: 'action', orderable: false },
    ],
    rowId: function (a) {
      return a.id;
    },
    order: [0, 'asc']
  });
});


// show business details
$(document).on('click', '.show_details', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'get',
    url: $(this).data('action'),
    dataType: 'html',
    success: function (res) {
      if (res != '') {
        $('#modal-xl').modal({ backdrop: 'static', keyboard: false }, 'show');
        $('.show_content').html(res);
        $('.modal-title').html('Business details');
      } else {
        toastr.error("Something went wrong. Please try again.");
      }
    },
    error: function (data) {
      console.log(data);
    }
  });
});

// Business status change
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
        url: routeUrl + '/admin/business/status-update',
        data: { 'id': id },
        success: (data) => {
          if (data.status == true) {
            toastr.success(data.message, 'Success');
          } else if (data.status == false) {
            toastr.error(data.message);
          }
          var oTable = eval($('#business-show').dataTable());
          oTable.fnDraw(false);
        },
        error: function (data) {
          console.log("Error");
        }
      });
    } else {
      var oTable = eval($('#business-show').dataTable());
      oTable.fnDraw(false);
    }
  });
});

// Delete single business
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
            var oTable = eval($('#business-show').dataTable());
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

// Delete multiple business
$(document).on('click', '#multiple_delete_btn', function (e) {
  var table = $('#business-show').dataTable();
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
          url: routeUrl + '/admin/delete-business',
          data: { 'ids': selected_rows_array[0] },
          success: (data) => {
            if (data.status == false) {
              toastr.error(data.message);
            } else if (data.status == true) {
              toastr.success(data.message, 'Success');
              var oTable = $('#business-show').dataTable();
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
