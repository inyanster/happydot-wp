/**
 * Main FlexCore Server public JavaScript
 */
(function($) {
    'use strict';

    // FlexCore translations object
    window.flexcoreServerAjax = window.flexcoreServerAjax || {
        i18n: {
            loginFailed: 'Login failed. Please try again.',
            errorOccurred: 'An error occurred. Please try again.',
            updateFailed: 'Update failed. Please try again.',
            resetLinkFailed: 'Failed to send reset link. Please try again.',
            resetFailed: 'Failed to reset password. Please try again.',
            deleteFailed: 'Failed to delete account. Please try again.',
            pleaseConfirmDelete: 'Please confirm that you want to delete your account.',
            confirmDeleteAccount: 'Are you absolutely sure you want to delete your account? This action cannot be undone.',
            passwordsDoNotMatch: 'Passwords do not match.',
            weakPassword: 'Weak',
            mediumPassword: 'Medium',
            strongPassword: 'Strong',
            veryStrongPassword: 'Very Strong',
            returnToLogin: 'Return to login',
            clickToLogin: 'Click here to login',
            // Add OTP-related translations
            verificationFailed: 'OTP verification failed. Please try again.',
            invalidOTP: 'Please enter a valid 6-digit OTP code.',
            otpSent: 'OTP has been sent to your email.',
            // Add password reset related translations
            emailRequired: 'Please enter your email address.',
            invalidPassword: 'Please enter a valid password.',
            passwordTooWeak: 'Password is too weak. Please make it stronger.',
            // Add profile-related translations
            loadFailed: 'Failed to load profile data. Please try again.',
            updateFailed: 'Failed to update profile. Please try again.',
            consentRequired: 'Please confirm that you agree to the data usage consent.',
            invalidDateFormat: 'Please enter a valid date in DD/MM/YYYY format.',
            invalidMobileNumber: 'Please enter a valid mobile number.',
            profileUpdated: 'Profile updated successfully.',
            fillRequired: 'Please fill in all required fields.'
        }
    };

    // Add page URLs
    flexcoreServerAjax.loginUrl =  flexcoreServerAjax.loginUrl || '';
    flexcoreServerAjax.dashboardUrl =  flexcoreServerAjax.dashboardUrl || '' ;
    flexcoreServerAjax.profileUrl =  flexcoreServerAjax.profileUrl ||'/my-profile/';
    flexcoreServerAjax.resetPasswordUrl = flexcoreServerAjax.resetPasswordUrl || '/reset-password/';
    flexcoreServerAjax.registerUrl = flexcoreServerAjax.registerUrl || '/become-a-happydotter/';
    // flexcoreServerAjax.myAccountUrl =  '/my-account/';
    


    // Voucher-provider dynamic validation and URL rewrite logic

    // Reward categories that require the member to acknowledge the voucher terms
    // before the reward can be claimed. A WOGI reward gets the same acknowledgement
    // step as a UNIQGIFT one; every other provider keeps its previous behaviour.
    var VOUCHER_ACK_CATEGORIES = ['uniqgift', 'wogi'];

    // The ONE place that decides the acknowledgement wording.
    // voucherTermsText comes from the reward payload (per reward, nullable); when a
    // reward carries no terms we use provider-neutral wording that points the member
    // at the voucher itself. Never a hardcoded date, never another brand's copy.
    // The value is used as the complement of "must be utilised by", so it reads
    // correctly for a date ("31st May 2026") and for a phrase
    // ("the expiry date stated in your voucher email").
    function getVoucherAcknowledgement(termsText) {
        var terms = (typeof termsText === 'string') ? termsText.trim() : '';
        var utilisation = terms !== ''
            ? 'must be utilised by ' + terms
            : 'must be utilised by the expiry date shown on the voucher';

        return {
            acknowledgement: 'I acknowledge that this e-voucher ' + utilisation +
                '. Unutilised vouchers will not be reinstated, and points used for the redemption will not be returned after this date.',
            error: 'Please acknowledge that the e-voucher ' + utilisation + ' before claiming the reward.'
        };
    }

    function captureRewardProviderState(data) {
        var rewardObj = null;
        
        // Handle deeply nested WordPress AJAX response format {success: true, data: { currentPoints: ..., data: {...}}}
        if (data && data.data && data.data.data && data.data.data.rewardType) {
            rewardObj = data.data.data;
        } else if (data && data.data && data.data.rewardType) {
            rewardObj = data.data;
        } else if (data && data.rewardType) {
            rewardObj = data;
        }

        if (rewardObj) {
            if (rewardObj.name) window.__currentRewardName = rewardObj.name;
            if (rewardObj.rewardType && rewardObj.rewardType.category) {
                window.__rewardCategory = String(rewardObj.rewardType.category).toLowerCase();
                window.__requiresVoucherAck = VOUCHER_ACK_CATEGORIES.indexOf(window.__rewardCategory) !== -1;
                window.__voucherTermsText = (typeof rewardObj.voucherTermsText === 'string') ? rewardObj.voucherTermsText : '';
            }
        }
    }

    // Intercept Fetch API to capture reward details dynamically
    if (window.fetch) {
        var originalFetch = window.fetch;
        window.fetch = function() {
            var promise = originalFetch.apply(this, arguments);
            promise.then(function(response) {
                var clone = response.clone();
                clone.json().then(function(data) {
                    captureRewardProviderState(data);
                }).catch(function(e) {});
            }).catch(function(e) {});
            return promise;
        };
    }

    // Intercept XHR to capture reward details dynamically
    if (window.XMLHttpRequest) {
        var originalXHR = window.XMLHttpRequest;
        function newXHR() {
            var xhr = new originalXHR();
            xhr.addEventListener('load', function() {
                try {
                    if (xhr.responseText) {
                        var data = JSON.parse(xhr.responseText);
                        captureRewardProviderState(data);
                    }
                } catch (e) {}
            });
            return xhr;
        }
        window.XMLHttpRequest = newXHR;
    }

    $(function() {
        // Run a periodic check to handle dynamic client-side rendering (React/Vue/AJAX)
        setInterval(function() {
            // 1. URL rewrite logic for the dynamic reward page
            if (window.location.pathname.indexOf('/rewards_preview') !== -1) {
                
                // Statically set the browser tab title to "Redeem Reward"
                var expectedTitle = 'Redeem Reward - HappyDot.sg';
                if (document.title !== expectedTitle) {
                    document.title = expectedTitle;
                    var titleEls = document.getElementsByTagName('title');
                    for (var i = 0; i < titleEls.length; i++) {
                        titleEls[i].innerText = expectedTitle;
                    }
                }

                var urlParams = new URLSearchParams(window.location.search);
                var id = urlParams.get('id');
                var rewardName = '';
                
                // Find the text element right before the points (e.g. "$10 UNIQGIFT E-Voucher")
                var pointsElements = Array.from(document.querySelectorAll('*')).filter(function(el) {
                    return el.children.length === 0 && el.textContent.trim().match(/^[0-9,]+\s+Points$/i) && !el.closest('.current-balance');
                });
                
                if (pointsElements.length > 0) {
                    var pointsEl = pointsElements[pointsElements.length - 1];
                    if (pointsEl.previousElementSibling) {
                        rewardName = pointsEl.previousElementSibling.textContent.trim();
                    } else if (pointsEl.parentElement && pointsEl.parentElement.previousElementSibling) {
                        rewardName = pointsEl.parentElement.previousElementSibling.textContent.trim();
                    }
                }
                
                var finalRewardName = window.__currentRewardName || rewardName;

                if (finalRewardName && finalRewardName.length > 0 && finalRewardName.toLowerCase().indexOf('redeem') === -1) {
                    if (id) {
                        var slug = finalRewardName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                        if (slug) {
                            // Update the URL using a query parameter to prevent 404s on refresh
                            var newUrl = '/rewards_preview/?reward=' + slug + '&id=' + id;
                            if (window.location.search.indexOf('reward=' + slug) === -1) {
                                window.history.replaceState(null, '', newUrl);
                            }
                        }
                    }
                }

                // 2. Add the voucher acknowledgement step for the providers that need it
                // Strictly rely on the Flexcore API to determine the reward category. No text fallback to prevent false positives!
                var requiresVoucherAck = window.__requiresVoucherAck === true;
                
                if (requiresVoucherAck) {
                    // The wording is produced in exactly one place (getVoucherAcknowledgement)
                    // from this reward's own voucherTermsText.
                    var ackCopy = getVoucherAcknowledgement(window.__voucherTermsText);
                    $('#voucher-ack-text').text(ackCopy.acknowledgement);
                    $('#voucher-ack-error-msg').text(ackCopy.error);
                    $('#voucher-ack-wrapper').show();

                    // Attach listener to hide error when checked
                    if (!$('#voucher-ack-checkbox').data('bound')) {
                        $('#voucher-ack-checkbox').data('bound', true).on('change', function() {
                            if (this.checked) {
                                $('#voucher-ack-error-msg').hide();
                            }
                        });
                    }
                } else {
                    $('#voucher-ack-wrapper').hide();
                }

                // 3. Attach capture-phase event listeners to REDEEM buttons to prevent React/Vue events from firing
                if (requiresVoucherAck) {
                    var redeemBtns = document.querySelectorAll('button, input[type="button"], input[type="submit"]');
                    redeemBtns.forEach(function(btn) {
                        var text = btn.innerText ? btn.innerText.trim().toUpperCase() : '';
                        var val = btn.value ? btn.value.trim().toUpperCase() : '';
                        
                        if (text === 'REDEEM' || val === 'REDEEM') {
                            if (!btn.hasAttribute('data-voucher-ack-bound')) {
                                btn.setAttribute('data-voucher-ack-bound', 'true');
                                btn.addEventListener('click', function(e) {
                                    var checkbox = document.getElementById('voucher-ack-checkbox');
                                    if (checkbox && !checkbox.checked) {
                                        e.preventDefault();
                                        e.stopPropagation(); // Stop propagation to React root
                                        document.getElementById('voucher-ack-error-msg').style.display = 'block';
                                    }
                                }, true); // Use capture phase to intercept before React/Vue
                            }
                        }
                    });
                }

                // 4. Provider-specific voucher terms.
                // The merchant list / exclusions carried by the template are UNIQGIFT's
                // branding, so they are only shown for UNIQGIFT rewards. The WOGI block
                // ships empty and reveals itself as soon as copy is added to it, so a
                // WOGI voucher shows nothing today and needs no code change later.
                if (window.__rewardCategory === 'uniqgift') {
                    $('#uniqgift-terms').show();
                } else {
                    $('#uniqgift-terms').hide();
                }
                var hasWogiTerms = $.trim($('#wogi-terms').text()) !== '';
                $('#wogi-terms').toggle(hasWogiTerms);
            } // END of pathname check
        }, 1000);
    });

    window.FlexcoreSpinner = (function () {
        var overlay = null;
        function ensure() {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'flexcore-loading-overlay';
                overlay.innerHTML = '<div class="flexcore-spinner"></div>';
                document.body.appendChild(overlay);
            }
            return overlay;
        }
        return {
            show: function () { ensure().style.display = 'flex'; },
            hide: function () { if (overlay) overlay.style.display = 'none'; }
        };
    })();
})(jQuery);
