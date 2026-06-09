<?php
/**
 * Login form template
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
select{
background: #ffffff !important;
}
#referral_code_label
{
display:none;
}
#referral_code{
display:none;
}
.hd-formfild:disabled {
    border: 1px solid, #7A7A7A !important;
    background:  #ECECEC !important;
}
    </style>
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
<div class="flexcore-form flexcore-login-form">
    <?php
    // Debug information
    if (WP_DEBUG) {
        echo '<div style="display:none" id="debug-info">';
        echo 'AJAX URL: ' . esc_url(admin_url('admin-ajax.php')) . '<br>';
        echo 'Nonce: ' . wp_create_nonce('flexcore_login') . '<br>';
        echo '</div>';
    }
    ?>
    <form id="flexcore-login-form" novalidate>
        <?php wp_nonce_field('flexcore_login', 'flexcore_nonce'); ?>
        <input type="hidden" id="login_attempts" name="login_attempts" value="0">

        <div class="hd-admin-login">
            <div class="hd-form-group">
                <label class="hd-label" for="email"><?php esc_html_e('Email Address', 'flexcore-server'); ?></label>
                <input class="hd-formfild" type="email" id="email" name="email" required />
                  <div class="field-error" id="error-email"></div>
            </div>

            <div class="hd-form-group">
                <label class="hd-label" for="password"><?php esc_html_e('Password', 'flexcore-server'); ?></label>
                <div class="position-relative password-eye">
                    <input class="hd-formfild password-input" type="password" id="password" name="password" required />
                    <span class="hd-login-toggle-password"><i class="fa fa-eye"></i></span>
                      <div class="field-error" id="error-password"></div>
                </div>
            </div>

           

            <!-- <div class="form-links" style="margin-bottom: 10px;">
                <a href="<?php echo esc_url(get_permalink(get_option('flexcore_forgot_password_page'))); ?>">
                    <?php esc_html_e('Forgot Password?', 'flexcore-server'); ?>
                </a>
            </div> -->

            <div class="hd-form-btn">
                <button type="submit" class="hd-btn"><?php esc_html_e('Log In', 'flexcore-server'); ?></button>
            </div>
             <div id="login-message" class="flexcore-message" style="display: none;"></div>
        </div>
    </form>

</div>
<script>
    // console.log('FlexCore Debug:', {
    //     ajaxUrl: flexcoreServerAjax.ajaxUrl,
    //     loginUrl: flexcoreServerAjax.loginUrl,
    //     dashboardUrl: flexcoreServerAjax.dashboardUrl
    // });
</script>
