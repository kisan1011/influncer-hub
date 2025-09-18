$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  CKEDITOR.replace('description');
});


$("#static-form").validate({
  submitHandler: function (form, e) {
    var formData = new FormData(form);
    var desc = CKEDITOR.instances['description'].getData();
    formData.set('description', desc);
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: routeUrl + '/admin/static-page',
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (result) {
        $('#btn').prop('disabled', false);
        if (result.status) {
          toastr.success(result.message);
        } else {
          toastr.error(result.message);
        }
      },
      error: function () {
        toastr.error('Please Reload Page.');
        $('#loader').hide();
        $('#btn').prop('disabled', false);
      }
    });
    return false;
  }
});
