document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const rewardId = urlParams.get("id");
  if (!rewardId) {
    // alert("Reward ID missing in URL.");
    return;
  }
  const nonce = flexcoreServerAjax.rewardNonce;

  fetch(flexcoreServerAjax.ajaxUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "flexcore_preview_rewards",
      nonce: nonce,
      id: rewardId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        document.getElementById("no-reward-message").style.display = "block";
        document.getElementById("reward-found").style.display = "none";
        return;
      }
      document.getElementById("no-reward-message").style.display = "none";
      document.getElementById("reward-found").style.display = "block";
      //
      var text='';
      const reward = data.data;
      //  console.log("Reward Data:", reward.data.rewardType);
      window.currentRewardCategory = reward.data.rewardType.category;
       if (reward.data.rewardType.category == "donation") {
          document.querySelector(".rewardType").textContent = "REDEEM FOR DONATION";
          document.querySelector(".amount-to-redeem").textContent = "Amount to donate:";
        const declarationSpan = document.querySelector(".declaration");
         declarationSpan.textContent = "I confirm the above information is accurate, and agree for HappyDot.sg to collect and use it to contact me and process my donation.";
      } else if (reward.data.rewardType.category == "paynow") {
        const declarationSpan = document.querySelector(".declaration");
        declarationSpan.textContent = "I hereby acknowledge that the PayNow mobile number I have submitted above is correct.";
        document.querySelector(".consent-checkbox-2").style.display = "block";
        document.querySelector(".declaration2").textContent = "I hereby acknowledge that the PayNow mobile number I have submitted belongs to me.";
        document.querySelector(".consent-checkbox-2 input[type='checkbox']").required = true;
      } else if (reward.data.rewardType.category == "physical-reward") {
        const declarationSpan = document.querySelector(".declaration");
        declarationSpan.textContent = "I hereby acknowledge that the information I have submitted above is correct.";
      }
      let { showName, showEmail, editName, editEmail, showAddress, editAddress, showMobile, editMobile } = reward.data;

      if (reward.data.rewardType.category == "paynow") {
        showName = true;
        editName = false;
        showMobile = true;
        editMobile = true;
      }
      
      if( editName || editEmail || editAddress || editMobile) {
        document.querySelector(".consent-checkbox").style.display = "block";
        document.querySelector(".consent-checkbox input[type='checkbox']").required = true;
      } 
      if (showName) {
        document.querySelector("#name-group").style.display = "block";
        document.querySelector("#name").value = reward.user.name || "";
        document.querySelector("#name").readOnly = !editName;
        if (reward.data.rewardType.category == "paynow") {
          document.querySelector("#name").style.backgroundColor = "#e9ecef";
          document.querySelector("#name").style.color = "#6c757d";
        }
      }

      if (showEmail) {
        document.querySelector("#email-group").style.display = "block";
        document.querySelector("#email").value = reward.user.email || "";
        document.querySelector("#email").readOnly = !editEmail;
      }

      if (showAddress) {
        document.querySelector("#address-group").style.display = "block";
        document.querySelector("#address").value = "";
        document.querySelector("#address").readOnly = !editAddress;
      }

      if (showMobile) {
        const mobile = reward.user?.metaData?.mobileNumber || "" ;
        document.querySelector("#contact-group").style.display = "block";
        document.querySelector("#contact").value = Number(mobile) || "";
        document.querySelector("#contact").readOnly = !editMobile;
      }

      const userBalance = reward.currentPoints || 0;
      
      document.querySelector(".rewards-points.tmp").innerText = `${userBalance} Points`;
      document.querySelector(".voucher-img img").src = reward.data.imageUrl;
      document.querySelector(".voucher-img img").alt = reward.data.name;
      document.querySelector(".voucher-ctn p").innerText = reward.data.name;
      document.querySelector(".voucher-ctn span").innerText = `${reward.data.points} Points`;
      document.querySelector('input[name="redeem_id"]').value = reward.data.id;
      document.querySelector('input[name="redeem_voucher"]').value = reward.data.name;
      document.querySelector("h4.redeem-counth4").innerText = `${reward.data.points} Points`;
      document.querySelector("#info").innerHTML = reward.data.rewardDescription;
      document.querySelector(".termCondition-ctn").innerHTML = reward.data.termsAndCondition;
     
      
      window.voucherPrice = reward.points;

      const errorElement = document.querySelector(".hd-error");
      if (userBalance < reward.data.points) {
        errorElement.textContent = "You do not have enough points to redeem this reward.";
        errorElement.style.display = "block";
      } else {
        errorElement.style.display = "none";
      }

      const qtyInput = document.querySelector(".numOf-qty");
      let maxQty = reward.data.quantity === -1 ? (reward.data.rewardType.category == "paynow" ? 1 : 1) : reward.data.remainingQuantity;
      if (reward.data.rewardType.category == "paynow") {
        maxQty = Math.min(maxQty, 1);
      }
      const maxRedeemable = reward.data.rewardType.category == "paynow" ? 1 : 1;

      qtyInput.max = Math.min(maxQty, maxRedeemable);
      if(maxQty < maxRedeemable) {
         text = `You can redeem a maximum of ${maxQty} item.`;
      }
      else {
         if (reward.data.rewardType.category == "paynow") {
           text = `You may redeem up to 1 quantity at this time.`;
         } else {
           text = `You can redeem only 1 reward at this moment..`;
         }
      }
      qtyInput.value = 1;

      const qtyMinus = document.querySelector(".qty-count-minus-flex");
      const qtyAdd = document.querySelector(".qty-count-add-flex");

      qtyMinus.addEventListener("click", () => {
        let qty = parseInt(qtyInput.value, 10);
        if (qty > 1) {
          qtyInput.value = qty - 1;
          document.getElementById("error-quantity").style.display = "none";
        }
      });

      qtyAdd.addEventListener("click", () => {
        let qty = parseInt(qtyInput.value, 10);
        if (qty < qtyInput.max) {
          qtyInput.value = qty + 1;
          document.getElementById("error-quantity").style.display = "none";
        } else {
          document.getElementById("error-quantity").style.display = "block";
          document.getElementById("error-quantity").textContent = text;
        }
      });

      qtyInput.addEventListener("input", () => {
        let qty = parseInt(qtyInput.value, 10);
        if (qty < 1) qtyInput.value = 1;
        else if (qty > qtyInput.max) {
          qtyInput.value = qtyInput.max;
          document.getElementById("error-quantity").style.display = "block";
          document.getElementById("error-quantity").textContent = text;
        } else {
          document.getElementById("error-quantity").style.display = "none";
          
        }
      });

      // FORM SUBMIT
      document.querySelector("#RedeemRewards").addEventListener("submit", function (e) {
        e.preventDefault();
        

        const validations = [];

        document.querySelector("#error-consent").style.display = "none";
        const errorConsent2 = document.querySelector("#error-consent2");
        if (errorConsent2) errorConsent2.style.display = "none";

        if (editName) validations.push(validateField("name", isValidFullName, "Please enter at least two words, each at least 2 characters."));
        if (editEmail) validations.push(validateField("email", isValidEmail, "Please enter a valid email address."));
        if (editMobile) {
            const errorMsg = (reward.data.rewardType.category === "paynow") 
                ? "Mobile number must begin with 8 or 9 and be exactly 8 digits." 
                : "Please enter a valid phone number.";
            validations.push(validateField("contact", isValidPhone, errorMsg));
        }
        if (editAddress) validations.push(validateField("address", isNonEmpty, "Address is required."));

        if (validations.includes(false)) {
          document.querySelector(".message-reward").textContent = "Please correct the errors above before submitting.";
          return;
        }
        if(editName || editEmail || editAddress || editMobile) {
          const consentCheckbox = document.querySelector(".consent-checkbox");
          if (consentCheckbox && !consentCheckbox.querySelector("input[type='checkbox']").checked) {
            document.querySelector("#error-consent").textContent = "You must agree to the terms.";
            document.querySelector("#error-consent").style.display = "block";
            return;
          } 
          
          if (reward.data.rewardType.category == "paynow") {
            const consentCheckbox2 = document.querySelector(".consent-checkbox-2");
            if (consentCheckbox2 && !consentCheckbox2.querySelector("input[type='checkbox']").checked) {
              document.querySelector("#error-consent2").textContent = "You must agree to all terms.";
              document.querySelector("#error-consent2").style.display = "block";
              return;
            } 
          }
        }
        const quantity = document.querySelector(".numOf-qty").value;
        const payload = {
          quantity,
          rewardId,
        };

        if (showName) payload.name = document.querySelector("#name").value.trim();
        if (editEmail) payload.email = document.querySelector("#email").value.trim();
        if (editAddress) payload.address = document.querySelector("#address").value.trim();
        if (editMobile) payload.mobile = document.querySelector("#contact").value.trim();
        document.getElementById("Redeem-").disabled = true;
        fetch(flexcoreServerAjax.ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "flexcore_redeem_reward",
            nonce: flexcoreServerAjax.redeemNonce,
            data: JSON.stringify(payload),
          }),
        })
          .then((res) => res.json())
          .then((result) => {
            const msgDiv = document.querySelector(".message-reward");
            msgDiv.classList.remove("success", "error");
            // msgDiv.style.display = "block";
            const redeemRewardInitiateId = result.data.id || "";
            if (result.success) {
              document.getElementById("Redeem-").disabled = false;
              // console.log("Redeem result:", redeemRewardInitiateId);
              msgDiv.textContent = result.data.message || "Reward redeemed successfully!";
              msgDiv.classList.add("success");
              const otpField = `
<div class="hd-form-group" style="display:block" id="otp-group">
    <label class="hd-label" for="otp">Enter OTP<span style="color:red; font-size:20px; margin-left:2px">*</span></label>
    <input class="hd-formfild" type="text" id="otp" name="otp" maxlength="6" placeholder="Enter 6-digit OTP"><br><br>
    <button class="hd-btn hd-otp-btn" id="otp-submit">verify OTP</button>
    <p class="otp-error field-error" id="otp-error"></p>
</div>
`;
document.querySelector("#RedeemRewards").style.display = "none";
document.querySelector("#name-group").style.display = "none";
document.querySelector("#email-group").style.display = "none";
document.querySelector("#contact-group").style.display = "none";
document.querySelector("#address-group").style.display = "none";
document.querySelector(".hd-redeem-btn-check").style.display = "none";
document.querySelector('.voucher-otp').innerHTML = otpField;
document.querySelector('.voucher-otp').style.display = 'block';
// Wait for the OTP DOM to render before attaching listeners
setTimeout(() => {
  const otpInput = document.getElementById("otp");
  const otpError = document.getElementById("otp-error");
  const otpBtn = document.getElementById("otp-submit");

  if (otpInput && otpBtn) {
    // Validation on input change
    otpInput.addEventListener("input", () => {
      const val = otpInput.value.trim();
      if (val.length !== 6 || !/^\d{6}$/.test(val)) {
        otpInput.classList.add("has-error");
        otpError.textContent = "OTP must be exactly 6 digits.";
        otpError.style.display = "block";
      } else {
        otpInput.classList.remove("has-error");
        otpError.textContent = "";
        otpError.style.display = "none";
      }
    });

    // Click event for otp-submit button
    otpBtn.addEventListener("click", (e) => {
      e.preventDefault();
      

      const otpValue = otpInput.value.trim();

      if (otpValue.length !== 6 || !/^\d{6}$/.test(otpValue)) {
        otpInput.classList.add("has-error");
        otpError.textContent = "OTP must be exactly 6 digits.";
        otpError.style.display = "block";
        return;
      }

      otpInput.classList.remove("has-error");
      otpError.textContent = "";
      otpError.style.display = "none";
      document.getElementById("otp-submit").disabled = true;
      // ✅ This is where you will later add the actual AJAX verification
      fetch(flexcoreServerAjax.ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "flexcore_verify_redeem_otp",
          nonce: flexcoreServerAjax.otpRewardNonce,
          otp: otpValue,
          rewardId: rewardId,
          redeemRewardInitiateId: result.data.id || "",
        }),
      })
          .then((res) => res.json())
          .then((result) => {
            // console.log("OTP verification result:", result);
  if  (result.success) {
    document.getElementById("otp-submit").disabled = false;
    msgDiv.textContent = result.data.message || "OTP verified successfully!";
    msgDiv.classList.add("success");
    msgDiv.classList.remove("error");
    setTimeout(() => {
      window.location.href =  "/reward";
    }, 2000);
  } else {
    document.getElementById("otp-submit").disabled = false;
    msgDiv.textContent = result.data.message || "OTP verification failed.";
    msgDiv.classList.add("error");
    msgDiv.classList.remove("success");
  }
})
.catch(() => {
  msgDiv.textContent = result.data.message;
  msgDiv.classList.add("error");
  msgDiv.classList.remove("success");
});

      
    });
  }
}, 100); // Slight delay to ensure DOM is ready

            } else {
              msgDiv.textContent = result.data.message || "Redemption failed.";
              msgDiv.classList.add("error");
            }
          })
          .catch(() => {
            document.querySelector(".message-reward").textContent = "Something went wrong. Try again.";
          });
      });
    });
});

// Move these functions OUTSIDE the DOMContentLoaded callback
function validateField(fieldId, validatorFn, errorMessage) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.querySelector(`#${fieldId}-error`);
  const isValid = validatorFn(field.value.trim());

  if (!isValid) {
    field.classList.add("has-error");
    if (errorDiv) errorDiv.textContent = errorMessage;
    if (errorDiv) errorDiv.style.display = "block";
    
  } else {
    field.classList.remove("has-error");
    if (errorDiv) errorDiv.textContent = "";
    
  }

  return isValid;
}

function isValidFullName(name) {
  const words = name.trim().split(/\s+/);
  return words.length >= 2 && words.every(w => w.length >= 2);
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
  if (window.currentRewardCategory === "paynow") {
    return /^[89]\d{7}$/.test(phone);
  }
  return /^(\+)?[0-9]{7,15}$/.test(phone);
}

function isNonEmpty(value) {
  return value.length > 0;
}

// Hook validations to input change
["name", "email", "contact", "address"].forEach((id) => {
  const el = document.getElementById(id);
  if (el) {
    el.addEventListener("input", () => {
      if (id === "name") validateField("name", isValidFullName, "Please enter at least two words, each at least 2 characters.");
      if (id === "email") validateField("email", isValidEmail, "Please enter a valid email address.");
      if (id === "contact") {
        const errorMsg = (window.currentRewardCategory === "paynow") 
            ? "Mobile number must begin with 8 or 9 and be exactly 8 digits." 
            : "Please enter a valid phone number.";
        validateField("contact", isValidPhone, errorMsg);
      }
      if (id === "address") validateField("address", isNonEmpty, "Address is required.");
    });
  }
});
