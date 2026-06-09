document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);

    // Auto-fill and hide referral code if present
    const referralCode = urlParams.get('referral_code');
    const referralInput = document.getElementById('referral_code');
    const referralLabel = document.getElementById('referral_code_label');
    
    const sourceSelect = document.getElementById('register_source');
    const sourceFormGroup = document.getElementById('source-group');
    const utmStringInput = document.getElementById('utm_string');
    // const referedByAFriend= document.getElementById('register_source');
    
        
    if (referralCode) {
        referralInput.value = referralCode;
        referralInput.style.display = 'block';
        referralLabel.style.display = 'block';
        referralInput.readOnly = true;
        sourceSelect.value = "Referred by a friend";
        sourceSelect.disabled = true;
        utmStringInput.value = 'utm_source=email&utm_medium=referral&utm_campaign=raf-happydotter';
    } else {
        // referralInput.closest('.hd-form-group').style.display = 'none';
    }

    const registerSource = document.getElementById('register_source');
    const campaignInput = document.getElementById('campaign_id');
    
    let campaignId = '';
    if (urlParams.has('campaign_id')) {
        campaignId = urlParams.get('campaign_id');
    } else {
        campaignId = 'become-a-happydotter';
    }
    campaignInput.value = campaignId;

    const utm_string_present = (
        urlParams.has('fbclid') ||
        urlParams.has('utm_source') ||
        urlParams.has('utm_medium') ||
        urlParams.has('utm_campaign')
    );

    const referral_code_absent = !urlParams.has('referral_code');
    if (utm_string_present && referral_code_absent) {
        const fullUTMString = window.location.search.substring(1);
        utmStringInput.value = fullUTMString;
        // registerSource.value = 'I saw it from a brochure';
        registerSource.required = false;

        if (registerSource) {
            registerSource.style.require = 'none';
            sourceFormGroup.style.display = 'none';
        }
    } else {
        if (registerSource && campaignInput) {
            registerSource.addEventListener('change', function () {
                const howHeard = this.value.trim();
                let utmString = '';
                if(howHeard !== '') {
                    registerSource.classList.remove('has-error');
                    document.getElementById('error-register_source').style.display = 'none';
                }
                switch (howHeard) {
                    case 'I saw it from a brochure':
                        utmString = 'utm_source=brochure&utm_medium=offline&utm_campaign=manual_entry';
                        referralInput.style.display = 'none';
                        referralLabel.style.display = 'none';
                        referralInput.required = false;
                        break;
                    case 'Referred by a friend':
                        // console.log('Referral code present, setting UTM string');
                        utmString = 'utm_source=email&utm_medium=referral&utm_campaign=raf-happydotter';
                        referralInput.style.display = 'block';
                        referralLabel.style.display = 'block';
                        referralInput.required = true;  
                        
                        break;
                    case 'I heard about it from family/friends':
                        utmString = 'utm_source=wom&utm_medium=organic&utm_campaign=wom-generic-signup';
                        referralInput.style.display = 'none';
                        referralLabel.style.display = 'none';
                        referralInput.required = false;
                        break;
                    case 'I saw it from the search engine results (e.g. Google/Bing)':
                        utmString = 'utm_source=google&utm_medium=seo&utm_campaign=seo-generic-signup';
                        referralInput.style.display = 'none';
                        referralLabel.style.display = 'none';
                        referralInput.required = false;
                        break;
                    default:
                        utmString = utmString;
                        referralInput.style.display = 'none';
                        referralLabel.style.display = 'none';
                        referralInput.required = false;
                }

                utmStringInput.value = utmString;
            });
        }
        else{
            
        }
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');

    const lengthReq = document.getElementById('flexcore_length');
    const uppercaseReq = document.getElementById('flexcore_uppercase');
    const lowercaseReq = document.getElementById('flexcore_lowercase');
    const numberReq = document.getElementById('flexcore_number');
    const specialReq = document.getElementById('flexcore_special');

    passwordInput.addEventListener('input', function () {
        const value = passwordInput.value;

        if (value.length >= 12 && value.length <= 15) {
            lengthReq.classList.add('valid');
        } else {
            lengthReq.classList.remove('valid');
        }

        if (/[A-Z]/.test(value)) {
            uppercaseReq.classList.add('valid');
        } else {
            uppercaseReq.classList.remove('valid');
        }

        if (/[a-z]/.test(value)) {
            lowercaseReq.classList.add('valid');
        } else {
            lowercaseReq.classList.remove('valid');
        }

        if (/\d/.test(value)) {
            numberReq.classList.add('valid');
        } else {
            numberReq.classList.remove('valid');
        }

        if (/[!@#$%^&*()]/.test(value)) {
            specialReq.classList.add('valid');
        } else {
            specialReq.classList.remove('valid');
        }
    });
});
