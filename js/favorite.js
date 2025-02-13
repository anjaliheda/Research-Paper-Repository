$(document).ready(function() {
  // Initial load of favorites
  loadFavorites();

  function loadFavorites() {
      $("#allFavList").html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
      
      $.ajax({
          url: 'request.php',
          type: 'POST',
          data: {
              action: 'getFavorites'
          },
          dataType: 'json',
          success: function(response) {
              if (response.success && response.data && response.data.length > 0) {
                  appendPaperCards(response.data);
              } else {
                  $('#allFavList').html(`
                      <div class="text-center text-muted">
                          <i class="fas fa-heart fa-3x mb-3"></i>
                          <p>${response.message || 'No favorite papers found.'}</p>
                      </div>
                  `);
              }
          },
          error: function(xhr, status, error) {
              console.error('AJAX Error:', error);
              if (xhr.status === 401) {
                  window.location.href = 'login.php';
              } else {
                  $('#allFavList').html(`
                      <div class="text-center text-danger">
                          <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                          <p>Failed to load favorites. Please refresh the page.</p>
                      </div>
                  `);
                  toastr.error('Error loading favorites. Please try again.');
              }
          }
      });
  }

  function appendPaperCards(papers) {
      const container = $("#allFavList");
      container.empty();

      papers.forEach((paper) => {
          const cardHTML = `
              <div class="card paper-card" id="paper-${paper.paper_id}">
                  <button class="favorite-btn" 
                          data-paper-id="${paper.paper_id}"
                          title="Remove from favorites">
                      <i class="fas fa-heart"></i>
                  </button>
                  <div class="card-body">
                      <h5 class="card-title">${escapeHtml(paper.paper_title)}</h5>
                      <p class="card-text">By ${escapeHtml(paper.author_name)}</p>
                      <div class="card-footer">
                          <a href="${escapeHtml(paper.pdf_link)}" 
                             class="btn btn-primary" 
                             target="_blank"
                             rel="noopener noreferrer">
                              <i class="fas fa-file-pdf me-2"></i>View PDF
                          </a>
                      </div>
                  </div>
              </div>
          `;
          container.append(cardHTML);
      });
  }

  // Handle remove favorite
  $(document).on('click', '.favorite-btn', function(e) {
      e.preventDefault();
      const btn = $(this);
      const paperId = btn.data('paper-id');
      const paperCard = $(`#paper-${paperId}`);
      
      if (confirm('Are you sure you want to remove this paper from favorites?')) {
          btn.prop('disabled', true);
          
          $.ajax({
              url: 'request.php',
              type: 'POST',
              data: { 
                  action: 'removeFavorite',
                  paper_id: paperId
              },
              dataType: 'json',
              success: function(response) {
                  if (response.success) {
                      paperCard.fadeOut(300, function() {
                          $(this).remove();
                          if ($('.paper-card').length === 0) {
                              loadFavorites(); // Reload to show empty state
                          }
                      });
                      toastr.success('Paper removed from favorites');
                  } else {
                      toastr.error(response.message || 'Failed to remove from favorites');
                      btn.prop('disabled', false);
                  }
              },
              error: function(xhr) {
                  if (xhr.status === 401) {
                      window.location.href = 'login.php';
                  } else {
                      toastr.error('Failed to remove from favorites. Please try again.');
                  }
                  btn.prop('disabled', false);
              }
          });
      }
  });

  // Helper function to escape HTML and prevent XSS
  function escapeHtml(unsafe) {
      return unsafe
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
  }
});