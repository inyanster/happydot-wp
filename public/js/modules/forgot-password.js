/**
 * Forgot password form functionality
 */
(function ($) {
  "use strict";

  const ForgotPassword = {
    init: function () {
      $("#flexcore-forgot-password-form").on("submit", this.handleSubmit);
    },

    handleSubmit: function (e) {
      e.preventDefault();
      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      const messageDiv = $(".flexcore-message");
      const email = $("#email").val().trim().toLowerCase();
      const emailPattern =  /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!email) {
        $("#email").addClass("has-error");
        messageDiv
          .removeClass("success")
          .addClass("error")
          .html("Email is required.")
          .show();
        return;
      }
      if (!emailPattern.test(email) || email.includes("..")) {
        $("#email").addClass("has-error");
        console.error(messageDiv);
        messageDiv
          .removeClass("success")
          .addClass("error")
          .html("Please enter a valid email address.")
          .show();
        return;
      }
      $("#email").removeClass("has-error");
      messageDiv.removeClass("success error").html("").hide();

      submitBtn.prop("disabled", true).addClass("loading");
      messageDiv.hide();

      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_forgot_password",
          email: email,
          nonce: $("#flexcore_nonce").val(),
        },
        success: function (response) {
          if (response.success) {
            messageDiv
              .removeClass("error")
              .addClass("success")
              .html(response.data.message)
              .show();
            // Redirect to reset password page with email parameter
            setTimeout(function () {
              window.location.href =
                flexcoreServerAjax.resetPasswordUrl +
                "?email=" +
                encodeURIComponent(email);
            }, 5000);
          } else {
            messageDiv
              .removeClass("success")
              .addClass("error")
              .html(
                response.data.message || flexcoreServerAjax.i18n.resetLinkFailed
              )
              .show();
          }
        },
        error: function () {
          messageDiv
            .removeClass("success")
            .addClass("error")
            .html(flexcoreServerAjax.i18n.errorOccurred)
            .show();
        },
        complete: function () {
          submitBtn.prop("disabled", false).removeClass("loading");
        },
      });
    },
  };

  // Initialize when document is ready
  $(document).ready(function () {
    ForgotPassword.init();
  });
})(jQuery);
