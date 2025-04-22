<?php
session_start();

$response = ['success' => false, 'message' => 'Coupon non valido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $couponCode = strtoupper(trim($_POST['coupon_code']));
    $validCoupons = ['SCONTO10' => 0.10, 'SCONTO20' => 0.20];

    if (isset($_SESSION['applied_coupon'])) {
        $response['message'] = 'Hai già utilizzato uno sconto!';
    } elseif (array_key_exists($couponCode, $validCoupons)) {
        $_SESSION['applied_coupon'] = [
            'code' => $couponCode,
            'discount' => $validCoupons[$couponCode]
        ];
        $response = [
            'success' => true,
            'message' => 'Coupon applicato con successo!',
            'discount' => $validCoupons[$couponCode]
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);