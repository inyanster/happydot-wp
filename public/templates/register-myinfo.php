<?php
/**
 * Register with Singpass MyInfo form template
 *
 * UX flow:
 *  - Form is ALWAYS visible on page load
 *  - "Retrieve with Singpass MyInfo" button at top of form
 *  - If user clicks it: redirects to MyInfo → returns → form prefilled + locked
 *  - If user skips it: full manual registration
 *
 * URL params on callback:
 *   step=callback&status=new_user&flowId=UUID   → pre-fill form with MyInfo fields
 *   step=callback&status=ineligible             → show ineligibility lightbox, manual form
 *   step=callback&status=existing_user          → show "account exists" notice
 *
 * @package FlexCore_Server
 */
if (!defined('ABSPATH')) {
    exit;
}

$step    = isset($_GET['step'])    ? sanitize_text_field($_GET['step'])    : '';
$status  = isset($_GET['status'])  ? sanitize_text_field($_GET['status'])  : '';
$flow_id = isset($_GET['flowId']) ? sanitize_text_field($_GET['flowId']) : '';
?>
<style>
    /* Singpass retrieve button */
    .singpass-retrieve-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #CA0D07;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        margin-bottom: 20px;
        transition: background 0.2s;
    }
    .singpass-retrieve-btn:hover { background: #a30b05; color: #fff; }
    .singpass-retrieve-btn svg { width: 22px; height: 22px; flex-shrink: 0; }

    /* Ineligibility lightbox */
    .myinfo-lightbox {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
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
    .myinfo-lightbox .btn-secondary {
        background: transparent; color: #666;
        border: 1px solid #ccc; border-radius: 8px;
        padding: 12px 24px; font-size: 15px; cursor: pointer; margin-left: 12px;
    }

    /* Existing user notice */
    .myinfo-existing-notice {
        background: #FFF3CD; border: 1px solid #FFECB5;
        border-radius: 8px; padding: 16px; margin-bottom: 24px;
        color: #856404; font-size: 14px;
    }

    /* Pre-filled notice */
    .myinfo-prefilled-notice {
        background: #E8F5E9; border: 1px solid #C8E6C9;
        border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
        font-size: 14px; color: #2E7D32; display: none;
    }
    .myinfo-prefilled-notice.show { display: block; }

    /* Locked field styling */
    .myinfo-locked[readonly] {
        background: #f0f0f0 !important;
        color: #666 !important;
        cursor: not-allowed;
        border-color: #ddd !important;
    }
    .myinfo-locked[disabled] {
        background: #f0f0f0 !important;
        color: #666 !important;
        cursor: not-allowed;
        border-color: #ddd !important;
    }
    .myinfo-locked-badge {
        display: none;
        font-size: 11px; color: #888; margin-top: 4px;
    }
    .myinfo-locked-badge.show { display: block; }

    /* Singpass loading */
    .singpass-loading {
        display: none; text-align: center; padding: 24px; color: #666;
    }
    .singpass-loading.show { display: block; }
    .singpass-spinner {
        border: 3px solid #f3f3f3; border-top: 3px solid #CA0D07;
        border-radius: 50%; width: 32px; height: 32px;
        animation: flexcore-spin 0.8s linear infinite;
        margin: 0 auto 12px;
    }
    @keyframes flexcore-spin {
        0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); }
    }

    /* Section divider */
    .form-divider {
        text-align: center; margin: 20px 0;
        position: relative; color: #999; font-size: 13px;
    }
    .form-divider::before {
        content: ''; position: absolute; top: 50%; left: 0; right: 0;
        height: 1px; background: #eee;
    }
    .form-divider span {
        background: #fff; padding: 0 12px; position: relative;
    }
</style>

<!-- Ineligibility lightbox -->
<div class="myinfo-lightbox" id="myinfo-ineligible-lightbox">
    <div class="myinfo-lightbox-content">
        <div class="myinfo-lightbox-icon">🔒</div>
        <h3>Unable to use Singpass</h3>
        <p id="myinfo-ineligible-reason">
            Unfortunately, you are not eligible to register using Singpass MyInfo at this time.
        </p>
        <button class="btn-primary" onclick="FlexcoreRegisterMyinfo.closeIneligibleLightbox()">
            Continue with manual registration
        </button>
    </div>
</div>

<!-- Existing user notice -->
<div class="myinfo-existing-notice" id="myinfo-existing-notice" style="display:none;">
    <strong>You already have an account.</strong> This Singpass is already linked to an existing Happydot account.
    Please <a href="#" id="myinfo-login-link">log in</a> with your email/password instead.
</div>

<!-- Pre-filled notice -->
<div class="myinfo-prefilled-notice" id="myinfo-prefilled-notice">
    ✅ Your details have been verified via Singpass MyInfo. Locked fields cannot be changed.
</div>

<!-- Loading state -->
<div class="singpass-loading" id="singpass-loading">
    <div class="singpass-spinner"></div>
    <p>Redirecting to Singpass...</p>
</div>

<!-- Hidden state from URL params -->
<input type="hidden" id="myinfo_flow_id" value="<?php echo esc_attr($flow_id); ?>">
<input type="hidden" id="myinfo_step"    value="<?php echo esc_attr($step); ?>">
<input type="hidden" id="myinfo_status"  value="<?php echo esc_attr($status); ?>">

<!-- Hidden flow ID for MyInfo pre-filled state -->
<input type="hidden" id="myinfo_flow_id" value="<?php echo esc_attr($flow_id); ?>">

<!-- Always-visible registration form -->
<div id="singpass-form-section">
    <!-- Retrieve with Singpass button + promo text -->
    <div class="myinfo-top-row" style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px; flex-wrap: wrap;">
        <button type="button" id="btn-retrieve-myinfo" onclick="FlexcoreRegisterMyinfo.startMyInfo(); return false;" style="background:none;border:none;padding:0;cursor:pointer;">
            <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__, 2)) . 'public/images/singpass-button.png'); ?>" alt="Retrieve Myinfo with Singpass" style="height:48px;width:auto;">
        </button>
        <span class="myinfo-promo-text" style="color: #1a56db; font-style: italic; font-size: 15px; line-height: 1.5; max-width: 480px;">
            Get your profile verified by Singpass and be awarded with extra 100 HappyPoints and start doing surveys immediately!
        </span>
    </div>

    <?php
    // Load the merged registration form
    echo FlexCore_Server_Template_Loader::load_template('margedRegistration');
    ?>
</div>

<script>
(function($) {
    'use strict';

    window.FlexcoreRegisterMyinfo = {
        // MyInfo endpoints live on the Node backend (staging.flexcore.theadventus.com),
        // NOT on WP admin-ajax. The flexcoreServerAjax.ajax_url is the WP REST proxy URL,
        // which would 404 for /auth/myinfo/* routes.
        apiBase: (window.flexcoreServerAjax && window.flexcoreServerAjax.myinfoApiBase)
                 || 'https://staging.flexcore.theadventus.com/api/v1',

        startMyInfo: function() {
            $('#btn-retrieve-myinfo').hide();
            $('#singpass-loading').addClass('show');
            window.location.href = this.apiBase + '/auth/myinfo/start?returnTo='
                + encodeURIComponent(window.location.origin + window.location.pathname + '?step=callback');
        },

        closeIneligibleLightbox: function() {
            $('#myinfo-ineligible-lightbox').removeClass('show');
        },

        init: function() {
            var step   = $('#myinfo_step').val();
            var status = $('#myinfo_status').val();
            var flowId = $('#myinfo_flow_id').val();

            // On callback from MyInfo flow
            if (step === 'callback') {
                this.handleCallback(status, flowId);
            }
        },

        handleCallback: function(status, flowId) {
            // Hide the retrieve button once we've come back from MyInfo
            $('#btn-retrieve-myinfo').hide();

            if (status === 'ineligible') {
                $('#myinfo-ineligible-reason').text(
                    'Unfortunately, only Singapore Citizens and Permanent Residents can sign up via Singpass MyInfo. Please fill in manually below.'
                );
                $('#myinfo-ineligible-lightbox').addClass('show');
                // Show the manual form section (already visible)
            } else if (status === 'existing_user') {
                $('#myinfo-existing-notice').show();
                var loginUrl = window.flexcoreServerAjax?.loginUrl || '/flexcore-login';
                $('#myinfo-login-link').attr('href', loginUrl);
            } else if (status === 'new_user' && flowId) {
                // Show the MyInfo section header
                $('#myinfo-section-header').show();
                // Mark as pre-filled from MyInfo
                $('#myinfo-prefilled-notice').addClass('show');
                this.lockMyInfoFields();
                this.fetchPrefillData(flowId);
            }
            // For any other state, form is already visible — nothing more to do
        },

        // Lock MyInfo-sourced fields so user can't edit them
        // Note: postal_code is NOT locked — always user-editable per requirement
        lockMyInfoFields: function() {
            var lockedFields = ['dob', 'gender', 'race', 'citizenship', 'name', 'marital_status'];
            var self = this;

            lockedFields.forEach(function(field) {
                var $field = $('#' + field);
                if ($field.length) {
                    $field.prop('readonly', true).prop('disabled', true).addClass('myinfo-locked');
                    // Add badge under the field
                    var $group = $field.closest('.hd-form-group');
                    if ($group.length && !$group.find('.myinfo-locked-badge').length) {
                        $group.append('<div class="myinfo-locked-badge">🔒 Verified via Singpass MyInfo</div>');
                        $group.find('.myinfo-locked-badge').addClass('show');
                    }
                }
            });

            // Also lock name
            var $name = $('#name');
            if ($name.length) {
                $name.prop('readonly', true).addClass('myinfo-locked');
                var $nameGroup = $name.closest('.hd-form-group');
                if ($nameGroup.length && !$nameGroup.find('.myinfo-locked-badge').length) {
                    $nameGroup.append('<div class="myinfo-locked-badge">🔒 Verified via Singpass MyInfo</div>');
                    $nameGroup.find('.myinfo-locked-badge').addClass('show');
                }
            }
        },

        fetchPrefillData: function(flowId) {
            var self = this;
            $.ajax({
                url: this.apiBase + '/auth/myinfo/prefill?flowId=' + encodeURIComponent(flowId),
                method: 'GET',
                success: function(data) {
                    if (data.mappedFields) {
                        self.applyPrefillData(data.mappedFields);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 409) {
                        // UUID already linked to another account — show lightbox
                        $('#myinfo-ineligible-reason').text(
                            (xhr.responseJSON && xhr.responseJSON.error)
                            || 'This SingPass ID is already linked to another account. Please contact support.'
                        );
                        $('#myinfo-ineligible-lightbox').addClass('show');
                    } else {
                        console.warn('[FlexcoreRegisterMyinfo] Could not fetch pre-fill data');
                    }
                }
            });
        },

        applyPrefillData: function(fields) {
            var self = this;

            // Helper: set form value
            function setVal(formId, value) {
                var $el = $(formId);
                if ($el.length && value !== undefined && value !== null && value !== '') {
                    $el.val(value);
                }
            }

            // Map MyInfo race codes to form values
            // MyInfo race values: CHINESE, MALAY, INDIAN, EURASIAN, or any other free-text value (e.g. EUROPEAN)
            if (fields.race !== undefined && fields.race !== null && fields.race !== '') {
                var raceMap = {
                    'CHINESE':   'chinese',
                    'MALAY':     'malay',
                    'INDIAN':    'indian',
                    'EURASIAN':  'eurasian',
                    'OTHERS':    'others'
                };
                var mappedRace = raceMap[fields.race.toUpperCase()];
                if (mappedRace) {
                    setVal('#race', mappedRace);
                    // Show "Please Specify" if race is "others"
                    if (mappedRace === 'others') {
                        var othersGroup = document.getElementById('othersRaceGroup');
                        if (othersGroup) othersGroup.style.display = 'block';
                        // Map all known Singpass race codes to full names
                        var raceRawMap = {
                            'CN': 'CHINESE', 'MY': 'MALAY', 'IN': 'INDIAN',
                            'EA': 'EURASIAN', 'OT': 'OTHERS', 'OT02': 'OTHERS',
                            'EU': 'EUROPEAN', 'AR': 'ARAB', 'AF': 'AFRICAN',
                            'AM': 'ARMENIAN', 'AU': 'AUSTRALIAN', 'BD': 'BANGLADESHI',
                            'BU': 'BURMESE', 'CA': 'CAUCASIAN', 'CE': 'CEYLONESE',
                            'FI': 'FILIPINO', 'ID': 'INDONESIAN', 'JP': 'JAPANESE',
                            'KH': 'KOREAN', 'LA': 'LAOTIAN', 'NE': 'NEPALESE',
                            'NZ': 'NEW ZEALANDER', 'PA': 'PAKISTANI', 'SI': 'SINHALESE',
                            'TH': 'THAI', 'VI': 'VIETNAMESE', 'XX': 'OTHERS'
                        };
                        var raceDisplay = raceRawMap[fields.raceRaw] || fields.raceRaw || fields.race;
                        setVal('#others', raceDisplay);
                        // Lock and grey out
                        var $othersInput = $('#others');
                        if ($othersInput.length) {
                            $othersInput.prop('readonly', true).addClass('myinfo-locked');
                            $othersInput.closest('.hd-form-group').find('.myinfo-locked-badge').remove();
                            $othersInput.closest('.hd-form-group').append('<div class="myinfo-locked-badge">🔒 Verified via Singpass MyInfo</div>');
                            $othersInput.closest('.hd-form-group').find('.myinfo-locked-badge').addClass('show');
                        }
                    }
                } else {
                    // Not a standard race — set to "Others" and populate the spec field
                    // with the raw Singpass code (e.g. "EUROPEAN")
                    setVal('#race', 'others');
                    setVal('#others', fields.raceRaw || fields.race);
                    // Show the "Please Specify" field since it's now active
                    var othersGroup = document.getElementById('othersRaceGroup');
                    if (othersGroup) othersGroup.style.display = 'block';
                    // Lock and grey out the spec field since it's from MyInfo
                    var $othersInput = $('#others');
                    if ($othersInput.length) {
                        $othersInput.prop('readonly', true).addClass('myinfo-locked');
                        $othersInput.closest('.hd-form-group').find('.myinfo-locked-badge').remove();
                        $othersInput.closest('.hd-form-group').append('<div class="myinfo-locked-badge">🔒 Verified via Singpass MyInfo</div>');
                        $othersInput.closest('.hd-form-group').find('.myinfo-locked-badge').addClass('show');
                    }
                }
            }

            // Map MyInfo nationality to citizenship
            // SG → singaporecitizen, anything else → leave blank (non-SG can't register)
            if (fields.nationality !== undefined && fields.nationality !== null && fields.nationality !== '') {
                var nationalityMap = {
                    'SG':  'singaporecitizen',
                    'SINGAPORE': 'singaporecitizen'
                };
                var mappedCitz = nationalityMap[fields.nationality.toUpperCase()];
                if (mappedCitz) {
                    setVal('#citizenship', mappedCitz);
                }
                // Non-SG nationality: leave citizenship blank → user can't proceed
                // (the form will catch this on submit)
            }

            // Map MyInfo sex to gender
            // Backend already lowercases to 'male'/'female'/'others'; MyInfo raw codes are 'M'/'F'
            if (fields.sex !== undefined && fields.sex !== null && fields.sex !== '') {
                var sexVal = (typeof fields.sex === 'object' && fields.sex !== null) ? fields.sex.code : fields.sex;
                var sexMap = { 'M': 'male', 'F': 'female', 'male': 'male', 'female': 'female', 'others': 'others' };
                var mappedSex = sexMap[(sexVal || '').toUpperCase()] || sexMap[(sexVal || '').toLowerCase()];
                if (mappedSex) { setVal('#gender', mappedSex); }
            }

            // Map MyInfo marital status to form values
            // Backend enum values: single, married, divorced, widowed
            // Also handle possible uppercase codes (SINGLE, MARRIED, etc.)
            if (fields.maritalStatus !== undefined && fields.maritalStatus !== null && fields.maritalStatus !== '') {
                var maritalMap = {
                    'SINGLE': 'single',
                    'MARRIED': 'married',
                    'DIVORCED': 'divorced',
                    'WIDOWED': 'widowed',
                    'SEPARATED': 'separated',
                    'SOONTOBEMARRIED': 'soontobemarried',
                    'single': 'single',
                    'married': 'married',
                    'divorced': 'divorced',
                    'widowed': 'widowed',
                    'separated': 'separated',
                    'soontobemarried': 'soontobemarried',
                };
                var mappedMarital = maritalMap[fields.maritalStatus] || maritalMap[fields.maritalStatus.toUpperCase()];
                if (mappedMarital) {
                    setVal('#marital_status', mappedMarital);
                }
            }

            setVal('#name',         fields.name);
            // Convert MyInfo dateOfBirth from YYYY-MM-DD to DD/MM/YYYY
            if (fields.dateOfBirth && /^\d{4}-\d{2}-\d{2}$/.test(fields.dateOfBirth)) {
                var parts = fields.dateOfBirth.split('-');
                setVal('#dob', parts[2] + '/' + parts[1] + '/' + parts[0]);
            } else {
                setVal('#dob', fields.dateOfBirth);
            }
            setVal('#postal_code',  fields.postalCode);

            if (fields.preferredName) {
                setVal('#preferred_name', fields.preferredName);
            }
        }
    };

    $(function() {
        if ($('#btn-retrieve-myinfo').length || $('#myinfo_step').val() === 'callback') {
            FlexcoreRegisterMyinfo.init();
        }
    });

})(jQuery);
</script>
