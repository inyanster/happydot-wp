(function ($) {
  "use strict";

  const getMembershipStatus = {
    init: function () {
      this.getStatus();
    },

    getStatus: function () {
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "get_membership_status",
          nonce: flexcoreServerAjax.membershipStatusNonce,
        },
        success: function (response) {
          if (response.success) {
            // console.log("Membership status fetched successfully:", response);
            const status =  parseInt(response.data.membershipstatus, 10);
         
            // let path = window.location.pathname;
          
            // if (path.endsWith("/")) {
            //   path = path.slice(0, -1);
            // }
            // const fileName = path.substring(path.lastIndexOf("/") + 1);

           
            
            //   const origin = window.location.origin; // e.g., http://localhost
            //   const pathname = window.location.pathname; // e.g., /happydot/refer-a-friend-how-it-works/

            //   const firstPathSegment = pathname.split("/")[1]; // "happydot"
            //   const baseUrl = `${origin}/${firstPathSegment}`;
              
            // if ((fileName === "refer-a-friend-how-it-works" && status !== 4) || (fileName === "refer-a-friend" && status !== 4)) {
                     
            //   window.location.href = baseUrl + "/refer-a-friend-complete-your-account";
            // }
            // else if ((fileName === "refer-a-friend-complete-your-account" && status !== 4) || (fileName === "refer-a-friend" && status === 4)) {
            //   window.location.href = baseUrl + "/refer-a-friend-how-it-works";
            // }
            
            // console.log("Membership Status:", status);
            localStorage.setItem("membershipStatus", status);
          } else {
            console.error(
              "Error fetching membership status:",
              response.data.message
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX error:", status, error);
        },
      });
    },
  };
  $(document).ready(function () {
    getMembershipStatus.init();
  });
})(jQuery);
