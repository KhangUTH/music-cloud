<?php
if (!isset($GLOBALS['__session_started'])) {
	if (session_status() == PHP_SESSION_NONE) session_start();
	$GLOBALS['__session_started'] = true;
}
if (!class_exists('Action')) {
	ini_set('display_errors', 1);
	class Action {
	private $db;

	public function __construct() {
		ob_start();
	include 'db_connect.php';
	
	$this->db = $conn;
	}
	function __destruct() {
		$this->db->close();
		ob_end_flush();
	}

	function login(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where email = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			$row = $qry->fetch_array();
			foreach ($row as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($row['type'])) $_SESSION['login_type'] = $row['type'];
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM users where email = '".$email."' and password = '".md5($password)."'  and type= 2 ");
		if($qry->num_rows > 0){
			$row = $qry->fetch_array();
			foreach ($row as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($row['type'])) $_SESSION['login_type'] = $row['type'];
				return 1;
		}else{
			return 3;
		}
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($cpass) && !empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','month','day','year')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(isset($email)){
			$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
			if($check > 0){
				return 2;
				exit;
			}
		}
		if(isset($_FILES['pp']) && $_FILES['pp']['tmp_name'] != ''){
			$fnamep = strtotime(date('y-m-d H:i')).'_'.$_FILES['pp']['name'];
			$move = move_uploaded_file($_FILES['pp']['tmp_name'],'assets/uploads/'. $fnamep);
			$data .= ", profile_pic = '$fnamep' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					if($k = 'pp'){
						$k ='profile_pic';
					}
					if($k = 'cover'){
						$k ='cover_pic';
					}
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
					if(isset($_FILES['pp']) &&$_FILES['pp']['tmp_name'] != '')
						$_SESSION['login_profile_pic'] = $fnamep;
					if(!isset($type))
						$_SESSION['login_type'] = 2;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table')) && !is_numeric($k)){
				if($k =='password')
					$v = md5($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function save_genre(){
		extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
			}

		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_photo = '$fname' ";
		}
		if(empty($id)){
			if(empty($_FILES['cover']['tmp_name']))
			$data .= ", cover_photo = 'default_cover.jpg' ";
			$save = $this->db->query("INSERT INTO genres set $data");
		}else{
			$save = $this->db->query("UPDATE genres set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_genre(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM genres where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_music(){
		extract($_POST);
		$data = "";

		global $conn;
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover','audio','item_code','action')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				$v = $conn->real_escape_string($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$data .=",user_id = '{$_SESSION['login_id']}' ";

		// Xử lý upload cover
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			if(!$move){
				echo json_encode(['error'=>'Không thể upload ảnh bìa. Kiểm tra quyền thư mục assets/uploads hoặc kích thước file!']);
				exit;
			}
			$data .= ", cover_image = '$fname' ";
		}

	   // Xử lý upload audio
	   if(isset($_FILES['audio']) && $_FILES['audio']['tmp_name'] != ''){
		   $audio = strtotime(date('y-m-d H:i')).'_'.$_FILES['audio']['name'];
		   $move = move_uploaded_file($_FILES['audio']['tmp_name'],'assets/uploads/'. $audio);
		   if(!$move){
			   echo json_encode(['error'=>'Không thể upload file nhạc. Kiểm tra quyền thư mục assets/uploads hoặc kích thước file!']);
			   exit;
		   }
		   $data .= ", upath = '$audio' ";
	   }

	   // Kiểm tra các trường bắt buộc
	   $required = ['title', 'artist', 'genre_id'];
	   $missing = [];
	   foreach($required as $field) {
		   if(empty($_POST[$field])) $missing[] = $field;
	   }
	   if(!isset($_FILES['audio']) || $_FILES['audio']['tmp_name'] == '') {
		   $missing[] = 'audio';
	   }
	   if(count($missing) > 0) {
		   echo json_encode(['error'=>'Thiếu trường bắt buộc: '.implode(', ', $missing)]);
		   exit;
	   }

	   if(empty($id)){
		   if(empty($_FILES['cover']['tmp_name']))
			   $data .= ", cover_image = 'default_cover.jpg' ";
		   $query = "INSERT INTO uploads set $data";
		   $save = $this->db->query($query);
		   if(!$save){
			   echo json_encode(['error'=>'Lỗi SQL: '.$this->db->error, 'query'=>$query, 'data'=>$data]);
			   exit;
		   }
		   return 1;
	   }else{
		   $query = "UPDATE uploads set $data where id = $id";
		   $save = $this->db->query($query);
		   if(!$save){
			   echo json_encode(['error'=>'Lỗi SQL: '.$this->db->error, 'query'=>$query, 'data'=>$data]);
			   exit;
		   }
		   return 1;
	   }
	}
	function delete_music(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM uploads where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_details(){
		extract($_POST);
		$get = $this->db->query("SELECT * FROM uploads where id = $id")->fetch_array();
		$data = array("cover_image"=>$get['cover_image'],"title"=>$get['title'],"artist"=>$get['artist']);
		return json_encode($data);
	}
	function save_playlist(){
		extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
			}
			$data .=",user_id = '{$_SESSION['login_id']}' ";
			if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_image = '$fname' ";
		}
		if(empty($id)){
			if(empty($_FILES['cover']['tmp_name']))
			$data .= ", cover_image = 'play.jpg' ";
			$save = $this->db->query("INSERT INTO playlist set $data");
		}else{
			$save = $this->db->query("UPDATE playlist set $data where id = $id");
		}
		if($save){
			if(empty($id))
			$id = $this->db->insert_id;
			return $id;
		}
	}
	function delete_playlist(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM playlist where id = $id");
		if($delete){
			return 1;
		}
	}
	function find_music(){
		extract($_POST);
		$get = $this->db->query("SELECT id,title,upath,artist,cover_image FROM uploads where title like '%$search%' or artist like '%$search%' ");
		$data = array();
		while($row = $get->fetch_assoc()){
			$data[] = $row;
		}
		return json_encode($data);
	}
	function save_playlist_items(){
		extract($_POST);
		$ids=array();
		foreach($music_id as $k => $v){
			$data = " playlist_id = $playlist_id ";
			$data .= ", music_id = {$music_id[$k]} ";
			$check = $this->db->query("SELECT * FROM playlist_items where playlist_id = $playlist_id and  music_id = {$music_id[$k]}")->num_rows;
			if($check <= 0){
				if($save[] = $this->db->query("INSERT INTO playlist_items set $data ")){
					$ids[]=$music_id[$k];
				}
			}else{
				$save[] = 1;
			}

		}
		if(isset($save)){
			$this->db->query("DELETE FROM playlist_items where playlist_id = $playlist_id and music_id not in (".implode(',',$music_id).") ");
			return 1;
		}
	}
	function toggle_favorite($user_id, $music_id) {
		// Kiểm tra đã yêu thích chưa
		$check = $this->db->query("SELECT * FROM favorites WHERE user_id = $user_id AND music_id = $music_id");
		if($check->num_rows > 0) {
			// Nếu đã yêu thích, huỷ yêu thích
			$this->db->query("DELETE FROM favorites WHERE user_id = $user_id AND music_id = $music_id");
			// Xoá khỏi playlist My Favourite nếu có
			$playlist = $this->db->query("SELECT id FROM playlist WHERE user_id = $user_id AND title = 'My Favourite'");
			if($playlist->num_rows > 0) {
				$pid = $playlist->fetch_assoc()['id'];
				$this->db->query("DELETE FROM playlist_items WHERE playlist_id = $pid AND music_id = $music_id");
			}
			return 'removed';
		} else {
			// Thêm vào favorites
			$this->db->query("INSERT INTO favorites (user_id, music_id) VALUES ($user_id, $music_id)");
			// Kiểm tra playlist My Favourite
			$playlist = $this->db->query("SELECT id FROM playlist WHERE user_id = $user_id AND title = 'My Favourite'");
			if($playlist->num_rows == 0) {
				$this->db->query("INSERT INTO playlist (user_id, title, description, cover_image) VALUES ($user_id, 'My Favourite', 'Danh sách yêu thích', 'play.jpg')");
				$pid = $this->db->insert_id;
			} else {
				$pid = $playlist->fetch_assoc()['id'];
			}
			// Thêm vào playlist_items nếu chưa có
			$check_item = $this->db->query("SELECT * FROM playlist_items WHERE playlist_id = $pid AND music_id = $music_id");
			if($check_item->num_rows == 0) {
				$this->db->query("INSERT INTO playlist_items (playlist_id, music_id) VALUES ($pid, $music_id)");
			}
			return 'added';
		}
	}
	// Thêm bình luận mới
	function add_comment($song_id, $user_id, $rating, $comment) {
		$song_id = intval($song_id);
		$user_id = intval($user_id);
		$rating = intval($rating);
		$comment = $this->db->real_escape_string($comment);
		if($song_id <= 0 || $user_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) return 0;
		$sql = "INSERT INTO song_comments (song_id, user_id, rating, comment, created_at) VALUES ($song_id, $user_id, $rating, '$comment', NOW())";
		return $this->db->query($sql) ? 1 : 0;
	}

	// Xóa bình luận (chỉ admin)
	function delete_comment($comment_id) {
		$comment_id = intval($comment_id);
		if($comment_id <= 0) return 0;
		$sql = "DELETE FROM song_comments WHERE id = $comment_id";
		return $this->db->query($sql) ? 1 : 0;
	}

	// Lấy danh sách bình luận cho 1 bài hát
	function get_comments($song_id) {
		$song_id = intval($song_id);
		$comments = [];
		$sql = "SELECT c.*, CONCAT(u.firstname, ' ', u.lastname) as user_name, u.id as user_id FROM song_comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.song_id = $song_id ORDER BY c.created_at DESC";
		$res = $this->db->query($sql);
		while($row = $res->fetch_assoc()) {
			$comments[] = $row;
		}
		return json_encode($comments);
	}
	}
}