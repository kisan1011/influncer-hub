$(document).ready(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
});

$(document).ready(function () {

    // Load datatable
    $('#country-show').DataTable({
      processing: true,
      serverSide: true,
      ajax: routeUrl + '/admin/country',
      columnDefs: [
        {
          targets: 0,
          checkboxes: {
            selectRow: true
          }
        }
      ],
      columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false},
        { data: 'name', name: 'name', searchable: true },
        { data: 'code', name: 'code', searchable: true },
        { data: 'action', name: 'action', orderable: false, searchable: false },
      ],
      rowId: function (a) {
        return a.id;
      },
      order: [0, 'asc']
    });
});

// new country add form
$(document).on('click', '.add_form', function (e) {
    e.preventDefault();
    $('#modal_div').modal({ backdrop: 'static', keyboard: false }, 'show');
    $('.modal-title').html('Add country');
    $('#data_form')[0].reset();
    $('#id').val('');
    $('#preview_div').css('display','none');
    submit_form();
});

// Modal close button event
$("#modal_div").on("hidden.bs.modal", function () {
    $('#data_form').validate().resetForm();
    $('#data_form').find('.error').removeClass('error');
});

// submit form
function submit_form() {
    $("#data_form").validate({
      rules: {
        name: {
            required: true,
            minlength: 3,
            maxlength: 25,
        },
        code: {
            required: true,
            minlength: 2,
            maxlength: 2,
        },
    },
    messages: {
        name: {
          required: "Name must be required.",
          minlength: "Please enter a name minimum of 3 characters.",
          maxlength: "Please enter a name maximum of 25 characters.",
        },
        code: {
          required: "Code must be required.",
          minlength: "Please enter a code minimum of 2 characters.",
          maxlength: "Please enter a code maximum of 2 characters.",
        },
      },
      submitHandler: function (form) {
        var formData = new FormData(form);
        $.ajax({
          type: 'POST',
          url: routeUrl + '/admin/country',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          success: (data) => {
            if (data.status == false) {
              toastr.error(data.message);
            } else if (data.status == true) {
              toastr.success(data.message, 'Success');
              $('#data_form')[0].reset();
              $('#modal_div').modal('hide');
              var oTable = $('#country-show').dataTable();
              oTable.fnDraw(false);
            }
          },
          error: function (data) {
            console.log("Error");
          }
        });
  
      }
    });
}

// Delete single country
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
              var oTable = eval($('#country-show').dataTable());
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

// Delete multiple country
$(document).on('click', '#multiple_delete_btn', function (e) {
    var table = $('#country-show').dataTable();
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
            url: routeUrl + '/admin/delete-country',
            data: { 'ids': selected_rows_array[0] },
            success: (data) => {
              if (data.status == false) {
                toastr.error(data.message);
              } else if (data.status == true) {
                toastr.success(data.message, 'Success');
                var oTable = $('#country-show').dataTable();
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