<?php
$profile_data = FlexCore_Server_Session::get_user_profile();
$profile = (isset($profile_data['userData']) && is_array($profile_data['userData'])) ? $profile_data['userData'] : $profile_data;
$is_singpass_user = !empty($profile['metaData']['myInfoSubject']);
$membership_status = FlexCore_Server_Session::get_user_membership_status();
?>

<!-- Header: name group (left) + balance/stats (right), hidden initially -->
<div class="myaccount-header" style="display:none;">
    <div class="myaccount-name-wrap">
        <h2 id="myaccount-preferred-name" style="color:var(--red, #D92632); font-weight:700; margin:0;"></h2>
        <div id="myaccount-account-name"></div>
        <div id="myaccount-joined-date" class="myaccount-joined-date"></div>
    </div>
    <div class="myaccount-stats-bar">
        <div class="myaccount-balance">
            <div class="balance-line">
                <span class="balance-label">Current Balance</span>
                <span class="balance-icon">🪙</span>
                <span class="balance-number" id="myaccount-points">0</span>
                <span class="balance-unit">Points</span>
            </div>
            <div class="points-expiry" id="myaccount-points-expiry" style="display:none;"></div>
        </div>
        <div class="myaccount-stat-boxes">
            <div class="stat-box">
                <div class="stat-box-top">
                    <span class="stat-icon">💬</span>
                    <span class="stat-value" id="myaccount-surveys-done">0</span>
                </div>
                <span class="stat-label">Surveys completed</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-top">
                    <span class="stat-icon">🎁</span>
                    <span class="stat-value" id="myaccount-lucky-draw">0</span>
                </div>
                <span class="stat-label">Lucky Draw</span>
            </div>
            <div class="stat-box">
                <div class="stat-box-top">
                    <span class="stat-icon">🧰</span>
                    <span class="stat-value" id="myaccount-game-chances">0</span>
                </div>
                <span class="stat-label">Chances</span>
            </div>
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
                    <h3>HELLO &amp; WELCOME</h3>
                    <p>Your account is successfully created.</p>
                </div>
            </div>
        </div>

        <!-- Step 2: Lifestyle Survey -->
        <div class="profile-step survey-step">
            <div class="profile-step-number">2</div>
            <div class="profile-step-info disable">
                <div class="heading-wrap">
                    <h3>YOUR LIFESTYLE SURVEY</h3>
                    <p>Share a bit more about yourself and get your Welcome Voucher</p>
                </div>
                <div class="content">
                    <button class="hd-btn" id="take_survey">TAKE PART</button>
                </div>
            </div>
        </div>

        <!-- Unnumbered: Complete Profile Verification with Singpass (between step 2 and step 3) -->
        <div class="profile-step verification-step<?php echo $is_singpass_user ? ' singpass-greyed' : ''; ?>">
            <div class="profile-step-number" style="visibility:hidden;"></div>
            <div class="profile-step-info<?php echo $is_singpass_user ? ' disable' : ''; ?>">
                <div class="heading-wrap">
                    <h3>COMPLETE PROFILE VERIFICATION WITH SINGPASS <span class="verification-points">+50 HappyPoints</span></h3>
                    <p>Quick profile verification by clicking on Retrieve Myinfo with Singpass on My Profile page.</p>
                </div>
            </div>
        </div>

        <!-- Final Step: Get Started -->
        <div class="profile-step get-started-step">
            <div class="profile-step-number">3</div>
            <div class="profile-step-info">
                <div class="heading-wrap">
                    <h3>GET STARTED!</h3>
                    <p>Receive your welcome voucher, start doing surveys and earn happypoints</p>
                </div>
            </div>
        </div>
    </div>
</div>
