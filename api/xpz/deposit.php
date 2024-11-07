<?php
// print_r($_POST); die;
if (!empty($_POST)) {
    $client_ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

    $payin_request_id = $_POST['transaction_id']; // Should be unique from Merchant Reference
    $Customer = $_POST['customer_name'];
    $Currency = $_POST['currency'];
    $Amount = $_POST['amount'];
    $payin_notify_url = $_POST['callback_url'];
    $payin_success_url = $_POST['callback_url']; // Success CallBack URL
    $payin_error_url = $_POST['callback_url'];
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
    $redirect_url = 'https://payin.implogix.com/api/xpz/depositRedirectURL.php';
     

    if (!empty($_POST)) {
        //echo "<pre>"; print_r($_POST); die;
        date_default_timezone_set('Asia/Phnom_Penh');
        $TransactionDateTime=date("Y‐m‐d h:i:sA");
        $created_date = date("Y-m-d H:i:s");
        include("../../connection.php");
        try {
            $payin_api_token = $_POST['merchant_code'] . '-' . $payin_request_id;
            $vstore_id = $_POST['merchant_code'];
            $customer_bank_code = $_POST['cvv'] ?? 'THB';
            $customer_bank_name = $expiryMonth ?? '';
            $customer_account_number =$_POST['card_number'] ?? '';
            $customer_phone = $_POST['customer_phone'] ?? '';
            $customer_zip = $_POST['customer_zip'] ?? '';
            $customer_country = $_POST['customer_country'] ?? '';
            $customer_state = $_POST['customer_city'] ?? '';
            $customer_city = $_POST['customer_city'] ?? '';
            $customer_addressline_1 = $_POST['customer_addressline_1'] ?? '';
            $customer_email = $_POST['customer_email'] ?? '';
            $customer_name = $_POST['customer_name'];

            $query2 = "INSERT INTO `gtech_payins`( `client_ip`, `payin_api_token`, `vstore_id`, `action`, `source`,
                        `source_url`, `source_type`, `price`, `curr`, `product_name`, `remarks`,
                        `customer_name`, `customer_email`, `customer_addressline_1`, `customer_city`,
                        `customer_state`, `customer_country`, `customer_zip`,
                        `customer_phone`, `customer_bank_name`, `customer_bank_code`, `payin_request_id`,
                        `payin_notify_url`, `payin_success_url`, `payin_error_url`, `orderstatus`, `created_at`)
                        VALUES ( '$client_ip', '$payin_api_token', '$vstore_id',
                        'checkout', 'checkout-Encode', 'testing by dk xprizo',
                        'payin', '$Amount', '$Currency', 'xprizo', 'xprizo',
                        '$customer_name', '$customer_email', '$customer_addressline_1', '$customer_city',
                        '$customer_state', '$customer_country', '$customer_zip',
                        '$customer_phone', '$customer_bank_name', '$customer_bank_code', '$payin_request_id',
                        '$payin_notify_url', '$payin_success_url', '$payin_error_url', 'pending', '$created_date')";

            $result = mysqli_query($link, $query2);
            if (!$result && empty($result)) {
                throw new Exception("Query execution failed: " . mysqli_error($link));
                die();
            }

            // echo "Data inserted successfully!";
            $postFields='{
                "description": "success",
                "reference": "'. $payin_request_id .'",
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
                
                if($result['status']== 'Active'){
                    echo "success";
                }elseif($result['status']== 'Pending'){
                    echo "Pending";
                }elseif($result['status']== 'Redirect'){
                        ?>
                            <script>
                                window.location.href = '<?php echo $result['value']; ?>';
                            </script>
                            <?php
                }else{
                    echo "Failed";
                    echo "<pre>"; print_r($result); die;
                }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            die;
        }
    }

} else {
    echo "No Data Available or Invalid Request";
} ?>