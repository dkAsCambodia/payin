<?php
if(empty($_GET)){
    return "invalid request";
}
$amount=base64_decode($_GET['aa']);
$invoice_number=base64_decode($_GET['in']);
session_start();
ob_start();
if(array_key_exists('paynow',$_POST)){
	checkout($amount,$invoice_number);
}
function checkout($amount,$invoice_number){
    
   // SpeedPay
        $apiUrl = 'https://payment.implogix.com/api/payment';
        // $apiUrl = 'http://127.0.0.1:8000/api/payment';
        $params = [
            'merchant_code' => 'FCmerchant001',
            'product_id' => '24',    
            'transaction_id' => $_POST['payin_request_id'],
            'callback_url' => 'https://payin.implogix.com/payin_response_url.php',
            'currency' => 'THB',
            'amount' => $amount,  
            'customer_email' => 'sirichai.ewallet@gmail.com',   
            'customer_phone' => $invoice_number,
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
    <meta property="og:image" content="assets/images/logo.png">
    <meta name="format-detection" content="telephone=no">
    <title>Grand Diamond Poipet Resort</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favicon.png">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/toastr.min.css">
    <script src="../assets/js/toastr_jquery.min.js"></script>
    <script src="../assets/js/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
             .invalid-feedback{
                font-size: 14px;
            }
            .auth-form {
                padding: 20px 20px !important;
            }
            .form-control {
                height: 2.5rem !important;
                border: 2px solid gray;
            }
            .justify-content-center {
                margin-top: 120px;
            }
        </style>
    </head>
    <body>
        
        <div class="authincation h-100">
            
            <div class="container h-100">
<div class="row justify-content-center h-100 align-items-center">
    <div class="col-md-8">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                        <h3 class="text-center mb-4"><b>Grand Diamond Poipet Resort</b></h3>
                        <form class="form-horizontal" enctype="multipart-formdata" method="post" action="#">
							<div class="row mb-4" style="display:none;">
                                <label for="Reference" class="col-md-4 form-label">Reference ID</label>
                                <div class="col-md-8">
								<input class="form-control" name="payin_request_id" id="payin_request_id" placeholder="Enter Reference ID" value="<?php echo $invoice_number; ?>" required readonly type="text">
                                </div>
                            </div>
                            <div class="row mb-4 hidden cardFiled">
                                <label for="invoice_number" class="col-md-4 form-label">Customer Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="<?php echo base64_decode($_GET['cu']) ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-4 hidden cardFiled">
                                <label for="invoice_number" class="col-md-4 form-label">Invoice Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="invoice_number" id="invoice_number" value="<?php echo $invoice_number ?>" maxlength='16' readonly>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="customer_name" class="col-md-4 form-label">Bank Account Name</label>
                                <div class="col-md-8">
                                <input list="browsers" id="browser" class="form-control" required name="customer_name" id="customer_name" placeholder="Enter Bank account name" type="text">
                                    <datalist id="browsers">
                                        <option value="<?php echo base64_decode($_GET['cu']) ?>">
                                    </datalist>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="Bank-Code" class="col-md-4 form-label">Bank Code</label>
                                <div class="col-md-8">
										<select class="form-control select2-show-search form-select  text-dark" name="bank_type" required data-placeholder="---">
                                            <option value="">Select Bank</option>
                                            <option value="BBL">Bangkok Bank</option>
                                            <option value="BOA">Bank of AYUDHYA</option>
                                            <option value="KTB">Krung Thai Bank</option>
                                            <option value="SCB">Siam Commercial Bank</option>
                                            <option value="KKR">Kasikorn Bank</option>
                                            <option value="GSB">Government Savings Bank</option>
                                            <option value="SCBT">Standard Chartered Bank</option>
                                            <option value="KNK">KIATNAKIN PHATRA Bank</option>
                                            <option value="TMB">Thai Military Bank (TMB THANACHART Bank)</option>
										</select>
                                </div>
                            </div>
                            <div class="row mb-4">
								<label for="customer_account_number" class="col-md-4 form-label">Bank Account Number</label>
								<div class="col-md-8">
									<input class="form-control" required name="customer_account_number" id="customer_account_number" placeholder="Enter Bank Account Number" type="text">
								</div>
							</div>
                            <div class="text-center">
                                <button type="submit" name="paynow" id="paynow" class="btn btn-primary btn-block">Pay Now <?php echo $amount ?>฿</button>
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
    <script src="../assets/vendor/global/global.min.js"></script>
    <script src="../assets/js/custom.min.js"></script>
    <script src="../assets/js/dlabnav-init.js"></script>
    </body>
</html>

