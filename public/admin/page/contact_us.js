$(document).ready(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
});

$(document).ready(function () {

    // Load datatable
    $('#contact-us-show').DataTable({
      processing: true,
      serverSide: true,
      ajax: routeUrl + '/admin/contact-us',
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
        { data: 'fullname', name: 'fullname', orderable: false },
        { data: 'email', name: 'email', orderable: false },
        { data: 'phone', name: 'phone', orderable: false },
        { data: 'message', name: 'message', orderable: false },
        { data: 'action', name: 'action', orderable: false },
      ],
      rowId: function (a) {
        return a.id;
      },
      order: [0, 'asc']
    });
  });

  // Delete single category
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
              var oTable = eval($('#contact-us-show').dataTable());
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
  
  // Delete multiple category
  $(document).on('click', '#multiple_delete_btn', function (e) {
    var table = $('#contact-us-show').dataTable();
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
            url: routeUrl + '/admin/delete-contact-us',
            data: { 'ids': selected_rows_array[0] },
            success: (data) => {
              if (data.status == false) {
                toastr.error(data.message);
              } else if (data.status == true) {
                toastr.success(data.message, 'Success');
                var oTable = $('#contact-us-show').dataTable();
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

  