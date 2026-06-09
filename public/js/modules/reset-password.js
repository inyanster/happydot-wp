/**
 * Reset password form functionality
 */
(function ($) {
    'use strict';

    const ResetPassword = {
        init: function () {
            $('#flexcore-reset-password-form').on('submit', this.handleSubmit);
            $('#password').on('input', this.checkPasswordStrength);
            this.setupOTPInput();
        },

        setupOTPInput: function () {
            const input = $('#otp');
            input.focus();

            // Allow only numeric input
            input.on('input', function () {
                const sanitized = $(this).val().replace(/\D/g, '');
                $(this).val(sanitized);
            });
        },

        handleSubmit: function (e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const messageDiv = $('#reset-password-message');

            const email = $('#email').val();
            const otp = $('#otp').val();
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            const nonce = $('#flexcore_nonce').val();
            const isValid = true;
            messageDiv.removeClass('success error').html('').hide();

            // === Basic Validation ===
            if (!email) {
                // $('#email').addClass('has-error');
                isValid = false;
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.emailRequired || 'Email is required.');
            }

            if (!otp) {
                $('#otp').addClass('has-error');
                $('#otp-error').text(flexcoreServerAjax.i18n.otpRequired || 'OTP is required.').show();
                isValid = false;
                
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.otpRequired || 'OTP is required.');
            }

            if (otp.length !== 6) {
                $('#otp').addClass('has-error');
                $('#otp-error').text(flexcoreServerAjax.i18n.invalidOTP || 'Invalid OTP. It must be 6 digits.').show();
                isValid = false;
                
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.invalidOTP || 'Invalid OTP. It must be 6 digits.');
            }
            else {
                $('#otp').removeClass('has-error');
                $('#otp-error').hide();
            }

            if (!password) {
                $('#otp').removeClass('has-error');
                $('#otp-error').hide();               
                
                $('#password').addClass('has-error');
                // $('#confirm_password').addClass('has-error');
                $('#password-error').text(flexcoreServerAjax.i18n.passwordRequired || 'Password is required.').show();
                isValid = false;
                
                // $('#confirm-password-error').text(flexcoreServerAjax.i18n.passwordRequired || 'Confirm password is required.').show();
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.passwordRequired || 'Password and confirm password are required.');
            }
            else {
                $('#password').removeClass('has-error');
                $('#password-error').hide();
                $('#confirm_password').removeClass('has-error');
                $('#confirm-password-error').hide();
            }
             if (password.length < 12 || password.length > 15) {
                $('#password').addClass('has-error');
                $('#confirm_password').addClass('has-error');
                $('#password-error').text(flexcoreServerAjax.i18n.passwordLength || 'Password must be between 12 and 15 characters.').show();
                $('#confirm-password-error').text(flexcoreServerAjax.i18n.passwordLength || 'Password must be between 12 and 15 characters.').show();
                isValid = false;
              
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.passwordLength || 'Password must be between 12 and 15 characters.');
            }
             else {
                $('#password').removeClass('has-error');
                $('#password-error').hide();
                $('#confirm_password').removeClass('has-error');
                $('#confirm-password-error').hide();
            }
            if(!confirmPassword) {
                $('#password').removeClass('has-error');
                $('#password-error').hide();
                
                $('#confirm_password').addClass('has-error');
                $('#confirm-password-error').text(flexcoreServerAjax.i18n.confirmPasswordRequired || 'Confirm password is required.').show();
                isValid = false;
             
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.confirmPasswordRequired || 'Confirm password is required.');
            }
            else {
                $('#password').removeClass('has-error');
                $('#password-error').hide();
                $('#confirm_password').removeClass('has-error');
                $('#confirm-password-error').hide();
            }
            if (password !== confirmPassword) {
                $('#password').addClass('has-error');
                $('#confirm_password').addClass('has-error');
                // $('#confirm_password').val('').focus();
                $('#confirm-password-error').text(flexcoreServerAjax.i18n.passwordsDoNotMatch || 'Passwords do not match.').show();
                isValid = false;
                
                // return ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.passwordsDoNotMatch || 'Passwords do not match.');
            }
            else {
                $('#password').removeClass('has-error');
                $('#confirm_password').removeClass('has-error');
                $('#password-error').hide();
                $('#confirm-password-error').hide();
                
            }
            
            // Optional: Uncomment to enforce strength requirements
            // if (!ResetPassword.isStrongEnough(password)) {
            //     return ResetPassword.showError(messageDiv, 'Password does not meet strength requirements.');
            // }
            if (!isValid) {
                return; // Stop submission if validation fails
            }
            // === Submit Form via AJAX ===
            submitBtn.prop('disabled', true).addClass('loading');

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_reset_password',
                    email: email,
                    otp: otp,
                    password: password,
                    nonce: nonce
                },
                success: function (response) {
                    if (response.success) {
                        form.hide();
                        messageDiv.removeClass('error').addClass('success').html(response.data.message).show();

                        setTimeout(() => {
                            window.location.href = flexcoreServerAjax.loginUrl;
                        }, 3000);
                    } else {
                        ResetPassword.showError(messageDiv, response.data.message || flexcoreServerAjax.i18n.resetFailed || 'Password reset failed. Please try again.');
                    }
                },
                error: function () {
                    ResetPassword.showError(messageDiv, flexcoreServerAjax.i18n.errorOccurred || 'An unexpected error occurred. Please try again later.');
                },
                complete: function () {
                    submitBtn.prop('disabled', false).removeClass('loading');
                }
            });
        },

        checkPasswordStrength: function () {
            const password = $('#password').val();
            const meter = $('.password-strength-meter');
            const text = $('.password-strength-text');

            // Reset meter and requirement indicators
            meter.removeClass('weak medium strong very-strong');
            $('#length, #uppercase, #lowercase, #number, #special').removeClass('matched');

            if (!password.length) {
                text.text('');
                $('#h6').hide();
                return;
            }

            let score = 0;

            if (password.length >= 12 && password.length <= 15) {
                $('#length').addClass('matched');
                score++;
            }
            if (/[A-Z]/.test(password)) {
                $('#uppercase').addClass('matched');
                score++;
            }
            if (/[a-z]/.test(password)) {
                $('#lowercase').addClass('matched');
                score++;
            }
            if (/\d/.test(password)) {
                $('#number').addClass('matched');
                score++;
            }
            if (/[!@#$%^&*()]/.test(password)) {
                $('#special').addClass('matched');
                score++;
            }

            $('#h6').toggle(score === 5);

            // Update strength meter text and class
            switch (score) {
                case 1:
                    meter.addClass('weak');
                    text.text(flexcoreServerAjax.i18n.weakPassword);
                    break;
                case 2:
                case 3:
                    meter.addClass('medium');
                    text.text(flexcoreServerAjax.i18n.mediumPassword);
                    break;
                case 4:
                    meter.addClass('strong');
                    text.text(flexcoreServerAjax.i18n.strongPassword);
                    break;
                case 5:
                    meter.addClass('very-strong');
                    text.text(flexcoreServerAjax.i18n.veryStrongPassword);
                    break;
                default:
                    text.text('');
            }
        },

        showError: function (element, message) {
            element.removeClass('success').addClass('error').html(message).show();
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        ResetPassword.init();
    });

})(jQuery);
