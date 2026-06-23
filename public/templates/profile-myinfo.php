<?php
/**
 * MyInfo Profile Update form template
 * Same pattern as register-myinfo but for existing users updating their profile
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

    .myinfo-prefilled-notice {
        background: #E8F5E9; border: 1px solid #C8E6C9;
        border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
        font-size: 14px; color: #2E7D32; display: none;
    }
    .myinfo-prefilled-notice.show { display: block; }

    .myinfo-locked[readonly], .myinfo-locked[disabled] {
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

<input type="hidden" id="myinfo_flow_id" value="<?php echo esc_attr($flow_id); ?>">
<input type="hidden" id="myinfo_step"    value="<?php echo esc_attr($step); ?>">
<input type="hidden" id="myinfo_status"  value="<?php echo esc_attr($status); ?>">

<div class="singpass-loading" id="singpass-loading">
    <div class="singpass-spinner"></div>
    <p>Redirecting to Singpass...</p>
</div>

<div id="singpass-form-section">
    <div class="myinfo-top-row" style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px; flex-wrap: wrap;">
        <button type="button" id="btn-retrieve-myinfo" onclick="FlexcoreProfileMyinfo.startMyInfo(); return false;" style="background:none;border:none;padding:0;cursor:pointer;">
            <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__, 2)) . 'public/images/singpass-button.png'); ?>" alt="Retrieve Myinfo with Singpass" style="height:48px;width:auto;">
        </button>
        <span class="myinfo-promo-text" style="color: #1a56db; font-style: italic; font-size: 15px; line-height: 1.5; max-width: 480px;">
            Get your profile verified by Singpass and be awarded with extra 50 HappyPoints!
        </span>
    </div>

    <div class="myinfo-prefilled-notice" id="myinfo-prefilled-notice">
        ✅ Your details have been verified via Singpass MyInfo. Locked fields cannot be changed.
    </div>

    <?php
    // Load the profile form template
    echo FlexCore_Server_Template_Loader::load_template('profile-form');
    ?>
</div>

<script>
(function($) {
    'use strict';

    window.FlexcoreProfileMyinfo = {
        apiBase: (window.flexcoreServerAjax && window.flexcoreServerAjax.myinfoApiBase)
                 || 'https://staging.flexcore.theadventus.com/api/v1',

        startMyInfo: function() {
            $('#btn-retrieve-myinfo').hide();
            $('#singpass-loading').addClass('show');
            window.location.href = this.apiBase + '/auth/myinfo/start?returnTo='
                + encodeURIComponent(window.location.pathname + '?step=callback');
        },

        init: function() {
            var step   = $('#myinfo_step').val();
            var status = $('#myinfo_status').val();
            var flowId = $('#myinfo_flow_id').val();

            if (step === 'callback') {
                this.handleCallback(status, flowId);
            }
        },

        handleCallback: function(status, flowId) {
            $('#btn-retrieve-myinfo').hide();

            if (status === 'ineligible') {
                alert('Unfortunately, only Singapore Citizens and Permanent Residents can verify via Singpass MyInfo. Please update your profile manually.');
            } else if (status === 'existing_user') {
                var loginUrl = window.flexcoreServerAjax?.loginUrl || '/flexcore-login';
                alert('This Singpass is already linked to an existing account. Please log in with that account instead.');
            } else if (status === 'new_user' && flowId) {
                $('#myinfo-prefilled-notice').addClass('show');
                this.lockMyInfoFields();
                this.fetchPrefillData(flowId);
            }
        },

        lockMyInfoFields: function() {
            var lockedFields = ['dob', 'gender', 'race', 'citizenship', 'name', 'marital_status'];
            var self = this;

            lockedFields.forEach(function(field) {
                var $field = $('#' + field);
                if ($field.length) {
                    $field.prop('readonly', true).prop('disabled', true).addClass('myinfo-locked');
                    var $group = $field.closest('.hd-form-group');
                    if ($group.length && !$group.find('.myinfo-locked-badge').length) {
                        $group.append('<div class="myinfo-locked-badge">🔒 Verified via Singpass MyInfo</div>');
                        $group.find('.myinfo-locked-badge').addClass('show');
                    }
                }
            });

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
                error: function() {
                    console.warn('[FlexcoreProfileMyinfo] Could not fetch pre-fill data');
                }
            });
        },

        applyPrefillData: function(fields) {
            function setVal(formId, value) {
                var $el = $(formId);
                if ($el.length && value !== undefined && value !== null && value !== '') {
                    $el.val(value);
                }
            }

            // Race mapping
            if (fields.race !== undefined && fields.race !== null && fields.race !== '') {
                var raceMap = {
                    'CHINESE': 'chinese', 'MALAY': 'malay',
                    'INDIAN': 'indian', 'EURASIAN': 'eurasian'
                };
                var mappedRace = raceMap[fields.race.toUpperCase()];
                if (mappedRace) {
                    setVal('#race', mappedRace);
                } else {
                    setVal('#race', 'others');
                    setVal('#others', fields.race);
                    var othersGroup = document.getElementById('othersRaceGroup');
                    if (othersGroup) othersGroup.style.display = 'block';
                    var $othersInput = $('#others');
                    if ($othersInput.length) {
                        $othersInput.prop('readonly', true).addClass('myinfo-locked');
                    }
                }
            }

            // Nationality → citizenship
            if (fields.nationality !== undefined && fields.nationality !== null && fields.nationality !== '') {
                var nationalityMap = { 'SG': 'singaporecitizen', 'SINGAPORE': 'singaporecitizen' };
                var mappedCitz = nationalityMap[fields.nationality.toUpperCase()];
                if (mappedCitz) { setVal('#citizenship', mappedCitz); }
            }

            // Sex → gender
            if (fields.sex !== undefined && fields.sex !== null && fields.sex !== '') {
                var sexVal = (typeof fields.sex === 'object' && fields.sex !== null) ? fields.sex.code : fields.sex;
                var sexMap = { 'M': 'male', 'F': 'female', 'male': 'male', 'female': 'female', 'others': 'others' };
                var mappedSex = sexMap[(sexVal || '').toUpperCase()] || sexMap[(sexVal || '').toLowerCase()];
                if (mappedSex) { setVal('#gender', mappedSex); }
            }

            // Marital status mapping
            if (fields.maritalStatus !== undefined && fields.maritalStatus !== null && fields.maritalStatus !== '') {
                var maritalMap = {
                    'SINGLE': 'single', 'MARRIED': 'married', 'DIVORCED': 'divorced',
                    'WIDOWED': 'widowed', 'SEPARATED': 'separated', 'SOONTOBEMARRIED': 'soontobemarried',
                    'single': 'single', 'married': 'married', 'divorced': 'divorced',
                    'widowed': 'widowed', 'separated': 'separated', 'soontobemarried': 'soontobemarried',
                };
                var mappedMarital = maritalMap[fields.maritalStatus] || maritalMap[fields.maritalStatus.toUpperCase()];
                if (mappedMarital) { setVal('#marital_status', mappedMarital); }
            }

            setVal('#name', fields.name);
            setVal('#dob', fields.dateOfBirth);
            setVal('#postal_code', fields.postalCode);

            if (fields.preferredName) {
                setVal('#preferred_name', fields.preferredName);
            }

            // Store flowId for submit
            if (!$('#myinfo_flow_id').val() && window._profileMyinfoFlowId) {
                $('#myinfo_flow_id').val(window._profileMyinfoFlowId);
            }
        }
    };

    $(function() {
        if ($('#btn-retrieve-myinfo').length || $('#myinfo_step').val() === 'callback') {
            FlexcoreProfileMyinfo.init();
        }
    });

})(jQuery);
</script>
