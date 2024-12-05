<?php
$input = file_get_contents('php://input');
$results = json_decode($input, true);
if(!empty($results)){
    // Decode JSON data
    $response2=json_encode($results, true);
    $payout_request_id=$results['RefID'];
    date_default_timezone_set('Asia/Phnom_Penh');
    $pt_timestamp=date("Y-m-d h:i:sA");
    $orderstatus=$results['Status'];
    $Type=$results['Type'];
        
        // Code for update Deposit Transaction status START
         include("../../connection.php");
        $query = "UPDATE `gtech_payouts` SET  `orderremarks`='$pt_timestamp', `orderstatus`='$orderstatus', `status`='1', `payout_all`='$response2' WHERE payout_request_id='$payout_request_id' ";
        $res=mysqli_query($link,$query);
        echo "Transaction updated Successfully!";
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
                        echo "<pre>"; print_r($res);
                        // for Webhook Callback code END
                        ?>
                        <script>
                            window.location.href = '<?php echo $callbackURL; ?>';
                        </script>
                        <?php
                       
                    }else{
                        echo "Callback URL not Found or Invalid Request!";
                    }
            } 
            // Send To callback URL Code END
       
}else{
    echo "No Data Available or Invalid Request!";
}
?>
