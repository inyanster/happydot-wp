<?php
$profile_data = FlexCore_Server_Session::get_user_profile();
// $avatar_id=$avatar_id = isset($profile_data['avatarId']) ? $profile_data['avatarId'] : '23400';
if (isset($profile_data['userData']['metaData']['referralCode'])) {

    $referralCode = $profile_data['userData']['metaData']['referralCode'];
} elseif (isset($profile_data['metaData']['referralCode'])) {
    error_log('Avatar ID found in metaData');


    $referralCode = $profile_data['metaData']['referralCode'];
} else {

    $referralCode = '';
}

$referral_src = site_url("referralsignup/?referral_code={$referralCode}");
?>



<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
    }

    .referral-box {
        border: 2px solid #ccc;
        padding: 20px;
        max-width: 650px;
        margin: auto;
    }

    .referral-box h2 {
        color: red;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .referral-input {
        flex-grow: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
    }

    .copy-button {
        background-color: #;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
    }

    .copy-button:hover {
        background-color: #45a049;
    }

    .icon-row {
    display: flex;
    justify-content: center; /* Center align */
    gap: 15px;
    margin-top: 20px;
    align-items: center;
    flex-wrap: wrap; /* Optional: Makes icons wrap on smaller screens */
}


    .icon-row .icon-wrapper img {
        display:block !important;
    width: 50px;
    height: 50px;
    cursor: pointer;
    border-radius: 50%;
    /* border: 2px solid #ccc; Optional */
    /* padding: 5px; */
    /* background-color: #f8f8f8; Optional */
    object-fit: cover;
}


    .cross-icon {
        position: relative;
    }

    .cross-icon::after {
        content: '✖';
        color: red;
        font-weight: bold;
        position: absolute;
        top: -10px;
        left: 12px;
        font-size: 28px;
        pointer-events: none;
    }
    .icon-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 12px;
    text-align: center;
    width: 60px;
}
.icon-wrapper span {
    margin-top: 6px;
    color: #333;
}
#more{
    border: 2px solid #000000;  
}
</style>
<p> Your referral code is  <strong>"<?php echo esc_html($referralCode); ?>"</strong></p>
<div class="referral-box">
    <h2>Refer your friends and family now!</h2>
    
    <div class="input-group">
        <input
            type="text"
            id="referralLink"
            class="referral-input"
            value="<?php echo esc_url($referral_src); ?>"
            readonly />
        <button class="copy-button" onclick="copyLink()">Copy</button>
    </div>

   <div class="icon-row">
    <div class="icon-wrapper">
        <img src="<?php echo site_url('/wp-content/uploads/social/whatsapp.png'); ?>" alt="Whatsapp" />
        <span>WhatsApp</span>
    </div>
    <div class="icon-wrapper">
        <img id="sms" src="<?php echo site_url('/wp-content/uploads/social/sms.png');?>" alt="SMS" />
        <span>SMS</span>
    </div>
    <!-- <div class="icon-wrapper">
        <img src="<?php //echo site_url('/wp-content/uploads/social/messanger.png'); ?>" alt="Messanger" />
        <span>Messenger</span>
    </div> -->
    <!-- <div class="icon-wrapper">
        <img src="<?php //echo site_url('/wp-content/uploads/social/instagram.png'); ?>" alt="Instagram" />
        <span>Instagram</span>
    </div> -->
    <div class="icon-wrapper">
        <img src="<?php echo site_url('/wp-content/uploads/social/gmail.png'); ?>" alt="Gmail" />
        <span>Email</span>
    </div>
    <div class="icon-wrapper">
        <img id="more" src="<?php echo site_url('/wp-content/uploads/social/more.jpg'); ?>" alt="More" />
        <span>More</span>
    </div>
</div>

</div>

<script>
    // Make this available globally
    function copyLink() {
    const linkInput = document.getElementById("referralLink");
    const copyButton = document.querySelector(".copy-button");

    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand("copy");

    // Change button text to "Copied"
    copyButton.textContent = "Copied";

    // Revert back to "Copy" after 2 seconds
    setTimeout(() => {
        copyButton.textContent = "Copy";
    }, 2000);
}


    // Keep event listeners inside DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {
        const whatsappIcon = document.querySelector('img[alt="Whatsapp"]');
        whatsappIcon.addEventListener('click', function () {
            const link = document.getElementById('referralLink').value;
            const message = `"Hey! Join me at HappyDot.sg where you can earn rewards for sharing your opinions on local hot button issues. Sign Up now using my referral link: ${link} and get a $10 Welcome Voucher!"`;
            const whatsappURL = `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;
            window.open(whatsappURL, '_blank');
        });

        const smsIcon = document.getElementById('sms');
        smsIcon.addEventListener('click', function () {
            const link = document.getElementById('referralLink').value;
            const message = `"Hey! Join me at HappyDot.sg where you can earn rewards for sharing your opinions on local hot button issues. Sign Up now using my referral link: ${link} and get a $10 Welcome Voucher!"`;
            const smsURL = `sms:?&body=${encodeURIComponent(message)}`;
            window.open(smsURL);
        });

        const gmailIcon = document.querySelector('img[alt="Gmail"]');
        gmailIcon.addEventListener('click', function () {
            const link = document.getElementById('referralLink').value;
            const subject = 'Join me at HappyDot.sg';
            const message = `"Hey! Join me at HappyDot.sg where you can earn rewards for sharing your opinions on local hot button issues. Sign Up now using my referral link: ${link} and get a $10 Welcome Voucher!"`;
            const mailtoLink = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoLink;
        });

        const more = document.getElementById('more');
        more.addEventListener('click', function () {
            const link = document.getElementById('referralLink').value;
            const text = `"Hey! Join me at HappyDot.sg where you can earn rewards for sharing your opinions on local hot button issues. Sign Up now using my referral link: ${link} and get a $10 Welcome Voucher!"`;
            if (navigator.share) {
                navigator.share({
                    title: 'Join HappyDot',
                    text: text,
                    url: link,
                }).catch(console.error);
            } else {
                alert('Sharing not supported in this browser.');
            }
        });
    });
</script>
