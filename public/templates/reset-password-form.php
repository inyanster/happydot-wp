<?php

/**
 * Reset password form template
 *
 * @package FlexCore_Server
 * @var string $email User's email address
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
    .requiments li.matched {
        color: green;
        font-weight: bold;
    }

    .flexcore_requiments li.valid {
        color: green !important;
    }

    #referral_code,
    #referral_code_label {
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
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    input[type="password"]::-ms-reveal {
        display: none !important;
    }
</style>

<div class="flexcore-form flexcore-reset-password-form">
    <h2><?php esc_html_e('Reset Password', 'flexcore-server'); ?></h2>
    <p class="form-description">
        <?php esc_html_e('Enter the OTP sent to your email and choose a new password.', 'flexcore-server'); ?><br>
        <strong><?php echo esc_html($email); ?></strong>
    </p>

    <form id="flexcore-reset-password-form" method="post" novalidate>
        <?php wp_nonce_field('flexcore_reset_password', 'flexcore_nonce'); ?>
        <input type="hidden" id="email" value="<?php echo esc_attr($email); ?>">

        <div class="resetpasswod-wrap">
            <div class="hd-admin-login">

                <div class="hd-form-group">
                    <label class="hd-label" for="otp"><?php esc_html_e('Enter OTP', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="text" id="otp" name="otp" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric">
                    <small class="form-hint"><?php esc_html_e('Enter the 6-digit code sent to your email', 'flexcore-server'); ?></small>
                    <div class="field-error" id="otp-error"></div>
                </div>

                <div class="hd-form-group">
                    <label class="hd-label" for="password"><?php esc_html_e('New Password', 'flexcore-server'); ?><span>*</span></label>
                    <div class="position-relative password-eye">
                        <input class="hd-formfild password-input" type="password" id="password" name="password" required>
                        <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                    <div class="password-strength-meter"></div>
                    <div class="password-strength-text"></div>
                    <div class="field-error" id="password-error"></div>
                </div>

                <div class="hd-form-group">
                    <label class="hd-label" for="confirm_password"><?php esc_html_e('Confirm Password', 'flexcore-server'); ?><span>*</span></label>
                    <div class="position-relative password-eye">
                        <input class="hd-formfild password-input" type="password" id="confirm_password" name="confirm_password" required>
                        <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                    </div>
                    <div class="field-error" id="confirm-password-error"></div>
                </div>


                <div class="hd-form-btn">
                    <input class="hd-btn" type="submit" value="<?php esc_attr_e('Reset Password', 'flexcore-server'); ?>" />
                </div>
            </div>

            <div class="hd-signup-info">
                <p><?php esc_html_e('Your password must:', 'flexcore-server'); ?></p>
                <ul class="requiments">
                    <li id="length">Be between 12 - 15 characters in length</li>
                    <li id="uppercase">Contain at least 1 uppercase (capital) letter</li>
                    <li id="lowercase">Contain at least 1 lowercase (small) letter</li>
                    <li id="number">Contain at least 1 number</li>
                    <li id="special">Contain at least 1 of the following special characters (!@#$%^&*())</li>
                </ul>
                <h6 id="h6" class="hd-requirements-matched" style="display:none;">Password requirement all met!</h6>
            </div>
        </div>
    </form>

    <div id="reset-password-message" class="hd-ajax-response flexcore-message" style="display: none;"></div>
</div>
