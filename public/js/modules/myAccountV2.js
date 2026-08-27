(function ($) {
  "use strict";

  const AccountDetailsFetcher = {
    init: function () {
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
        beforeSend: function () {
          window.FlexcoreSpinner && window.FlexcoreSpinner.show();
        },
        complete: function () {
          window.FlexcoreSpinner && window.FlexcoreSpinner.hide();
        },
        success: function (response) {
          if (response.success && response.data) {

            // Populate header stat fields
            var points = response.data.data.currentPoints || 0;
            var luckyDrawChances = response.data.data.luckyDrawChances || 0;
            var gameChances = response.data.data.gameChances || 0;
            var totalSurveyDone = response.data.data.totalSurveyDone || 0;
            var pointsExpiryNotice = response.data.data.pointsExpiryNotice || "";

            $("#myaccount-points").text(points.toLocaleString());
            $("#myaccount-surveys-done").text(totalSurveyDone);
            $("#myaccount-lucky-draw").text(luckyDrawChances);
            $("#myaccount-game-chances").text(gameChances);

            // Points-expiry line: show verbatim if non-empty, else hide
            if (pointsExpiryNotice) {
              $("#myaccount-points-expiry").text(pointsExpiryNotice).show();
            } else {
              $("#myaccount-points-expiry").hide().text("");
            }

            // Header: preferred name + full name (left)
            var preferredName = response.data.data.metaData && response.data.data.metaData.preferredName;
            var fullName = response.data.data.fullName || response.data.data.name || "";
            var showPreferred = !!(preferredName && preferredName !== fullName);
            $("#myaccount-preferred-name").text(showPreferred ? preferredName : fullName);
            if (showPreferred) {
              $("#myaccount-account-name").text(fullName).show();
            } else {
              $("#myaccount-account-name").hide().text("");
            }

            // Joined date (from dateWhenMembershipStarted)
            var joinedDate = response.data.data.dateWhenMembershipStarted;
            if (joinedDate) {
              var d = new Date(joinedDate);
              if (!isNaN(d.getTime())) {
                $("#myaccount-joined-date").text("Joined " + d.toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" }));
              }
            }

            $(".myaccount-header").show();

            // Handle membershipStatus = 4: hide onboarding, show membership message
            if (response.data.data.membershipStatus == 4 || response.data.data.membershipStatus == "4") {
              $("#onboarding-section").hide();
              if (response.data.data.membershipMessageHtml) {
                $("#membership-message-content").html(response.data.data.membershipMessageHtml);
                $("#membership-message-box").show();
              }
              return;
            }

            // Show onboarding for status != 4
            $("#onboarding-section").show();
            $("#membership-message-box").hide();

            var lifeStyleSurveyCompleted = response.data.data.lifestyleStatus;
            var isSingpassUser = !!(response.data.data.metaData && response.data.data.metaData.myInfoSubject);

            // Step 1: Welcome — always completed (markup provides the checkmark)

            // Step 2: Lifestyle Survey
            if (lifeStyleSurveyCompleted == 1) {
              $(".profile-step.survey-step .profile-step-number").addClass("completed");
              $(".profile-step.survey-step .profile-step-number").html('<i aria-hidden="true" class="icon icon-check"></i>');
              $(".profile-step.survey-step .profile-step-info").removeClass("active").addClass("disable");
              $(".profile-step.survey-step .content").hide();
            } else {
              $(".profile-step.survey-step").addClass("active");
              $(".profile-step.survey-step .profile-step-number").addClass("active");
              $(".profile-step.survey-step .profile-step-info").removeClass("disable").addClass("active");
              $(".profile-step.survey-step .content").show();
            }

            // Unnumbered verification box: greyed-out for Singpass, active otherwise
            if (isSingpassUser) {
              $(".profile-step.verification-step").addClass("singpass-greyed");
              $(".profile-step.verification-step .profile-step-info").addClass("disable");
            } else {
              $(".profile-step.verification-step").removeClass("singpass-greyed");
              $(".profile-step.verification-step .profile-step-info").removeClass("disable");
            }

            // Step 3: Get Started! — final step (always shown)

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
    AccountDetailsFetcher.init();
  });
})(jQuery);
