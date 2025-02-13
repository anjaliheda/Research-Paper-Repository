$(document).ready(function() {
    $("#addPaperModal").submit(function(event) {
        event.preventDefault();
    
        var formData = new FormData();
    
        formData.append('title', $("#title").val());
        formData.append('authors', $("#authors").val());
        formData.append('keywords', $("#keywords").val());
        formData.append('publication_info', $("#publication_info").val());
        formData.append('pdf_link', $("#pdf_link").val());
        formData.append('featured', $("#featured").val());
        formData.append('action', "createPaper");
    
    
    
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
                        toastr.success("Paper Created successfully!", "Success");
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
    
    $("#editPaperModal").submit(function(event) {
        event.preventDefault();
    
        var formData = new FormData();
    
        formData.append('paper_id', $("#viewPaperId").val());
        formData.append('title', $("#viewPaperTitle").val());
        formData.append('authors', $("#viewPaperAuthors").val());
        formData.append('keywords', $("#viewPaperKeywords").val());
        formData.append('publication_info', $("#viewPaperPublicationInfo").val());
        formData.append('pdf_link', $("#viewPaperPdfLink").val());
        formData.append('featured', $("#viewPaperFeatured").val());
        formData.append('action', "updatePaper");
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
                action: 'getPapers',
                
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                   console.log(response.data);
                   
                   appendPaperCards(response.data);
                    
    
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while fetching Paper details.');
            }
        });
      });
    
    
      $(document).ready(function () {
        $(document).on('click', '.view-profile-btn', function () {
            const PaperId = $(this).data('id'); 
            $('#viewPaperTitle').val('');
            $('#viewPaperEmail').val('');
            $('#viewPaperRole').val('');
            $('#viewPaperPassword').val('');
            $('#viewPaperId').val('');
            $('#viewPaperAuthors').val('');
            $('#viewPaperKeywords').val('');
            $('#viewPaperPublicationInfo').val('');
            $('#viewPaperPdfLink').val('');
            $('#viewPaperFeatured').val('');
    
            $.ajax({
                url: 'request.php',
                type: 'POST',
                data: {
                    action: 'getPaper',
                    paper_id: PaperId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#viewPaperTitle').val(response.data.title);
                        $('#viewPaperEmail').val(response.data.email);
                        $('#viewPaperRole').val(response.data.role_id);
                        $('#viewPaperId').val(response.data.paper_id);
                        $('#viewPaperAuthors').val(response.data.authors); 
                        $('#viewPaperKeywords').val(response.data.keywords); 
                        $('#viewPaperPublicationInfo').val(response.data.publication_info); 
                        $('#viewPaperPdfLink').val(response.data.pdf_link); 
                        $('#viewPaperFeatured').val(response.data.featured); 
    
                        
    
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching Paper details.');
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
      
      function appendPaperCards(Papers) {
        const container = $("#allPapersList");
        container.empty();
    
        Papers.forEach((Paper) => {
            const cardHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="editPaper(${Paper.paper_id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-Paper-btn" data-id="${Paper.paper_id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <h6 class="card-title">${Paper.title}</h6>
                        <p class="author-text">Author: ${Paper.author_name}</p>
                        <a href="#" class="btn btn-primary btn-sm view-paper-btn" data-id="${Paper.paper_id}">
                            <i class="fas fa-paper -2"> </i>
                             View Paper
                        </a>
                    </div>
                </div>
            `;
            container.append(cardHTML);
        });
    }
      // Add this function to handle paper editing
      function editPaper(paperId) {
        // Reset form values
        $('#viewPaperTitle').val('');
        $('#viewPaperAuthors').val('');
        $('#viewPaperKeywords').val('');
        $('#viewPaperPublicationInfo').val('');
        $('#viewPaperPdfLink').val('');
        $('#viewPaperFeatured').val('');
        $('#viewPaperId').val('');
      
        // Fetch paper details
        $.ajax({
          url: 'request.php',
          type: 'POST',
          data: {
            action: 'getPaper',
            paper_id: paperId
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $('#viewPaperTitle').val(response.data.title);
              $('#viewPaperAuthors').val(response.data.authors);
              $('#viewPaperKeywords').val(response.data.keywords);
              $('#viewPaperPublicationInfo').val(response.data.publication_info);
              $('#viewPaperPdfLink').val(response.data.pdf_link);
              $('#viewPaperFeatured').val(response.data.featured);
              $('#viewPaperId').val(response.data.paper_id);
              $('#viewProfileModal').modal('show');
            }
          },
          error: function() {
            toastr.error('Failed to fetch paper details');
          }
        });
      }
    
      $(document).on("click", ".delete-Paper-btn", function(event) {
        event.preventDefault();
        var PaperId = $(this).data("id"); 
    
        if (confirm("Are you sure you want to delete this Paper?")) {
            $.ajax({
                type: "POST",
                url: "request.php",
                data: { action: "deletePaper", paper_id: PaperId },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        toastr.success("Paper deleted successfully!", "Success");
                        setTimeout(function() {
                            window.location.reload(); 
                        }, 1000);
                    } else {
                        toastr.warning(response.message, "Something went wrong");
                    }
                },
                error: function() {
                    toastr.error("Failed to delete the Paper. Please try again.", "Error");
                }
            });
        }
    });
    
      
     