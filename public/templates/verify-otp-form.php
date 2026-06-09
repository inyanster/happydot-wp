<?php
/**
 * OTP Verification form template
 *
 * @package FlexCore_Server
 * @var string $email User's email address
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    .flexcore_requiments li.valid {
        color: green !important;
    }
    #referral_code, #referral_code_label {
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
    .password-input{
       
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
#error-otp {
    color: red;
    font-size: 13px;
    margin-top: 4px;
    display: none;
}

</style>
<div class="flexcore-form flexcore-verify-otp-form">
    <h2><?php esc_html_e('Verify OTP', 'flexcore-server'); ?></h2>
    <p class="form-description">
        <?php esc_html_e('Please enter the 6-digit code sent to your email.', 'flexcore-server'); ?>
        <br>
        <strong><?php echo esc_html($email); ?></strong>
    </p>
    <form id="flexcore-verify-otp-form" novalidate>
        <?php wp_nonce_field('flexcore_verify_otp', 'flexcore_nonce'); ?>
        <input type="hidden" id="email" name="email" value="<?php echo esc_attr($email); ?>">

        <div class="hd-admin-login">
            <div class="hd-form-group">
                <label class="hd-label" for="otp">
                    <?php esc_html_e('Enter the 6-digit verification code here:', 'flexcore-server'); ?><br />
                    <input class="hd-formfild" type="text" id="otp" name="otp" required pattern="[0-9]{6}" maxlength="6"
                        inputmode="numeric" />
                </label>
                <div class="field-error" id="error-otp"></div>
                <small class="form-hint"><?php esc_html_e('Enter the 6-digit code', 'flexcore-server'); ?></small>
            </div>

            <!-- <a href="javascript:void(0);" class="hd-resend-link hd-resend-login hd-none">Resend OTP</a> -->

            <div class="hd-form-btn">
                <input class="hd-btn" type="submit" value="<?php esc_attr_e('Verify', 'flexcore-server'); ?>" />
                <a href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>"
                    class="hd-btn hd-return-btn">
                    <?php esc_html_e('Back to Login', 'flexcore-server'); ?>
                </a>
            </div>
        </div>

        <div id="verify-otp-message" class="flexcore-message" style="display: none;"></div>
    </form>

</div>
