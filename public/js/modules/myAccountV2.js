(function ($) {
  "use strict";

  const AccountDetailsFetcher = {
    init: function () {
      //   this.accountDetailsContainer = $(".account-details-wrapper .row");

      // Debug: Ensure container exists
      // console.log("Account Details container found:", this.accountDetailsContainer.length > 0);

      this.fetchAccountDetails();
    },
    fetchAccountDetails: function () {
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        method: "POST",
        dataType: "json",
        data: {
          action: "flexcore_get_account_details",
          nonce: flexcoreServerAjax.myAccountV2Nonce,
        },
        success: function (response) {
          if (response.success && response.data) {
            
            // Show stats bar with points/chances
            var points = response.data.data.currentPoints || 0;
            var chances = response.data.data.luckyDrawChances || 0;
            $('#myaccount-points').text(points.toLocaleString());
            $('#myaccount-chances').text(chances);
            $('.myaccount-stats-bar').show();

            // Greeting header: 'Hi, PREFERRED NAME' (+ full name on secondary line)
            var preferredName = response.data.data.metaData && response.data.data.metaData.preferredName;
            var fullName = response.data.data.fullName;
            var usePreferredName = !!(preferredName && preferredName !== fullName);
            $('#myaccount-display-name').text(usePreferredName ? preferredName : fullName);
            if (usePreferredName) {
                $('#myaccount-full-name').text(fullName).show();
            } else {
                $('#myaccount-full-name').hide().text('');
            }
            $('.myaccount-greeting').show();

            // Handle membershipStatus = 4: hide onboarding, show membership message
            if (response.data.data.membershipStatus == 4 || response.data.data.membershipStatus == '4') {
                $('#onboarding-section').hide();
                if (response.data.data.membershipMessageHtml) {
                    $('#membership-message-content').html(response.data.data.membershipMessageHtml);
                    $('#membership-message-box').show();
                }
                return; // skip onboarding step logic
            }

            // Show onboarding for status != 4
            $('#onboarding-section').show();
            $('#membership-message-box').hide();
                
            var isProfileComplete = response.data.data.metaData.isProfileCompleted;
            var lifeStyleSurveyCompleted = response.data.data.lifestyleStatus;
            var membershipStatus = response.data.data.membershipStatus;
            // Singpass users: skip Step 4 (verification) entirely
            var isSingpassUser = !!(response.data.data.metaData && response.data.data.metaData.myInfoSubject);

            // Early hide: ensure verification step never shows for Singpass users,
            // regardless of which onboarding branch runs below.
            if (isSingpassUser) {
                $(".profile-step.verification-step").hide();
                $(".profile-step.get-started-step .profile-step-number").text("4");
            }
            
            if (!isProfileComplete) {
              $(".profile-step.account-step").addClass("active");
              $(".profile-step.account-step .profile-step-number").addClass("active");
              $(".profile-step.account-step .profile-step-info").addClass("active");
              $(".profile-step.account-step .profile-step-number").removeClass("disable");
              $(".profile-step.account-step .profile-step-info").removeClass("disable");
              $(".profile-step.account-step .content").show();         
             
              
            //   $(".profile-step.survey-step").removeClass("active");
            //   $(".profile-step.survey-step .profile-step-number").removeClass("active");
            //   $(".profile-step.survey-step .profile-step-info").removeClass("active");
            //   $(".profile-step.survey-step .profile-step-info").addClass("disable");              
            //   $(".profile-step.survey-step .profile-step-number").removeClass("disable");
            //   $(".profile-step.survey-step .content").hide();
              
         
              
            //   $(".profile-step.verification-step").removeClass("active");
            //   $(".profile-step.verification-step .profile-step-number").removeClass("active");
            //     $(".profile-step.verification-step .profile-step-info").removeClass("active");
            //     $(".profile-step.verification-step .profile-step-info").addClass("disable");
            //   $(".profile-step.verification-step .profile-step-number").removeClass("disable");
              
            // $(".profile-step.get-started-step").removeClass("active");
            
            }
            else if (isProfileComplete && lifeStyleSurveyCompleted != 1) {
              $(".profile-step.account-step").removeClass("active");
              $(".profile-step.account-step .profile-step-number").removeClass("active");
             
             $(".profile-step.account-step .profile-step-number").addClass("completed");
             $(".profile-step.account-step .profile-step-number").html('<i aria-hidden="true" class="icon icon-check"></i>');
                $(".profile-step.account-step .profile-step-info").removeClass("active");
                $(".profile-step.account-step .profile-step-info").addClass("disable");
                 $(".profile-step.account-step .content").hide();  
                
              
              $(".profile-step.survey-step").addClass("active");
              $(".profile-step.survey-step .profile-step-number").addClass("active");
                $(".profile-step.survey-step .profile-step-number").removeClass("disable");
                $(".profile-step.survey-step .profile-step-info").removeClass("disable");
              $(".profile-step.survey-step .profile-step-info").addClass("active");
              $(".profile-step.survey-step .content").show();
              
                
              //   $(".profile-step.verification-step").removeClass("active");
              // $(".profile-step.verification-step .profile-step-number").removeClass("active");
              // $(".profile-step.verification-step .profile-step-number").removeClass("disable");
              //   $(".profile-step.verification-step .profile-step-info").removeClass("active");
              //   $(".profile-step.verification-step .profile-step-info").addClass("disable");
              
            }
            // Singpass users skip the verification step; go directly to Get Started
            else if (isSingpassUser) {
                $(".profile-step.account-step").removeClass("active");
                $(".profile-step.account-step .profile-step-number").removeClass("active");
                
                $(".profile-step.account-step .profile-step-info").removeClass("active");
                $(".profile-step.account-step .profile-step-info").addClass("disable");
                 $(".profile-step.account-step .content").hide();  
                 $(".profile-step.account-step .profile-step-number").addClass("completed");
             $(".profile-step.account-step .profile-step-number").html('<i aria-hidden="true" class="icon icon-check"></i>');
                
                $(".profile-step.survey-step").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").addClass("completed");
                $(".profile-step.survey-step .profile-step-number").html( '<i aria-hidden="true" class="icon icon-check"></i>');
                $(".profile-step.survey-step .profile-step-info").removeClass("active");
                $(".profile-step.survey-step .profile-step-info").addClass("disable");
              $(".profile-step.survey-step .content").hide();
                
                // Skip verification — go straight to Get Started
               $(".profile-step.get-started-step").addClass("active");
                $(".profile-step.get-started-step .profile-step-number").addClass("active");
                    $(".profile-step.get-started-step .profile-step-number").removeClass("disable");
                $(".profile-step.get-started-step .profile-step-info").removeClass("disable");
            }
            else if(membershipStatus !=4){
                $(".profile-step.account-step").removeClass("active");
                $(".profile-step.account-step .profile-step-number").removeClass("active");
                
                $(".profile-step.account-step .profile-step-info").removeClass("active");
                $(".profile-step.account-step .profile-step-info").addClass("disable");
                 $(".profile-step.account-step .content").hide();  
                 $(".profile-step.account-step .profile-step-number").addClass("completed");
             $(".profile-step.account-step .profile-step-number").html('<i aria-hidden="true" class="icon icon-check"></i>');
                
                
                
                $(".profile-step.survey-step").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").addClass("completed");
                $(".profile-step.survey-step .profile-step-number").html( '<i aria-hidden="true" class="icon icon-check"></i>');
                $(".profile-step.survey-step .profile-step-info").removeClass("active");
                $(".profile-step.survey-step .profile-step-info").addClass("disable");
              $(".profile-step.survey-step .content").hide();
                
                
                $(".profile-step.verification-step").addClass("active");
                $(".profile-step.verification-step .profile-step-number").addClass("active");
                    $(".profile-step.verification-step .profile-step-number").removeClass("disable");
                $(".profile-step.verification-step .profile-step-info").removeClass("disable");
                $(".profile-step.verification-step .profile-step-info").addClass("active");
            }
            
            else{
              
                $(".profile-step.account-step").removeClass("active");
                $(".profile-step.account-step .profile-step-number").removeClass("active");
                
                $(".profile-step.account-step .profile-step-info").removeClass("active");
                $(".profile-step.account-step .profile-step-info").addClass("disable");
                 $(".profile-step.account-step .content").hide();  
                 $(".profile-step.account-step .profile-step-number").addClass("completed");
             $(".profile-step.account-step .profile-step-number").html('<i aria-hidden="true" class="icon icon-check"></i>');
                
                
                
                $(".profile-step.survey-step").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").removeClass("active");
                $(".profile-step.survey-step .profile-step-number").addClass("completed");
                $(".profile-step.survey-step .profile-step-number").html( '<i aria-hidden="true" class="icon icon-check"></i>');
                $(".profile-step.survey-step .profile-step-info").removeClass("active");
                $(".profile-step.survey-step .profile-step-info").addClass("disable");
              $(".profile-step.survey-step .content").hide();
                
                
                $(".profile-step.verification-step").removeClass("active");
                $(".profile-step.verification-step .profile-step-number").removeClass("active");              
                $(".profile-step.verification-step .profile-step-number").addClass("completed");
                $(".profile-step.verification-step .profile-step-number").html( '<i aria-hidden="true" class="icon icon-check"></i>');
                $(".profile-step.verification-step .profile-step-info").removeClass("active");
                $(".profile-step.verification-step .profile-step-info").addClass("disable");
                
               $(".profile-step.get-started-step").addClass("active");
                $(".profile-step.get-started-step .profile-step-number").addClass("active");
                    $(".profile-step.get-started-step .profile-step-number").removeClass("disable");
                $(".profile-step.get-started-step .profile-step-info").removeClass("disable");
                // $(".profile-step.get-started-step .profile-step-info").addClass("active");
              
            }                 
              
         
          } else {
            console.error("Failed to fetch account details:", response);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
        },
      });
    },
  };
  $(document).ready(function () {
    $(".profile-step.survey-step .content").hide();
    $(".profile-step.account-step .content").hide(); 
    AccountDetailsFetcher.init();
  });
})(jQuery);
