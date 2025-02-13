$(document).ready(function() {
    $("#addUserModal").submit(function(event) {
        event.preventDefault();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@rvce\.edu\.in$/;
        const email = $("#email").val().trim(); 
        if (!emailPattern.test(email)) {
            toastr.warning("Only emails ending with @rvce.edu.in are allowed", "Invalid Email");
            return;
        }
        var formData = new FormData();
    
        formData.append('email', email);
        formData.append('password', $("#password").val());
        formData.append('name', $("#name").val());
        formData.append('role', $("#role").val());
        formData.append('action', "createUser");
    
        var fileInput = $("#profileImage")[0]; 
        if (fileInput.files.length > 0) {
            formData.append('profileImage', fileInput.files[0]);
        }
    
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
    

    $("#editUserModal").submit(function(event) {

        event.preventDefault();
        const email = $("#viewUserEmail").val().trim(); 
        const emailPattern = /^[a-zA-Z0-9._%+-]+@rvce\.edu\.in$/;
        if (!emailPattern.test(email)) {
            toastr.warning("Only emails ending with @rvce.edu.in are allowed", "Invalid Email");
            return;
        }
        var formData = new FormData();
    
        formData.append('id', $("#viewUserId").val());
        formData.append('email', email);
        formData.append('password', $("#viewUserPassword").val());
        formData.append('name', $("#viewUserName").val());
        formData.append('role', $("#viewUserRole").val());
        formData.append('action', "updateUser");
    
        var fileInput = $("#viewUserProfileImage")[0];  
        if (fileInput && fileInput.files.length > 0) {
            formData.append('profileImage', fileInput.files[0]);
        }
    
        $.ajax({
            type: "POST",
            url: "request.php",  
            data: formData,
            processData: false,  
            contentType: false, 
            success: function(response, textStatus, jqXHR) {
                console.log(response); 
                if (jqXHR.status === 200) {
                    if (response.success != false) {
                        toastr.success(response.message, "Success");
                        setTimeout(function() {
                            window.location.reload(); 
                        }, 1000); 
                    } else {
                        toastr.warning(response.message, response);
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
    

    $.ajax({
        url: 'request.php',
        type: 'POST',
        data: {
            action: 'getUsers',
            
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
               
               appendUserCards(response.data);
                

            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('An error occurred while fetching user details.');
        }
    });
  });


  $(document).ready(function () {
    $(document).on('click', '.view-profile-btn', function () {
        const userId = $(this).data('id'); 

        $('#viewUserName').val('');
        $('#viewUserEmail').val('');
        $('#viewUserRole').val('');
        $('#viewUserPassword').val('');
        $('#viewUserId').val('');

        $.ajax({
            url: 'request.php', 
            type: 'POST',
            data: {
                action: 'getUser',
                user_id: userId
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#viewUserName').val(response.data.name);
                    $('#viewUserEmail').val(response.data.email);
                    $('#viewUserRole').val(response.data.role_id);
                    $('#viewUserId').val(response.data.user_id);

                    

                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while fetching user details.');
            }
        });

        $('#viewProfileModal').modal('show');
    });
});

const roleMapping = {
    1: "Admin",
    2: "Author",
    3: "Student",
  };
  
  function appendUserCards(users) {
    const container = $("#allUserList");
    container.empty();

    users.forEach((user) => {
        const roleClass = {
            1: 'role-admin',
            2: 'role-author',
            3: 'role-student'
        }[user.role_id] || '';

        const roleName = {
            1: 'Admin',
            2: 'Author',
            3: 'Student'
        }[user.role_id] || 'Unknown';

        const profileImage = user.profile_image ? user.profile_image : 'img/profile.jpg';

        const cardHTML = `
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="user-card card position-relative">
                    <div class="btn-group-user-actions">
                        <button class="btn-action btn-edit view-profile-btn" data-id="${user.user_id}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete delete-user-btn" data-id="${user.user_id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-header text-center">
                        <img src="${profileImage}" class="user-image mb-3" alt="${user.name}">
                        <h5 class="card-title mb-1">${user.name}</h5>
                        <span class="user-role ${roleClass}">${roleName}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <p class="text-muted mb-2">
                                <i class="fas fa-envelope me-2"></i>${user.email}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(cardHTML);
    });
}

  $(document).on("click", ".delete-user-btn", function(event) {
    event.preventDefault();
    var userId = $(this).data("id"); 

    if (confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            type: "POST",
            url: "request.php",
            data: { action: "deleteUser", id: userId },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    toastr.success("User deleted successfully!", "Success");
                    setTimeout(function() {
                        window.location.reload(); 
                    }, 1000);
                } else {
                    toastr.warning(response.message, "Something went wrong");
                }
            },
            error: function() {
                toastr.error("Failed to delete the user. Please try again.", "Error");
            }
        });
    }
});

  
 