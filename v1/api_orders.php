<?php
// Connect to database
include("../connection.php");
$db = new dbObj();
$connection =  $db->getConnstring();
 
$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            if(!empty($_GET["status"])) {
                get_order_status($id);
            } else {
                get_order($id);
            }
        } else {
            get_orders();
        }
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
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

function get_order_status($id) {
    global $connection;
    $query = "SELECT * FROM order_data WHERE external_id = ". $id;
    $response = array();
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_object($result)) {
        $response[] = $row;
    }

    header("Content-Type: text/plain");
    echo $response[0]->order_status;

    mysqli_close($connection);
}

function get_orders() {
    global $connection;
    $query = "SELECT * FROM order_data";
    $response = array();
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_object($result)) {
        $response[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($response, JSON_NUMERIC_CHECK);

    mysqli_close($connection);
}

?>