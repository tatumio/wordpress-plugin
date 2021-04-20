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
        const post_type = $("#post_type").val()
        const status = $("#api_key_status").text()

        if (post_type === 'api_key') {
            $("#misc-publishing-actions").css('display', 'none');
            $("#delete-action").css('display', 'none');
            $("#minor-publishing-actions").css('display', 'none');
            $("#publishing-action").css('text-align', 'center');
            $("#publishing-action").css('float', 'unset');

            if (['wallet_generated', 'contract_transaction_sent', 'contract_address_obtained'].includes(status)) {
                $('#title').prop('disabled', true);
            }
        }
    });
})(jQuery);
