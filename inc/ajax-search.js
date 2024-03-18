jQuery(document).ready(function ($) {
    // Function to handle product selection
    function handleProductSelection(productTitle) {
        // You can perform any additional actions here, such as adding the selected product to a list
        // For demonstration purposes, let's just log the selected product title
        console.log('Selected product: ' + productTitle);
    }

    // Event listener for keyup event on search input
    $('#bought_together_search').keyup(function () {
        var searchTerm = $(this).val();

        // Check if the search term has 3 or more characters
        if (searchTerm.length >= 3) {
            // Trigger the search automatically
            searchProducts(searchTerm);
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
});
