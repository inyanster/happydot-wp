(function ($) {
  "use strict";
    
  const RewardFetcher = {
    init: function () {
      this.rewardsContainer = $(".rewards-box-wrapper .row");

      // Debug: Ensure container exists
      // console.log("Reward container found:", this.rewardsContainer.length > 0);

      this.fetchRewards();
    },

    fetchRewards: function () {
      // alert("HERE");
      // console.log("Fetching rewards from server...");
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        method: "POST",
        dataType: "json",
        data: {
          action: "flexcore_get_perks",
          nonce: flexcoreServerAjax.perksNonce,
        },
        success: function (response) {
          // console.log("AJAX Response:", response);
          const profileData = response?.data?.data?.profile;
          const rawRewards = response?.data?.data?.rewards;
          const rewards = Array.isArray(rawRewards)
            ? rawRewards
            : Object.values(rawRewards);
          if(profileData.membershipTier ==="1"){
            $(".notFM").hide();
            $('.notFM-text').hide();
          }
          else{
            $(".notFM").show();
            $('.notFM-text').show();
          }
          // console.log("Converted Rewards Array:", rewards);
          // console.log("Rewards Length:", rewards.length);

          if (response.success && rewards.length > 0) {
            // 👉 Sort rewards by `ordering` (ascending)
            rewards.sort((a, b) => a.ordering - b.ordering);
            var count=1;
            rewards.forEach(function (reward) {
        //       const rewardHTML = `
        //     <div class="rewards-col">
        //         <div class="rewards-box">
        //             <div class="rewards-img position-relative">
        //                 <img src="${
        //                   reward.imageUrl
        //                 }" class="objectFit-cover" alt="${reward.name}" />
        //             </div>
        //             <div class="rewards-ctn">
        //                 <h3>${reward.name}</h3>
        //                 <span class="rewards-points">${
        //                   reward.points
        //                 } Points</span>
        //                 ${
        //                   response.data.data.buttons?.[reward.id] ||
        //                   response.data.data.button ||
        //                   '<a href="#" class="hd-btn">SELECT</a>'
        //                 }
        //             </div>
        //             <div class="short-description">
        //                 ${reward.provider?.description || "No description"}
        //             </div>
        //         </div>
        //     </div>
        //   `;
        //       RewardFetcher.rewardsContainer.append(rewardHTML);
              // console.log("Reward HTML added:", reward.isPerk);
              // console.log("Reward status:", reward.status);
              if (reward.isPerk && count <= 12 && reward.status === true) {
                // console.log("Reward is a perk:", reward.name);
                var rewardCard = createRewardCard({
                  points: reward.points,
                  imageSrc: reward.imageUrl,
                  imageAlt: reward.name,
                  title: reward.name,
            stockPercent: (typeof reward.quantity === 'number' && reward.quantity > 0)? (( reward.remainingQuantity) / reward.quantity) * 100  : reward.quantity == -1 ? 100 : 0,
                stockLeft:
                    typeof reward.remainingQuantity === "number" &&
                    reward.remainingQuantity >= 0
                      ? reward.remainingQuantity
                      : "Unlimited",
                  showFlame:reward.remainingQuantity < 16 && reward.remainingQuantity > 0,
                  url:  'rewards_preview/?id=' + reward.id,
                  only: (reward.remainingQuantity < 16 && reward.remainingQuantity > 0),
                    button: reward.remainingQuantity==0?  '<p class="hd-error" style=" margin-top:2px;display:none;">OUT OF STOCK</p>' : response.data.data.buttons?.[reward.id] ||
                        response.data.data.button ||
                        '<a href="#" class="hd-btn">SELECT</a>',
                  isDiscounted: reward.isDiscounted,
                  strikePoints: reward.strikePoints? reward.strikePoints : ''
                });
                // console.log("Reward Card HTML:", rewardCard);
                // const container = document.querySelector(".rewards-grid");
                // if (!container) {
                //   // console.error("Element `.rewards-grid` not found.");
                //   return;
                // }
                // container.innerHTML += rewardCard;
               RewardFetcher.rewardsContainer.append(rewardCard);
                count++;
              }
            });
          } else {
            RewardFetcher.rewardsContainer.append(
              '<p class="error">No rewards available.</p>'
            );
            // console.warn("No rewards returned or array is empty.");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          console.error("XHR Response:", xhr.responseText);

          RewardFetcher.rewardsContainer.html(
            '<p class="error">Failed to load rewards. Please try again later.</p>'
          );
        },
      });
    },
  };
  function createRewardCard({
    points,
    imageSrc,
    imageAlt,
    title,
    stockPercent,
    stockLeft,
    showFlame,
    url,
    only,
    button,
    isDiscounted,
    strikePoints
  }) {
    return `
    <div class="rewards-col">
                <div class="rewards-box">
                    <div class="rewards-img position-relative">
                        <img src="${
                          imageSrc
                        }" class="objectFit-cover" alt="${imageAlt}" />
                    </div>
                    <div class="rewards-ctn">
                        <h3>${title}</h3>
                        <span class="rewards-points">${isDiscounted? `<span class="strikethrough">${strikePoints} Points</span>`:""}${
                          points
                        } Points</span>
                        ${
                          button
                        }
                    </div>
                   <div class="reward-stock-bar">
        ${showFlame ? '<span class="flame">🔥</span>' : ""}
        <div class="stock-bar-bg">
          <div class=${
            showFlame ? "stock-bar-fill" : "stock-bar-fill2"
          } style="width: ${stockPercent}%;"></div>
          <span class="stock-count">${only? 'ONLY': ''} ${stockLeft} LEFT</span>
        </div>
      </div>
                </div>
            </div>  
    
    `;
  }
 
  $(".notFM").show();
  $('.notFM-text').show();
  $(document).ready(function () {
   
    // alert("Document is ready");

    RewardFetcher.init();
  });
})(jQuery);
