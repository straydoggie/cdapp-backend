<?php
/*
 *{
 *   "identifier": "",
 *   "type": "",
 *   "content": [],
 *   "options": [],
 *   "answers":[],
 *   "author": "",
 *   "created": "",
 *   "updated": "",
 *   "keywords": []
 *}
 */
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$action = $_GET['action'];
if (!$action) {
    $post = file_get_contents('php://input');
    if ($post) {
        $post = json_decode($post, true);
        $action = $post['action'];
        $payload = $post['payload'];
    }
}

if ($action == 'get') {
    getData();
} else if ($action == 'add') {
    if ($payload != null) {
        addData($payload);
    }
}

function getData()
{
    $data = array();
    $sql = 'SELECT * FROM tkplus_library WHERE status = 0 ORDER BY number DESC';
    $res = db_exec($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            array_push($data, array_merge_recursive(array('identifier' => $row['identifier']), json_decode($row['data'], true)));
        }
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
function addData($payload)
{
    $identifier = $payload['identifier'];
    $data = json_encode(array_slice($payload, 1), JSON_UNESCAPED_UNICODE);
    $sql = "INSERT INTO tkplus_library (identifier, data) VALUES ('{$identifier}','{$data}')";
    echo $sql;
    db_exec($sql);
}
function db_exec($sql)
{
    $dbhost = 'localhost';
    $dbusr = 'cdapp';
    $dbpwd = 'gzzxjjwt1';
    $bdname = 'cdapp';
    $link = mysqli_connect($dbhost, $dbusr, $dbpwd, $bdname);
    if (!$link) {
        echo mysqli_connect_errno();
        echo mysqli_connect_error();
        return null;
    }
    mysqli_query($link, "set character set 'utf8'");
    mysqli_query($link, "set names 'utf8'");
    if ($result = mysqli_query($link, $sql)) {
        mysqli_close($link);
        return $result;
    }
    mysqli_close($link);
    return null;
}
