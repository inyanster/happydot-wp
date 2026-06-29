<style>
  .rewards-box-wrapper .row {
    gap: 20px 0;
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
  }

  .rewards-box-wrapper .row>div {
    padding: 0 10px;
  }

  img {
    display: block !important;
  }
  .hd-error{
      margin-top: 21px;
    display: block !important;
    color: red !important;
    font-weight: bold !important;
    font-size: 14px !important;}
  .rewards-box {
    padding: 30px;
    background-color: #F5F5F5;
    border-radius: 20px;
    height: 100%;
    position: relative;
  }

  .rewards-col {
    flex: 0 0 25%;
    max-width: 25%;
    position: relative;
  }

  .rewards-section {
    background-color: #fff7d6;
    padding: 10px;
    font-family: Arial, sans-serif;
    margin-bottom: 15px;
     position: relative; /* ensure it's a positioned parent */
  overflow: hidden; 
  min-height:334px !important;
  }

  .rewards-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .rewards-header h2 {
    font-size: 22px;
    font-weight: bold;
    margin: 0;
    color:#D92632
  }

  .see-all {
    font-size: 20px;
    color: #e63946;
    text-decoration: none;
    font-weight: bold;
  }

  .rewards-grid {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
    margin-bottom: 15px;
    
  }

  .reward-card {
    background: white;
    border-radius: 8px;
    width: 312px;
    padding: 30px;
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
    position: relative;
    text-align: center;
  }

  .reward-card.highlighted {
    border: 2px dashed #999;
  }

  .reward-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background-color: #e63946;
    color: white;
    font-weight: bold;
    font-size: 12px;
    padding: 4px 6px;
    border-radius: 4px;
    line-height: 1.2;
  }

  .reward-badge .strike {
    text-decoration: line-through;
    font-size: 10px;
    color: #ffd6d6;
  }

  .reward-image {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 25px;
  }

  .reward-image img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
  }

  .reward-title {
    font-weight: bold;
    font-size: 14px;
    margin-top: 8px;
        min-height: 53px;

  }

  .reward-title .brand {
    color: #e63946;
    font-weight: bolder;
  }

  .reward-stock-bar {
    display: flex;
    align-items: center;
    margin-top: 8px;
    font-size: 12px;
    font-weight: bold;
    width: 100%;
  }

  .flame {
    font-size: 31.2px;
    margin-bottom: 9px;
    z-index: 1;
    position: absolute;
    left: 8px;
    color: #ff5722;
  }

  .stock-bar-bg {
    position: relative;
    flex: 1;
    background-color: #ffe5e5;
    border-radius: 20px;
    height: 25px;
    overflow: hidden;
  }

  .stock-bar-fill {
    background: linear-gradient(to right, #ff3e3e, #ffa1a1);
    height: 100%;
    border-radius: 20px 0 0 20px;
  }

  .stock-count {
    position: absolute;
    width: 100%;
    text-align: center;
    color: white;
    font-weight: bold;
    font-size: 15px;
    line-height: 25px;
    top: 0;
    left: 0;
  }
  .slider-item {
  width: 200px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  text-align: center;
  padding: 10px;
  margin: 0 10px;
  position: relative;
  font-family: sans-serif;
}

.points-badge {
  background-color: #ff5c5c;
  color: #fff;
  font-weight: bold;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 12px;
  position: absolute;
  top: 8px;
  left: 8px;
}

.old-points {
  display: block;
  font-size: 10px;
  opacity: 0.7;
}

.perk-image img {
  width: 100px;
  height: auto;
  margin: 20px auto 10px;
}

.perk-title {
  font-size: 14px;
  font-weight: bold;
  color: #d10000;
  margin-top: 10px;
}

.stock-left {
  background-color: red;
  color: white;
  font-weight: bold;
  font-size: 12px;
  padding: 5px 0;
  margin-top: 10px;
  border-radius: 3px;
}

.perk-card {
  width: 200px;
  background: white;
  border-radius: 10px;
  padding: 15px;
  margin: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  font-family: sans-serif;
  position: relative;
  text-align: center;
}

.badge-top {
  background-color: #ff5c5c;
  color: white;
  border-radius: 5px;
  padding: 5px;
  font-weight: bold;
  font-size: 12px;
}

.old-points {
  font-size: 10px;
  opacity: 0.8;
}

.perk-image img {
  width: 100px;
  height: auto;
  margin: 15px 0;
}

.perk-title {
  color: #d10000;
  font-weight: bold;
  font-size: 14px;
}

.stock-solid {
  background: red;       /* Solid red */
  color: white;
  font-weight: bold;
  border-radius: 6px;
  padding: 8px 0;
  margin-top: 12px;
}
.stock-bar-fill2{
background-color: #ff5c5c;
height: 100%;
    border-radius: 20px 0 0 20px;

}
.notFM {
  width: 100%;
  height: 100%;
  background: #00000054;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  min-height: 100px;
}

.notFM-text {
     max-width: 80%;
    background:rgba(255, 255, 255, 0.88);
    padding: 50px;
    margin: 10px;
    border-radius: 10px;
    height: auto;

}

.strikethrough {
  text-decoration: line-through;
  font-size: 12px;
    color: #00000082;
    text-decoration: line-through;
    font-weight: bold;
    padding-top: 5px;
}
.strikethroughExclusive{

    text-decoration: line-through;
    font-size: 10px;
    color: #ffffffc4;
    text-decoration: line-through;
    font-weight: bold;
    padding-top: 5px;

}
.rewards-ctn h3{
height:62px !important;
}
  @media (max-width: 1200px) {
    .rewards-col {
      flex: 0 0 33.33%;
      max-width: 33.33%;
    }
  }

  @media (max-width: 768px) {
    .rewards-col {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }

  @media (max-width: 480px) {
    .rewards-col {
      flex: 0 0 100%;
      max-width: 100%;
    }
    .notFM-text p {
      display: flex !important;
      flex-direction: column !important;
      font-size:16px !important;
      gap:0   !important;

}
    .notFM-text {
      padding: 20px !important;
      margin: 10px !important;

  }
} 
</style>
<div class="rewards-balance-wrap" style="display:block;">
    <div class="rewards-balance-box">
        <h5>Current Balance</h5>
        <span class="rewards-points tmp" id="rewards-tmp"> Points</span>
    </div>
</div>
<!-- <p>Redeem your <strong>HappyPoints</strong> for e-vouchers here! Please note that you can only redeem 1 item at a time.</p> -->
<div class="perks">


<div class="rewards-section">
<div class="notFM">
  <div class="notFM-text">
    
  <p style="color: #d92632; font-size: 22px;"><b>UNLOCK EXCLUSIVE PERKS!<br>

You are almost there!​<br>
Complete just 1 more survey to unlock access.​</b>​<br>

Don’t miss out on limited-time rewards available only to our engaged members. ​​<br>
Start earning now by taking more surveys!​</p></div>
</div>
  
  <div class="rewards-header">
    <h2>EXCLUSIVE PERKS</h2>
    <a href="/reward/exclusive_perks/" class="see-all">See all perks ></a>
  </div>

  <div class="rewards-grid">
    <!-- Reward Card Start -->

    <!-- Reward Card End -->
  </div>
</div>
</div>
<div class="rewards-box-wrapper">
  <div class="row">
    <!-- Repeat .rewards-col as needed for more rewards -->
  </div>
</div>
