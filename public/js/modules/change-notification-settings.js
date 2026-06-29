jQuery(document).ready(function($) {
    const form = $('#flexcore-notification-form');
    const messageBox = $('#notification-message');
    const submitBtn = form.find('button[type="submit"]');

    if (form.length) {
        form.find('input[type="radio"]').on('change', function() {
            submitBtn.prop('disabled', false);
        });

        form.on('submit', function(e) {
            e.preventDefault();

            messageBox.removeClass('success error').hide();
            submitBtn.prop('disabled', true);

            const ajaxData = {
                action: 'flexcore_notification_settings',
                nonce: $('#change_notification_settings_nonce').val(),
                smsNotifications: form.find('input[name="sms_notification"]:checked').val() === 'yes',
                emailNotifications: form.find('input[name="email_notification"]:checked').val() === 'yes'
            };

            $.post(flexcoreServerAjax.ajaxUrl, ajaxData, function(response) {
                if (response.success) {
                    messageBox
                        .addClass('success')
                        .text(response.data?.message || 'Notification settings updated successfully.')
                        .show();
                } else {
                    messageBox
                        .addClass('error')
                        .text(response.data?.message || 'Failed to update notification settings.')
                        .show();
                }
            }).fail(function() {
                messageBox
                    .addClass('error')
                    .text(flexcoreServerAjax.i18n.errorOccurred || 'An error occurred. Please try again.')
                    .show();
            }).always(function() {
                submitBtn.prop('disabled', false);
            });
        });
    }
});
