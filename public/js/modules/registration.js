(function ($) {
  "use strict";
  const Registration = {
    init: function () {
      const form = $("#flexcore-register-form");

      form.on("submit", this.handleSubmit);

      $("#name").on("input blur", function () {
        Registration.validateName();
      });

      $("#email").on("input blur", function () {
        Registration.validateEmail();
      });

      $("#confirm_email").on("input blur", function () {
        Registration.validateConfirmEmail();
      });

      $("#password").on("input blur", function () {
        Registration.validatePassword();
      });

      $("#confirm_password").on("input blur", function () {
        Registration.validateConfirmPassword();
      });

      $("#register_source").on("change", function () {
        Registration.validateRegisterSource();
      });

      $("#consent").on("change", function () {
        Registration.validateConsent();
      });
    },

    validateName: function () {
      const name = $("#name").val().trim();
      const nameParts = name.split(/\s+/).filter(word => word.length >= 2);

      if (!name) {
        $("#name").addClass("has-error").removeClass("is-valid");
        $("#error-name").text("Please enter your fullname").show();
        return false;
      } else if (nameParts.length < 2) {
        $("#name").addClass("has-error").removeClass("is-valid");
        $("#error-name").text("Full name must contain at least two words and each word must have at least two characters.").show();
        return false;
      } else {
        $("#name").removeClass("has-error");
        $("#error-name").hide();
        return true;
      }
    },

    validateEmail: function () {
      const email = $("#email").val().trim();
      const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!email) {
        $("#email").addClass("has-error").removeClass("is-valid");
        $("#error-email").text("Please enter email").show();
        return false;
      } else if (!emailPattern.test(email) || email.includes('..')) {
        $("#email").addClass("has-error").removeClass("is-valid");
        $("#error-email").text("Please enter valid email address").show();
        return false;
      } else {
        $("#email").removeClass("has-error");
        $("#error-email").hide();
        return true;
      }
    },

    validateConfirmEmail: function () {
      const email = $("#email").val().trim();
      const confirmEmail = $("#confirm_email").val().trim();
    const emailPattern =  /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!confirmEmail) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email").text("Please confirm your email").show();
        return false;
      } else if (!emailPattern.test(confirmEmail)) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email").text("Please enter a valid email address.").show();
        return false;
      } else if (email !== confirmEmail) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email").text("Please enter the same value again.").show();
        return false;
      } else {
        $("#confirm_email").removeClass("has-error");
        $("#error-confirm_email").hide();
        return true;
      }
    },

    validatePassword: function () {
      const password = $("#password").val();
      const passwordErrors = [];

      if (!password) {
        $("#password").addClass("has-error").removeClass("is-valid");
        $("#error-password").text("Please enter your password").show();
        return false;
      }

      if (password.length < 12 || password.length > 15) passwordErrors.push("between 12 and 15 characters");
      if (!/[A-Z]/.test(password)) passwordErrors.push("an uppercase letter");
      if (!/[a-z]/.test(password)) passwordErrors.push("a lowercase letter");
      if (!/[0-9]/.test(password)) passwordErrors.push("a number");
      if (!/[^A-Za-z0-9]/.test(password)) passwordErrors.push("a special character");

      if (passwordErrors.length > 0) {
        $("#password").addClass("has-error").removeClass("is-valid");
        $("#error-password").text("Password should match all criteria.").show();
        return false;
      } else {
        $("#password").removeClass("has-error");
        $("#error-password").hide();
        return true;
      }
    },

    validateConfirmPassword: function () {
      const password = $("#password").val();
      const confirmPassword = $("#confirm_password").val();

      if (!confirmPassword) {
        $("#confirm_password").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_password").text("Please confirm your password").show();
        return false;
      } else if (password !== confirmPassword) {
        $("#confirm_password").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_password").text("Please enter the same value again.").show();
        return false;
      } else {
        $("#confirm_password").removeClass("has-error");
        $("#error-confirm_password").hide();
        return true;
      }
    },

    validateRegisterSource: function () {
      const registerSource = $("#register_source").val();
      if (!registerSource || registerSource.trim() === "") {
        $("#register_source").addClass("has-error").removeClass("is-valid");
        $("#error-register_source").text("Please select an option").show();
        return false;
      } else {
        $("#register_source").removeClass("has-error");
        $("#error-register_source").hide();
        return true;
      }
    },

    validateConsent: function () {
      if (!$("#consent").is(":checked")) {
        $("#error-consent").text("Please confirm the information").show();
        return false;
      } else {
        $("#error-consent").hide();
        return true;
      }
    },

    validateFields: function () {
      let valid = true;
      if (!this.validateName()) valid = false;
      if (!this.validateEmail()) valid = false;
      if (!this.validateConfirmEmail()) valid = false;
      if (!this.validatePassword()) valid = false;
      if (!this.validateConfirmPassword()) valid = false;
      if (!this.validateRegisterSource()) valid = false;
      if (!this.validateConsent()) valid = false;
      return valid;
    },

    handleSubmit: function (e) {
      e.preventDefault();
      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      const messageDiv = $("#register-message");

      if (!Registration.validateFields()) {
        return;
      }

      submitBtn.prop("disabled", true).addClass("loading");
      messageDiv.hide();

      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_register",
          register_nonce: $("#register_nonce").val(),
          email: $("#email").val(),
          name: $("#name").val(),
          password: $("#password").val(),
          referral_code: $("#referral_code").val() || "",
          utm_string: $("#utm_string").val() || "",
          campaign_id: $("#campaign_id").val() || "",
          register_source: $("#register_source").val() || "",
        },
        success: function (response) {
          if (response.success) {
            messageDiv
              .removeClass("error")
              .addClass("success")
              .html(response.data.message)
              .show();
            if (response.data.redirect) {
              window.location.href = '/lifestyle-survey/';
            }
          } else {
            messageDiv
              .removeClass("success")
              .addClass("error")
              .html(response.data.message || flexcoreServerAjax.i18n.registrationFailed)
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

  $(document).ready(function () {
    Registration.init();
  });
})(jQuery);
