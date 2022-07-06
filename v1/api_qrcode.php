<?php

$config = parse_ini_file("../gateway_config.ini", true);
$pix_webhook_url = $config['pix']['wbhook_url'];
$pix_qrcode_duration = $config['pix']['qrcode_duration'];
$pix_token = $config['pix']['token'];
$pix_user_id = $config['pix']['user_id'];
$pix_external_pos_id = $config['pix']['external_pos_id'];

$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method) {

    case 'GET':
        get_qrcode_data();
        break;

    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_expiration() {
    global $pix_qrcode_duration;
    $current_time = new DateTime('NOW');
    $current_time->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    $current_time->add(new DateInterval('PT' . $pix_qrcode_duration . 'M'));
    $stamp =  $current_time->format('Y-m-d\TH:i:s.vP');
    return $stamp;
}

function create_order($external_id, $value = 0.25) {
    global $pix_webhook_url;
    $order_data = array(
        "external_reference" => $external_id,
        "title" => "Compra pix teste",
        "description" => "Compra pix teste",
        "notification_url" => $pix_webhook_url,
        "expiration_date" => get_expiration(),
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
    //header("Content-Type: application/json");
    //echo json_encode($order_data);
    $url = "https://api.mercadopago.com/instore/orders/qr/seller/collectors/" . $pix_user_id . 
    "/pos/" . $pix_external_pos_id . "/qrs";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
        "Authorization: Bearer " . $pix_token,
        "Content-Type: application/json"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);

    $response_object =  json_decode($response);

    header("Content-Type: text/plain");
    echo $response_object->{"qr_data"};

}

function get_qrcode_data() {
    
    if(!empty($_GET["id"])) {
        $id = $_GET["id"];
        if(!empty($_GET["value"])) {
            $value = floatval($_GET["value"]);
            create_order($id, $value);
        } else {
            create_order($id);
        }

    } else {
        echo "error";
    }
    

}

?>