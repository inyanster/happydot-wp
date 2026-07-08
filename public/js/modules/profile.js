(function ($) {
  "use strict";

  const Profile = {
    init: function () {
      // Skip if MyInfo profile form exists on this page — it has its own JS
      if ($("#flexcore-profile-myinfo-form").length) return;
      this.loadProfileData();
      const form = $("#flexcore-profile-form");

      form.on("submit", this.handleSubmit);
      $("#race").on("change", this.toggleOthersField);
      this.toggleOthersField();

      $("#dob").on("input", this.formatDate);
      $("#dob").on("change", this.validateDOB);
      $("#mobile").on("input", this.formatMobile);
      $("#mobile").on("input change", this.validateMobile);
      $("#postal_code").on("input", function() {
        $(this).data("touched", true);
        Profile.validatePostalCode(true);
      });

      form.find("input, select").on("blur", function () {
        const id = $(this).attr("id");

        switch (id) {
          case "dob":
            Profile.validateDOB();
            break;
          case "mobile":
            Profile.validateMobile();
            break;
          case "postal_code":
            Profile.validatePostalCode(true);
            break;
          default:
            if (this.value.trim() === "") {
              $(this).addClass("has-error").removeClass("is-valid");
              // Show error message for blank field only after interaction
              const errorElem = $("." + id + "-error");
              if (errorElem.length && ($(this).is(":focus") || $(this).data("touched") || $("#submit-btn").is(":focus"))) {
                errorElem.text("This field is required.").show();
              } else {
                errorElem.text("").hide();
              }
              $("#submit-btn").removeClass("has-error");
            } else {
              $(this).addClass("is-valid").removeClass("has-error");
              // Hide error message when filled
              const errorElem = $("." + id + "-error");
              if (errorElem.length) {
                errorElem.text("").hide();
              }
              $("#submit-btn").removeClass("is-valid");
            }
        }
      });
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
    setFieldError: function (fieldId, message) {
      const input = $(`#${fieldId}`);
      const errorElem = $(`.${fieldId}-error`);
      if (message) {
        input.addClass("has-error").removeClass("is-valid");
        errorElem.text(message).show();
      } else {
        input.removeClass("has-error").addClass("is-valid");
        errorElem.text("").hide();
      }
    },

    loadProfileData: function () {
      const form = $("#flexcore-profile-form");
      const messageDiv = $("#profile-message");

      form.find('button[type="submit"]').prop("disabled", true);

      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_get_profile",
          nonce: flexcoreServerAjax.profileNonce,
        },
        success: function (response) {
          if (response.success && response.data) {
            const data = response.data;
            const metaData = data.metaData || {};

            $("#email").val(data.email || "");
            $("#name").val(data.name || "");
            $("#dob").val(metaData.dateOfBirth || "");
            $("#gender").val(metaData.gender || "");
            $("#race").val(metaData.race || "");
            $("#others").val(metaData.raceDetails || "");
            $("#marital_status").val(metaData.maritalStatus || "");
            $("#mobile").val(metaData.mobileNumber || "");
            $("#citizenship").val(metaData.citizenship || "");
            $("#postal_code").val(metaData.postalCode || "");
            

            if (data.email) {
              $("#email").prop("readonly", true);
            }

            if (metaData.raceDetails?.trim() || metaData.race === "others") {
              $("#othersRaceGroup").show();
              $("#others").prop("required", true);
            } else {
              $("#othersRaceGroup").hide();
            }
            if(data.membershipStatus == 4){
              $("#submit-btn").show();
              
            }
            form.data("loaded", true);
          } else {
            messageDiv
              .removeClass("success")
              .addClass("error")
              .html(response.data)
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
          form.find('button[type="submit"]').prop("disabled", false);
        },
      });
    },

    formatDate: function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 8) value = value.slice(0, 8);

      if (value.length >= 4) {
        const year = value.slice(0, 4);
        const month = value.slice(4, 6);
        const day = value.slice(6, 8);
        e.target.value = `${year}-${month}${day ? "-" + day : ""}`;
      } else {
        e.target.value = value;
      }
    },

    validateDOB: function () {
      const input = $(this);
      const value = input.val();
      const errorDiv = $(".dob-error");

      if (!value) return false;

      if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.show();
        return false;
      }

      const [year, month, day] = value.split("-").map(Number);
      const date = new Date(year, month - 1, day);
      if (
        date.getFullYear() !== year ||
        date.getMonth() + 1 !== month ||
        date.getDate() !== day
      ) {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.show();
        return false;
      }

      const today = new Date();
      let age = today.getFullYear() - date.getFullYear();
      const m = today.getMonth() - date.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < date.getDate())) age--;

      if (age >= 15) {
        input.addClass("is-valid").removeClass("has-error");
        errorDiv.hide();
        return true;
      } else {
        input.addClass("has-error").removeClass("is-valid");
        errorDiv.show();
        return false;
      }
    },

 

    // validatePostalCode: function (showError) {
    //   const input = $("#postal_code");
    //   const value = input.val().trim();
    //   const errorDiv = $(".postal-error");
    //   if (!value) {
    //     input.addClass("has-error").removeClass("is-valid");
    //     if (showError) errorDiv.text("Postal code is required.").show();
    //     else errorDiv.text("").hide();
    //     return false;
    //   } else if (!/^\d{6}$/.test(value)) {
    //     input.addClass("has-error").removeClass("is-valid");
    //     if (showError) errorDiv.text("Postal code must be exactly 6 digits.").show();
    //     else errorDiv.text("").hide();
    //     return false;
    //   } else {
    //     input.removeClass("has-error").addClass("is-valid");
    //     errorDiv.text("").hide();
    //     return true;
    //   }
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
    }
	else{
		input.removeClass("has-error");
		errorDiv.text("").hide();
		isValidPostalCode = true;
	}
 
    // Send the full 6-digit postal code to OneMap for validation
    const postal6D = value;
	const submitBtn = document.getElementById("submit-btn");
	submitBtn.disabled = true;
	$.ajax({
	url: flexcoreServerAjax.ajaxUrl,
	type: "POST",
	data: {
	action: "flexcore_postalcode_validation",					
	register_nonce: $("#register_nonce").val(),					
	postal_code: postal6D,
				},
				success: function (result) {
					
            if (result.data.response.found > 0 && result.data.response.results[0].POSTAL !== "NIL") {
                // If valid postal code is found, consider it as valid
                isValidPostalCode = true;
				 input.removeClass("has-error").addClass("is-valid");
        		errorDiv.text("").hide();
        		submitBtn.disabled = false;
            } else {
                isValidPostalCode = false;
				input.addClass("has-error").removeClass("is-valid");
       			 errorDiv.text("Invalid postal code. Please enter a valid postal code.").show();
				 submitBtn.disabled = true;
        		
            }
        },
        error: function () {
            isValidPostalCode = false;
        }
			});
  return isValidPostalCode;
 
    
},
    formatMobile: function (e) {
      let value = e.target.value.trim();

      // Remove all characters except digits and '+'
      value = value.replace(/[^+\d]/g, "");

      // Optionally trim to 15 characters
      value = value.slice(0, 15);

      e.target.value = value;
    },

    validateFields: function () {
      let isValid = true;
      const fields = [
        { id: "name", required: true, message: "Name is required." },
        { id: "dob", required: true, message: "Date of Birth is required." },
        { id: "gender", required: true, message: "Gender is required." },
        { id: "race", required: true, message: "Race is required." },
        { id: "mobile", required: true, message: "Mobile Number is required." },
        { id: "citizenship", required: true, message: "Citizenship is required." },
        { id: "postal_code", required: true, message: "Postal Code is required." },
      ];

      fields.forEach((field) => {
        const value = $(`#${field.id}`).val();
        if (field.required && (!value || value.trim() === "")) {
          Profile.setFieldError(field.id, field.message);
          isValid = false;
        } else {
          Profile.setFieldError(field.id, "");
        }
      });

      // Additional validation: DOB format
      if (!/^\d{4}-\d{2}-\d{2}$/.test($("#dob").val())) {
        Profile.setFieldError("dob", flexcoreServerAjax.i18n.invalidDateFormat);
        isValid = false;
      }

      // Additional validation: Mobile
      if (!/^\+?[\d\s-]{8,}$/.test($("#mobile").val())) {
        Profile.setFieldError(
          "mobile",
          flexcoreServerAjax.i18n.invalidMobileNumber
        );
        isValid = false;
      }

      // Postal code validation
      if (!Profile.validatePostalCode(true)) {
        isValid = false;
      }

      // Consent checkbox
      if (!$("#consent").is(":checked")) {
        $("#consent").addClass("has-error");
        $(".consent-error")
          .text("You must provide consent to proceed.")
          .show();
        isValid = false;
      } else {
        $("#consent").removeClass("has-error");
        $(".consent-error").text("").hide();
      }

      return isValid;
    },

    handleSubmit: function (e) {
      e.preventDefault();
      if (!validateRequiredFields()) {
        return;
      }

      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      const messageDiv = $("#profile-message");

      if (!Profile.validateFields()) return;

      submitBtn.prop("disabled", true).addClass("loading");
      messageDiv.hide();
      let value = $("#mobile").val().trim();
      if (value.startsWith("+") && value.length >= 3) {
        value = value.substring(3);
      }
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_update_profile",
          nonce: flexcoreServerAjax.updateProfileNonce,
          // name: $("#name").val(),
          dateOfBirth: $("#dob").val(),
          // gender: $("#gender").val(),
          // race: $("#race").val(),
          // others: $("#others").val(),
          mobileNumber: value,
          citizenship: $("#citizenship").val(),
          postalCode: $("#postal_code").val(),
          maritalStatus: $("#marital_status").val() || "",
          preferredName: $("#preferred_name").val() || "",
          redirect_to_dashboard: true,
        },
        success: function (response) {
          if (response.success) {
            messageDiv
              .removeClass("error")
              .addClass("success")
              .html(response.data.message)
              .show();

            if (response.data.redirect) {
              setTimeout(function () {
                window.location.href = "/happydot/my-account/";
              }, 1500);
            }
          } else {
            let messageContent = "";

            if (typeof response.data.message === "object") {
              for (const field in response.data.message) {
                if (Array.isArray(response.data.message[field])) {
                  messageContent += `<strong>${field}:</strong> ${response.data.message[
                    field
                  ].join(", ")}<br>`;
                }
              }
            } else {
              messageContent =
                response.data.message || flexcoreServerAjax.i18n.updateFailed;
            }

            messageDiv
              .removeClass("success")
              .addClass("error")
              .html(messageContent)
              .show();
          }
        },
        error: function (jqXHR) {
          let errorMessage =
            flexcoreServerAjax.i18n.errorOccurred ||
            "An error occurred while updating your profile.";

          try {
            const response = JSON.parse(jqXHR.responseText);
            if (response.data && response.data.message) {
              if (typeof response.data.message === "object") {
                for (const field in response.data.message) {
                  if (Array.isArray(response.data.message[field])) {
                    errorMessage += `<br><strong>${field}:</strong> ${response.data.message[
                      field
                    ].join(", ")}`;
                  }
                }
              } else {
                errorMessage = response.data.message;
              }
            }
          } catch (e) {}

          messageDiv
            .removeClass("success")
            .addClass("error")
            .html(errorMessage)
            .show();
        },
        complete: function () {
          submitBtn.prop("disabled", false).removeClass("loading");
        },
      });
    },
  };
  /**
   * Checks if all required fields are filled
   */
  function validateRequiredFields() {
    let isValid = true;

    // Clear existing error states
    $(".field-error").hide();
    $(".hd-formfild").removeClass("has-error");

    const requiredFields = [
      "#name",
      "#dob",
      "#gender",
      "#race",
      "#mobile",
      "#citizenship",
    ];

    requiredFields.forEach((selector) => {
      const $field = $(selector);
      if ($field.length && $field.is(":visible") && !$field.val().trim()) {
        $field.closest(".hd-formfild").addClass("has-error");
        $("." + $field.attr("id") + "-error").show();
        isValid = false;
      }
    });

    // Extra check for "others" if race is 'others'
    const raceVal = $("#race").val().toLowerCase();
    if (raceVal === "others") {
      const $others = $("#others");
      if (!$others.val().trim()) {
        $others.closest(".hd-formfild").addClass("has-error");
        $(".race_details-error").show();
        isValid = false;
      }
    }

    return isValid;
  }

  $(document).ready(() => {
    const input = document.getElementById("dob");

    if (input && typeof input.showPicker === "function") {
      // Use showPicker if the browser supports it
      $("#dob").on("click", function () {
        input.showPicker();
      });
    } else {
      // Fallback: focus (may trigger native picker on some browsers)
      $("#dob").on("click", function () {
        this.focus();
      });
    }
    Profile.init();
  });
})(jQuery);
