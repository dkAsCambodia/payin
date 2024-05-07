<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Gtechz PSP – Payment Service Provider</title>
  <style>
    .abf-frame p {
	font-family: Helvetica!important;
	line-height: 18px;
	margin: 0!important;
	padding: 0!important
}
.abf-frame {
	height: 760px;
	color: #000!important;
	background-color: #fff!important;
	position: absolute;
	top: 10% !important;
	left: calc(50% - 230px);
	font-family: Helvetica!important;
	width: 500px;
	margin-top: 0;
	text-align: left;
	/* box-shadow: 0 12px 28px rgba(0,0,0,0.1); */
  box-shadow: 0px 1px 4.83px -1.83px;
	border-radius: 15px;
	font-size: 13px
}
.abf-frame a {
	font-family: Helvetica!important;
	color: #6A8FC2!important
}
.abf-frame a:hover {
	color: #676573!important
}
.abf-form {
	padding: 0 24px 24px
}
.abf-header {
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	padding: 5px 24px;
	min-height: 93px
}
.abf-header div:nth-child(1) img {
	display: inline-block;
	margin: 5px 0
}
.abf-ash1 {
	text-align: center;
	font-size: 14px;
	margin: 12px 0
}
.abf-ash2 {
	font-size: 12px;
	text-align: center;
	margin: 12px 0;
	font-weight: 700
}
.abf-topline {
	border-top: 1px solid #dedede!important;
	padding-top: 12px
}
.abf-list-item {
	padding: 4px 0;
	display: flex;
	align-items: baseline
}
.abf-label {
	display: inline-block;
	width: 45%;
	padding-right: 24px;
	box-sizing: border-box;
	vertical-align: top;
	font-size: 16px;
	opacity: .5;
	text-align: right
}
.abf-value {
	display: inline-block;
	width: 48%;
	box-sizing: border-box;
    color: dimgrey;
}
.abf-confirmations {
	display: inline-block;
	background-color: #dc3545!important;
	width: 12px;
	height: 12px;
	font-size: 9px;
	line-height: 12px;
	text-align: center;
	color: #fff!important;
	border-radius: 50%;
	margin-left: 3px
}
.abf-green {
	background-color: #28a745!important
}
.abf-img-height {
	max-height: 80px
}
    </style>
</head>
<body>
<!-- partial:index.partial.html -->
<div class="abf-frame">
  <!-- <div class="abf-header">
    
   
  </div> -->
  <div class="abf-form">
  <br/>
    <hr style="border-top: 1px solid #dedede;">
    <h2 style="text-align:center; color:#28a745!important;">Quick Scan For Payment</h2>
    <hr style="border-top: 1px solid #dedede;"><br/>
    <div class="abf-topline"><div>
      <!-- <div style="text-align:center; height: 241px"><img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=bitcoin%3A1DonateWffyhwAjskoEwXt83pHZxhLTr8H%3Famount%3D0.04656914&choe=UTF-8" style="display: inline;height:auto;width:300px;margin-top: -5%;" alt="QR code for payment"> </div> -->
      <div style="text-align:center; height: 241px"><img src="<?php echo base64_decode($_GET['token']); ?>" style="display: inline;height:270px;width:300px;margin-top: -5%;" alt="official QR code for payment"> </div>
    </div>
      <div class="abf-list">
        <div class="abf-list-item">
          <div class="abf-label">Amount:</div>
          <div class="abf-value"><b><span class="abf-remains"><?php echo base64_decode($_GET['amount']); ?></span>THB</b></div>
        </div>
        <div class="abf-list-item">
          <div class="abf-label">BackCode:</div>
          <div class="abf-value"><b><span class="abf-remains"><?php echo base64_decode($_GET['ref_bank_code']); ?></span></b></div>
        </div>
        <div class="abf-list-item abf-tx-block">
          <div class="abf-label">Transaction ID:</div>
          <div class="abf-value abf-tx">
            <div><a href="#"> <?php echo base64_decode($_GET['order_id']); ?></a>
              <div class="abf-confirmations abf-green" title="Confirmations count">1</div>
            </div>
          </div>
        </div>
        <div class="abf-list-item">
          <div class="abf-label">DateTime:</div>
          <div class="abf-value"><b><?php date_default_timezone_set('Asia/Phnom_Penh');
            echo date("Y-m-d h:i A"); ?> </b></div>
        </div>
        
        <div class="abf-list-item">
          <div class="abf-label">Account Number:</div>
          <div class="abf-value"><b><span class="abf-remains"><?php echo base64_decode($_GET['ref_account_no']); ?></span></b></div>
        </div>
        <div class="abf-list-item">
          <div class="abf-label">Account Holder Name:</div>
          <div class="abf-value"><b><span class="abf-remains"><?php echo base64_decode($_GET['ref_name']); ?></span></b></div>
        </div>
        <div class="abf-list-item">
          <div class="abf-label" style="color:#dc3545 !important;">Estimed Time:</div>
          <div class="abf-value"><b><span id="timer" class="abf-status" style="color:#dc3545 !important;">00:00 seconds</span></b></div>
        </div>
       
        
      </div>
    </div>
    <h3 style="color:#495057;">Reminder:</h3>
    <div class="abf-address abf-topline abf-ash2 abf-input-address" style="color:#dc3545 !important;">> Please do not refresh the page untill payment completed.</div>
    <div class="abf-address abf-topline abf-ash2 abf-input-address" style="color:#dc3545 !important;">> Payment must be completed within 3 minutes of transaction.</div>
    <div class="abf-address abf-topline abf-ash2 abf-input-address" style="color:#dc3545 !important;">> If refresh the page then you need wait again with start timer.</div>
    <div class="abf-address abf-topline abf-ash2 abf-input-address" style="color:#dc3545 !important;">> Make sure above Bank Details are correct otherwise transaction will be Failed.</div>
  </div>
</div>
<!-- partial -->
<?php
$redirecturl='https://payin.implogix.com/api/PaymentPage.php';
$callbackURL=$redirecturl.'?token='.$_GET['token'].'&amount='.$_GET['amount'].'&ref_bank_code='.$_GET['ref_bank_code'].'&ref_account_no='.$_GET['ref_account_no'].'&order_id='.$_GET['order_id'].'&ref_name='.$_GET['ref_name'];
?>
<script>
        var startTime = new Date();
        // Function to update the timer every second
        function updateTimer() {
            var currentTime = new Date();
            var elapsedTime = Math.floor((currentTime - startTime) / 1000); // in seconds
            var minutes = Math.floor(elapsedTime / 60);
            var seconds = elapsedTime % 60;
            // Add leading zeros if needed
            minutes = (minutes < 10 ? "0" : "") + minutes;
            seconds = (seconds < 10 ? "0" : "") + seconds;
            // Display the current time with seconds
            document.getElementById("timer").innerHTML = minutes + ":" + seconds + " seconds";
            // Check if 4 minutes and 30 seconds have passed, then redirect
            if (minutes >= 3) {
                window.location.href = "<?php echo $callbackURL; ?>";
            }
        }
        // Update the timer every second
        setInterval(updateTimer, 1000);
    </script>
</body>
</html>
