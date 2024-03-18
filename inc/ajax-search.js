jQuery(document).ready(function ($) {
    // Function to handle product selection
    function handleProductSelection(productTitle) {
        // You can perform any additional actions here, such as adding the selected product to a list
        // For demonstration purposes, let's just log the selected product title
        console.log('Selected product: ' + productTitle);
    }

    // Variables for debouncing search input
    var searchTimer;
    var debounceDelay = 300; // milliseconds

    // Event listener for keyup event on search input
    $('#bought_together_search').on('input', function () {
        var searchTerm = $(this).val().trim();

        // Check if the search term has reached the minimum length
        if (searchTerm.length >= 1) {
            clearTimeout(searchTimer);
            // Debounce the search input to prevent frequent AJAX requests
            searchTimer = setTimeout(function () {
                searchProducts(searchTerm);
            }, debounceDelay);
        } else {
            // Clear the search results if the search term is less than 3 characters
            $('#bought_together_search_results').empty();
        }
    });

    // Function to perform product search
    function searchProducts(searchTerm) {
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'search_products',
                search_term: searchTerm,
                security: ajax_params.search_nonce // You need to add a security nonce here
            },
            beforeSend: function () {
                // Display a loading spinner or message while waiting for search results
                $('#bought_together_search_results').html('<p>Loading...</p>');
            },
            success: function (response) {
                $('#bought_together_search_results').html(response);
            },
            error: function (xhr, status, error) {
                console.error(status + ': ' + error);
            }
        });
    }

    // Event delegation for dynamically added elements (products)
    $('#bought_together_search_results').on('click', 'p', function () {
        var productTitle = $(this).text(); // Get the selected product title
        handleProductSelection(productTitle); // Handle the product selection
    });

    // Clear button functionality
    $('#clear_search').click(function () {
        $('#bought_together_search').val('').focus();
        $('#bought_together_search_results').empty();
    });
});
