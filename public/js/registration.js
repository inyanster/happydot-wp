/**
 * Registration form functionality
 */
(function($) {
    'use strict';

    const Registration = {
        init: function() {
            const form = $('#flexcore-register-form');
            form.on('submit', this.handleSubmit);
            
            // Add validation classes on blur
            form.find('input').on('blur', function() {
                if (this.value) {
                    $(this).addClass('is-valid').removeClass('has-error');
                }
            });

            // Password confirmation validation
            $('#confirm_password').on('input', this.validatePasswordMatch);
        },

        validatePasswordMatch: function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            
            if (confirmPassword && password !== confirmPassword) {
                $(this).addClass('has-error').removeClass('is-valid');
                return false;
            } else if (confirmPassword) {
                $(this).addClass('is-valid').removeClass('has-error');
                return true;
            }
        },

        validateFields: function() {
            const messageDiv = $('#register-message');
            const form = $('#flexcore-register-form');
            
            // Check required fields
            const requiredFields = ['email', 'name', 'password', 'confirm_password'];
            for (const field of requiredFields) {
                const value = $(`#${field}`).val();
                if (!value || value.trim() === '') {
                    messageDiv.removeClass('success').addClass('error')
                        .html(flexcoreServerAjax.i18n.fillRequired)
                        .show();
                    return false;
                }
            }

            // Validate email format
            const email = $('#email').val();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                messageDiv.removeClass('success').addClass('error')
                    .html(flexcoreServerAjax.i18n.invalidEmail)
                    .show();
                return false;
            }

            // Validate password match
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            if (password !== confirmPassword) {
                messageDiv.removeClass('success').addClass('error')
                    .html(flexcoreServerAjax.i18n.passwordsDoNotMatch)
                    .show();
                return false;
            }

            // Check password strength
            if (password.length < 8 || 
                !/[A-Z]/.test(password) || 
                !/[a-z]/.test(password) || 
                !/[0-9]/.test(password) || 
                !/[^A-Za-z0-9]/.test(password)) {
                messageDiv.removeClass('success').addClass('error')
                    .html(flexcoreServerAjax.i18n.passwordTooWeak)
                    .show();
                return false;
            }

            // Check consent
            if (!$('#consent').is(':checked')) {
                messageDiv.removeClass('success').addClass('error')
                    .html(flexcoreServerAjax.i18n.consentRequired)
                    .show();
                return false;
            }

            return true;
        },

        handleSubmit: function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const messageDiv = $('#register-message');
            
            // Validate fields before submission
            if (!Registration.validateFields()) {
                return;
            }
            
            submitBtn.prop('disabled', true).addClass('loading');
            messageDiv.hide();

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_register',
                    nonce: flexcoreServerAjax.registerNonce,
                    email: $('#email').val(),
                    name: $('#name').val(),
                    password: $('#password').val(), 
                    // referral_code: $('#referral_code').val() || '',
                    // utm_string: $('#utm_string').val() || '',
                    // campaign_id: $('#campaign_id').val() || '',
                    // register_source: $('#register_source').val() || '', 
                    },
                success: function(response) {
                    if (response.success) {
                        messageDiv.removeClass('error').addClass('success')
                            .html(response.data.message)
                            .show();
                        
                        if (response.data.redirect) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 1500);
                        }
                    } else {
                        messageDiv.removeClass('success').addClass('error')
                            .html(response.data.message || flexcoreServerAjax.i18n.registrationFailed)
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
        Registration.init();
    });

})(jQuery);
