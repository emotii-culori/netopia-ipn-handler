<?php
require 'vendor/autoload.php';

use Netopia\Payment\Request\PaymentAbstract;

$privateKeyPath = __DIR__ . '/private.key';

try {
    $paymentRequest = PaymentAbstract::factoryFromEncrypted(
        $_POST['env_key'],
        $_POST['data'],
        $privateKeyPath
    );

    if ($paymentRequest->objPmNotify->action === 'confirmed') {
        $clientEmail = $paymentRequest->invoice->getBillingAddress()->getEmail();

        $payload = json_encode([
            'email' => $clientEmail,
            'orderId' => $paymentRequest->orderId
        ]);

        $makeWebhook = 'https://hook.eu2.make.com/74kisern1xhpoxlre4vtkgq4sk10bv19';

        $ch = curl_init($makeWebhook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }

    echo 'OK';
} catch (Exception $e) {
    echo $e->getMessage();
}
