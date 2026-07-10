<?php
/**
 * Registration form template
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
</style>

<div class="flexcore-form flexcore-register-form becomehappydotter-wrap">
    
    <p class="form-description"><?php esc_html_e('Please fill in all required fields', 'flexcore-server'); ?></p>
    <form id="flexcore-register-form" method="post" novalidate>
        <input type="hidden" id="register_nonce" name="nonce" value="<?php echo wp_create_nonce('flexcore_register'); ?>">
        <div class="row">
        <div class="hd-col-12">
                <div class="hd-form-group">
                    <label class="hd-label" for="name">
                        <?php esc_html_e('Full Name', 'flexcore-server'); ?><span>*</span>
                        <!-- <small>
                             <?php//esc_html_e('Full name should consist of your given & family name', 'flexcore-server'); ?> 
                        </small> -->
                    </label>
                    <input class="hd-formfild" type="text" id="name" name="name" required maxlength="254">
                     <div class="field-error" id="error-name"></div>
                </div>
               
        </div>
        
            <div class="hd-col-6">
                <div class="hd-form-group">
                    <label class="hd-label" for="email"><?php esc_html_e('Email', 'flexcore-server'); ?><span>*</span></label>
                    <input class="hd-formfild" type="email" id="email" name="email" required maxlength="100">
                    <small class="form-hint"><?php esc_html_e('This will be your login email', 'flexcore-server'); ?></small>
                                    <div class="field-error" id="error-email"></div>
                </div>

            </div>

            <div class="hd-col-6">
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
                   
                    <div class="field-error" id="error-password"></div>
                    </div>
                 
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

            <div class="hd-col-6">
                <div class="hd-signup-info">
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

            <div class="hd-col-12">
                <div class="hd-form-group consent-checkbox">
                    <label class="hd-label">
                        <input type="checkbox" id="consent" name="consent" required>
                        <span style="color:black"><?php esc_html_e('I confirm the above information is accurate, and agree for HappyDot.sg to collect and use it to contact me and process my membership registration.', 'flexcore-server'); ?></span>
                    </label>
                <div class="field-error" id="error-consent"></div>
                    
                </div>
            </div>

            <div class="hd-col-12 form-submit hd-form-btn">
                <button type="submit" class="button button-primary hd-btn" id="submitBtn"><?php esc_html_e('CREATE ACCOUNT', 'flexcore-server'); ?></button>
                <p class="login-link">
                    <?php esc_html_e('Already have an account?', 'flexcore-server'); ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>">
                        <?php esc_html_e('Login here', 'flexcore-server'); ?>
                    </a>
                </p>
            <div class="field-error" id="error-name"></div>
                
            </div>
        </div>

        <div id="register-message" class="flexcore-message" style="display: none;"></div>
    </form>
</div>
