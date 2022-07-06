<?php
// Connect to database
include("../connection.php");
$db = new dbObj();
$connection =  $db->getConnstring();

$config = parse_ini_file("../gateway_config.ini", true);
$pix_token = $config['pix']['token'];
 
$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'POST':
        receive_webhook();
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function insert_data($external_id, $order_status, $last_update) {
    global $connection;

    $query = "INSERT INTO order_data (external_id, order_status, last_update) ".
        "VALUES (".$external_id.", ".$order_status.", '".$last_update."') ".
        "ON DUPLICATE KEY ".
        "UPDATE order_status = ".$order_status.", last_update = '".$last_update."';";

    mysqli_query($connection, $query) or die(mysqli_error($connection));
    mysqli_close($connection);
}

function verify_url_data($url) {
    global $pix_token;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
        "Authorization: Bearer " . $pix_token
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($curl);
    curl_close($curl);

    $resp_obj =  json_decode($resp);
    
    $last_update = new DateTime($resp_obj->{"last_updated"});
    $last_update->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    $external_id = intval($resp_obj->{"external_reference"});
    $order_status = 0;

    if ($resp_obj->{"status"} == "closed" && count($resp_obj->{"payments"}) > 0 && $resp_obj->{"payments"}[0]->{"status"} == "approved") {
        $order_status = 1;
    } else {
        $order_status = 0;
    }
    insert_data($external_id, $order_status, $last_update->format('Y-m-d H:i:s'));

}

function receive_webhook() {
    $post_data = file_get_contents('php://input');
    $data_json = json_decode($post_data);
    $url = $data_json->{"resource"};
    $fp = fopen('data.txt', 'w');
    fwrite($fp, $post_data);
    fclose($fp);
    verify_url_data($url);
}

function get_order($id) {
    global $connection;
    $query = "SELECT * FROM order_data WHERE external_id = ". $id;
    $response = array();
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_object($result)) {
        $response[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($response[0], JSON_NUMERIC_CHECK);

    mysqli_close($connection);
}

?>