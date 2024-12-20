<?php
// echo "This is Xprizo deposit webhook response created by DK";
// {
//     "statusType": 3,
//     "status": "Rejected",  // New /Accepted/Cancelled
//     "description": "Reason for rejection",
//     "actionedById": 1,
//     "affectedContactIds": [],
//     "transaction": {
//       "id": 0,
//       "createdById": 2,
//       "type": "UCD",
//       "date": "2021-04-20T20:34:00.7606173+02:00",
//       "reference": 234234234,
//       "currencyCode": "USD",
//       "amount": 100.00
//     }
// }
  
$results= json_decode(file_get_contents('php://input'), true);
if(!empty($results)){

    // Decode JSON data
    $payin_all=json_encode($results, true);
    $payin_request_id=$results['transaction']['reference'];
    date_default_timezone_set('Asia/Phnom_Penh');
    $pt_timestamp=date("Y-m-d h:i:sA");
    // $orderstatus=$results['status'];
    if($results['status']== 'Accepted'){
        $orderstatus='success';
    }elseif($results['status']== 'New'){
            $orderstatus='processing';
    }else{
        $orderstatus='Failed';
    }
  
        // Code for update Deposit Transaction status START
        include("../../connection.php");
        if($results['transaction']['type']== 'UCD'){
            $query1 = "UPDATE `gtech_payins` SET `orderremarks`='$pt_timestamp', `orderstatus`='$orderstatus', `status`='1', `payin_all`='$payin_all' WHERE payin_request_id='$payin_request_id' ";
            mysqli_query($link,$query1);
            // Code for update Deposit Transaction status END
            // echo "Transaction updated Successfully!";
            // Send To callback URL Code START
            $query2 = "SELECT price,customer_email,payin_request_id,payin_notify_url,
            payin_success_url,payin_error_url,orderid,orderremarks,orderstatus 
            FROM `gtech_payins` WHERE payin_request_id='$payin_request_id' ";

            $qrv = mysqli_query($link, $query2);
            $row = mysqli_fetch_assoc($qrv);
            if (!empty($row)) {
                    if ($orderstatus == 'Successful' || $orderstatus == 'Success' || $orderstatus == 'Approved' || $orderstatus == 'success') {
                    $paymentStatus = 'success';
                    $redirecturl = $row['payin_success_url'];
                    } elseif (
                    $orderstatus == 'Failed' ||
                    $orderstatus == 'Rejected'  ||
                    $orderstatus == 'Cancelled'
                    ) {
                    $paymentStatus = 'failed';
                    $redirecturl = $row['payin_notify_url'];
                    } elseif ($orderstatus == 'Pending' || $orderstatus == 'pending') {
                    $paymentStatus = 'pending';
                    $redirecturl = $row['payin_error_url'];
                    } else {
                    $paymentStatus = 'processing';
                    $redirecturl = $row['payin_success_url'];
                    }

                if (!empty($redirecturl)) {
                    $info = [
                        'payment_transaction_id' => $row['orderid'],
                        'orderstatus' => $orderstatus,
                        'payment_email' => $row['customer_email'],
                        'transaction_id' => $row['payin_request_id'],
                        'payment_amount' => $row['price'],
                        'payment_timestamp' => $row['orderremarks'],
                        'payment_status' => $paymentStatus,
                    ];
                    $queryString = http_build_query($info, '', '&');
                    $callbackURL = $redirecturl . '?' . $queryString;
                    // for Webhook Callback code START
                    $ch = curl_init($callbackURL);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        // 'Content-Type: application/json',
                        // 'Content-Length: ' . strlen($jsonData)
                    ));
                    $res = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'Curl error: ' . curl_error($ch);
                    }
                    curl_close($ch);
                    // echo "<pre>"; print_r($res);
                    // for Webhook Callback code END

                    // Set the response code to 200
                        http_response_code(200);
                        // Define the response body
                        $response = [
                            "status" => "success",
                            "TransactionType" => $results['transaction']['type'],
                            "message" => "Transaction Updated Successfully!"
                        ];
                        // Return the JSON response
                        header('Content-Type: application/json');
                        echo json_encode($response);
                    
                } else {
                    echo "Callback URL not Found or Invalid Request!";
                }
            } else {
                echo "No Data Available or Invalid Request!";
            }
            // Send To callback URL Code END
       
        }else{
            $query = "UPDATE `gtech_payouts` SET  `orderremarks`='$pt_timestamp', `orderstatus`='$orderstatus', `status`='1', `payout_all`='$payin_all' WHERE payout_request_id='$payin_request_id' ";
            $res=mysqli_query($link,$query);
            // echo "Withdraw Transaction updated Successfully!";
            // Code for update Transaction status END
            // Send To callback URL Code START
            $query3 = "SELECT price,customer_email,payout_request_id,payout_notify_url,payout_success_url,payout_error_url,orderid,created_at,orderstatus FROM `gtech_payouts` WHERE payout_request_id='$payin_request_id' ";
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
                            'orderremarks' => $pt_timestamp,
                        ];
                        $queryString = http_build_query($info, '', '&');
                        $callbackURL = $redirecturl . '?' . $queryString;
            
                         // for Webhook Callback code START
                         $ch = curl_init($callbackURL);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            // 'Content-Type: application/json',
                            // 'Content-Length: ' . strlen($jsonData)
                        ));
                        $res = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'Curl error: ' . curl_error($ch);
                        }
                        curl_close($ch);
                        // echo "<pre>"; print_r($res);
                        // for Webhook Callback code END
                        // Set the response code to 200
                        http_response_code(200);
                        // Define the response body
                        $response = [
                            "status" => "success",
                            "TransactionType" => $results['transaction']['type'],
                            "message" => "Transaction Updated Successfully!"
                        ];
                        // Return the JSON response
                        header('Content-Type: application/json');
                        echo json_encode($response);
                       
                    }else{
                        echo "Callback URL not Found or Invalid Request!";
                    }
            } 
            // Send To callback URL Code END
        }

        

}else{
    echo "No Data Available or Invalid Request!";
}
?>
