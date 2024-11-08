<?php
if(!empty($_POST)){
        $client_ip =(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        $payout_request_id= $_POST['transaction_id']; // Should be unique from Merchant Reference
        $Currency=$_POST['currency'];
        $Amount = $_POST['amount'];
        $payout_api_token	=$_POST['merchant_code'] . '-' . $payout_request_id;
        $vstore_id	=$_POST['merchant_code']; // For Gtechz Official
        $product_name= 'testing card dk';// Any Thing
        $customer_name=$_POST['customer_name']; // Customer Name
        $customer_email=$_POST['customer_email'];
        $customer_addressline_1=$_POST['customer_addressline_1']; // Customer Address Line 1
        $customer_city=$_POST['customer_city']; // Customer City
        $customer_country=$_POST['customer_country']; // Customer Country
        $customer_zip=$_POST['customer_zip']; // Customer Zipcode
        $customer_phone=$_POST['customer_phone']; // Customer 78760
        $payout_membercode=$_POST['merchant_code'];
        $payout_notify_url=$_POST['callback_url'];
        $payout_success_url=$_POST['callback_url']; // Success CallBack URL
        $payout_error_url=$_POST['callback_url'];

        $card_number =$_POST['card_number'];
        @$expiration =$_POST['expiration'];
        if(empty($expiration)){
            $expiryMonth =$_POST['expiryMonth'];
            $expiryYear =$_POST['expiryYear'];
        }else{
            list($expiryMonth, $expiryYear) = explode('/', $expiration);
        }
        $cvv =$_POST['cvv'];
        $accountId = $_POST['accountId']; 
        $transferAccountId = $_POST['transferAccountId']; 
        $apiKey = $_POST['apiKey']; 
        $api_url = $_POST['api_url'];

		date_default_timezone_set('Asia/Phnom_Penh');
        $TransactionDateTime=date("Y‐m‐d h:i:sA");
		$created_date=date("Y-m-d H:i:s");
		include("../../connection.php");
		try {
			$query2 = "INSERT INTO `gtech_payouts`( `client_ip`, `payout_api_token`, `vstore_id`, `action`, `source`, `source_url`, `price`, `curr`, `product_name`, 
            `customer_name`, `customer_email`, `customer_addressline_1`, `customer_city`, `customer_country`, `customer_zip`,
             `customer_phone`, `customer_bank_name`, `customer_bank_code`, `customer_account_number`, `payout_request_id`, `payout_membercode`, `payout_notify_url`, `payout_success_url`, `payout_error_url`, `orderstatus`, `created_at`)
             VALUES ( '$client_ip', '$payout_api_token', '$vstore_id', 'payout', 'payout-Encode', 'xprizo_payout', '$Amount', '$Currency', '$product_name', '$customer_name', '$customer_email', '$customer_addressline_1', '$customer_city', '$customer_country', '$customer_zip',
               '$customer_phone', '$expiryMonth', '$cvv', '$card_number', '$payout_request_id', '$payout_membercode', '$payout_notify_url', '$payout_success_url', '$payout_error_url', 'Pending', '$created_date')";
			$result = mysqli_query($link, $query2);
			if (!empty($result)) {
				// echo "Data inserted successfully!";
			} else {
				throw new Exception("Query execution failed: " . mysqli_error($link));  die;
			}
		} catch (Exception $e) {
			echo "Error: " . $e->getMessage(); die;
		}

        // Code for Xprizo Withdrawal API START
        $redirect_url="http://localhost/xprizo/xprizoWithdrawalCallback.php";
        
        $postFields='{
          "description": "pass",
          "reference": "'. $payout_request_id .'",
          "amount": "'. $Amount .'",
          "currencyCode": "'. $Currency .'",
          "accountId": "'. $accountId .'",
          "transferAccountId": "'. $transferAccountId .'",
          "customer": "'. $customer_email .'",
          "creditCard": {
            "name": "'. $customer_name .'",
            "number": "'. $card_number .'",
            "expiryMonth": "'. $expiryMonth .'",
            "expiryYear": "'. $expiryYear .'",
            "cvv": "'. $cvv .'"
          },
          "productCode": "",
          "redirect": "'. $redirect_url .'",
          "sourceType": ""
        }';
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'x-api-version: 1.0',
                'x-api-key: '.$apiKey,
                'Accept: text/plain',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result= json_decode($response, true);
        // echo "<pre>"; print_r($result); die;
    
            if(!empty($result)){
               
                if($result['status']== 'Active'){
                    $orderstatus = "success";
                }elseif($result['status']== 'Pending'){
                    $orderstatus = "processing";
                }else{
                    $orderstatus = "Failed";
                }
                    // Code for update Transaction status START
                    $Transactionid = $result['key'];
                    $orderremarks = $result['description'];
                   
                    $query = "UPDATE `gtech_payouts` SET  `orderid`='$Transactionid', `orderremarks`='$orderremarks', `orderstatus`='$orderstatus', `status`='1', `payout_aar`='$response' WHERE payout_request_id='$payout_request_id' ";
                    $res=mysqli_query($link,$query);
                    // Code for update Transaction status END
    
                    // Send To callback URL Code START
                    $query3 = "SELECT price,customer_email,payout_request_id,payout_notify_url,payout_success_url,payout_error_url,orderid,created_at,orderstatus FROM `gtech_payouts` WHERE payout_request_id='$payout_request_id' ";
                    $qrv3=mysqli_query($link,$query3);
                    $row=mysqli_fetch_assoc($qrv3);
                    if(!empty($row)){
                            if($row['orderstatus'] == 'Successful' || $row['orderstatus'] == 'Success' || $row['orderstatus'] == 'Approved' || $row['orderstatus'] == 'success' || $row['orderstatus'] == 'SUCCESS') {
                            $paymentStatus = 'success';
                            $redirecturl = $row['payout_success_url'];
                            }elseif($row['orderstatus'] == 'Failed' || $row['orderstatus'] == 'Rejected' || $row['orderstatus'] == 'Cancelled' || $row['orderstatus'] == 'failed' || $row['orderstatus'] == 'FAILED') {
                            $paymentStatus = 'failed';
                            $redirecturl = $row['payout_notify_url'];
                            }elseif($row['orderstatus'] == 'Pending' || $row['orderstatus'] == 'pending' || $row['orderstatus'] == 'PENDING') {
                            $paymentStatus = 'pending';
                            $redirecturl = $row['payout_error_url'];
                            }else{
                            $redirecturl = $row['payout_success_url'];
                            $paymentStatus = 'processing';
                            }
    
                            if (!empty($redirecturl)) {
                                     $info = [
                                            'settlement_trans_id' => $row['orderid'],
                                            'orderstatus' => $row['orderstatus'],
                                            'payment_email' => $row['customer_email'],
                                            'transaction_id' => $row['payout_request_id'],
                                            'payment_amount' => $row['price'],
                                            'payment_timestamp' => $row['created_at'],
                                            'payment_status' => $paymentStatus,
                                            'orderremarks' => $orderremarks,
                                    ];
                                    // print_r($info); die;
                                    $queryString = http_build_query($info, '', '&');
                                    $callbackURL = $redirecturl . '?' . $queryString;
                                    ?>
                                    <script>
                                    window.location.href = '<?php echo $callbackURL; ?>';
                                    </script>
                                    <?php
                            }else{
                                    echo "Callback URL not Found or Invalid Request!";
                            }
                    } 
            }else{
                echo "<pre>"; print_r($result);
            }
        // Code forXprizo Withdrawal API END
}else{
        echo "No Data Available or Invalid Request";
} ?>
   
