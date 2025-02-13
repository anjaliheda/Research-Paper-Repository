document.addEventListener('DOMContentLoaded', () => {
  const signInSection = document.querySelector('.sign-in');
  const signUpSection = document.querySelector('.sign-up');
  const signUpToggle = document.querySelectorAll('.toggle-btn');
  
  signUpToggle.forEach(btn => {
    btn.addEventListener('click', () => {
      signInSection.classList.toggle('active');
      signUpSection.classList.toggle('active');
    });
  });
});

$(document).ready(function() {
  $("#sign-in").submit(function(event) {
      event.preventDefault();

      $.ajax({
          type: "POST",
          url: "request.php",
          data: {
              email: $("#email").val(),
              password: $("#password").val(),
              action: "login"
          },
          dataType: "json",
          success: function(response, textStatus, jqXHR) {
              if (jqXHR.status === 200) {
                  if (response.success === true) {
                      toastr.success("Login successful!", "Success");
                      setTimeout(function() {
                          window.location.href = "dashboard.php"; 
                      }, 1000); 
                  } else {
                      toastr.warning(response.message, "Something went wrong");
                  }
              } else {
                  console.error("Unexpected success callback with wrong status code: ", jqXHR.status);
                  toastr.error("An unexpected error has occurred", "Error");
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
              if (jqXHR.status === 401 || jqXHR.status === 400) {
                  try {
                      let response = JSON.parse(jqXHR.responseText);
                      toastr.warning(response.message, "Someting Went wrong"); 
                  } catch (e) {
                      console.error("Error parsing JSON:", e);
                      toastr.error("An error occurred. Please try again later.", "Error");
                  }
              } else if (textStatus === "timeout") {
                  toastr.error("Request timed out. Please try again later.", "Timeout");
              } else if (textStatus === "abort") {
                  toastr.info("Request was aborted.", "Info");
              } else {
                  toastr.error("A network or server error occurred. Please try again later.", "Error");
                  console.error("AJAX Error:", textStatus, errorThrown, jqXHR);
              }
          }
      });
  });
});


toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-top-right", 
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

$("#sign-up").submit(function(event) {
    event.preventDefault();
    const emailPattern = /^[a-zA-Z0-9._%+-]+@rvce\.edu\.in$/;
    const email = $("#remail").val().trim(); 
    if (!emailPattern.test(email)) {
        toastr.warning("Only emails ending with @rvce.edu.in are allowed", "Invalid Email");
        return;
    }
    var formData = new FormData();

    formData.append('email', email);
    formData.append('password', $("#rpassword").val());
    formData.append('name', $("#rname").val());
    formData.append('role', $("#rrole_id").val());
    formData.append('action', "createUser");

   

    $.ajax({
        type: "POST",
        url: "request.php",
        data: formData,
        processData: false, 
        contentType: false, 
        dataType: "json",
        success: function(response, textStatus, jqXHR) {
            if (jqXHR.status === 200) {
                if (response.success === true) {
                    toastr.success("User Created successful!", "Success");
                    setTimeout(function() {
                        window.location.reload(); 
                    }, 1000); 
                } else {
                    toastr.warning(response.message, "Something went wrong");
                }
            } else {
                console.error("Unexpected success callback with wrong status code: ", jqXHR.status);
                toastr.error("An unexpected error has occurred", "Error");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 401 || jqXHR.status === 400) {
                try {
                    let response = JSON.parse(jqXHR.responseText);
                    toastr.warning(response.message, "Something Went wrong"); 
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    toastr.error("An error occurred. Please try again later.", "Error");
                }
            } else if (textStatus === "timeout") {
                toastr.error("Request timed out. Please try again later.", "Timeout");
            } else if (textStatus === "abort") {
                toastr.info("Request was aborted.", "Info");
            } else {
                toastr.error("A network or server error occurred. Please try again later.", "Error");
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR);
            }
        }
    });
});