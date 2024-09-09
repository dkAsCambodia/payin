<?php
// print_r($_POST); die;
if (!empty($_POST)) {
    $client_ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    $payin_request_id = $_POST['transaction_id']; // Should be unique from Merchant Reference
    $Customer = $_POST['customer_name'];
    $Currency = $_POST['currency'];
    $customer_bank_name = $_POST['customer_bank_name'];
    $Amount = $_POST['amount'];
    $payin_notify_url = $_POST['callback_url'];
    $payin_success_url = $_POST['callback_url']; // Success CallBack URL
    $payin_error_url = $_POST['callback_url'];

    $api_agent_code = $_POST['api_agent_code']; 
    $apiKey = $_POST['apiKey']; 
    $api_agent_username = $_POST['api_agent_username']; 

    if (!empty($_POST)) {
        //echo "<pre>"; print_r($_POST); die;
        date_default_timezone_set('Asia/Phnom_Penh');
        $TransactionDateTime=date("Y‐m‐d h:i:sA");
        $created_date = date("Y-m-d H:i:s");
        include("../../connection.php");
        try {
            $payin_api_token = $_POST['merchant_code'] . '-' . $payin_request_id;
            $vstore_id = $_POST['merchant_code'];
            $customer_bank_code = $_POST['customer_bank_code'] ?? 'THB';
            $customer_bank_name = $_POST['customer_bank_name'];
            $customer_account_number =$_POST['customer_account_number']; 
            $customer_phone = $_POST['customer_phone'];
            $customer_zip = $_POST['customer_zip'];
            $customer_country = $_POST['customer_country'];
            $customer_state = $_POST['customer_city'];
            $customer_city = $_POST['customer_city'];
            $customer_addressline_1 = $_POST['customer_addressline_1'];
            $customer_email = $_POST['customer_email'];
            $customer_name = $_POST['customer_name'];

            $query2 = "INSERT INTO `gtech_payins`( `client_ip`, `payin_api_token`, `vstore_id`, `action`, `source`,
                        `source_url`, `source_type`, `price`, `curr`, `product_name`, `remarks`,
                        `customer_name`, `customer_email`, `customer_addressline_1`, `customer_city`,
                        `customer_state`, `customer_country`, `customer_zip`,
                        `customer_phone`, `customer_bank_name`, `customer_bank_code`, `payin_request_id`,
                        `payin_notify_url`, `payin_success_url`, `payin_error_url`, `orderstatus`, `created_at`)
                        VALUES ( '$client_ip', '$payin_api_token', '$vstore_id',
                        'checkout', 'checkout-Encode', 'testing by dk speed pay',
                        'payin', '$Amount', '$Currency', 'product_name', 'remarks',
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
            $postFields = http_build_query(array(
                'ClientIp' => $client_ip,
                'RefID' => $payin_request_id,
                'CustomerID' => 'ZCUST1001',
                'CurrencyCode' => $Currency,
                'Amount' => $Amount,
                'TransactionDateTime' => $TransactionDateTime,
                'Remark' => 'payment',
                'CustomerFullName' => $customer_name,
                'BankCode' => 'THAIQR',
                'UrlFront' => 'https://payin.implogix.com/api/s2p/s2p_payin_response.php',
                'CustomerAccountNumber' => $customer_account_number,
                'CustomerAccountBankCode' => $customer_bank_name
            ));

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://agent.99speedpay.com/api/services/RequestDeposit',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => array(
                    'API-AGENT-CODE: '.$api_agent_code,
                    'API-KEY: '.$apiKey,
                    'API-AGENT-USER-NAME: '.$api_agent_username,
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $result= json_decode($response, true);
            echo "<pre>"; print_r($result); die; 
            ?>
            <script>
                window.location.href = '<?php echo $result['RedirectionUrl'] ?>';
            </script>
            <?php

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage(); die;
        }
    }


} else {
    echo "No Data Available or Invalid Request";
} ?>