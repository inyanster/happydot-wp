<?php
/**
 * Change Password Form Template
 *
 * @package FlexCore_Server
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Load WordPress text domain for translations
load_plugin_textdomain('flexcore-server', false, dirname(plugin_basename(__FILE__)) . '/languages/');
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
        background-color:rgb(173, 242, 189) !important;

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
    .flexcore-change-password {
        max-width: 98%;
        margin: auto;
    }
</style>
<div class="flexcore-change-password">
    <h2><?php esc_html_e('Change Password', 'flexcore-server'); ?></h2>
    <div class="form-description">
    
    <form id="change-password-form" class="flexcore-form" novalidate>
        <?php wp_nonce_field('flexcore_change_password', 'change_password_nonce'); ?>

        <div class="hd-form-group">
            <label for="old_password" class="hd-label"><?php esc_html_e('Current Password', 'flexcore-server'); ?><span>*</span></label>
            <div class="position-relative password-eye">
            <input type="password" id="old_password" name="old_password" class="hd-formfild password-input" required>
             <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                </div>
                
        </div>
        <div class="current-pass-error"></div>

        <!-- START: Flex container for password fields and signup info -->
        <div class="password-section" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">
            <div class="password-fields" style="flex: 1 1 300px;">
                <div class="hd-form-group">
                    <label for="new_password" class="hd-label"><?php esc_html_e('New Password', 'flexcore-server'); ?><span>*</span></label>
                     <div class="position-relative password-eye">
                    <input type="password" id="new_password" name="new_password" class="hd-formfild password-input" required>
                     <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                </div>
                <div class="new-pass-error"></div>
                </div>

                <div class="hd-form-group">
                    <label for="confirm_password" class="hd-label"><?php esc_html_e('Confirm New Password', 'flexcore-server'); ?><span>*</span></label>
                     <div class="position-relative password-eye">
                    <input type="password" id="confirm_password" name="confirm_password" class="hd-formfild password-input" required>
                     <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                </div>
                <div class="confirm-pass-error"></div>
                </div>
            </div>

            <div class="hd-signup-info" style="flex: 1 1 300px;">
                <p><?php esc_html_e('Your password must:', 'flexcore-server'); ?></p>
                <ul class="requiments" style="font-size:15px">
                    <li id="length">Be between 12 - 15 characters in length</li>
                    <li id="uppercase">Contain at least 1 uppercase (capital) letter</li>
                    <li id="lowercase">Contain at least 1 lowercase (small) letter</li>
                    <li id="number">Contain at least 1 number</li>
                    <li id="special">Contain at least 1 of the following special characters (!@#$%^&*())</li>
                </ul>
                <h6 id="h6" class="hd-requirements-matched" style="display:none;">Password requirement all met!</h6>
            </div>
        </div>
        <!-- END: Flex container -->

        <div class="form-submit">
            <button type="submit" class="button hd-btn button-primary">
                <?php esc_html_e('Update Password', 'flexcore-server'); ?>
            </button>
        </div>
    </form>

    <div id="change-password-message" class="flexcore-message" style="display: none;"></div>
    <br>
    <br>
    <br>
    
</div>
</div>
