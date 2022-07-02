<?php
 
$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method) {

    case 'GET':
        get_teste();
        break;

    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function create_order($external_id, $value = 0.25) {
    $pix_webhook_host = "teste.com";

    $order_data = array(
        "external_reference" => $external_id,
        "title" => "Compra pix teste",
        "description" => "Compra pix teste",
        "notification_url" => "https://" . $pix_webhook_host . "/pix/webhook/",
        "expiration_date" => gerar_data_hora(),
        "total_amount" => $value,
        "items" => array(
            array(
                "title" => "Item de teste",
                "description" => "Item de teste",
                "unit_price" => $value,
                "quantity" => 1,
                "unit_measure" => "unit",
                "total_amount" => $value
            )
        )
    );
}

function get_qrcode_data() {

}

function get_teste() {
    $response = "00020101021226940014BR.GOV.BCB.PIX2572pix-qr.mercadopago.com/instore/o/v2/".
    "73055cb8-ceb7-4c9a-8328-298b0630c6c85204000053039865802BR5904Luan6009SAO PAULO62070503***63042300";
    header("Content-Type: text/plain");
    echo $response;
}

?>