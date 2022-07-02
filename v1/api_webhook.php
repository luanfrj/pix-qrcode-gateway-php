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

function verify_url_data($url) {

}

function receive_webhook() {
    $post_data = file_get_contents('php://input');
    $data_json = json_decode($post_data);
    $url = $data_json["resource"];
    $fp = fopen('data.txt', 'w');
    fwrite($fp, $post_data);
    fclose($fp);
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