<?php
session_start();
ob_start();
include("../../connection.php");
$selrev = "SELECT * FROM `gtech_currencies`";
$qrv=mysqli_query($link,$selrev);
$referenceNo="GZTRN".time().generateRandomString(3);
if(array_key_exists('paynow',$_POST)){
	payout();
}
function payout(){
    if($_POST['currency_namez']=="USD(Cambodia)"){
	    $currency = "USD";
	}else{
	    $currency = $_POST['currency_namez'];
	}
    
    if($_POST['source_type']=='source2'){
        
            // $apiUrl = 'http://127.0.0.1:8000/api/gpayout';
            $apiUrl = 'https://payment.implogix.com/api/gpayout';
            $params = [
                'merchant_code' => 'FCmerchant001',
                'product_id' => '25',                            // for FC department
                'transaction_id' => $_POST['payout_request_id'],
                'callback_url' => 'https://payin.implogix.com/payout/payout_response_url.php',
                'currency' => $currency,
                'amount' => $_POST['amount'],  
                'customer_email' => 'sirichai.ewallet@gmail.com',   
                'customer_phone' => '+855968509332',
                'customer_name' => $_POST['customer_name'],    // account holder name 
                'customer_bank_name' => $_POST['bank_type'],                  // BankCode
                'customer_account_number' => $_POST['customer_account_number'],      // bank account number 
                'customer_addressline_1' => 'Singapore',            
                'customer_zip' => '670592',                         
                'customer_country' => 'TH',                      
                'customer_city' => 'Singapore',                     
            ];
            $queryString = http_build_query($params, '', '&');
            $callPaymentUrl = $apiUrl . '?' . $queryString;
            ?>
            <script>
                window.location.href = '<?php echo $callPaymentUrl; ?>';
            </script>
            <?php
           
	}else{
		echo "Source not found!";
	}
	
}

function generateRandomString($length = 3) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {$randomString .= $characters[rand(0, $charactersLength - 1)];}
      return $randomString;
   }	
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <link rel="dns-prefetch" href="//127.0.0.1:8000">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="LtVoJUqPiAmr2GwQeh4q91sK2LmXMyRERPMrYtGy">
        <meta name="keywords" content="admin, dashboard">
        <meta name="author" content="Soeng Souy">
        <meta name="robots" content="index, follow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Payment Gateway">
        <meta property="og:title" content="Payment Gateway">
        <meta property="og:description" content="Payment Gateway">
        <meta property="og:image" content="https://payin.implogix.com/assets/images/logo.png">
        <meta name="format-detection" content="telephone=no">
        <title>Gtechz PSP – Payment Service Provider</title>
        <link rel="shortcut icon" type="image/png" href="https://payin.implogix.com/assets/images/favicon.png">
        <link href="https://payin.implogix.com/assets/css/style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://payin.implogix.com/assets/css/toastr.min.css">
        <script src="https://payin.implogix.com/assets/js/toastr_jquery.min.js"></script>
        <script src="https://payin.implogix.com/assets/js/toastr.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    </head>
    <body>
        <style>
            .invalid-feedback{
                font-size: 14px;
            }
            .auth-form {
                padding: 20px 20px !important;
            }
            .form-control {
                height: 2.5rem !important;
            }
        </style>
        <div class="authincation h-100">
            <div class="container h-100">
<div class="row justify-content-center h-100 align-items-center">
    <div class="col-md-9"><br/><br/>
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                        <h3 class="text-center mb-4"><b>Grand Diamond FC Department - Withdrawal</b></h3>
                        <form class="form-horizontal" enctype="multipart-formdata" method="post" action="#">
							<div class="row mb-4">
                                <label for="Reference" class="col-md-3 form-label">Reference ID</label>
                                <div class="col-md-9">
								<input class="form-control" name="payout_request_id" id="payout_request_id" placeholder="Enter Reference ID" value="<?php echo $referenceNo; ?>" required readonly type="text">
                                </div>
                            </div>
                            <div class="row mb-4" style="display:none">
                                <label for="Source" class="col-md-3 form-label">Source</label>
                                <div class="col-md-9">
									<input type="hidden" name="source_typez" id="source_typez"/>
										<select class="form-control select2-show-search form-select  text-dark" id="source_type" name="source_type" required data-placeholder="---" tabindex="-1" aria-hidden="true">
											<option value="">---</option>
											<option value="source2" selected>source2</option>
										</select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="Currency" class="col-md-3 form-label">Currency</label>
                                <div class="col-md-9">
										<input type="hidden" name="currency_namez" id="currency_namez"/>
										<select class="form-control select2-show-search form-select  text-dark" id="currency" name="currency" required data-placeholder="---" tabindex="-1" aria-hidden="true">
											<option value="">---</option>
											<?php while($rowrv=mysqli_fetch_array($qrv)){ ?>
											<option value="<?php echo $rowrv['id']?>"><?php echo $rowrv['currency_name']?></option>
											<?php } ?>
										</select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="price" class="col-md-3 form-label">Amount (minimum 350)</label>
                                <div class="col-md-9">
									<input class="form-control" required name="amount" id="price" placeholder="Enter your Amount" value="350.00" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="customer_name" class="col-md-3 form-label">Account Holder Name</label>
                                <div class="col-md-9">
								<input class="form-control" required name="customer_name" id="customer_name" placeholder="Enter Account Holder Name" type="text" value="">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="Bank-Code" class="col-md-3 form-label">Bank Code</label>
                                <div class="col-md-9">
										<select class="form-control select2-show-search form-select  text-dark" id="bank_type" name="bank_type" required data-placeholder="---" tabindex="-1" aria-hidden="true">
											<option value="">---</option>
										</select>
                                </div>
                            </div>
							<div class="row mb-4">
								<label for="customer_account_number" class="col-md-3 form-label">Bank Account Number</label>
								<div class="col-md-9">
									<input class="form-control" required name="customer_account_number" id="customer_account_number" placeholder="Enter Bank Account Number" type="text" value="">
								</div>
							</div>
                            <div class="text-center">
                                <button type="submit" name="paynow" id="paynow" class="btn btn-primary btn-block">Submit</button>
                            </div>
                        </form>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                
            </div>
        </div>
    <script src="https://payin.implogix.com/assets/vendor/global/global.min.js"></script>
    <script src="https://payin.implogix.com/assets/js/custom.min.js"></script>
    <script src="https://payin.implogix.com/assets/js/dlabnav-init.js"></script>
	<script>
	$(document).ready(function(){
    $('#currency').on('change', function(){
        var iso2 = $(this).val();
        var iso3 = $('#source_type').val();
        var currencyname=$("#currency option:selected");
        var sourcetype=$("#source_type option:selected");
        //$('#currency_namez').val(currencyname);
        $('#currency_namez').val(currencyname.text());
        $('#source_typez').val(sourcetype.text());
        // alert(iso2);
        if(iso2 && iso3){
            $.ajax({
                type:'POST',
                url:'../../getBankData.php',
                /*data:{'iso2_val='+iso2},*/
                data: {iso2_val:iso2, iso3_val:iso3},
                success:function(html){
                    //alert(html);
                    $('#bank_type').html(html);
                }
            }); 
        }else{
            $('#bank_type').html('<option value="">---</option>'); 
        }
    });
});
</script>
    </body>
</html>

