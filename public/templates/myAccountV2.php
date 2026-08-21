<?php
$profile_data = FlexCore_Server_Session::get_user_profile();
$profile = (isset($profile_data['userData']) && is_array($profile_data['userData'])) ? $profile_data['userData'] : $profile_data;
$is_singpass_user = !empty($profile['metaData']['myInfoSubject']);
$membership_status = FlexCore_Server_Session::get_user_membership_status();
?>

<!-- Greeting Header: preferred name (red) + account name (normal) below (hidden initially, populated by JS) -->
<div class="myaccount-greeting" style="display:none;">
    <h2 id="myaccount-preferred-name" style="color:#CA0D07; font-weight:600;"></h2>
    <div id="myaccount-account-name"></div>
</div>

<!-- Top Right: Balance + Chances (hidden initially, populated by JS) -->
<div class="myaccount-stats-bar" style="display:none;">
    <div class="stats-item">
        <span class="stats-icon">🪙</span>
        <div class="stats-text">
            <span class="stats-label">Current Balance</span>
            <span class="stats-value" id="myaccount-points">0</span>
            <span class="stats-unit">Points</span>
        </div>
    </div>
    <div class="stats-item">
        <span class="stats-icon">💼</span>
        <div class="stats-text">
            <span class="stats-label">Lucky Draw</span>
            <span class="stats-value" id="myaccount-chances">0</span>
            <span class="stats-unit">Chances</span>
        </div>
    </div>
</div>

<!-- Membership Message Box (shown when status=4, hidden initially) -->
<div class="myaccount-membership-box" id="membership-message-box" style="display:none;">
    <div class="membership-message-content" id="membership-message-content"></div>
</div>

<!-- Onboarding Steps (shown when status != 4) -->
<div class="myaccount-infodiv" id="onboarding-section" style="display:none;">
    <h2>Complete Your Account To Continue</h2>
    <h4>Awesome! You're almost there to become a HappyDotter!</h4>
    <p>Click on the following checkpoints to complete your registration</p>

    <div class="complete-profile-wrapper">
        <!-- Step 1: Welcome (always completed) -->
        <div class="profile-step welcome-step">
            <div class="profile-step-number completed"><i aria-hidden="true" class="icon icon-check"></i></div>
            <div class="profile-step-info">
                <div class="heading-wrap">
                    <h3>Hello & Welcome!</h3>
                    <p>Your account is successfully created.</p>
                </div>
            </div>
        </div>

        <!-- Step 2: Complete Profile -->
        <div class="profile-step account-step">
            <div class="profile-step-number">2</div>
            <div class="profile-step-info disable">
                <div class="heading-wrap">
                    <h3>Complete Your Profile</h3>
                    <p>Your profile helps us send you the most relevant surveys. Keep it accurate and up-to-date.</p>
                </div>
                <div class="content">
                    <a href="/my-profile/" class="hd-btn">Complete</a>
                </div>
            </div>
        </div>

        <!-- Step 3: Lifestyle Survey -->
        <div class="profile-step survey-step">
            <div class="profile-step-number">3</div>
            <div class="profile-step-info disable">
                <div class="heading-wrap">
                    <h3>Take Part In Lifestyle Survey</h3>
                    <p>Share a bit more about yourself and choose your Welcome Voucher.</p>
                </div>
                <div class="content">
                    <button class="hd-btn" id="take_survey">Take Part</button>
                </div>
            </div>
        </div>

        <?php if (!$is_singpass_user): ?>
        <!-- Step 4: Verification (non-Singpass only) -->
        <div class="profile-step verification-step">
            <div class="profile-step-number">4</div>
            <div class="profile-step-info disable">
                <div class="heading-wrap">
                    <h3>Complete Account Verification</h3>
                    <p>A quick confirmation of your details against your NRIC to ensure everything is in order*</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Final Step: Get Started -->
        <div class="profile-step get-started-step">
            <div class="profile-step-number"><?php echo $is_singpass_user ? '4' : '5'; ?></div>
            <div class="profile-step-info">
                <div class="heading-wrap">
                    <h3>Get Started</h3>
                    <p>Receive your welcome voucher, start doing surveys and earn happypoints</p>
                    <?php if (!$is_singpass_user): ?>
                    <p><br><br>*Note: A simple ID confirmation is necessary. Our team will be reaching out to you - please do extend your kind cooperation</p>
                    <p>(HappyDot.sg is part of RySense, a Singapore based research organization where we pride ourselves in the authenticity of our data collection. Hence, verifying our member's details is part of our onboarding process in making sure our community is genuine and legitimate.)</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
