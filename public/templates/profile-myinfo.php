<?php
/**
 * MyInfo Profile Update form — Singpass bind/unbind, immutable MyInfo fields, limited editable fields
 *
 * @package FlexCore_Server
 */
if (!defined('ABSPATH')) exit;

$step    = isset($_GET['step'])    ? sanitize_text_field($_GET['step'])    : '';
$status  = isset($_GET['status'])  ? sanitize_text_field($_GET['status'])  : '';
$flow_id = isset($_GET['flowId']) ? sanitize_text_field($_GET['flowId']) : '';
?>
<style>
    .myinfo-prefilled-notice { background: #E8F5E9; border: 1px solid #C8E6C9; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 14px; color: #2E7D32; display: none; }
    .myinfo-prefilled-notice.show { display: block; }
    .field-immutable { background: #f0f0f0 !important; color: #666 !important; cursor: not-allowed; border-color: #ddd !important; }
    .singpass-loading { display: none; text-align: center; padding: 24px; color: #666; }
    .singpass-loading.show { display: block; }
    .singpass-spinner { border: 3px solid #f3f3f3; border-top: 3px solid #CA0D07; border-radius: 50%; width: 32px; height: 32px; animation: flexcore-spin 0.8s linear infinite; margin: 0 auto 12px; }
    @keyframes flexcore-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .form-divider { text-align: center; margin: 20px 0; position: relative; color: #999; font-size: 13px; }
    .form-divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #eee; }
    .form-divider span { background: #fff; padding: 0 12px; position: relative; }
    .has-error { background-color: rgba(245,159,159,0.75) !important; border: 1px solid red !important; }
    .is-valid { background-color: #d4edda !important; }
    .field-error { color: red; font-size: 12px; margin-top: 4px; display: none; }
    .hd-my-profile { max-width: 800px; margin: 0 auto; }
    .hd-my-profile h2 { margin-bottom: 10px; }
    .hd-form-group { margin-bottom: 16px; }
    .hd-form-group label { display: block; font-weight: 600; margin-bottom: 4px; color: #333; }
    .hd-formfild { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
    select.hd-formfild { background: #fff; }

    /* MyInfo lightbox — shared with register flow */
    .myinfo-lightbox {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.6); z-index: 9999;
        align-items: center; justify-content: center;
    }
    .myinfo-lightbox.show { display: flex; }
    .myinfo-lightbox-content {
        background: #fff; border-radius: 16px;
        padding: 40px; max-width: 480px; width: 90%;
        text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .myinfo-lightbox-icon { font-size: 48px; margin-bottom: 16px; }
    .myinfo-lightbox h3 { margin: 0 0 12px; font-size: 22px; color: #1a1a1a; }
    .myinfo-lightbox p { color: #555; line-height: 1.6; margin: 0 0 24px; }
    .myinfo-lightbox .btn-primary {
        background: #CA0D07; color: #fff; border: none;
        border-radius: 8px; padding: 12px 32px;
        font-size: 16px; font-weight: 600; cursor: pointer;
    }

    /* Message styles — match signup form */
    .flexcore-message {
        padding: 12px 16px;
        border-radius: 8px;
        margin-top: 16px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .flexcore-message.success {
        background-color: #e6f9ec;
        color: #256029;
        border: 1px solid #8dd9a2;
        box-shadow: 0 0 6px rgba(0, 128, 0, 0.1);
    }
    .flexcore-message.error {
        background-color: #ffe6e6;
        color: #8b0000;
        border: 1px solid #ffaaaa;
        box-shadow: 0 0 6px rgba(255, 0, 0, 0.1);
    }
</style>

<input type="hidden" id="myinfo_flow_id" value="<?php echo esc_attr($flow_id); ?>">
<input type="hidden" id="myinfo_step"    value="<?php echo esc_attr($step); ?>">
<input type="hidden" id="myinfo_status"  value="<?php echo esc_attr($status); ?>">

<!-- UUID conflict lightbox -->
<div class="myinfo-lightbox" id="myinfo-conflict-lightbox">
    <div class="myinfo-lightbox-content">
        <div class="myinfo-lightbox-icon">⚠️</div>
        <h3>Singpass Already Linked</h3>
        <p id="myinfo-conflict-reason">
            This SingPass ID is already linked to another account. Please contact support.
        </p>
        <button class="btn-primary" onclick="document.getElementById('myinfo-conflict-lightbox').classList.remove('show');">
            OK
        </button>
    </div>
</div>

<div class="singpass-loading" id="singpass-loading">
    <div class="singpass-spinner"></div><p>Redirecting to Singpass...</p>
</div>

<div class="hd-my-profile">
    <h2>PROFILE DETAILS</h2>

    <!-- Promo message (hidden by default, shown if singpassPointFlag !== '1') -->
    <div id="myinfo-promo" style="display:none; background:#FFF3CD; border:1px solid #FFECB5; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#856404; font-size:14px;">
        Verify with Singpass today and be awarded with 50 points immediately!
    </div>

    <!-- Singpass button -->
    <div id="myinfo-buttons" style="display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
        <button type="button" id="btn-retrieve-myinfo" style="background:none;border:none;padding:0;cursor:pointer;">
            <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__, 2)) . 'public/images/singpass-button.png'); ?>" alt="Retrieve Myinfo with Singpass" style="height:48px;width:auto;">
        </button>
    </div>

    <div class="myinfo-prefilled-notice" id="myinfo-prefilled-notice">
        Your details have been verified via Singpass MyInfo. Locked fields cannot be changed.
    </div>

    <div class="form-divider"><span>Info retrieved from MyInfo</span></div>

    <form id="flexcore-profile-myinfo-form" novalidate>
        <input type="hidden" id="register_nonce" value="<?php echo wp_create_nonce('flexcore_register'); ?>">

        <!-- IMMUTABLE: Full Name -->
        <div class="hd-form-group">
            <label>Full Name (As per NRIC) <span style="color:red">*</span></label>
            <input class="hd-formfild field-immutable" type="text" id="name" readonly>
        </div>

        <!-- IMMUTABLE: DOB | Citizenship -->
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Date of Birth <span style="color:red">*</span></label>
                <input class="hd-formfild field-immutable" type="text" id="dob" readonly>
            </div>
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Citizenship <span style="color:red">*</span></label>
                <input class="hd-formfild field-immutable" type="text" id="citizenship_display" readonly>
            </div>
        </div>

        <!-- IMMUTABLE: Gender | Marital Status -->
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Sex <span style="color:red">*</span></label>
                <input class="hd-formfild field-immutable" type="text" id="gender_display" readonly>
            </div>
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Marital Status <span style="color:red">*</span></label>
                <input class="hd-formfild field-immutable" type="text" id="marital_status_display" readonly>
            </div>
        </div>

        <!-- IMMUTABLE: Race | RaceSpecify -->
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Race <span style="color:red">*</span></label>
                <input class="hd-formfild field-immutable" type="text" id="race_display" readonly>
            </div>
            <div class="hd-form-group" id="othersRaceGroup" style="flex:1; min-width:200px; display:none;">
                <label>Please Specify</label>
                <input class="hd-formfild field-immutable" type="text" id="others_display" readonly>
            </div>
        </div>

        <div class="form-divider"><span>Editable Details</span></div>

        <!-- Editable: Preferred Name -->
        <div class="hd-form-group">
            <label>Preferred Name <span style="color:red">*</span></label>
            <input class="hd-formfild" type="text" id="preferred_name" name="preferred_name" required maxlength="100" placeholder="What should we call you?">
        </div>

        <!-- Editable: Mobile No. | Postal Code -->
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Mobile No. <span style="color:red">*</span></label>
                <input class="hd-formfild" type="tel" id="mobile" name="mobile" required maxlength="8" pattern="[89][0-9]{7}" placeholder="Phone Number" title="8-digit Singapore mobile number starting with 8 or 9">
                <div class="field-error mobileNo-error"></div>
            </div>
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Postal Code <span style="color:red">*</span></label>
                <input class="hd-formfild" type="text" id="postal_code" name="postal_code" required maxlength="6" pattern="^\d{6}$" placeholder="Postal Code">
                <div class="field-error postal-error"></div>
            </div>
        </div>

        <!-- Consent -->
        <div class="hd-condition-read" style="margin-top:20px;">
            <input class="hd-checkbox" type="checkbox" id="consent" name="consent" required>
            <label for="consent">I hereby confirm that the particulars I have provided are accurate and complete and I consent to the utilization of my personal data for HappyDot.sg and services related to it.</label>
        </div>

        <div class="hd-form-btn" style="margin-top:20px;">
            <button type="submit" class="button button-primary hd-btn" id="submit-btn">Save</button>
        </div>

    </form>
    <div id="profile-message" class="flexcore-message" style="display:none;"></div>
</div>

<script>
(function($) {
    'use strict';

    var apiBase = 'https://staging.flexcore.theadventus.com/api/v1';

    /**
     * Pre-fill the MyInfo immutable fields on the form from mapped fields.
     * Mirrors FlexcoreRegisterMyinfo.applyPrefillData in register-myinfo.php.
     */
    function applyMyInfoPrefill(fields) {
        // Name
        if (fields.name) $('#name').val(fields.name);

        // DOB (YYYY-MM-DD → DD/MM/YYYY)
        if (fields.dateOfBirth && /^\d{4}-\d{2}-\d{2}$/.test(fields.dateOfBirth)) {
            var parts = fields.dateOfBirth.split('-');
            $('#dob').val(parts[2] + '/' + parts[1] + '/' + parts[0]);
        }

        // Citizenship
        var citizenshipMap = { 'SG': 'Singapore Citizen', 'SINGAPORE': 'Singapore Citizen', 'PR': 'Permanent Resident' };
        $('#citizenship_display').val(citizenshipMap[fields.nationality] || '');

        // Gender
        var genderMap = { 'M': 'Male', 'F': 'Female', 'male': 'Male', 'female': 'Female' };
        $('#gender_display').val(genderMap[fields.sex] || fields.sex || '');

        // Race
        var raceMap = { 'CHINESE': 'Chinese', 'MALAY': 'Malay', 'INDIAN': 'Indian', 'EURASIAN': 'Eurasian', 'OTHERS': 'Others' };
        var raceDisplay = raceMap[fields.race?.toUpperCase()] || fields.raceRaw || fields.race;
        $('#race_display').val(raceDisplay);

        // Marital status
        if (fields.maritalStatus) {
            var ms = fields.maritalStatus;
            $('#marital_status_display').val(ms.charAt(0).toUpperCase() + ms.slice(1));
        }
    }

    function loadProfileData() {
        $.ajax({
            url: flexcoreServerAjax.ajaxUrl,
            type: 'POST',
            data: { action: 'flexcore_get_profile', nonce: flexcoreServerAjax.profileNonce },
            success: function(res) {
                if (!res.success || !res.data) return;
                var d = res.data, meta = d.metaData || {};

                // Immutable display fields
                $('#name').val(d.fullName || '');
                if (meta.dateOfBirth) {
                    var parts = meta.dateOfBirth.split('-');
                    $('#dob').val(parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : meta.dateOfBirth);
                }
                $('#citizenship_display').val(meta.citizenship === 'singaporecitizen' ? 'Singapore Citizen' : meta.citizenship === 'permanentResident' ? 'Permanent Resident' : '');
                $('#gender_display').val(meta.gender === 'male' ? 'Male' : meta.gender === 'female' ? 'Female' : meta.gender === 'others' ? 'Others' : '');
                $('#marital_status_display').val((meta.maritalStatus || '').charAt(0).toUpperCase() + (meta.maritalStatus || '').slice(1));
                $('#race_display').val((meta.race || '').charAt(0).toUpperCase() + (meta.race || '').slice(1));
                if (meta.race === 'others') {
                    $('#othersRaceGroup').show();
                    $('#others_display').val(meta.raceDetails || '');
                }

                // Editable fields
                $('#mobile').val(meta.mobileNumber || '');
                $('#postal_code').val(meta.postalCode || '');
                $('#preferred_name').val(meta.preferredName || '');

                // MyInfo verified notice — show if user has Singpass UUID
                if (meta.myInfoSubject) {
                    $('#myinfo-prefilled-notice').addClass('show');
                }

                // Promo message — show if point flag is 2 or null
                if (d.singpassPointFlag === '2' || !d.singpassPointFlag) {
                    $('#myinfo-promo').show();
                } else {
                    $('#myinfo-promo').hide();
                }
            }
        });
    }

    function bindEvents() {
        // Live validation — mobile number
        $('#mobile').on('input change', function() {
            var val = $(this).val().trim();
            var clean = val.replace(/\D/g, '');
            var isValid = false;
            if (val.startsWith('+')) {
                isValid = /^\+65[89]\d{7}$/.test(val);
            } else {
                isValid = /^[89]\d{7}$/.test(val);
            }
            if (isValid) {
                $(this).removeClass('has-error').addClass('is-valid');
                $('.mobileNo-error').hide();
            } else if (val.length > 0) {
                $(this).addClass('has-error').removeClass('is-valid');
                $('.mobileNo-error').text('Please enter a valid Singapore mobile number starting with 8 or 9.').show();
            } else {
                $(this).removeClass('has-error is-valid');
                $('.mobileNo-error').hide();
            }
        });

        // Live validation — postal code (format + API)
        $('#postal_code').on('input', function() {
            var val = $(this).val().trim();
            if (!val) {
                $(this).removeClass('has-error is-valid');
                $('.postal-error').hide();
            } else if (!/^\d{6}$/.test(val)) {
                $(this).addClass('has-error').removeClass('is-valid');
                $('.postal-error').text('Postal code must be exactly 6 digits.').show();
            } else {
                // Validate via OneMap API
                var el = $(this);
                var postal5D = val.substring(0, 5);
                $.ajax({
                    url: flexcoreServerAjax.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'flexcore_postalcode_validation',
                        register_nonce: $('#register_nonce').val(),
                        postal_code: postal5D
                    },
                    success: function(result) {
                        if (result.data && result.data.response && result.data.response.found > 0
                            && result.data.response.results[0].POSTAL !== 'NIL') {
                            el.removeClass('has-error').addClass('is-valid');
                            $('.postal-error').hide();
                        } else {
                            el.addClass('has-error').removeClass('is-valid');
                            $('.postal-error').text('Invalid postal code. Please enter a valid postal code.').show();
                        }
                    },
                    error: function() {
                        // API unavailable — allow format-only validation to pass
                        el.removeClass('has-error').addClass('is-valid');
                        $('.postal-error').hide();
                    }
                });
            }
        });

        // Submit profile update
        $('#flexcore-profile-myinfo-form').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#submit-btn'), msg = $('#profile-message');
            btn.prop('disabled', true); msg.hide();

            var mobileVal = $('#mobile').val().trim();

            // Client-side validation
            var mobileEl = $('#mobile');
            if (!/^[89]\d{7}$/.test(mobileVal) && !/^\+65[89]\d{7}$/.test(mobileVal)) {
                mobileEl.addClass('has-error');
                $('.mobileNo-error').text('Please enter a valid Singapore mobile number starting with 8 or 9.').show();
                msg.removeClass('success').addClass('error').html('Please fix the errors below.').show();
                btn.prop('disabled', false);
                return;
            }
            var postalVal = $('#postal_code').val().trim();
            if (!/^\d{6}$/.test(postalVal)) {
                $('#postal_code').addClass('has-error');
                $('.postal-error').text('Postal code must be exactly 6 digits.').show();
                msg.removeClass('success').addClass('error').html('Please fix the errors below.').show();
                btn.prop('disabled', false);
                return;
            }
            if (mobileVal.startsWith('+65')) mobileVal = mobileVal.substring(3);

            var formData = {
                action: 'flexcore_update_profile',
                nonce: flexcoreServerAjax.updateProfileNonce,
                mobileNumber: mobileVal,
                postalCode: $('#postal_code').val(),
                preferredName: $('#preferred_name').val(),
                redirect_to_dashboard: false
            };

            // If a MyInfo flow is pending, bind it on save
            var flowId = $('#myinfo_flow_id').val();
            if (flowId) {
                formData.myInfoFlowId = flowId;
            }

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(res) {
                    if (res.success) {
                        $('#myinfo_flow_id').val('');
                        var pointsMsg = flowId ? ' 50 points awarded!' : '';
                        var msgText = 'Profile updated successfully!' + pointsMsg;
                        msg.removeClass('error').addClass('success').html(msgText).show();
                        loadProfileData();
                    } else {
                        var errMsg = (res.data && res.data.message) || (res.data && res.data.details) || 'Update failed';
                        msg.removeClass('success').addClass('error').html(errMsg).show();
                    }
                },
                error: function(xhr) {
                    var errText = 'An error occurred.';
                    try { var r = JSON.parse(xhr.responseText); if (r.data && r.data.message) errText = r.data.message; } catch(e) {}
                    msg.removeClass('success').addClass('error').html(errText).show();
                },
                complete: function() { btn.prop('disabled', false); }
            });
        });

        // Retrieve with Singpass button
        $('#btn-retrieve-myinfo').on('click', function() {
            window.location.href = apiBase + '/auth/myinfo/start?returnTo=' + encodeURIComponent(window.location.pathname + '?step=callback');
        });
    }

    $(function() { bindEvents(); loadProfileData(); handleMyInfoCallback(); });

    // MyInfo callback — strip params immediately, data comes from loadProfileData
    function handleMyInfoCallback() {
        var step = $('#myinfo_step').val(), status = $('#myinfo_status').val(), flowId = $('#myinfo_flow_id').val();
        if (step !== 'callback') return;
        $('#btn-retrieve-myinfo').hide();

        // Just strip callback params and reload — profile data is already loaded from DB.
        // The flowId is kept in the hidden field so Save can bind it if needed.
        var url = new URL(window.location.href);
        url.searchParams.delete('step');
        url.searchParams.delete('status');
        window.history.replaceState({}, '', url.toString());
    }
})(jQuery);
</script>
