jQuery(document).ready(function($) {
    $('#bought_together_search_button').click(function(e) {
        e.preventDefault();

        var searchTerm = $('#bought_together_search').val();

        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'search_products',
                search_term: searchTerm,
                security: ajax_params.search_nonce // You need to add a security nonce here
            },
            success: function(response) {
                // Update the search results container with the response
                $('#bought_together_search_results').html(response);
            },
            error: function(xhr, status, error) {
                console.error(status + ': ' + error);
            }
        });
    });
});
