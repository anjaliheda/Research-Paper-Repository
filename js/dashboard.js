//for search and favourite function in the dashboard

$(document).ready(function () {
    $('#searchDropdown').select2({
        placeholder: 'Search Papers, Authors, or Keywords...',
        minimumInputLength: 0,
        ajax: {
            url: 'searchPapers.php',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('#searchDropdown').on('select2:select', function (e) {
        var selectedData = e.params.data;
        if (selectedData.url) {
            window.open(selectedData.url, '_blank');
        }
    });

    // Updated favorite button click handler
    $(document).on('click', '.favorite-btn', function (e) {
        e.preventDefault();
        const btn = $(this);
        const paperId = btn.data('paper-id');
        const icon = btn.find('i');
        const isFavorited = icon.hasClass('fas');
        
        btn.prop('disabled', true);
        
        $.ajax({
            url: 'request.php',
            type: 'POST',
            data: {
                action: 'addFavorite',
                paper_id: paperId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Toggle heart icon
                    if (isFavorited) {
                        icon.removeClass('fas').addClass('far');
                        toastr.success('Removed from favorites!');
                    } else {
                        icon.removeClass('far').addClass('fas');
                        toastr.success('Added to favorites!');
                    }
                } else {
                    toastr.error(response.message || 'Error updating favorites');
                }
                btn.prop('disabled', false);
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    toastr.error('Please log in to add favorites');
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    toastr.error('Failed to update favorites. Please try again.');
                }
                btn.prop('disabled', false);
            }
        });
    });
});
