(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */


    $(document).ready(function () {
        // function get_status_name(post_status) {
        //     switch (post_status) {
        //         case 'wallet_generated':
        //             return 'Wallet generated';
        //         case 'contract_transaction_sent':
        //             return 'Contract transaction sent';
        //         default:
        //             return post_status
        //     }
        // }
        //
        // const post_type = $("#post_type").val()
        // const post_status = $("#original_post_status").val()
        // const formatted_post_status = get_status_name(post_status)
        //
        // if (post_type === 'rdm-quote' && post_status === 'wallet_generated') {
        //     $('#title').prop('disabled', true);
        // }
        //
        //
        // $("#post_status").append(`<option value="${post_status}" selected>${formatted_post_status}</option>`);
        // $("#post-status-display").append(formatted_post_status);
        // $(".edit-post-status").css('display', 'none');

    });
})(jQuery);
