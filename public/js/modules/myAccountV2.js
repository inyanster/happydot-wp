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
                
            var isProfileComplete = response.data.data.metaData.isProfileCompleted;
            var lifeStyleSurveyCompleted = response.data.data.lifestyleStatus;
            var membershipStatus = response.data.data.membershipStatus;
            
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
