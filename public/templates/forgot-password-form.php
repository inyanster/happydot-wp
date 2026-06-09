<?php
/**
 * Forgot password form template
 *
 * @package FlexCore_Server
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
    </style>
<div class="flexcore-form flexcore-forgot-password-form">
    
    <p class="form-description">
        <?php esc_html_e('Enter your email address and we will send you an OTP to reset your password.', 'flexcore-server'); ?>
    </p>

    <form id="flexcore-forgot-password-form" method="post" novalidate>
        <?php wp_nonce_field('flexcore_forgot_password', 'flexcore_nonce'); ?>

        <div class="hd-admin-login">
            <div class="hd-form-group">
                <label class="hd-label" for="email">
                    <?php esc_html_e('Email Address', 'flexcore-server'); ?><span>*</span>
                </label>
                <input class="hd-formfild" type="email" id="email" name="email" required>
            </div>

         

           <div class="hd-ajax-response flexcore-message" id="forgot-password-message " style="display: none;"></div>


            <div class="hd-form-btn">
                <input class="hd-btn" type="submit" value="<?php esc_attr_e('Send OTP', 'flexcore-server'); ?>" />
            </div>
            <br><br>
            <div class="hd-form-group">
                <a href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>" class="hd-btn hd-return-btn">
                    <?php esc_html_e('Return to Login', 'flexcore-server'); ?>
                </a>
            </div>
        </div>
    </form>
</div>
