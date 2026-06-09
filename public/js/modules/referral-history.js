(function ($) {
  "use strict";

  const referralHistory = {
    init: function () {
      this.setDefaultDateRange();
      this.getReferralHistory();

      const self = this;

      // Handle changes in date filters
      $("#min, #max").on("change", function () {
        self.logDateRange();
        self.getReferralHistory();
      });

      // Ensure max date is always ≥ min date and ≤ today
      $("#min").on("change", function () {
        const minVal = $(this).val();
        const today = new Date().toISOString().split("T")[0];

        // Update max's min and max attributes
        $("#max").attr("min", minVal);
        $("#max").attr("max", today);

        // If current max value is before new min, reset to today
        if ($("#max").val() < minVal) {
          $("#max").val(today);
        }
      });
    },

    setDefaultDateRange: function () {
      const today = new Date();
      const oneMonthAgo = new Date();
      oneMonthAgo.setMonth(today.getMonth() - 1);

      const formatDate = (date) => date.toISOString().split("T")[0];
      const todayFormatted = formatDate(today);
      const oneMonthAgoFormatted = formatDate(oneMonthAgo);

      $("#min").attr("max", todayFormatted).val(oneMonthAgoFormatted);
      $("#max").attr("max", todayFormatted).val(todayFormatted);
      $("#max").attr("min", oneMonthAgoFormatted);
    },

    getReferralHistory: function () {
      const referralHistoryBody = $("#referralHistoryBody");
      const luckyDrawChanceElement = $("#lucky-draw-chance");
      referralHistoryBody.empty();

      const pointHistoryTableBody = $("#pointHistoryBody");
      pointHistoryTableBody.empty(); // Clear previous data
      this.logDateRange();
      const fromDate = $("#min").val();
      const toDate = $("#max").val();

      $.ajax({
        url: flexcoreServerAjax.ajaxUrl,
        type: "POST",
        data: {
          action: "flexcore_get_referral_history",
          nonce: flexcoreServerAjax.referralHistoryNonce,
          from: fromDate,
          to: toDate,
        },
        success: function (response) {
          const referralHistoryBody = $("#referralHistoryBody");
          const pointHistoryTableBody = $("#pointHistoryBody");
          referralHistoryBody.empty();
          pointHistoryTableBody.empty();

          if (response.success && response.data) {
            let referralHistory = response.data.data.history || [];
            let pointHistory = response.data.data.pointHistory || [];

            // Sort by date descending
            referralHistory.sort(
              (a, b) => new Date(b.referredAt) - new Date(a.referredAt)
            );
            pointHistory.sort(
              (a, b) =>
                new Date(b.attributes.Created) - new Date(a.attributes.Created)
            );
			const luckyDrawChances = response.data.data.luckyDrawChances ?? 0;

            const referralMessage = response.data.data.referralMessage || "";
            const pointMessage = response.data.data.pointMessage || "";
            const currentPoints = response.data.data.currentPoints ?? 0;
			 if (luckyDrawChanceElement.length) {
              luckyDrawChanceElement.text(`${luckyDrawChances} Chance`);
            }
            // Render point history table
            if (pointHistory.length > 0) {
              pointHistory.forEach((entry) => {
                const attr = entry.attributes;
                const date = formatDateToDDMMYYYY(attr.Created);

                const comment = attr.Comment || "-";
                const match = comment.match(/\[([^\]]+)\]/);
                const type = match ? match[1] : "-";

                const points = attr.Credit;

                const row = `<tr><td>${date}</td><td>${type}</td><td>${points}</td></tr>`;
                pointHistoryTableBody.append(row);
              });
            } else {
              pointHistoryTableBody.append(
                `<tr><td colspan="3" class="text-center">No points history available.</td></tr>`
              );
            }

            // Update current points
            $(".rewards-points").text(currentPoints + " Points");

            // Render referral history table
            if (referralHistory.length > 0) {
              referralHistory.forEach((item) => {
                const row = `
          <tr>
        
            <td>${formatDateToDDMMYYYY(item.referredAt)} </td>
            <td>${item.referredFriendName}</td>
            <td>${item.referredTo}</td>
            <td>${item.referralStatus}</td>
          </tr>`;
                referralHistoryBody.append(row);
              });
            } else {
              referralHistoryBody.append(
                `<tr><td colspan="5" class="text-center"> No referral history found.</td></tr>`
              );
            }
          } else {
            referralHistoryBody.append(
              '<tr><td colspan="5" class="text-center">Error loading referral history.</td></tr>'
            );
            pointHistoryTableBody.append(
              '<tr><td colspan="3" class="text-center">Error loading point history.</td></tr>'
            );
          }
        },

        error: function () {
          referralHistoryBody.append(
            '<tr><td colspan="5">Error loading referral history.</td></tr>'
          );
          pointHistoryTableBody.append(
            '<tr><td colspan="3">Error loading point history.</td></tr>'
          );
        },
      });
    },

    logDateRange: function () {
      const fromDate = $("#min").val();
      const toDate = $("#max").val();
      // console.log("Selected Date Range → From:", fromDate, "| To:", toDate);
    },
  };
  function formatDateToDDMMYYYY(dateString) {
    const date = new Date(dateString);
    const isoDate = date.toISOString().split("T")[0];
    const [year, month, day] = isoDate.split("-");
    return `${day}/${month}/${year}`;
  }

  $(document).ready(function () {
    // console.log("Referral history module loaded");
    referralHistory.init();
  });
})(jQuery);