(function ($) {
  "use strict";
  const MergedRegistration = {
    init: function () {
      // alert('Merged Registration Initialized');
      const form = $("#flexcore-merged-registration-form");

      // Autofill and referral code logic from register.js
      this.handleReferralAndUTM();

      // Field validation events from registration.js and profile.js
      $("#name").on("input blur", this.validateName);
      $("#email").on("input blur", this.validateEmail);
      $("#confirm_email").on("input blur", this.validateConfirmEmail);
      $("#password").on("input blur", this.validatePassword);
      $("#confirm_password").on("input blur", this.validateConfirmPassword);
      $("#register_source").on("change", this.validateRegisterSource);
      $("#consent").on("change", this.validateConsent);
      $("#dob").on("input change", this.validateDOB);
      $("#mobile").on("input change", this.validateMobile);
      $("#postal_code").on("input", this.validatePostalCode);
      $("#race").on("change", this.toggleOthersField);
      this.toggleOthersField();

      // Password requirements UI
      $("#password").on("input", this.updatePasswordRequirementsUI);

      form.on("submit", this.handleSubmit.bind(this));
    },

    handleReferralAndUTM: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const referralCode = urlParams.get("referral_code");
      const referralInput = document.getElementById("referral_code");
      const referralLabel = document.getElementById("referral_code_label");
      const sourceSelect = document.getElementById("register_source");
      const sourceFormGroup = document.getElementById("source-group");
      const utmStringInput = document.getElementById("utm_string");
      const campaignInput = document.getElementById("campaign_id");

      if (referralCode) {
        referralInput.value = referralCode;
        referralInput.style.display = "block";
        referralLabel.style.display = "block";
        referralInput.readOnly = true;
        sourceSelect.value = "Referred by a friend";
        sourceSelect.disabled = true;
        utmStringInput.value =
          "utm_source=email&utm_medium=referral&utm_campaign=raf-happydotter";
      }

      // let campaignId = urlParams.get("campaign_id") || "become-a-happydotter";
          const pathSegments = window.location.pathname.split('/').filter(Boolean);
const campaignId = pathSegments.length > 0 ? pathSegments[pathSegments.length - 1] : 'become-a-happydotter';
      campaignInput.value = campaignId;

      const utm_string_present =
        urlParams.has("fbclid") ||
        urlParams.has("utm_source") ||
        urlParams.has("utm_medium") ||
        urlParams.has("utm_campaign");
      const referral_code_absent = !urlParams.has("referral_code");
if (utm_string_present && referral_code_absent) {
  utmStringInput.value = window.location.search.substring(1);

  if (sourceSelect) {
    // Disable validation
    sourceSelect.required = false;
    sourceSelect.removeAttribute("required");
    sourceSelect.value = "";

    // Hide after paint
    setTimeout(() => {
      sourceFormGroup.style.display = "none";
    }, 0);
  }
} else {
        if (sourceSelect && campaignInput) {
          sourceSelect.addEventListener("change", function () {
            const howHeard = this.value.trim();
            let utmString = "";
            if (howHeard !== "") {
              sourceSelect.classList.remove("has-error");
              document.getElementById("error-register_source").style.display =
                "none";
            }
            switch (howHeard) {
              case "I saw it from a brochure":
                utmString =
                  "utm_source=brochure&utm_medium=offline&utm_campaign=manual_entry";
                referralInput.style.display = "none";
                referralLabel.style.display = "none";
                referralInput.required = false;
                break;
              case "Referred by a friend":
                utmString =
                  "utm_source=email&utm_medium=referral&utm_campaign=raf-happydotter";
                referralInput.style.display = "block";
                referralLabel.style.display = "block";
                referralInput.required = true;
                break;
              case "I heard about it from family/friends":
                utmString =
                  "utm_source=wom&utm_medium=organic&utm_campaign=wom-generic-signup";
                referralInput.style.display = "none";
                referralLabel.style.display = "none";
                referralInput.required = false;
                break;
              case "I saw it from the search engine results (e.g. Google/Bing)":
                utmString =
                  "utm_source=google&utm_medium=seo&utm_campaign=seo-generic-signup";
                referralInput.style.display = "none";
                referralLabel.style.display = "none";
                referralInput.required = false;
                break;
              default:
                referralInput.style.display = "none";
                referralLabel.style.display = "none";
                referralInput.required = false;
            }
            utmStringInput.value = utmString;
          });
        }
      }
    },

    toggleOthersField: function () {
      const raceValue = $("#race").val();
      const showOthers = $("#othersRaceGroup");
      if (raceValue && raceValue.toLowerCase() === "others") {
        showOthers.show();
        $("#others").prop("required", true);
      } else {
        showOthers.hide();
        $("#others").prop("required", false).val("");
      }
    },

    updatePasswordRequirementsUI: function () {
      const value = $("#password").val();
      const lengthReq = $("#flexcore_length");
      const uppercaseReq = $("#flexcore_uppercase");
      const lowercaseReq = $("#flexcore_lowercase");
      const numberReq = $("#flexcore_number");
      const specialReq = $("#flexcore_special");
      if (value.length >= 12 && value.length <= 15) {
        lengthReq.addClass("valid");
      } else {
        lengthReq.removeClass("valid");
      }
      if (/[A-Z]/.test(value)) {
        uppercaseReq.addClass("valid");
      } else {
        uppercaseReq.removeClass("valid");
      }
      if (/[a-z]/.test(value)) {
        lowercaseReq.addClass("valid");
      } else {
        lowercaseReq.removeClass("valid");
      }
      if (/\d/.test(value)) {
        numberReq.addClass("valid");
      } else {
        numberReq.removeClass("valid");
      }
      if (/[!@#$%^&*()]/.test(value)) {
        specialReq.addClass("valid");
      } else {
        specialReq.removeClass("valid");
      }
    },

    validateName: function () {
      const name = $("#name").val().trim();
      const nameParts = name.split(/\s+/).filter((word) => word.length >= 2);
      if (!name) {
        $("#name").addClass("has-error").removeClass("is-valid");
        $("#error-name").text("Please enter your fullname").show();
        return false;
      } else if (nameParts.length < 2) {
        $("#name").addClass("has-error").removeClass("is-valid");
        $("#error-name")
          .text(
            "Full name must contain at least two words and each word must have at least two characters."
          )
          .show();
        return false;
      } else {
        $("#name").removeClass("has-error");
        $("#error-name").hide();
        return true;
      }
    },

    validateEmail: function () {
      const email = $("#email").val().trim();
      const emailPattern =  /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!email) {
        $("#email").addClass("has-error").removeClass("is-valid");
        $("#error-email").text("Please enter email").show();
        return false;
      } else if (!emailPattern.test(email) || email.includes("..")) {
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
      const emailPattern =
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!confirmEmail) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email").text("Please confirm your email").show();
        return false;
      } else if (!emailPattern.test(confirmEmail)) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email")
          .text("Please enter a valid email address.")
          .show();
        return false;
      } else if (email !== confirmEmail) {
        $("#confirm_email").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_email")
          .text("Please enter the same value again.")
          .show();
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
      if (password.length < 12 || password.length > 15)
        passwordErrors.push("between 12 and 15 characters");
      if (!/[A-Z]/.test(password)) passwordErrors.push("an uppercase letter");
      if (!/[a-z]/.test(password)) passwordErrors.push("a lowercase letter");
      if (!/[0-9]/.test(password)) passwordErrors.push("a number");
      if (!/[^A-Za-z0-9]/.test(password))
        passwordErrors.push("a special character");
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
        $("#error-confirm_password")
          .text("Please confirm your password")
          .show();
        return false;
      } else if (password !== confirmPassword) {
        $("#confirm_password").addClass("has-error").removeClass("is-valid");
        $("#error-confirm_password")
          .text("Please enter the same value again.")
          .show();
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
        $("#consent").addClass("has-error");
        return false;
      } else {
        $("#error-consent").hide();
        $("#consent").removeClass("has-error");
        return true;
      }
    },
    // validateDOB: function () {
    //     const input = $("#dob");
    //     const value = input.val();
    //     const errorDiv = $(".dob-error");

    //     if (!value) return false;

    //     // Must be full dd/mm/yyyy (10 chars)
    //     if (!/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
    //         input.addClass("has-error").removeClass("is-valid");
    //         errorDiv.text("Enter date in dd/mm/yyyy format").show();
    //         return false;
    //     }

    //     const [day, month, year] = value.split("/").map(Number);
    //     const date = new Date(year, month - 1, day);

    //     if (
    //         date.getFullYear() !== year ||
    //         date.getMonth() + 1 !== month ||
    //         date.getDate() !== day
    //     ) {
    //         input.addClass("has-error").removeClass("is-valid");
    //         errorDiv.text("Invalid date").show();
    //         return false;
    //     }

    //     // Age check (≥15 years)
    //     const today = new Date();
    //     let age = today.getFullYear() - year;
    //     const m = today.getMonth() - (month - 1);
    //     if (m < 0 || (m === 0 && today.getDate() < day)) age--;

    //     if (age >= 15) {
    //         input.addClass("is-valid").removeClass("has-error");
    //         errorDiv.hide();
    //         return true;
    //     } else {
    //         input.addClass("has-error").removeClass("is-valid");
    //         errorDiv.text("Must be 15 years or older").show();
    //         return false;
    //     }
    // },

    validateDOB: function () {
      const input = $("#dob");
      let value = input.val().replace(/\D/g, "");
      const errorDiv = $(".field-error.dob-error"); // use the <div> for dynamic text

      // Auto format
      if (value.length > 2 && value.length <= 4) {
        value = value.slice(0, 2) + "/" + value.slice(2);
      } else if (value.length > 4) {
        value =
          value.slice(0, 2) + "/" + value.slice(2, 4) + "/" + value.slice(4, 8);
      }
      input.val(value);

      // 1. Format check
      if (!/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.text("Date must be in dd/mm/yyyy format.").show();
        return false;
      }

      const [day, month, year] = value.split("/").map(Number);
      const date = new Date(year, month - 1, day);

      // 2. Invalid date check (like 31/02/2020)
      if (
        date.getFullYear() !== year ||
        date.getMonth() + 1 !== month ||
        date.getDate() !== day
      ) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.text("Invalid date entered.").show();
        return false;
      }

      // 3. Age check
      const today = new Date();
      let age = today.getFullYear() - year;
      const m = today.getMonth() - (month - 1);
      if (m < 0 || (m === 0 && today.getDate() < day)) age--;

      if (age < 15) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.text("You must be at least 15 years old.").show();
        return false;
      }

      // ✅ Passed
      input.addClass("is-valid").removeClass("has-error");
      errorDiv.hide();
      return true;
    },

    // validateDOB: function () {
    // 	const input = $("#dob");
    // 	const value = input.val();
    // 	const errorDiv = $(".dob-error");
    // 	if (!value) return false;
    // 	if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
    // 		input.addClass("has-error").removeClass("is-valid");
    // 		errorDiv.show();
    // 		return false;
    // 	}
    // 	const [year, month, day] = value.split("-").map(Number);
    // 	const date = new Date(year, month - 1, day);
    // 	if (
    // 		date.getFullYear() !== year ||
    // 		date.getMonth() + 1 !== month ||
    // 		date.getDate() !== day
    // 	) {
    // 		input.addClass("has-error").removeClass("is-valid");
    // 		errorDiv.show();
    // 		return false;
    // 	}
    // 	const today = new Date();
    // 	let age = today.getFullYear() - date.getFullYear();
    // 	const m = today.getMonth() - date.getMonth();
    // 	if (m < 0 || (m === 0 && today.getDate() < date.getDate())) age--;
    // 	if (age >= 15) {
    // 		input.addClass("is-valid").removeClass("has-error");
    // 		errorDiv.hide();
    // 		return true;
    // 	} else {
    // 		input.addClass("has-error").removeClass("is-valid");
    // 		errorDiv.show();
    // 		return false;
    // 	}
    // },

    validateMobile: function () {
  const input = $("#mobile");
  const value = input.val().trim();
  const errorDiv = $(".mobileNo-error");

  // Remove all non-digit characters and store clean value
  const cleanValue = value.replace(/\D/g, "");
  this.value = cleanValue;

  let isValid = false;

  // Singapore number format: must start with 8 or 9 and have 8 digits
  if (value.startsWith("+")) {
    isValid = /^\+65[89]\d{7}$/.test(value);
  } else {
    isValid = /^[89]\d{7}$/.test(value);
  }

  if (isValid) {
    input.addClass("is-valid").removeClass("has-error");
    errorDiv.text("").hide();
    return true;
  } else {
    input.addClass("has-error").removeClass("is-valid");
    errorDiv.text("Please enter a valid Singapore mobile number starting with 8 or 9.").show();
    return false;
  }
},


    // validatePostalCode: function () {
    // 	const input = $("#postal_code");
    // 	const value = input.val().trim();
    // 	const errorDiv = $(".postal-error");
    // 	if (!value) {
    // 		input.addClass("has-error").removeClass("is-valid");
    // 		errorDiv.text("Postal code is required.").show();
    // 		return false;
    // 	} else if (!/^\d{6}$/.test(value)) {
    // 		input.addClass("has-error").removeClass("is-valid");
    // 		errorDiv.text("Postal code must be exactly 6 digits.").show();
    // 		return false;
    // 	} else {
    // 		input.removeClass("has-error");
    // 		errorDiv.text("").hide();
    // 		return true;
    // 	}
    // },
    validatePostalCode: function () {
      const input = $("#postal_code");
      const value = input.val().trim();
      const errorDiv = $(".postal-error");
      let isValidPostalCode = false;
      // Ensure the postal code is exactly 6 digits
      if (!value) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.text("Postal code is required.").show();
        isValidPostalCode = false;
        return false;
      } else if (!/^\d{6}$/.test(value)) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.text("Postal code must be exactly 6 digits.").show();
        isValidPostalCode = false;
        return false;
      } else {
        input.removeClass("has-error");
        errorDiv.text("").hide();
        isValidPostalCode = true;
      }

      // Extract the first 5 digits of the postal code
      const postal5D = value.substring(0, 6);
      const form = $("#flexcore-merged-registration-form");
      const submitBtn = form.find('button[type="submit"]');
      // Make the API call to OneMap to validate the first 5 digits of the postal code
      submitBtn.prop("disabled", true).addClass("loading");
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_postalcode_validation",
          register_nonce: $("#register_nonce").val(),
          postal_code: postal5D,
        },
        success: function (result) {
          if (
            result.data.response.found > 0 &&
            result.data.response.results[0].POSTAL !== "NIL"
          ) {
            // If valid postal code is found, consider it as valid
            isValidPostalCode = true;
            input.removeClass("has-error").addClass("is-valid");
            errorDiv.text("").hide();
            submitBtn.prop("disabled", false).removeClass("loading");
          } else {
            isValidPostalCode = false;
            input.addClass("has-error").removeClass("is-valid");
            errorDiv
              .text("Invalid postal code. Please enter a valid postal code.")
              .show();
            submitBtn.prop("disabled", true).addClass("loading");
          }
        },
        error: function () {
          isValidPostalCode = false;
        },
      });
      return isValidPostalCode;
    },
    validateFields: function () {
      let valid = true;
      if (!this.validateName()) valid = false;
      if (!this.validateEmail()) valid = false;
      if (!this.validateConfirmEmail()) valid = false;
      if (!this.validatePassword()) valid = false;
      if (!this.validateConfirmPassword()) valid = false;
    if ($("#register_source").is(":visible") && !this.validateRegisterSource()) {
  valid = false;
}

      if (!this.validateConsent()) valid = false;
      // Date of Birth
      // Mobile
      if (!this.validateMobile()) {
        $("#mobile").addClass("has-error").removeClass("is-valid");
        $(".mobile-error").text("Mobile number is required or invalid.").show();
        valid = false;
      } else {
        $("#mobile").removeClass("has-error");
        $(".mobile-error").hide();
      }
      // Postal Code
      // if (!this.validatePostalCode()) {
      // 	$("#postal_code").addClass("has-error").removeClass("is-valid");
      // 	$(".postal-error").text("Postal code is required or invalid.").show();
      // 	valid = false;
      // } else {
      // 	$("#postal_code").removeClass("has-error");
      // 	$(".postal-error").hide();
      // }
      if (!this.validateDOB()) {
        $("#dob").addClass("has-error").removeClass("is-valid");
        $(".dob-error").text("Date of Birth is required.").show();
        valid = false;
      } else {
        $("#dob").removeClass("has-error");
        $(".dob-error").hide();
      }
      // Gender
      if (!$("#gender").val()) {
        $("#gender").addClass("has-error").removeClass("is-valid");
        if ($(".gender-error").length) {
          $(".gender-error").text("Gender is required.").show();
        }
        valid = false;
      } else {
        $("#gender").removeClass("has-error");
        if ($(".gender-error").length) {
          $(".gender-error").hide();
        }
      }
      // Race
      if (!$("#race").val()) {
        $("#race").addClass("has-error").removeClass("is-valid");
        if ($(".race-error").length) {
          $(".race-error").text("Race is required.").show();
        }
        valid = false;
      } else {
        $("#race").removeClass("has-error");
        if ($(".race-error").length) {
          $(".race-error").hide();
        }
      }
      // Describe race validation
      if ($("#race").val() === "others") {
        if (!$("#others").val().trim()) {
          $("#others").addClass("has-error").removeClass("is-valid");
          $(".race_details-error").text("Please describe your race.").show();
          valid = false;
        } else {
          $("#others").removeClass("has-error");
          $(".race_details-error").hide();
        }
      }
      // Referral code validation
      if (
        $("#referral_code").prop("required") &&
        !$("#referral_code").val().trim()
      ) {
        $("#referral_code").addClass("has-error").removeClass("is-valid");
        $("#error-referral_code").text("Please enter referral code.").show();
        valid = false;
      } else {
        $("#referral_code").removeClass("has-error");
        $("#error-referral_code").hide();
      }
      // Citizenship
      if (!$("#citizenship").val()) {
        $("#citizenship").addClass("has-error").removeClass("is-valid");
        if ($(".citizen-error").length) {
          $(".citizen-error").text("Citizenship is required.").show();
        }
        valid = false;
      } else {
        $("#citizenship").removeClass("has-error");
        if ($(".citizen-error").length) {
          $(".citizen-error").hide();
        }
      }
      return valid;
    },

    handleSubmit: function (e) {
      e.preventDefault();
      const form = $("#flexcore-merged-registration-form");
      const submitBtn = form.find('button[type="submit"]');
      const messageDiv = $("#register-message");
      if (!MergedRegistration.validateFields()) {
        return;
      }
      submitBtn.prop("disabled", true).addClass("loading");
      messageDiv.hide();
      // Mobile formatting for Singapore numbers
      let mobileVal = $("#mobile").val();
      if (mobileVal.startsWith("+65")) {
        mobileVal = mobileVal.substring(3);
      }
      const dobInput = $("#dob").val(); // dd/mm/yyyy
      const dobApi = formatDOBForApi(dobInput); // yyyy-mm-dd
      // UTM string logic (preserved)
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_marged_register",

          register_nonce: $("#register_nonce").val(),

          email: $("#email").val(),
          name: $("#name").val(),
          password: $("#password").val(),
          confirm_password: $("#confirm_password").val(),
          referral_code: $("#referral_code").val() || "",
          utm_string: $("#utm_string").val() || "",
          campaign_id: $("#campaign_id").val() || "",
          register_source: $("#register_source").val() || "",
          dob: dobApi,
          gender: $("#gender").val(),
          race: $("#race").val(),
          others: $("#others").val(),
          mobile: mobileVal,
          postal_code: $("#postal_code").val(),
          citizenship: $("#citizenship").val(),
          consent: $("#consent").is(":checked") ? 1 : 0,
          preferredName: $("#preferred_name").val() || "",
          flowId: $("#myinfo_flow_id").val() || "",
        success: function (response) {
          if (response.success) {
            messageDiv
              .removeClass("error")
              .addClass("success")
              .html(response.data.message)
              .show();
            window.location.href = "/lifestyle-survey/";
          } else {
            messageDiv.empty().removeClass("success").addClass("error").show();

            if (
              response.data &&
              response.data.errors &&
              Object.keys(response.data.errors).length > 0
            ) {
              const errors = response.data.errors;
              for (const field in errors) {
                if (errors.hasOwnProperty(field)) {
                  const messages = errors[field];
                  messages.forEach((msg) => {
                    messageDiv.append(
                      `<div><strong>${field}:</strong> ${msg}</div>`
                    );
                  });
                }
              }
            } else if (response.data && response.data.message) {
              messageDiv.text(response.data.message);
            } else {
              messageDiv.text("An unknown error occurred.");
            }
          }
        },

        complete: function () {
          submitBtn.prop("disabled", false).removeClass("loading");
        },
      });
    },
  };
  function formatDOBForApi(value) {
    // value = dd/mm/yyyy
    const [day, month, year] = value.split("/");
    return `${year}-${month}-${day}`; // yyyy-mm-dd
  }

  $(document).ready(function () {
    MergedRegistration.init();
  });
})(jQuery);
