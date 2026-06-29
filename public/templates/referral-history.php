<style>
  h1{
    color: #D92632;
    font-family: "Montserrat", Sans-serif;
  }
  table{
  margin-bottom: 40px !important;
}
input[type="date"] {
  cursor: pointer;
}
.rewards-balance-wrap {
    
    display: flex !important;
    gap: 20px;
    
}
.rewards-balance-box {
  max-width:100% !important;
}
  </style>
<div class="rewards-balance-wrap">
  <div class="rewards-balance-box">
    <h5>Current Balance</h5>
    <span class="rewards-points"></span>
  </div>

    <div class="rewards-balance-box">
        <h5>Lucky Draw Chances</h5>
        <span class="lucky-draw" id="lucky-draw-chance"> Chances</span>
    </div>
</div>
<div class="point-history-wrapper">
  <div class="hd-table-date-filter">
    <div class="hd-form-date-group">
      <label for="" class="hd-form-date-label">From</label>
      <input type="date" id="min" name="min" />
    </div>
    <div class="hd-form-date-group">
      <label for="" class="hd-form-date-label">To</label>
      <input type="date" id="max" name="max" />
    </div>
  </div>
  <h1 class="hd-heading">Point History</h1>
  
  <div class="hd-table-responsive">
    <table class="hd-table hd-table-striped" id="pointHistory">
      <thead>
        <tr>
          <th>Date</th>
            <th>Type</th>
          <th>Points</th>
          
          <!-- <th>Points</th> -->
        </tr>
      </thead>
      <tbody id="pointHistoryBody">
        <!-- Points history will be dynamically inserted here -->
        <!-- <tr>
          <td colspan="3" class="text-center">No points history available.</td>
        </tr> -->
      </tbody>
    </table>
  </div>
  <!-- <h1 class="hd-heading">Referral History</h1>

  <div class="hd-table-responsive">
    <table class="hd-table hd-table-striped" id="referralHistory">
      <thead>
        <tr>
          <th>Date</th>
            <th>Referred Friend Name</th>
          <th>Referred Friend Email</th>
          <th>Status</th>
     
        </tr>
      </thead>
      <tbody id="referralHistoryBody">
    
      </tbody>
    </table>
  </div> -->
  
</div>
