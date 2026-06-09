
    <style>
        input.has-error {
    border-color: #dc3545;
    background-color: #fff5f5;
}
input.is-valid {
    border-color: #28a745;
    background-color: #f0fff0;
}

    </style>
    
    <div class="hd-refer-friend-form">
        <form class="flexcore-refer-friend" method="POST">
              <input type="hidden" id="flexcore_refer_nonce" name="flexcore_nonce" value="<?php echo wp_create_nonce('flexcore_refer_nonce_action'); ?>" />
    <!-- other form fields -->
           

            <!-- Your Name -->
            <div class="row">
                <div class="hd-col-12">
                    <div class="hd-form-group">
                        <label class="hd-label">Your Preferred Name<span>*</span></label>
                        <input class="hd-formfild" name="referral_name" type="text" required />
                    </div>
                </div>
            </div>

            <div class="hd-hr"></div>

            <!-- Friend Row Container -->
            <div class="hd-friends-container">
                <div class="row hd-refer-friend-row flexcore-refer-friend-row">
                    <div class="hd-col-6">
                        <div class="hd-form-group">
                            <label class="flexcore_friendname hd-label">Friend’s Name<span>*</span></label>
                            <input class="hd-formfild" name="flexcore_friend_data[0][name]" type="text" required />
                        </div>
                    </div>
                    <div class="hd-col-6">
                        <div class="hd-form-group">
                            <label class=" flexcore_friendemail hd-label">Friend’s Email<span>*</span></label>
                            <input class="hd-formfild" name="flexcore_friend_data[0][email]" type="email" required />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Friend Button -->
            <button type="button" class="hd-btn flexcore-add-row">+ Add More Friends</button>

            <div class="hd-hr"></div>

            <!-- Confirmation -->
            <div class="hd-condition-read">
                <input class="hd-checkbox" id="refercheck" type="checkbox" required />
                <label for="refercheck" class="hd-label">
                    By clicking <strong>'Refer my friends now'</strong>, I confirm the above friend(s) have agreed to
                    share their email(s) with HappyDot.sg to contact them about becoming a HappyDotter.
                </label>
            </div>

            <!-- Error Message -->
            <p class="hd-error" style="display:none;">Duplicate entries found. Your friend(s) might have already signed up.
                Please review and submit again.</p>

            <!-- Submit -->
            <div class="hd-form-btn">
                <input class="hd-btn" type="submit" value="Refer my friends now" />
            </div>
        </form>
        <div class="refer-message" ></div>
    </div>

   