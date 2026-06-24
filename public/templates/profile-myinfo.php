<?php
/**
 * MyInfo Profile Update form — with Singpass bind/unbind, immutable & lockable fields
 *
 * @package FlexCore_Server
 */
if (!defined('ABSPATH')) exit;

$step    = isset($_GET['step'])    ? sanitize_text_field($_GET['step'])    : '';
$status  = isset($_GET['status'])  ? sanitize_text_field($_GET['status'])  : '';
$flow_id = isset($_GET['flowId']) ? sanitize_text_field($_GET['flowId']) : '';
?>
<style>
    .singpass-retrieve-btn {
        display: inline-flex; align-items: center; gap: 10px;
        background: #CA0D07; color: #fff; border: none; border-radius: 8px;
        padding: 12px 24px; font-size: 15px; font-weight: 600; cursor: pointer;
        margin-bottom: 20px; transition: background 0.2s;
    }
    .singpass-retrieve-btn:hover { background: #a30b05; }

    .unbind-btn {
        background: #666; color: #fff; border: none; border-radius: 6px;
        padding: 8px 18px; font-size: 13px; cursor: pointer; margin-left: 12px;
    }
    .unbind-btn:hover { background: #444; }

    .myinfo-prefilled-notice {
        background: #E8F5E9; border: 1px solid #C8E6C9;
        border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
        font-size: 14px; color: #2E7D32; display: none;
    }
    .myinfo-prefilled-notice.show { display: block; }

    .field-locked[readonly], .field-locked[disabled], .field-immutable {
        background: #f0f0f0 !important; color: #666 !important;
        cursor: not-allowed; border-color: #ddd !important;
    }

    .singpass-loading { display: none; text-align: center; padding: 24px; color: #666; }
    .singpass-loading.show { display: block; }
    .singpass-spinner {
        border: 3px solid #f3f3f3; border-top: 3px solid #CA0D07;
        border-radius: 50%; width: 32px; height: 32px;
        animation: flexcore-spin 0.8s linear infinite; margin: 0 auto 12px;
    }
    @keyframes flexcore-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .form-divider {
        text-align: center; margin: 20px 0; position: relative; color: #999; font-size: 13px;
    }
    .form-divider::before {
        content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #eee;
    }
    .form-divider span { background: #fff; padding: 0 12px; position: relative; }

    .has-error { background-color: rgba(245,159,159,0.75) !important; border: 1px solid red !important; }
    .is-valid { background-color: #d4edda !important; }
    .field-error { color: red; font-size: 12px; margin-top: 4px; display: none; }

    .hd-my-profile { max-width: 800px; margin: 0 auto; }
    .hd-my-profile h2 { margin-bottom: 10px; }
</style>

<input type="hidden" id="myinfo_flow_id" value="<?php echo esc_attr($flow_id); ?>">
<input type="hidden" id="myinfo_step"    value="<?php echo esc_attr($step); ?>">
<input type="hidden" id="myinfo_status"  value="<?php echo esc_attr($status); ?>">

<div class="singpass-loading" id="singpass-loading">
    <div class="singpass-spinner"></div><p>Redirecting to Singpass...</p>
</div>

<div class="hd-my-profile">
    <h2>PROFILE DETAILS</h2>

    <!-- Promo message (hidden by default, shown if singpassPointFlag !== '1') -->
    <div id="myinfo-promo" style="display:none; background:#FFF3CD; border:1px solid #FFECB5; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#856404; font-size:14px;">
        🎁 Verify with Singpass today and be awarded with 50 points immediately!
    </div>

    <!-- Singpass + Unbind buttons -->
    <div id="myinfo-buttons" style="display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
        <button type="button" id="btn-retrieve-myinfo" style="background:none;border:none;padding:0;cursor:pointer;">
            <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__, 2)) . 'public/images/singpass-button.png'); ?>" alt="Retrieve Myinfo with Singpass" style="height:48px;width:auto;">
        </button>
        <button type="button" id="btn-unbind-myinfo" class="unbind-btn" style="display:none;">Unlink MyInfo</button>
    </div>

    <div class="myinfo-prefilled-notice" id="myinfo-prefilled-notice">
        ✅ Your details have been verified via Singpass MyInfo. Locked fields cannot be changed.
    </div>

    <div class="form-divider"><span>Info retrieved from MyInfo</span></div>

    <form id="flexcore-profile-myinfo-form" novalidate>
        <input type="hidden" id="profile_nonce" value="<?php echo wp_create_nonce('flexcore_register'); ?>">

        <!-- IMMUTABLE: Email -->
        <div class="hd-form-group">
            <label>Email <span style="color:red">*</span></label>
            <input class="hd-formfild field-immutable" type="email" id="email" readonly>
            <small>Email cannot be changed</small>
        </div>

        <!-- IMMUTABLE: Full Name -->
        <div class="hd-form-group">
            <label>Full Name (As per NRIC) <span style="color:red">*</span></label>
            <input class="hd-formfild field-immutable" type="text" id="name" readonly>
        </div>

        <!-- IMMUTABLE: DOB -->
        <div class="hd-form-group">
            <label>Date of Birth <span style="color:red">*</span></label>
            <input class="hd-formfild field-immutable" type="text" id="dob" readonly>
        </div>

        <!-- IMMUTABLE: Citizenship -->
        <div class="hd-form-group">
            <label>Citizenship <span style="color:red">*</span></label>
            <input class="hd-formfild field-immutable" type="text" id="citizenship_display" readonly>
            <input type="hidden" id="citizenship" name="citizenship">
        </div>

        <div class="row" style="display:flex; gap:16px; flex-wrap:wrap;">
            <!-- MyInfo-lockable: Sex -->
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Sex <span style="color:red">*</span></label>
                <select class="hd-formfild" id="gender" name="gender" required>
                    <option value="">Select sex</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="others">Others</option>
                </select>
            </div>

            <!-- Editable: Marital Status -->
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Marital Status <span style="color:red">*</span></label>
                <select class="hd-formfild" id="marital_status" name="marital_status" required>
                    <option value="">Select</option>
                    <option value="single">Single</option>
                    <option value="soontobemarried">Soon to be Married</option>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="separated">Separated</option>
                    <option value="widowed">Widowed</option>
                </select>
            </div>
        </div>

        <div class="row" style="display:flex; gap:16px; flex-wrap:wrap;">
            <!-- MyInfo-lockable: Race -->
            <div class="hd-form-group" style="flex:1; min-width:200px;">
                <label>Race <span style="color:red">*</span></label>
                <select class="hd-formfild" id="race" name="race" required>
                    <option value="">Select race</option>
                    <option value="chinese">Chinese</option>
                    <option value="malay">Malay</option>
                    <option value="indian">Indian</option>
                    <option value="eurasian">Eurasian</option>
                    <option value="others">Others</option>
                </select>
            </div>

            <!-- MyInfo-lockable: Specify Race -->
            <div class="hd-form-group" id="othersRaceGroup" style="flex:1; min-width:200px; display:none;">
                <label>Please Specify <span style="color:red">*</span></label>
                <input class="hd-formfild" type="text" id="others" name="others" maxlength="100">
            </div>
        </div>

        <!-- Editable: Postal Code -->
        <div class="hd-form-group">
            <label>Postal Code <span style="color:red">*</span></label>
            <input class="hd-formfild" type="text" id="postal_code" name="postal_code" required maxlength="6" pattern="^\d{6}$" placeholder="Postal Code">
            <div class="field-error postal-error"></div>
        </div>

        <!-- Editable: Preferred Name -->
        <div class="hd-form-group">
            <label>Preferred Name <span style="color:red">*</span></label>
            <input class="hd-formfild" type="text" id="preferred_name" name="preferred_name" required maxlength="100" placeholder="What should we call you?">
        </div>

        <!-- Editable: Mobile No -->
        <div class="hd-form-group">
            <label>Mobile No. <span style="color:red">*</span></label>
            <input class="hd-formfild" type="tel" id="mobile" name="mobile" required maxlength="8" pattern="[89][0-9]{7}" placeholder="Phone Number" title="8-digit Singapore mobile number starting with 8 or 9">
            <div class="field-error mobileNo-error"></div>
        </div>

        <!-- Consent -->
        <div class="hd-condition-read" style="margin-top:20px;">
            <input class="hd-checkbox" type="checkbox" id="consent" name="consent" required>
            <label for="consent">I hereby confirm that the particulars I have provided are accurate and complete and I consent to the utilization of my personal data for HappyDot.sg and services related to it.</label>
        </div>

        <div class="hd-form-btn" style="margin-top:20px;">
            <button type="submit" class="button button-primary hd-btn" id="submit-btn">Save</button>
        </div>

        <div id="profile-message" class="flexcore-message" style="display:none;"></div>
    </form>
</div>

<script>
(function($) {
    'use strict';

    var apiBase = (window.flexcoreServerAjax && window.flexcoreServerAjax.myinfoApiBase)
                 || 'https://staging.flexcore.theadventus.com/api/v1';

    // Load profile data and configure UI
    function loadProfileData() {
        $.ajax({
            url: flexcoreServerAjax.ajaxUrl,
            type: 'POST',
            data: { action: 'flexcore_get_profile', nonce: flexcoreServerAjax.profileNonce },
            success: function(res) {
                if (!res.success || !res.data) return;
                var d = res.data;
                var meta = d.metaData || {};

                // Immutable fields
                $('#email').val(d.email || '');
                $('#name').val(d.name || '');
                $('#citizenship_display').val(meta.citizenship === 'singaporecitizen' ? 'Singapore Citizen' : meta.citizenship === 'permanentResident' ? 'Permanent Resident' : '');
                $('#citizenship').val(meta.citizenship || '');

                // DOB format
                if (meta.dateOfBirth) {
                    var parts = meta.dateOfBirth.split('-');
                    if (parts.length === 3) {
                        $('#dob').val(parts[2] + '/' + parts[1] + '/' + parts[0]);
                    } else {
                        $('#dob').val(meta.dateOfBirth);
                    }
                }

                // Editable fields
                $('#mobile').val(meta.mobileNumber || '');
                $('#postal_code').val(meta.postalCode || '');
                $('#preferred_name').val(meta.preferredName || '');
                $('#gender').val(meta.gender || '');
                $('#race').val(meta.race || '');
                $('#marital_status').val(meta.maritalStatus || '');
                if (meta.race === 'others') {
                    $('#othersRaceGroup').show();
                    $('#others').val(meta.raceDetails || '');
                }

                // MyInfo locked state
                if (meta.myInfoSubject) {
                    lockMyInfoFields(true);
                    $('#btn-unbind-myinfo').show();
                    $('#myinfo-promo').hide();
                } else {
                    lockMyInfoFields(false);
                    $('#btn-unbind-myinfo').hide();
                    // Show promo if singpassPointFlag !== '1'
                    if (meta.singpassPointFlag !== '1') {
                        $('#myinfo-promo').show();
                    }
                }
            }
        });
    }

    function lockMyInfoFields(locked) {
        var fields = ['#gender', '#race'];
        fields.forEach(function(sel) {
            var $el = $(sel);
            if (locked) {
                $el.prop('disabled', true).addClass('field-locked');
            } else {
                $el.prop('disabled', false).removeClass('field-locked');
            }
        });
        if (locked) {
            $('#others').prop('readonly', true).addClass('field-locked');
        } else {
            $('#others').prop('readonly', false).removeClass('field-locked');
        }
    }

    // Race → toggle specify
    $('#race').on('change', function() {
        if ($(this).val() === 'others') {
            $('#othersRaceGroup').show();
        } else {
            $('#othersRaceGroup').hide();
            $('#others').val('');
        }
    });

    // Unbind MyInfo
    $('#btn-unbind-myinfo').on('click', function() {
        if (!confirm('Are you sure you want to unlink Singpass MyInfo from your account? This will unlock your verified fields.')) return;
        $.ajax({
            url: apiBase + '/auth/myinfo/unbind',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + (window._flexcoreToken || '') },
            success: function() {
                lockMyInfoFields(false);
                $('#btn-unbind-myinfo').hide();
                $('#myinfo-promo').show();
                $('#myinfo-prefilled-notice').hide();
                alert('MyInfo unlinked successfully.');
            },
            error: function(xhr) {
                alert('Failed to unlink: ' + (xhr.responseJSON?.error || 'Unknown error'));
            }
        });
    });

    // Submit
    $('#flexcore-profile-myinfo-form').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#submit-btn');
        var msg = $('#profile-message');
        btn.prop('disabled', true);
        msg.hide();

        var mobileVal = $('#mobile').val().trim();
        if (mobileVal.startsWith('+65')) mobileVal = mobileVal.substring(3);

        // Convert DOB from DD/MM/YYYY to YYYY-MM-DD for API
        var dobVal = $('#dob').val();
        var dobParts = dobVal.split('/');
        var dobApi = dobParts.length === 3 ? dobParts[2] + '-' + dobParts[1] + '-' + dobParts[0] : dobVal;

        $.ajax({
            url: flexcoreServerAjax.ajaxUrl,
            type: 'POST',
            data: {
                action: 'flexcore_update_profile',
                nonce: flexcoreServerAjax.updateProfileNonce,
                dateOfBirth: dobApi,
                mobileNumber: mobileVal,
                citizenship: $('#citizenship').val(),
                postalCode: $('#postal_code').val(),
                maritalStatus: $('#marital_status').val(),
                preferredName: $('#preferred_name').val(),
                gender: $('#gender').val(),
                race: $('#race').val(),
                raceDetails: $('#others').val() || '',
                redirect_to_dashboard: false
            },
            success: function(res) {
                if (res.success) {
                    msg.removeClass('error').addClass('success').html('Profile updated successfully.').show();
                } else {
                    msg.removeClass('success').addClass('error').html(res.data?.message || 'Update failed').show();
                }
            },
            error: function() {
                msg.removeClass('success').addClass('error').html('An error occurred.').show();
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // MyInfo callback
    function handleMyInfoCallback() {
        var step = $('#myinfo_step').val();
        var status = $('#myinfo_status').val();
        var flowId = $('#myinfo_flow_id').val();

        if (step !== 'callback') return;

        $('#btn-retrieve-myinfo').hide();

        if (status === 'ineligible') {
            alert('Only Singapore Citizens and Permanent Residents can verify via Singpass MyInfo.');
        } else if (status === 'existing_user') {
            alert('This Singpass is already linked to an existing account. Please contact support.');
        } else if (status === 'new_user' && flowId) {
            $.ajax({
                url: apiBase + '/auth/myinfo/complete-profile',
                type: 'POST',
                data: JSON.stringify({ flowId: flowId }),
                contentType: 'application/json',
                success: function() {
                    alert('Profile verified via Singpass MyInfo! 50 points awarded.');
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('MyInfo verification failed: ' + (xhr.responseJSON?.error || 'Unknown error'));
                }
            });
        }
    }

    // Singpass button click
    $('#btn-retrieve-myinfo').on('click', function() {
        window.location.href = apiBase + '/auth/myinfo/start?returnTo=' + encodeURIComponent(window.location.pathname + '?step=callback');
    });

    $(function() {
        loadProfileData();
        handleMyInfoCallback();
    });

})(jQuery);
</script>
