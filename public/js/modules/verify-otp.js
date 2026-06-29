/**
 * OTP verification functionality
 */
(function($) {
    'use strict';

    const VerifyOTP = {
        init: function() {
            // Pre-fill email from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            if (email) {
                $('#email').val(decodeURIComponent(email));
            }

            $('#flexcore-verify-otp-form').on('submit', this.handleSubmit);
            
            // Auto-focus first OTP input and format input
            this.setupOTPInput();
        },

        setupOTPInput: function() {
            const input = $('#otp');
            input.focus();
            
            // Only allow numbers
            input.on('input', function(e) {
                const value = $(this).val().replace(/\D/g, '');
                $(this).val(value);
            });
        },

        handleSubmit: function(e) {
    e.preventDefault();

    const otpInput = $('#otp');
    const otp = otpInput.val().trim();
    const errorOtp = $('#error-otp');
    const messageDiv = $('#verify-otp-message');
    const form = $(this);
    const submitBtn = form.find('button[type="submit"], input[type="submit"]');

    // Reset previous messages
    errorOtp.text('').hide();
    messageDiv.hide();

    // Validate OTP
    if (otp === '') {
        errorOtp.text('OTP is required.').show();
        otpInput.focus();
          $('#otp').addClass('has-error');
        // otpInput.style.backgroundColor = '#ffaaaa'; // Highlight input
        return;
    }
    if (!/^\d{6}$/.test(otp)) {
        errorOtp.text('OTP should be of 6 digits.').show();
        $('#otp').addClass('has-error');
        otpInput.focus();
        return;
    }
    //  otpInput.classList.removeClass('has-error');
    submitBtn.prop('disabled', true).addClass('loading');

    $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: 'POST',
        data: {
            action: 'flexcore_verify_otp',
            email: $('#email').val(),
            otp: otp,
            nonce: $('#flexcore_nonce').val()
        },
        success: function(response) {
            if (response.success) {
                // Redirect if success
                if (response.data) {
                    setTimeout(function() {
                        window.location.href = '/my-account/';
                    }, 1500);
                }
            } else {
                messageDiv.removeClass('success').addClass('error')
                    .html(response.data.message || flexcoreServerAjax.i18n.verificationFailed)
                    .show();
                otpInput.focus();
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
        VerifyOTP.init();
    });

})(jQuery);
