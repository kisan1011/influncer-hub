$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});

// Form submit function
$("#profile_frm").validate({
  rules: {
    firstname: {
      required: true,
      lettersonly: true
    },
    email: {
      required: true,
      email: true
    },
    password: {
      minlength: 8,
    },
    confirm_password: {
      equalTo: "#password"
    },
    profile: {
      accept: "image/jpg,image/jpeg,image/png"
    },
  },
  messages: {
    firstname: {
      required: "Please enter your name.",
      lettersonly: "Name allow only letters."
    },
    email: {
      required: "Please enter your email.",
      email: "Please enter valid email."
    },
    password: {
      required: "Please enter the password.",
      minlength: "Please enter a password of at least 8 characters."
    },
    confirm_password: {
      equalTo: "Password and confirm password not match."
    },
    profile: {
      accept: 'Profile allowed olny image.'
    },
  },
  submitHandler: function (form) {
    var formData = new FormData(form);
    var name = $("#name").val();
    $.ajax({
      type: "POST",
      url: routeUrl + "/admin/profile",
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (result) {
        $('#btn').prop('disabled', false);
        if (result.status) {
          $("#password,#confirm_password,#image").val('');
          toastr.success(result.message);
          let image = $(".admin_profile").attr("src");
          $(".profile_image").attr("src", image);
          $('#profile_frm')[0].reset();
          $("#username").html(name);
          $("#name").val(name);
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
