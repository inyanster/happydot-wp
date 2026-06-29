/**
 * Delete account form functionality
 */
(function($) {
    'use strict';

    const DeleteAccount = {
        init: function() {
            $('#flexcore-delete-account-form').on('submit', this.handleSubmit);
        },

        handleSubmit: function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const messageDiv = $('#delete-account-message');
            
            if (!$('#confirm_delete').is(':checked')) {
                messageDiv.removeClass('success').addClass('error')
                    .html(flexcoreServerAjax.i18n.pleaseConfirmDelete)
                    .show();
                return;
            }
            
            if (!confirm(flexcoreServerAjax.i18n.confirmDeleteAccount)) {
                return;
            }
            
            submitBtn.prop('disabled', true).addClass('loading');
            messageDiv.hide();

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_delete_account',
                    nonce: $('#flexcore_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        form.hide();
                        messageDiv.removeClass('error').addClass('success')
                            .html(response.data.message + ' <a href="' + flexcoreServerAjax.loginUrl + '">' + 
                                flexcoreServerAjax.i18n.returnToLogin + '</a>')
                            .show();
                    } else {
                        messageDiv.removeClass('success').addClass('error')
                            .html(response.data.message || flexcoreServerAjax.i18n.deleteFailed)
                            .show();
                    }
                },
                error: function() {
                    messageDiv.removeClass('success').addClass('error')
                        .html(flexcoreServerAjax.i18n.errorOccurred)
                        .show();
                },
                complete: function() {
                    submitBtn.prop('disabled', false).removeClass('loading');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        DeleteAccount.init();
    });

})(jQuery);
