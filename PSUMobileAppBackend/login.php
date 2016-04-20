<?php
include('db.php');
headers();
$privateKey = openssl_get_privatekey("-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBAILMveJq+2yD2rTo8Fu9ZqtRyylzLyIUUkrUwmPGXLhlXV9mBi6J
ljvQ2JWrh2j+KtHUvzPGyW4BEyB+Bk9lWdkCAwEAAQJAe9YccSGYqUSs7FseNb08
Vzc5giTrmvhicTa+VHiZkHoIaEpeF+p26KJk2D1yiPNYMf7Rh0bGbZTStXOk2YTb
gQIhANhxQEImeYqUeM/xaXtlECYW1dUDZAFiOI/IXfbRVfbvAiEAmrSBoUyMnh/p
eMEpk9g0YlDAUc4OwX4jWKg9Qw+De7cCIQCXvOW4unJw5d/AoFU7zcFBgrbMTEE6
+xn+KxE87MsgfwIgc2uelzfkZYjLiGMc4QfaNUun4KCKk8PHHTsP0bt+TksCIBhI
5kCV3coTiRr5adifXBBZs4FFyU3WQQPraEoZ1TxV
-----END RSA PRIVATE KEY-----");
    if(isset($_POST['UserID'])&&isset($_POST['UserPass'])){
        openssl_private_decrypt(base64_decode($_POST['UserID']), $id, $privateKey);
        openssl_private_decrypt(base64_decode($_POST['UserPass']), $pass, $privateKey);
        $query = $database->query('SELECT COUNT(*) AS res FROM Students_info WHERE Student_ID ='. addslashes($id)." AND WEB_PASSWORD='".md5($pass)."'");
        echo json_encode($database->fetch_array($query));
    }else{
    //not validated input
        echo json_encode(array("res"=>-1));
    }
?>