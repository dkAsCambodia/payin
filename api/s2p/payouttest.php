<?php
function generateRandomString($length = 3)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// $apiUrl = 'http://127.0.0.1:8000/api/gpayout';
$apiUrl = 'https://payment.implogix.com/api/gpayout';

$params = [
    'merchant_code' => $_GET['merchant_code'],
    'product_id' => '20',     
    'transaction_id' => "GTRN" . time() . generateRandomString(3),
    'callback_url' => 'https://payin.implogix.com/payout/payout_response_url.php',
    'currency' => 'THB',
    'amount' => $_GET['amount'],     
    'customer_email' => 'sirichai.ewallet@gmail.com',   
    'customer_phone' => '+855968509332',     
    'customer_name' => $_GET['customer_name'],                   // account holder name 
    'customer_bank_name' => $_GET['customer_bank_name'],                  // BankCode
    'customer_account_number' => $_GET['customer_account_number'],      // bank account number                               
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