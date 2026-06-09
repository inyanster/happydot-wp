/**
 * Login form functionality
 */
(function($) {
    'use strict';

    const Login = {
        init: function() {
            $('#flexcore-login-form').on('submit', this.handleSubmit);
        },

       handleSubmit: function(e) {
    e.preventDefault();
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const messageDiv = $('#login-message');

    // Clear previous errors
    form.find(".field-error").text("").hide();
    form.find("input").removeClass("has-error");

    const email = $('#email').val().trim().toLowerCase();
    // console.log("Email"+email);
    const password = $('#password').val().trim();
    let isValid = true;

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        $('#email').addClass('has-error');
        $('#error-email').text("Email is required.").show();
        isValid = false;
    } else if (!emailPattern.test(email)) {
        $('#email').addClass('has-error');
        $('#error-email').text("Please enter a valid email address.").show();
        isValid = false;
    }

    // Password validation
    if (!password) {
        $('#password').addClass('has-error');
        $('#error-password').text("Password is required.").show();
        isValid = false;
    }

    // Stop submission if invalid
    if (!isValid) {
        return;
    }

    submitBtn.prop('disabled', true).addClass('loading');
    messageDiv.hide();

    $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: 'POST',
        data: {
            action: 'flexcore_login',
            email: email,
            password: password,
            nonce: $('#flexcore_nonce').val()
        },
        success: function(response) {
            if (response.success) {
                messageDiv.removeClass('error').addClass('success')
                    .html(response.data.message)
                    .show();
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                }
            } else {
                messageDiv.removeClass('success').addClass('error')
                    .html(response.data.message || flexcoreServerAjax.i18n.loginFailed)
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
        Login.init();
    });

})(jQuery);
