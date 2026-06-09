// /**
//  * Profile form functionality
//  */
// (function($) {
//     'use strict';

//     const Profile = {
//         init: function() {
//             $('#flexcore-profile-form').on('submit', this.handleSubmit);
//         },

//         handleSubmit: function(e) {
//             e.preventDefault();
//             const form = $(this);
//             const submitBtn = form.find('input[type="submit"]');
//             const messageDiv = $('#reward-message'); // Optional div for showing messages

//             // Clear previous errors
//             form.find(".field-error").text("").hide();
//             form.find("input").removeClass("has-error");

//             // Get field values
//             const name = $('#name').val().trim();
//             const email = $('#email').val().trim().toLowerCase();
//             const contact = $('#contact').val().trim();
//             const address = $('#address').val().trim();
//             let isValid = true;

//             // Validation
//             if (!name) {
//                 $('#name').addClass('has-error');
//                 $('.name-error').text("Name is required.").show();
//                 isValid = false;
//             }

//             const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//             if (!email) {
//                 $('#email').addClass('has-error');
//                 $('.email-error').text("Email is required.").show();
//                 isValid = false;
//             } else if (!emailPattern.test(email)) {
//                 $('#email').addClass('has-error');
//                 $('.email-error').text("Enter a valid email.").show();
//                 isValid = false;
//             }

//             if (!contact || contact.length < 10) {
//                 $('#contact').addClass('has-error');
//                 $('.contact-error').text("Enter a valid mobile number.").show();
//                 isValid = false;
//             }

//             if (!address) {
//                 $('#address').addClass('has-error');
//                 $('.address-error').text("Address is required.").show();
//                 isValid = false;
//             }

//             if (!isValid) return;

//             // Disable button and show loading
//             submitBtn.prop('disabled', true).addClass('loading');
//             if (messageDiv.length) messageDiv.hide();

//             $.ajax({
//                 url: flexcoreServerAjax.ajaxUrl,
//                 type: 'POST',
//                 data: {
//                     action: 'flexcore_update_profile',
//                     name: name,
//                     email: email,
//                     contact: contact,
//                     address: address,
//                     nonce: $('#flexcore_nonce').val()
//                 },
//                 success: function(response) {
//                     if (response.success) {
//                         if (messageDiv.length) {
//                             messageDiv.removeClass('error').addClass('success')
//                                 .html(response.data.message || "Profile updated successfully.")
//                                 .show();
//                         } else {
//                             alert("Profile updated successfully.");
//                         }
//                     } else {
//                         if (messageDiv.length) {
//                             messageDiv.removeClass('success').addClass('error')
//                                 .html(response.data.message || "Update failed.")
//                                 .show();
//                         } else {
//                             alert("Update failed.");
//                         }
//                     }
//                 },
//                 error: function() {
//                     if (messageDiv.length) {
//                         messageDiv.removeClass('success').addClass('error')
//                             .html("An error occurred. Please try again.")
//                             .show();
//                     } else {
//                         alert("An error occurred.");
//                     }
//                 },
//                 complete: function() {
//                     submitBtn.prop('disabled', false).removeClass('loading');
//                 }
//             });
//         }
//     };

//     $(document).ready(function() {
//         Profile.init();
//     });

// })(jQuery);
