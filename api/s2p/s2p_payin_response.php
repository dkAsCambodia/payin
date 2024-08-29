<?php
// echo "This is speed pay Response created by DK";
$payin_request_id=$_GET['RefId'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://agent-demo.99speedpay.com/api/services/CheckDeposit',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "RefID=$payin_request_id",
  CURLOPT_HTTPHEADER => array(
    'API-AGENT-CODE: PGA001',
    'API-KEY: H0pX4tg2IzboclO5Q7ah6oF8L7xft23o',
    'API-AGENT-USER-NAME: zaffran',
    'Content-Type: application/x-www-form-urlencoded'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
$result= json_decode($response, true);
// echo "<pre>"; print_r($result); die;
$Transactionid = $result['info']['DepositID'];
$orderstatus = $result['info']['Status'];
$orderremarks = $result['info']['TransactionDate'];

 // Code for update Transaction status START
 if(!empty($Transactionid)){
    include("../../connection.php");
    $query1 = "UPDATE `gtech_payins` SET `orderid`='$Transactionid', `orderremarks`='$orderremarks', `orderstatus`='$orderstatus', `status`='1', `payin_aar`='$response' WHERE payin_request_id='$payin_request_id' ";
    mysqli_query($link,$query1);

        // Send To callback URL Code START
            $query2 = "SELECT price,customer_email,payin_request_id,payin_notify_url,
            payin_success_url,payin_error_url,orderid,orderremarks,orderstatus 
            FROM `gtech_payins` WHERE payin_request_id='$payin_request_id' ";

            $qrv = mysqli_query($link, $query2);
            $row = mysqli_fetch_assoc($qrv);
            if (!empty($row)) {
            if ($orderstatus == 'Successful' || $orderstatus == 'Success' || $orderstatus == 'Approved') {
            $paymentStatus = 'success';
            $redirecturl = $row['payin_success_url'];
            } elseif (
            $orderstatus == 'Failed' ||
            $orderstatus == 'Rejected'  ||
            $orderstatus == 'Cancelled'
            ) {
            $paymentStatus = 'failed';
            $redirecturl = $row['payin_notify_url'];
            } elseif ($orderstatus == 'Pending') {
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
            echo "<pre>"; print_r($res);
            // for Webhook Callback code END
            ?>
            <script>
                window.location.href = '<?php echo $callbackURL; ?>';
            </script>
            <?php
            } else {
            echo "Callback URL not Found or Invalid Request!";
            }
            } else {
            echo "No Data Available or Invalid Request!";
            }
            // Send To callback URL Code END

 }
 // Code for update Transaction status END
?>