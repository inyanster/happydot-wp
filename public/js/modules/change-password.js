jQuery(document).ready(function($) {
    const form = $('#change-password-form');
    const messageBox = $('#change-password-message');

    const $oldPassword = $('#old_password');
    const $newPassword = $('#new_password');
    const $confirmPassword = $('#confirm_password');

    const $requirements = {
        length: $('#length'),
        uppercase: $('#uppercase'),
        lowercase: $('#lowercase'),
        number: $('#number'),
        special: $('#special')
    };

    const passwordRequirements = {
        length: (pw) => pw.length >= 12 && pw.length <= 15,
        uppercase: (pw) => /[A-Z]/.test(pw),
        lowercase: (pw) => /[a-z]/.test(pw),
        number: (pw) => /\d/.test(pw),
        special: (pw) => /[!@#$%^&*()]/.test(pw),
    };

    function validatePasswordRequirements(password) {
        let allValid = true;

        for (const key in passwordRequirements) {
            const isValid = passwordRequirements[key](password);
            const $item = $requirements[key];

            if (isValid) {
                $item.addClass('matched');
            } else {
                $item.removeClass('matched');
                allValid = false;
            }
        }

        $('#h6').toggle(allValid);
        return allValid;
    }

    function validateField($field, isValid) {
        if (isValid) {
            $field.removeClass('has-error').addClass('is-valid');
        } else {
            $field.removeClass('is-valid').addClass('has-error');
        }
    }

    // Live validation for new password
    $newPassword.on('input', function() {
        const val = $(this).val();
        validatePasswordRequirements(val);
        validateField($newPassword, validatePasswordRequirements(val));
    });

    // Confirm password match
    $confirmPassword.on('input', function () {
    const confirmVal = $(this).val();
    const newVal = $newPassword.val();

    const isNewValid = validatePasswordRequirements(newVal);
    const isConfirmValid = confirmVal === newVal && isNewValid;

    validateField($confirmPassword, isConfirmValid);
});

    // Old password field simple non-empty validation
    $oldPassword.on('input', function() {
        validateField($oldPassword, $(this).val().trim().length > 0);
    });

    // Submit form
    if (form.length) {
        form.on('submit', function(e) {
            e.preventDefault();
            messageBox.removeClass('success error').hide();

            const oldPassword = $oldPassword.val().trim();
            const newPassword = $newPassword.val();
            const confirmPassword = $confirmPassword.val();

            const isOldValid = oldPassword.length > 0;
            const isNewValid = validatePasswordRequirements(newPassword);
            const isConfirmValid = newPassword === confirmPassword  && isNewValid;

            validateField($oldPassword, isOldValid);
            validateField($newPassword, isNewValid);
            validateField($confirmPassword, isConfirmValid);

            if (!(isOldValid && isNewValid && isConfirmValid)) {
                messageBox.addClass('error')
                    .text(flexcoreServerAjax.i18n.invalidFields || 'Please correct the highlighted fields .')
                    .show();
                return;
            }

            // Disable submit
            form.find('button[type="submit"]').prop('disabled', true);

            const data = {
                action: 'flexcore_change_password',
                nonce: $('#change_password_nonce').val(),
                old_password: oldPassword,
                new_password: newPassword
            };

            $.post(flexcoreServerAjax.ajaxUrl, data, function(response) {
                if (response.success) {
                    messageBox
                        .removeClass('error')
                        .addClass('success')
                        .text(response.data.message)
                        .show();

                    form[0].reset();
                    $('.hd-formfild').removeClass('has-error is-valid');
                    $('.requiments li').removeClass('matched');
                    $('#h6').hide();
                } else {
                    messageBox
                        .removeClass('success')
                        .addClass('error')
                        .text(response.data.message)
                        .show();
                }
            }).fail(function() {
                messageBox
                    .removeClass('success')
                    .addClass('error')
                    .text(flexcoreServerAjax.i18n.errorOccurred || 'An error occurred. Please try again.')
                    .show();
            }).always(function() {
                form.find('button[type="submit"]').prop('disabled', false);
            });
        });
    }
});
