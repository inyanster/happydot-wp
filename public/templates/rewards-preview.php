<style>
   
    img{
        display: block !important;
        object-fit: contain !important;
    }
    .redeemform-btndiv .hd-btn {
    margin-top: 42px !important;
    margin-bottom: 15px !important;
}

.error-message {
  color: red !important;
  font-size: 0.9rem !important;
  margin-top: 4px !important;
  display: block !important;
}
 .flexcore_requiments li.valid {
        color: green !important;
    }
    #referral_code, #referral_code_label {
        display: none !important;
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
  padding: 12px 16px !important;
  border-radius: 8px !important;
  margin-top: 16px !important;
  margin-bottom: 16px !important;
  
  font-size: 15px !important;
  font-weight: 500 !important;
  transition: all 0.3s ease !important;
}

.flexcore-message.success {
  background-color: #e6f9ec !important;
  color: #256029 !important;
  border: 1px solid #8dd9a2 !important;
  box-shadow: 0 0 6px rgba(0, 128, 0, 0.1) !important;
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

.hd-form-group{
display: none;
}
</style>


<div class="rewards-balance-wrap" >
    <div class="rewards-balance-box">
        <h5>Current Balance</h5>
        <span class="rewards-points tmp"> Points</span>
    </div>
</div>
<div class="flexcore-message error" id="no-reward-message" style="display:none;">
  No reward found.
</div>
 <div id="reward-found" style="display:none;">
    
<div class="voucher-wrapper" >
    <h2 class="rewardType">Redeem E-Voucher</h2>
    <span class="amount-to-redeem">Amount to redeem:</span>
    <div class="row">
        <div class="voucher-col">
            <a href="javascript:void(0);" class="active">
                <div class="voucher-box">
                    <div class="voucher-img">
                        <img src="" class="objectFit-cover" alt="voucher" />
                    </div>
                    <div class="voucher-info">
                        <h3 class="voucher-title"></h3>
                       
                    </div>
                    <div class="voucher-ctn">
                        <p></p>
                         <p class="voucher-description"></p>
                        <span>-Points</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
 
<div class="redeem-form-wrap">
    <div id="info" style="margin-bottom: 30px;"></div>

    <div id="uniqgift-validation-wrapper" style="display: none; margin: 20px 0; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;">
        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0; font-family: inherit;">
            <input type="checkbox" id="uniqgift-ack-checkbox" style="margin-top: 4px; flex-shrink: 0; width: 16px; height: 16px;">
            <span style="font-size: 14px; line-height: 1.5; color: #333;">I acknowledge that this e-voucher must be utilised by <strong>31st May 2026</strong>. Unutilised vouchers will not be reinstated, and points used for the redemption will not be returned after this date.</span>
        </label>
        <p id="uniqgift-error-msg" class="field-error" style="display: none; color: #dc2626; font-size: 14px; margin-top: 10px; margin-bottom: 0; font-weight: 500;">Please acknowledge that the e-voucher must be utilised by 31st May 2026 before claiming the reward.</p>
    </div>

    <div class="hd-error">You do not have enough points to redeem.</div>
 
    <p id="fail" style="display:none;">Redemption failed – Error code and message from Forsta API response. Please contact us via Contact Us for further assistance.</p>
 
    <div class="redeem-success" style="display:none;">
        <p>Thank you for redeeming! The E-voucher will be sent to your email shortly. </p>
        <p id="message">If you do not receive your E-voucher in your inbox, please check your Spam/Junk inbox too as it may have landed there. Please contact us via Contact Us if you are unable to find it.</p>
        <a href="[site_url]/reward" class="hd-btn" data-swp-font-size="18px">Return</a>
    </div>
 
    <form method="post" id="RedeemRewards" novalidate>
        <input type="hidden" name="redeem_id" value="[post_id]" />
        <input type="hidden" name="redeem_voucher" value="[voucher_title]">
        <input type="hidden" name="action" value="hd_redeem_the_voucher">
 
        <p class="hd-error" id="error" style="display:none;"></p>
		<div class="row">
  <div class="hd-form-group hd-col-6" id="name-group">
            <label class="hd-label" for="name"><?php esc_html_e('Name', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <input class="hd-formfild" type="text" id="name" name="name" readonly>
           
            <p class="name-error field-error" id="name-error" style="display: none;"></p>
        </div>
        <div class="hd-form-group hd-col-6" id="email-group">
            <label class="hd-label" for="email"><?php esc_html_e('Email', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <input class="hd-formfild" type="email" id="email" name="email" readonly>
           
            <p class="email-error field-error" id="email-error" style="display: none;"></p>
        </div>
		<br>
        <div class="hd-form-group hd-col-6" id="contact-group">
            <label class="hd-label" for="contact"><?php esc_html_e('Mobile No.', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <input class="hd-formfild" type="tel" id="contact" name="contact" readonly>
           
            <p class="contact-error field-error" id="contact-error" style="display: none;"></p>
        </div>
        <div class="hd-form-group hd-col-6" id="address-group">
            <label class="hd-label" for="address"><?php esc_html_e('Address', 'flexcore-server'); ?><span style="color:red; font-size:20px; margin-left:2px">*</span></label>
            <input class="hd-formfild" type="text" id="address" name="address" readonly>
           
            <p class="address-error field-error" id="address-error" style="display: none;"></p>
        </div>
		
		
                <div class="hd-form-group hd-col-12 consent-checkbox" style="margin-bottom: 5px !important;" >
                    <label class="hd-label">
                        <input type="checkbox" id="consent" name="consent" >
                        <span style="color:black" class="declaration"></span>
                    </label>
                </div>

                <div class="hd-form-group hd-col-12 consent-checkbox-2" style="display:none; margin-bottom: 5px !important;" >
                    <label class="hd-label">
                        <input type="checkbox" id="consent2" name="consent2" >
                        <span style="color:black" class="declaration2"></span>
                    </label>
                </div>
                
                <div class="field-error" id="error-consent" style="display:none; margin-bottom: 15px;"></div>
                <div class="field-error" id="error-consent2" style="display:none; margin-bottom: 15px;"></div>
            </div>
        <div class="hd-form-btn redeemform-btndiv">
            <div class="hd-form-group-qun mb-0">
                <label class="hd-label" id="quantity">Quantity</label>
                <div class="qty-count-wrapper" id="evoucher">
                    <button class="qty-count qty-count-minus-flex" data-action="minus" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
                            <path d="M13.5 9.5L4.5 9.5" stroke="#212121" stroke-linecap="round" />
                        </svg>
                    </button>
                    <input class="numOf-qty" name="total_redeem_point" type="number" min="0" max="[max_quantity]" value="1" readonly />
                    <button class="qty-count qty-count-add-flex" data-action="add" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
                            <path d="M9 5L9 14" stroke="#212121" stroke-linecap="round" />
                            <path d="M13.5 9.5L4.5 9.5" stroke="#212121" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
               
            </div>
	 
            <input class="hd-btn hd-redeem-btn-check" type="submit" id="Redeem-" value="Redeem"  />
            <!-- <input class="hd-btn" type="submit" id="Redeem" value="Return to Rewards" /> -->
            <h4 class="redeem-counth4">- Points</h4>
        </div>
    
<p class="hd-error" id="error-quantity" style=" margin-top:2px;display:none;"></p>
    
</div>
 
<script>
    var voucherPrice = "[voucher_price]";
</script>
      
		
        <div id="reward-message"class="flexcore-message" ></div>
 </form>
 <div class="message-reward flexcore-message"  ></div>
 <div class="voucher-otp"></div>
<div class="termCondition-section">
    <div class="container">
        <div class="termCondition-wrapper">
            <div class="termCondition-tite">
                <h3>TERMS AND CONDITIONS</h3>
            </div>
            <div class="termCondition-ctn">
               
            </div>
            <!-- <ul class="termCondition-list">
                <li>View shop exclusion <a href="https://www.uniqgiftvoucher.com/egift-card-groceries-shop-exclusions" target="_blank">here</a>.</li>
                <li>Voucher denomination: $10 only</li>
                <li>This voucher is issued by Option Gift Pte Ltd which owns the registered trademark UNIQGIFT.</li>
                <li>This voucher must be presented before payment.</li>
                <li>This voucher cannot be exchanged for cash and any unused balance at expiry will not be refunded.</li>
                <li>This voucher cannot be replaced if lost, damaged, stolen or expired.</li>
                <li>Option Gift Pte Ltd reserves the right to vary these terms and conditions at any time without prior notice.</li>
                <li>Option Gift Pte Ltd shall not be responsible for any issue that arises in connection with the redemption and/or use of this voucher and shall not be responsible or held liable for any loss, injury, damage or harm suffered or incurred by or in connection with the redemption or use of the voucher by any person.</li>
                <li>Redeemed vouchers can be used at participating merchants that accept $10 redemption amount indicated on <a href="#">https://www.uniqgiftvoucher.com/egiftcard/</a></li>
            </ul> -->
        </div>
</div>
    </div>
</div>
