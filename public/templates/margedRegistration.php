<?php
/**
 * Merged Registration Form
 * Combines fields from register-form.php and profile-form.php
 * @package FlexCore_Server
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    ol.flexcore_requiments li.valid {
        color: #2e7d32 !important;
        font-weight: 600;
    }

    /* Guide box in right col: force to occupy ~2 row-heights (matching Create+Confirm password) */
    .guide-2rows {
        min-height: 130px;
    }
    .guide-2rows .flexcore_requiments {
        margin: 0;
        padding-left: 20px;
    }
    .guide-2rows .flexcore_requiments li {
        font-size: 11px;
        color: #666;
        margin-bottom: 5px;
        list-style-type: decimal;
        transition: color 0.2s;
    }
    .guide-2rows .flexcore_requiments li.valid {
        color: #2e7d32 !important;
        font-weight: 600;
    }
    .guide-2rows .guide-title {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        margin: 0 0 8px;
    }

    .requiments li.matched {
        color: green;
        font-weight: bold;
    }

    .success-green {
        background-color: green;
    }

    #referral_code,
    #referral_code_label,
    #othersRaceGroup {
        display: none;
    }

    .has-error {
        background-color: rgba(245, 159, 159, 0.75) !important;
        border: 1px solid red !important;
    }

    .is-valid {
        background-color: #d4edda !important;
    }

    .hd-formfild:disabled {
        border: 1px solid #7A7A7A !important;
        background: #ECECEC !important;
    }

    select {
        background: #ffffff !important;
    }

    .password-input {
        padding-right: 50px !important;
    }

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

    .field-error {
        color: red;
        font-size: 15px;
        margin-top: 4px;
        display: none;
    }

    input[type="password"]::-ms-reveal {
        display: none !important;
    }

    input[type="checkbox"] {
        cursor: pointer;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }

    .plus-symbol {
        font-size: 18px;
        color: #D92632;
        font-weight: bold;
        padding: 2px 6px;
        width: 110px;
        border-radius: 4px;
    }

    input.hd-formfild:read-only,
    textarea.hd-formfild:read-only,
    select.hd-formfild:disabled {
        border: 1px solid #7A7A7A !important;
        background: #7a7a7a82 !important;
    }

    select {
        border: 1px solid #7A7A7A !important;
        background: #ffffff !important;
    }

    #dob {
        cursor: pointer;
    }

    .login-link {
        margin-top: 31px;
        margin-left: 25px;
    }

    /* Section divider */
    .form-divider {
        text-align: center;
        margin: 24px 0;
        position: relative;
        color: #999;
        font-size: 13px;
    }
    .form-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #eee;
    }
    .form-divider span {
        background: #fff;
        padding: 0 12px;
        position: relative;
    }

    /* MyInfo section label */
    .myinfo-section-label {
        font-size: 12px;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
</style>
<div class="flexcore-form flexcore-register-form becomehappydotter-wrap">

    <form id="flexcore-merged-registration-form" method="post" novalidate>
         <input type="hidden" id="register_nonce" name="nonce" value="<?php echo wp_create_nonce('flexcore_register'); ?>">
        <div class="row">

            <!-- =====================
                 SECTION 1: MyInfo Fields (top — Singpass greyable)
                 ===================== -->

            <div class="hd-col-12" id="myinfo-section-header" style="display:none;">
                <div class="form-divider"><span>Info retrieved from Myinfo</span></div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group">
                    <label class="hd-label" for="name">
                        <?php esc_html_e('Full Name', 'flexcore-server'); ?><span>*</span>
                    </label>
                    <input class="hd-formfild" type="text" id="name" name="name" required maxlength="100">
                    <div class="field-error name-error" id="error-name"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="dob">
                        <?php esc_html_e('Date of Birth', 'flexcore-server'); ?><span>*</span>
                    </label>
                    <input class="hd-formfild dob-input"
                           type="text"
                           id="dob"
                           name="dob"
                           placeholder="dd/mm/yyyy"
                           required
                           maxlength="10">
                    <small class="form-hint form-error dob-error" style="display: none;">
                        <?php esc_html_e('Must be 15 years or older', 'flexcore-server'); ?>
                    </small>
                    <div class="field-error dob-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group" style="position:relative;">
                    <label class="hd-label" for="citizenship">
                        <?php esc_html_e('Citizenship', 'flexcore-server'); ?><span>*</span>
                        <span style="position:relative;display:inline-block;">
                            <span style="cursor:pointer;" tabindex="0" class="citizenship-tooltip-icon"><i class="fa fa-question-circle" style="font-size:15px;  color: black;    margin-left: 2px;"></i></span>
                            <span class="citizenship-tooltip-text" style="display:none;position:absolute;left:25px;top:-10px;z-index:10;background:#222;color:#fff;padding:10px 12px;border-radius:8px;font-size:15px;min-width:320px;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                                As part of the HappyDot.sg community guidelines, you need to be a Singapore Citizen or Permanent Resident to qualify for the membership.<br>Please indicate your citizenship here.
                            </span>
                        </span>
                    </label>
                    <select class="hd-formfild" id="citizenship" name="citizenship" required>
                        <option value=""><?php esc_html_e('Select citizenship', 'flexcore-server'); ?></option>
                        <option value="singaporecitizen"><?php esc_html_e('Singapore Citizen', 'flexcore-server'); ?></option>
                        <option value="permanentResident"><?php esc_html_e('Permanent Resident', 'flexcore-server'); ?></option>
                    </select>
                    <div class="field-error citizen-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="gender"><?php esc_html_e('Sex', 'flexcore-server'); ?><span>*</span></label>
                    <select class="hd-formfild" id="gender" name="gender" required>
                        <option value=""><?php esc_html_e('Select sex', 'flexcore-server'); ?></option>
                        <option value="male"><?php esc_html_e('Male', 'flexcore-server'); ?></option>
                        <option value="female"><?php esc_html_e('Female', 'flexcore-server'); ?></option>
                        <option value="others"><?php esc_html_e('Others', 'flexcore-server'); ?></option>
                    </select>
                    <div class="field-error gender-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="marital_status"><?php esc_html_e('Marital Status', 'flexcore-server'); ?><span>*</span></label>
                    <select class="hd-formfild" id="marital_status" name="maritalStatus" required>
                        <option value=""><?php esc_html_e('Select marital status', 'flexcore-server'); ?></option>
                        <option value="single"><?php esc_html_e('Single', 'flexcore-server'); ?></option>
                        <option value="soontobemarried"><?php esc_html_e('Soon to be Married', 'flexcore-server'); ?></option>
                        <option value="married"><?php esc_html_e('Married', 'flexcore-server'); ?></option>
                        <option value="divorced"><?php esc_html_e('Divorced', 'flexcore-server'); ?></option>
                        <option value="separated"><?php esc_html_e('Separated', 'flexcore-server'); ?></option>
                        <option value="widowed"><?php esc_html_e('Widowed', 'flexcore-server'); ?></option>
                    </select>
                    <div class="field-error maritalStatus-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="race"><?php esc_html_e('Race', 'flexcore-server'); ?><span>*</span></label>
                    <select class="hd-formfild" id="race" name="race" required>
                        <option value=""><?php esc_html_e('Select race', 'flexcore-server'); ?></option>
                        <option value="chinese"><?php esc_html_e('Chinese', 'flexcore-server'); ?></option>
                        <option value="malay"><?php esc_html_e('Malay', 'flexcore-server'); ?></option>
                        <option value="indian"><?php esc_html_e('Indian', 'flexcore-server'); ?></option>
                        <option value="eurasian"><?php esc_html_e('Eurasian', 'flexcore-server'); ?></option>
                        <option value="others"><?php esc_html_e('Others', 'flexcore-server'); ?></option>
                    </select>
                    <div class="field-error race-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6" id="othersRaceGroup">
                <div class="hd-form-group">
                    <label class="hd-label" for="othersRace"><?php esc_html_e('Please Specify', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="text" id="others" name="others" value="" maxlength="100">
                    <div class="field-error race_details-error" style="display: none;"></div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var icon = document.querySelector('.citizenship-tooltip-icon');
                    var tooltip = document.querySelector('.citizenship-tooltip-text');
                    if (icon && tooltip) {
                        icon.addEventListener('mouseenter', function() {
                            tooltip.style.display = 'block';
                        });
                        icon.addEventListener('mouseleave', function() {
                            tooltip.style.display = 'none';
                        });
                        icon.addEventListener('focus', function() {
                            tooltip.style.display = 'block';
                        });
                        icon.addEventListener('blur', function() {
                            tooltip.style.display = 'none';
                        });
                    }

                    // Toggle "Please Specify" field when race changes
                    var raceSelect = document.getElementById('race');
                    var othersGroup = document.getElementById('othersRaceGroup');
                    if (raceSelect && othersGroup) {
                        raceSelect.addEventListener('change', function() {
                            if (raceSelect.value === 'others') {
                                othersGroup.style.display = 'block';
                            } else {
                                othersGroup.style.display = 'none';
                                // Clear the others field when switching away
                                var othersInput = document.getElementById('others');
                                if (othersInput) othersInput.value = '';
                            }
                        });
                    }
                });
            </script>

            <!-- =====================
                 SECTION 2: Account Fields (below MyInfo)
                 ===================== -->
            <div class="hd-col-12">
                <div class="form-divider"><span>Account Details</span></div>
            </div>

            <!-- Preferred Name -->
            <div class="hd-col-12" id="preferredNameGroup">
                <div class="hd-form-group">
                    <label class="hd-label" for="preferred_name">
                        <?php esc_html_e('Preferred Name', 'flexcore-server'); ?><span>*</span>
                    </label>
                    <input class="hd-formfild" type="text" id="preferred_name" name="preferredName" required maxlength="100" placeholder="<?php esc_attr_e('What should we call you?', 'flexcore-server'); ?>">
                    <div class="field-error preferredName-error" style="display:none;"></div>
                </div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group">
                    <label class="hd-label" for="email"><?php esc_html_e('Email', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="email" id="email" name="email" required maxlength="100">
                    <small class="form-hint"><?php esc_html_e('This will be your login email', 'flexcore-server'); ?></small>
                    <div class="field-error email-error" id="error-email"></div>
                </div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group">
                    <label class="hd-label" for="confirm_email"><?php esc_html_e('Confirm Email', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="email" id="confirm_email" name="confirm_email" required maxlength="100">
                    <div class="field-error" id="error-confirm_email"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="password"><?php esc_html_e('Create a Password', 'flexcore-server'); ?><span>*</span></label>
                    <div class="position-relative password-eye hp-pwd-protect">
                        <input class="hd-formfild password-input" type="password" id="password" name="password" required>
                        <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                    <div class="field-error" id="error-password"></div>
                </div>
                <div class="hd-form-group">
                    <label class="hd-label" for="confirm_password"><?php esc_html_e('Confirm Password', 'flexcore-server'); ?><span>*</span></label>
                    <div class="position-relative password-eye hp-pwd-protect">
                        <input class="hd-formfild password-input" type="password" id="confirm_password" name="confirm_password" required>
                        <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                    <div class="field-error" id="error-confirm_password"></div>
                </div>
            </div>

            <!-- RIGHT col: Password requirements guide box -->
            <div class="hd-col-6">
                <div class="hd-signup-info guide-2rows">
                    <p><?php esc_html_e('Your password must:', 'flexcore-server'); ?></p>
                    <ol class="flexcore_requiments">
                        <li id="flexcore_length">Be between 12 - 15 characters in length</li>
                        <li id="flexcore_uppercase">Contain at least 1 uppercase (capital) letter</li>
                        <li id="flexcore_lowercase">Contain at least 1 lowercase (small) letter</li>
                        <li id="flexcore_number">Contain at least 1 number</li>
                        <li id="flexcore_special">Contain at least 1 special character (!@#$%^&*())</li>
                    </ol>
                    <h6 id="h6" class="hd-requirements-matched" style="display: none;"><?php esc_html_e('Password requirement all met!', 'flexcore-server'); ?></h6>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="mobile"><?php esc_html_e('Mobile No.', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="tel" id="mobile" name="mobile" required maxlength="8" pattern="[89][0-9]{7}" placeholder="Phone Number" title="8-digit Singapore mobile number starting with 8 or 9" data-mask="mobile">
                    <small class="form-hint form-error mobile-error" style="display: none;">
                        <?php esc_html_e('Please enter a valid Singapore mobile number', 'flexcore-server'); ?>
                    </small>
                    <div class="field-error mobileNo-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="postal_code"><?php esc_html_e('Postal Code', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="text" id="postal_code" name="postal_code" required maxlength="6" pattern="^\d{6}$" placeholder="Postal Code">
                    <div class="field-error postal-error" style="display: none;"></div>
                </div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group">
                    <input class="hd-formfild" type="hidden" name="campaign_id" id="campaign_id" />
                    <input class="hd-formfild" type="hidden" name="utm_string" id="utm_string" />
                </div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group" id="source-group">
                    <label class="hd-label" for="register_source"><?php esc_html_e('How did you get to know about HappyDot.sg?', 'flexcore-server'); ?><span>*</span></label>
                    <select class="hd-formfild" name="register_source" id="register_source" required>
                        <option value=""><?php esc_html_e('Please select an option', 'flexcore-server'); ?></option>
                        <option value="I saw it from a brochure"><?php esc_html_e('I saw it from a brochure', 'flexcore-server'); ?></option>
                        <option value="Referred by a friend"><?php esc_html_e('Referred by a friend', 'flexcore-server'); ?></option>
                        <option value="I heard about it from family/friends"><?php esc_html_e('I heard about it from family/friends', 'flexcore-server'); ?></option>
                        <option value="I saw it from the search engine results (e.g. Google/Bing)"><?php esc_html_e('I saw it from the search engine results (e.g. Google/Bing)', 'flexcore-server'); ?></option>
                    </select>
                    <div class="field-error" id="error-register_source"></div>
                </div>
            </div>

            <div class="hd-col-12">
                <div class="hd-form-group">
                    <label class="hd-label" for="referral_code" id="referral_code_label"><?php esc_html_e('Referral Code', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="text" name="referral_code" id="referral_code" />
                    <div class="field-error" id="error-referral_code"></div>
                </div>
            </div>

            <div class="hd-condition-read">
                <input class="hd-checkbox" type="checkbox" id="consent" name="consent" required>
                <label class="hd-label" for="consent"><?php esc_html_e('I confirm that above information is accurate and agree for HappyDot.sg to collect and use it to contact me and process my membership registration', 'flexcore-server'); ?></label>
                <div class="field-error consent-error" style="display: none;"></div>
            </div>

            <div class="hd-condition-read">
                <input class="hd-checkbox" type="checkbox" id="terms_consent" name="terms_consent" required>
                <label class="hd-label" for="terms_consent">
                    I confirm that I have read and understood the <a href="/privacy-policy/" target="_blank" style="color:#1a56db;text-decoration:underline;">Privacy Policy</a> and <a href="/terms-conditions/" target="_blank" style="color:#1a56db;text-decoration:underline;">Terms & Conditions</a>, and I agree to be bound by them. I acknowledge that these may be updated from time to time, and I will refer to the provided links for the most current version.
                </label>
                <div class="field-error terms-error" style="display: none;"></div>
            </div>

        </div>
        <div class="hd-form-btn form-submit">
            <button type="submit" class="button button-primary hd-btn" id="submitBtn"><?php esc_html_e('CREATE ACCOUNT', 'flexcore-server'); ?></button>
        </div>
        <p class="login-link">
            <?php esc_html_e('Already have an account?', 'flexcore-server'); ?>
            <a href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>">
                <?php esc_html_e('Login here', 'flexcore-server'); ?>
            </a>
        </p>
</form>
<div id="register-message" class="flexcore-message" style="display: none;"></div>
</div><!-- close becomehappydotter-wrap -->
</div><!-- close flexcore-form -->
