<?php
/**
 * Profile form template
 *
 * @package FlexCore_Server
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
input, select,textarea{
cursor: pointer;
}
    .has-error {
        background-color: rgba(245, 159, 159, 0.75) !important;
        border: 1px solid red !important;
    }

    .is-valid {
        background-color:rgba(173, 242, 189, 0.59) !important;

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
.form-error{
        color: red;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }
    .field-error {
        color: red !important;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    input[type="password"]::-ms-reveal {
        display: none !important;
    }
    .plus-symbol {
    font-size: 18px;
    color: #D92632; /* WordPress primary blue */
    font-weight: bold;
    padding: 2px 6px;
   width:110px;
    border-radius: 4px;
    
}
input.hd-formfild:read-only,
textarea.hd-formfild:read-only,
select.hd-formfild:disabled {
    border: 1px solid, #7A7A7A !important;
    background: #7a7a7a82  !important;
}

select{
 border: 1px solid, #7A7A7A !important;
    background: #ffffff  !important;
}
.field-error {
    color: red;
    font-size: 12px;
    margin-top: 4px;
    /* display: none; */
}
#dob{
cursor: pointer;}
</style>
<div class="hd-my-profile">
    <h2><?php esc_html_e('PROFILE DETAILS', 'flexcore-server'); ?></h2>
    <p class="hd-form-navigeter"><?php esc_html_e('Please fill in all fields as they are mandatory', 'flexcore-server'); ?></p>

    <form id="flexcore-profile-form" novalidate>
        <?php 
        wp_nonce_field('flexcore_profile_get', 'get_profile_nonce');
        wp_nonce_field('flexcore_profile_update', 'update_profile_nonce');
        ?>
        <input type="hidden" id="register_nonce" name="nonce" value="<?php echo wp_create_nonce('flexcore_register'); ?>">

        <div class="hd-form-group">
            <label class="hd-label" for="email"><?php esc_html_e('Email', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <input class="hd-formfild" type="email" id="email" name="email" readonly>
            <small class="form-hint"><?php esc_html_e('Email cannot be changed', 'flexcore-server'); ?></small>
            <p class="email-error field-error" style="display: none;"></p>
        </div>

        <div class="hd-form-group">
            <label class="hd-label" for="name"><?php esc_html_e('Full name (As per NRIC)', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span>
                <span><?php esc_html_e('Full name should consist of your given & family name', 'flexcore-server'); ?></span>
            </label>
            <input class="hd-formfild" type="text" id="name" name="name" required maxlength="100" readonly>
            <p class="name-error field-error" style="display: none;"></p>
        </div>

        <div class="hd-form-group">
            <label class="hd-label" for="dob"><?php esc_html_e('Date of Birth', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span>
                
            </label>
           <input class="hd-formfild dob-input" type="date" id="dob" name="dob"
       placeholder="YYYY-MM-DD" required readonly
       pattern="^\d{4}-\d{2}-\d{2}$"
       max="<?php echo esc_attr( date('Y-m-d', strtotime('-15 years')) ); ?>">

            
            <small class="form-hint form-error dob-error" style="display: none;">
                <?php esc_html_e('Must be 15 years or older', 'flexcore-server'); ?>
            </small>
            <p class="dob-error field-error" style="display: none;"></p>
        </div>

        <div class="row">
            <div class="hd-col-4">
                <div class="hd-form-group">
                    <label class="hd-label" for="gender"><?php esc_html_e('Gender', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
                    <select class="hd-formfild" id="gender" name="gender" required disabled>
                        <option value=""><?php esc_html_e('Select gender', 'flexcore-server'); ?></option>
                        <option value="male"><?php esc_html_e('Male', 'flexcore-server'); ?></option>
                        <option value="female"><?php esc_html_e('Female', 'flexcore-server'); ?></option>
                    </select>
                     <p class="gender-error field-error" style="display: none;"></p>
                </div>
                
            </div>

            <div class="hd-col-4">
                <div class="hd-form-group">
                    <label class="hd-label" for="race"><?php esc_html_e('Race', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
                    <select class="hd-formfild" id="race" name="race" required disabled>
                        <option value=""><?php esc_html_e('Select race', 'flexcore-server'); ?></option>
                        <option value="chinese"><?php esc_html_e('Chinese', 'flexcore-server'); ?></option>
                        <option value="malay"><?php esc_html_e('Malay', 'flexcore-server'); ?></option>
                        <option value="indian"><?php esc_html_e('Indian', 'flexcore-server'); ?></option>
                        <option value="eurasian"><?php esc_html_e('Eurasian', 'flexcore-server'); ?></option>
                        <option value="others"><?php esc_html_e('Others', 'flexcore-server'); ?></option>
                    </select>
                     <p class="race-error field-error" style="display: none;"></p>
                </div>
            </div>

            
            <div class="hd-col-4">
                <div class="hd-form-group">
                    <label class="hd-label" for="postal_code"><?php esc_html_e('Postal Code', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
                    <input class="hd-formfild" type="text" id="postal_code" name="postal_code" required maxlength="6" pattern="^\d{6}$" placeholder="Postal Code">
                    <div class="field-error postal-error" style="display: none;"></div>
                </div>
</div>
        </div>
<div class="hd-col-12" id="othersRaceGroup">
             <div class="hd-form-group" >
            <label class="hd-label" for="othersRace"><?php esc_html_e('Please Specify', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span>
                
            </label>
            <input class="hd-formfild" type="text" id="others" name="others" value=""  maxlength="100" readonly>
             <p class="race_details-error field-error" style="display: none;"></p>
        </div>
        
        </div>

        <div class="hd-form-group">
            <label class="hd-label" for="mobile"><?php esc_html_e('Mobile No.', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span>
                
            </label>
            <div style="display: flex; align-items: center; gap: 5px;">
            <!-- <span class="plus-symbol"><select class="country-code"><option>+65</option></select></span> -->
            <input class="hd-formfild" type="text" id="mobile" name="mobile"  required maxlength="8" 
                   placeholder="Phone Number" data-mask="mobile">
                   </div>
            <small class="form-hint form-error mobile-error" style="display: none;">
                <?php esc_html_e('Please enter a valid international phone number', 'flexcore-server'); ?>
            </small>
             <p class="mobileNo-error field-error" style="display: none;"></p>
            
        </div>

        <div class="hd-form-group">
            <label class="hd-label" for="citizenship"><?php esc_html_e('Citizenship', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <select class="hd-formfild" id="citizenship" name="citizenship" required>
                <option value=""><?php esc_html_e('Select citizenship', 'flexcore-server'); ?></option>
                <option value="singaporecitizen"><?php esc_html_e('Singapore Citizen', 'flexcore-server'); ?></option>
                <option value="permanentResident"><?php esc_html_e('Permanent Resident', 'flexcore-server'); ?></option>
            </select>
             <p class="citizen-error field-error" style="display: none;"></p>
        </div>

        <div class="hd-condition-read">
            <input class="hd-checkbox" type="checkbox" id="consent" name="consent" required>
            <label class="hd-label" for="consent"><?php esc_html_e('I hereby confirm that the particulars I have provided are accurate and complete and I consent to the utilization of my personal data for HappyDot.sg and services related to it.', 'flexcore-server'); ?></label>
            <p class="consent-error field-error" style="display: none;"></p>
        </div>

        <div class="hd-form-btn">
            <input class="hd-btn" type="submit"  id="submit-btn" value="<?php esc_html_e('Save', 'flexcore-server'); ?>" />
        </div>

        <div id="profile-message" class="flexcore-message" style="display: none;"></div>
    </form>
</div>
