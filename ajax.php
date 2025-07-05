<?php
ob_start();
date_default_timezone_set("Asia/Manila");
// Đảm bảo có kết nối DB cho mọi action
include 'db_connect.php';
// Lấy action từ POST hoặc GET
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
include 'admin_class.php';
$crud = new Action();

// ==== AJAX cho bình luận ====
if($action == 'add_comment') {
    header('Content-Type: application/json');
    if (session_status() == PHP_SESSION_NONE) session_start();
    if(!isset($_SESSION['login_id'])) { echo json_encode(['error'=>'not_logged_in']); exit; }
    $song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;
    $user_id = $_SESSION['login_id'];
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    $result = $crud->add_comment($song_id, $user_id, $rating, $comment);
    if($result == 1) {
        // Lấy lại danh sách bình luận mới nhất
        $comments = $crud->get_comments($song_id);
        echo json_encode(['success'=>1, 'comments'=>json_decode($comments)]);
    } else {
        echo json_encode(['error'=>'Lỗi khi gửi bình luận!']);
    }
    exit;
}

if($action == 'delete_comment') {
    header('Content-Type: application/json');
    if (session_status() == PHP_SESSION_NONE) session_start();
    if(!isset($_SESSION['login_id']) || !isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1) { echo json_encode(['error'=>'not_admin']); exit; }
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    // Lấy song_id trước khi xóa để trả về danh sách mới
    $song_id = 0;
    // Lấy kết nối db từ $conn (biến toàn cục do include db_connect.php)
    global $conn;
    $row = $conn->query("SELECT song_id FROM song_comments WHERE id = $comment_id");
    if($row && $row->num_rows > 0) {
        $song_id = $row->fetch_assoc()['song_id'];
    }
    $result = $crud->delete_comment($comment_id);
    if($result == 1) {
        $comments = $crud->get_comments($song_id);
        echo json_encode(['success'=>1, 'comments'=>json_decode($comments)]);
    } else {
        echo json_encode(['error'=>'Lỗi khi xóa bình luận!']);
    }
    exit;
}

if($action == 'get_comments') {
    header('Content-Type: application/json');
    $song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;
    $result = $crud->get_comments($song_id);
    echo $result; exit;
}

// ...existing code...
ob_end_flush();
?>
<?php
ob_start();
date_default_timezone_set("Asia/Manila");
// Đảm bảo có kết nối DB cho mọi action
include 'db_connect.php';
// Lấy action từ POST hoặc GET
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
    $login = $crud->login();
    if($login)
        echo $login;
}
if($action == 'login2'){
    $login = $crud->login2();
    if($login)
        echo $login;
}
if($action == 'logout'){
    $logout = $crud->logout();
    if($logout)
        echo $logout;
}
if($action == 'logout2'){
    $logout = $crud->logout2();
    if($logout)
        echo $logout;
}

if($action == 'signup'){
    $save = $crud->signup();
    if($save)
        echo $save;
}
if($action == 'save_user'){
    $save = $crud->save_user();
    if($save)
        echo $save;
}
if($action == 'update_user'){
    $save = $crud->update_user();
    if($save)
        echo $save;
}
if($action == 'delete_user'){
    $save = $crud->delete_user();
    if($save)
        echo $save;
}
if($action == 'save_genre'){
    $save = $crud->save_genre();
    if($save)
        echo $save;
}
if($action == 'delete_genre'){
    $delete = $crud->delete_genre();
    if($delete)
        echo $delete;
}
if($action == 'save_music'){
    $save = $crud->save_music();
    if($save)
        echo $save;
}
if($action == 'delete_music'){
    $delete = $crud->delete_music();
    if($delete)
        echo $delete;
}
if($action == 'get_details'){
    $get = $crud->get_details();
    if($get)
        echo $get;
}
if($action == 'save_playlist'){
    $save = $crud->save_playlist();
    if($save)
        echo $save;
}
if($action == 'delete_playlist'){
    $delete = $crud->delete_playlist();
    if($delete)
        echo $delete;
}
if($action == 'find_music'){
    $get = $crud->find_music();
    if($get)
        echo $get;
}

if($action == 'save_playlist_items'){
    $save = $crud->save_playlist_items();
    if($save)
        echo $save;
}

// Thêm hoặc cập nhật nghệ sĩ
if(isset($_GET['action']) && $_GET['action'] == 'save_artist'){
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    if($id > 0){
        $sql = "UPDATE artists SET name='$name', description='$desc' WHERE id=$id";
    } else {
        $sql = "INSERT INTO artists (name, description) VALUES ('$name', '$desc')";
    }
    echo $conn->query($sql) ? 1 : 0;
    exit;
}
// Xóa nghệ sĩ
if(isset($_GET['action']) && $_GET['action'] == 'delete_artist'){
    $id = intval($_POST['id']);
    echo $conn->query("DELETE FROM artists WHERE id=$id") ? 1 : 0;
    exit;
}
if($action == 'toggle_favorite'){
    session_start();
    if(!isset($_SESSION['login_id'])) {
        echo 'not_logged_in'; exit;
    }
    $user_id = $_SESSION['login_id'];
    if(!isset($_POST['music_id']) || !is_numeric($_POST['music_id'])) {
        echo 'invalid_music_id'; exit;
    }
    $music_id = intval($_POST['music_id']);
    // Kiểm tra đã yêu thích chưa
    $check = $conn->query("SELECT id FROM favorites WHERE user_id = $user_id AND song_id = $music_id");
    if($check && $check->num_rows > 0) {
        // Đã yêu thích, xóa khỏi favorites
        $conn->query("DELETE FROM favorites WHERE user_id = $user_id AND song_id = $music_id");
        echo 'removed'; exit;
    } else {
        // Chưa yêu thích, thêm vào
        $ok = $conn->query("INSERT INTO favorites (user_id, song_id, added_at) VALUES ($user_id, $music_id, NOW())");
        if($ok) {
            echo 'added'; exit;
        } else {
            echo 'error'; exit;
        }
    }
}
// Xử lý thêm bài hát vào playlist
if($action == 'add_to_playlist') {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if(!isset($_SESSION['login_id'])) {
        echo 'Bạn cần đăng nhập!'; exit;
    }
    $user_id = $_SESSION['login_id'];
    $music_id = isset($_POST['music_id']) ? intval($_POST['music_id']) : 0;
    $playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;
    if($music_id <= 0 || $playlist_id <= 0) {
        echo 'Dữ liệu không hợp lệ!'; exit;
    }
    // Kiểm tra playlist có thuộc user không
    $pl_check = $conn->query("SELECT id FROM playlist WHERE id = $playlist_id AND user_id = $user_id");
    if(!$pl_check || $pl_check->num_rows == 0) {
        echo 'Playlist không hợp lệ!'; exit;
    }
    // Kiểm tra đã có bài hát trong playlist chưa
    $item_check = $conn->query("SELECT id FROM playlist_items WHERE playlist_id = $playlist_id AND music_id = $music_id");
    if($item_check && $item_check->num_rows > 0) {
        echo 'Bài hát đã có trong playlist!'; exit;
    }
    // Thêm vào playlist_items
    $ok = $conn->query("INSERT INTO playlist_items (playlist_id, music_id, added_at) VALUES ($playlist_id, $music_id, NOW())");
    if($ok) {
        echo 'success';
    } else {
        echo 'Lỗi khi thêm vào playlist!';
    }
    exit;
}
ob_end_flush();
?>
