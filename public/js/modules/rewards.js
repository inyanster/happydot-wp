(function ($) {
  "use strict";

  const RewardFetcher = {
    init: function () {
      this.rewardsContainer = $(".rewards-box-wrapper .row");

      this.fetchRewards();
    },

    fetchRewards: function () {
      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        method: "POST",
        dataType: "json",
        data: {
          action: "flexcore_get_rewards",
          nonce: flexcoreServerAjax.rewardNonce,
        },

        success: function (response) {
          // console.log("Rewards fetched successfully:", response);

          const profileData = response?.data?.data?.profile;

          /** -----------------------------
           *  UPDATE BALANCE + LUCKY DRAW
           * ----------------------------- */
          const userBalance = response?.data?.data?.currentPoints || 0;
          const luckyDrawChances = response?.data?.data?.luckydrawChances || 0;

          const balanceEl = document.querySelector("#rewards-tmp");
          if (balanceEl) balanceEl.innerText = `${userBalance} Points`;

          const chanceEl = document.querySelector("#lucky-draw-chance");
          if (chanceEl) chanceEl.innerText = `${luckyDrawChances} Chance`;

          /** -----------------------------
           *  MEMBERSHIP TIER LOGIC
           * ----------------------------- */
          if (profileData && profileData.membershipTier === "1") {
            $(".notFM, .notFM-text").hide();
          } else {
            $(".notFM, .notFM-text").show();
          }

          /** -----------------------------
           *  SAFE REWARDS LOAD
           * ----------------------------- */
          const rawRewards = response?.data?.data?.rewards || [];
          const rewards = Array.isArray(rawRewards)
            ? rawRewards
            : Object.values(rawRewards || {});

          // If no rewards → show message
          if (!response.success || rewards.length === 0) {
            $(".rewards-box-wrapper .row").append(
              '<p class="error">No rewards available.</p>'
            );
            return;
          }

          /** -----------------------------
           *  SORT AND RENDER REWARDS
           * ----------------------------- */
          rewards.sort((a, b) => a.ordering - b.ordering);

          let perkCount = 1;
          let rewardCount = 1;

          rewards.forEach(function (reward) {

            const rewardButtonHTML =
              reward.remainingQuantity > 0 || reward.remainingQuantity == -1
                ? response.data.data.buttons?.[reward.id] ||
                  response.data.data.button ||
                  '<a href="#" class="hd-btn">SELECT</a>'
                : '<p class="hd-error">OUT OF STOCK</p>';

            const rewardHTML = `
              <div class="rewards-col">
                <div class="rewards-box">
                  <div class="rewards-img position-relative">
                    <img src="${reward.imageUrl}" class="objectFit-cover" alt="${reward.name}" />
                  </div>
                  <div class="rewards-ctn">
                    <h3>${reward.name}</h3>
                    <span class="rewards-points">
                      ${reward.isDiscounted ? `<span class="strikethrough">${reward.strikePoints} Points</span>` : ""}
                      ${reward.points} Points
                    </span>
                    ${rewardButtonHTML}
                  </div>
                </div>
              </div>
            `;

            /** Normal rewards (limit 12) */
            if (!reward.isPerk && reward.status && rewardCount <= 12) {
              $(".rewards-box-wrapper .row").append(rewardHTML);
              rewardCount++;
            }

            /** Perks (limit 4) */
            if (reward.isPerk && reward.status && perkCount <= 4) {
              const rewardCard = createRewardCard({
                points: reward.points,
                imageSrc: reward.imageUrl,
                imageAlt: reward.name,
                title: reward.name,
                stockPercent:
                  reward.quantity > 0
                    ? (reward.remainingQuantity / reward.quantity) * 100
                    : reward.quantity === -1
                    ? 100
                    : 0,
                stockLeft:
                  reward.remainingQuantity >= 0
                    ? reward.remainingQuantity
                    : "Unlimited",
                showFlame:
                  reward.remainingQuantity < 16 &&
                  reward.remainingQuantity > 0,
                url:
                  reward.remainingQuantity == 0
                    ? "#"
                    : "rewards_preview/?id=" + reward.id,
                only:
                  reward.remainingQuantity < 16 &&
                  reward.remainingQuantity > 0,
                isDiscounted: reward.isDiscounted,
                strikePoints: reward.strikePoints || "",
              });

              const perkContainer = document.querySelector(".rewards-grid");
              if (perkContainer) perkContainer.innerHTML += rewardCard;

              perkCount++;
            }
          });
        },

        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          $(".rewards-box-wrapper .row").html(
            '<p class="error">Failed to load rewards. Please try again later.</p>'
          );
        },
      });
    },
  };

  /** -----------------------------
   *  PERK CARD TEMPLATE
   * ----------------------------- */
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
    isDiscounted,
    strikePoints,
  }) {
    return `
      <a href="${url}" class="reward-card-link" style="text-decoration: none;">
        <div class="reward-card">
          <div class="reward-badge">
            ${isDiscounted ? `<span class="strikethroughExclusive">${strikePoints} Points</span><br>` : ""}
            ${points} Points
          </div>
          <div class="reward-image">
            <img src="${imageSrc}" alt="${imageAlt}">
          </div>
          <div class="reward-title">${title}</div>
          <div class="reward-stock-bar">
            ${showFlame ? '<span class="flame">🔥</span>' : ""}
            <div class="stock-bar-bg">
              <div class="${showFlame ? "stock-bar-fill" : "stock-bar-fill2"}" style="width: ${stockPercent}%;"></div>
              <span class="stock-count">${only ? "ONLY" : ""} ${stockLeft} LEFT</span>
            </div>
          </div>
        </div>
      </a>
    `;
  }

  // Hide overlay initially
  $(".notFM, .notFM-text").hide();

  $(document).ready(function () {
    RewardFetcher.init();
  });
})(jQuery);
