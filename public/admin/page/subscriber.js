$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

$(document).ready(function () {

  // Load datatable
  $('#subscriber-show').DataTable({
    processing: true,
    serverSide: true,
    ajax: routeUrl + '/admin/subscriber',
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
      { data: 'email', name: 'email' },
      { data: 'status', name: 'status', orderable: false },
      { data: 'action', name: 'action', orderable: false },
    ],
    rowId: function (a) {
      return a.id;
    },
    order: [0, 'asc']
  });
});


// Subscriber status change
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
        url: routeUrl + '/admin/subscriber/status-update',
        data: { 'id': id },
        success: (data) => {
          if (data.status == true) {
            toastr.success(data.message, 'Success');
          } else if (data.status == false) {
            toastr.error(data.message);
          }
          var oTable = eval($('#subscriber-show').dataTable());
          oTable.fnDraw(false);
        },
        error: function (data) {
          console.log("Error");
        }
      });
    } else {
      var oTable = eval($('#subscriber-show').dataTable());
      oTable.fnDraw(false);
    }
  });
});

// Delete single subscriber
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
            var oTable = eval($('#subscriber-show').dataTable());
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

// Delete multiple subscriber
$(document).on('click', '#multiple_delete_btn', function (e) {
  var table = $('#subscriber-show').dataTable();
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
          url: routeUrl + '/admin/delete-subscriber',
          data: { 'ids': selected_rows_array[0] },
          success: (data) => {
            if (data.status == false) {
              toastr.error(data.message);
            } else if (data.status == true) {
              toastr.success(data.message, 'Success');
              var oTable = $('#subscriber-show').dataTable();
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
