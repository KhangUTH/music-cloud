<?php
include 'db_connect.php';
if(!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1){
    echo "<script>alert('Access Denied!'); window.location.href='index.php?page=home';</script>";
    exit;
}
$qry = $conn->query("SELECT * FROM uploads where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	if($k=='title')
		$k = 'mtitle';
	$$k = $v;
}
include 'new_music.php';
?>