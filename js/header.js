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

  $(document).ready(function() {
    $('#logout').on('click', function(event) {
    event.preventDefault();

    $.ajax({
        type: "POST",
        url: "request.php",
        data: {
            action: "logout"
        },
        dataType: "json",
        success: function(response, textStatus, jqXHR) {
            if (jqXHR.status === 200) {
                if (response.success === true) {
                    toastr.success("Logout successful!", "Success");
                    setTimeout(function() {
                        window.location.href = "login.php"; 
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