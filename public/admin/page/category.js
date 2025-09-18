$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

$(document).ready(function () {

  // Load datatable
  $('#category-show').DataTable({
    processing: true,
    serverSide: true,
    ajax: routeUrl + '/admin/category',
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
      { data: 'image', name: 'image', orderable: false, searchable: false},
      { data: 'name', name: 'name', searchable: true },
      { data: 'type', name: 'type', searchable: true },
      { data: 'status', name: 'status', orderable: false, searchable: false },
      { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    rowId: function (a) {
      return a.id;
    },
    order: [0, 'asc']
  });
});

// new category add form
$(document).on('click', '.add_form', function (e) {
  e.preventDefault();
      $('#modal_div').modal({ backdrop: 'static', keyboard: false }, 'show');
      $('.modal-title').html('Add channel category');
      $('#data_form')[0].reset();
      $('#id').val('');
      $('#preview_div').css('display','none');
      submit_form();
});

//  Edit category form
$(document).on('click', '.edit_form', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'get',
    url: $(this).data('action'),
    success: (data) => {
      if(data.status == true){
        console.log(data.data);

        $('#modal_div').modal({ backdrop: 'static', keyboard: false }, 'show');
        $('.modal-title').html('Edit channel category');
        $('#data_form')[0].reset();
        $('#id').val(data.data.id);
        $('#name').val(data.data.name);
        $('#image_preview').attr("src",data.data.logo);
        $('#preview_div').css('display','block');
        if (data.data.type == 0) {
            $('#type_youtube').prop('checked', true);
        } else if (data.data.type == 1) {
            $('#type_instagram').prop('checked', true);
        }
        submit_form();
      }else if (data.status == false){
        toastr.error(data.message);
      }
    },
    error: function (data) {
      console.log("Error");
    }
  });
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
      logo: {
        required: function (element) {
          if ($('#id').val() != '') {
            return false;
          } else {
            return true;
          }
        },
        accept: "image/jpg,image/jpeg,image/png",
      },
    },
    messages: {
      name: {
        required: "Name must be required.",
        minlength: "Please enter a name minimum of 3 characters.",
        maxlength: "Please enter a name maximum of 25 characters.",
        letterswithspace: "Category name only letters allowed.",
      },
      logo: {
        required: "Image must be required.",
        accept: "image only allowed jpg,jpeg,png.",
      },
    },
    submitHandler: function (form) {
      var formData = new FormData(form);
      $.ajax({
        type: 'POST',
        url: routeUrl + '/admin/category',
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
            var oTable = $('#category-show').dataTable();
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

// show category details
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
        $('.modal-title').html('Channel category details');
      } else {
        toastr.error("Something went wrong. Please try again.");
      }
    },
    error: function (data) {
      console.log(data);
    }
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
            var oTable = eval($('#category-show').dataTable());
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
  var table = $('#category-show').dataTable();
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
          url: routeUrl + '/admin/delete-category',
          data: { 'ids': selected_rows_array[0] },
          success: (data) => {
            if (data.status == false) {
              toastr.error(data.message);
            } else if (data.status == true) {
              toastr.success(data.message, 'Success');
              var oTable = $('#category-show').dataTable();
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

// category status change
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
        url: routeUrl + '/admin/category/status-update',
        data: { 'id': id },
        success: (data) => {
          if (data.status == true) {
            toastr.success(data.message, 'Success');
          } else if (data.status == false) {
            toastr.error(data.message);
          }
          var oTable = eval($('#category-show').dataTable());
          oTable.fnDraw(false);
        },
        error: function (data) {
          console.log("Error");
        }
      });
    } else {
      var oTable = eval($('#category-show').dataTable());
      oTable.fnDraw(false);
    }
  });
});
