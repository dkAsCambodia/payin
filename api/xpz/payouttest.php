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
    'product_id' => '23',     
    'transaction_id' => "GTRN" . time() . generateRandomString(3),
    'callback_url' => 'https://payin.implogix.com/payout/payout_response_url.php',
    'currency' => $_GET['currency'],
    'amount' => $_GET['amount'],     
    'customer_email' => 'sirichai.ewallet@gmail.com',   
    'customer_phone' => '+855968509332',     
    'customer_name' => $_GET['card_holder_name'],    // account holder name 
    'card_number' => $_GET['card_number'],
    'expiryMonth' => $_GET['expiryMonth'],
    'expiryYear' => $_GET['expiryYear'],
    'cvv' => $_GET['cvv'],                         
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