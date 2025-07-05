
<?php
include 'db_connect.php';
header('Content-Type: application/json');

// Toggle popular status for artist
if ($action == 'toggle_popular') {
    $artist_id = intval($_POST['artist_id'] ?? 0);
    $popular = intval($_POST['popular'] ?? 0);
    if ($artist_id) {
        $sql = "UPDATE artists SET popular = $popular WHERE id = $artist_id";
        if ($conn->query($sql)) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Lỗi SQL: ' . $conn->error;
        }
    } else {
        $response['error'] = 'ID nghệ sĩ không hợp lệ';
    }
    echo json_encode($response);
    exit;
}

function upload_avatar($file) {
    $target_dir = 'assets/uploads/';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'artist_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $target_file = $target_dir . $filename;
    if(move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    }
    return '';
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false];

if ($action == 'add') {
    // Hỗ trợ cả name và artist cho tương thích form cũ/mới
    $name = trim($_POST['name'] ?? ($_POST['artist'] ?? ''));
    $desc = trim($_POST['description'] ?? '');
    $avatar = '';
    if(isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
        $avatar = upload_avatar($_FILES['avatar']);
    }
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO artists (name, description, avatar) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $desc, $avatar);
        if($stmt->execute()) {
            $response['success'] = true;
        } else {
            if($conn->errno == 1062) {
                $response['error'] = 'Tên nghệ sĩ đã tồn tại.';
            } else {
                $response['error'] = 'Lỗi SQL: ' . $conn->error;
            }
        }
    } else {
        $response['error'] = 'Tên nghệ sĩ không được để trống.';
    }
}

if ($action == 'get') {
    $id = intval($_GET['id'] ?? 0);
    if($id) {
        $row = $conn->query("SELECT * FROM artists WHERE id=".$id)->fetch_assoc();
        if($row) {
            $response = [
                'success' => true,
                'data' => $row
            ];
        } else {
            $response['error'] = 'Nghệ sĩ không tồn tại';
        }
    } else {
        $response['error'] = 'ID nghệ sĩ không hợp lệ';
    }
}

if ($action == 'update') {
    $artist_id = intval($_POST['artist_id'] ?? 0);
    $name = trim($_POST['artist'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $avatar = '';
    if(isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
        $avatar = upload_avatar($_FILES['avatar']);
    }
    if ($artist_id && $name !== '') {
        $set_avatar = $avatar ? ", avatar='$avatar'" : '';
        $conn->query("UPDATE artists SET name='".$conn->real_escape_string($name)."', description='".$conn->real_escape_string($desc)."' $set_avatar WHERE id=$artist_id");
        // Gán lại các bài hát
        $conn->query("UPDATE uploads SET artist_id=NULL, artist='' WHERE artist_id=$artist_id");
        if(!empty($_POST['songs'])) {
            $song_ids = $_POST['songs'];
            foreach($song_ids as $sid) {
                $conn->query("UPDATE uploads SET artist_id=$artist_id, artist='".$conn->real_escape_string($name)."' WHERE id=".intval($sid));
            }
        }
        $response['success'] = true;
    }
}

if ($action == 'delete') {
    $artist = trim($_POST['artist'] ?? '');
    $row = $conn->query("SELECT id FROM artists WHERE name='".$conn->real_escape_string($artist)."'")->fetch_assoc();
    if($row) {
        $artist_id = $row['id'];
        $conn->query("DELETE FROM artists WHERE id=$artist_id");
        $conn->query("UPDATE uploads SET artist_id=NULL, artist='' WHERE artist_id=$artist_id");
        $response['success'] = true;
    }
}

if ($action == 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $avatar = '';
    if(isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name']) {
        $avatar = upload_avatar($_FILES['avatar']);
    }
    if ($id && $name !== '') {
        $set_avatar = $avatar ? ", avatar='$avatar'" : '';
        $sql = "UPDATE artists SET name='".$conn->real_escape_string($name)."', description='".$conn->real_escape_string($desc)."' $set_avatar WHERE id=$id";
        if($conn->query($sql)) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Lỗi SQL: ' . $conn->error;
        }
    } else {
        $response['error'] = 'Thiếu thông tin nghệ sĩ.';
    }
}

if ($action == 'add_song') {
    $artist_id = intval($_POST['artist_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $cover = '';
    $upath = '';
    if(isset($_FILES['cover_image']) && $_FILES['cover_image']['tmp_name']) {
        $cover = upload_avatar($_FILES['cover_image']);
    }
    if(isset($_FILES['upath']) && $_FILES['upath']['tmp_name']) {
        $target_dir = 'assets/uploads/';
        $ext = strtolower(pathinfo($_FILES['upath']['name'], PATHINFO_EXTENSION));
        $filename = 'music_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_file = $target_dir . $filename;
        if(move_uploaded_file($_FILES['upath']['tmp_name'], $target_file)) {
            $upath = $filename;
        }
    }
    if ($artist_id && $title && $upath) {
        $stmt = $conn->prepare("INSERT INTO uploads (title, genre, description, cover_image, upath, artist_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssi', $title, $genre, $desc, $cover, $upath, $artist_id);
        if($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Lỗi SQL: ' . $conn->error;
        }
    } else {
        $response['error'] = 'Thiếu thông tin bắt buộc.';
    }
}

if ($action == 'list_songs') {
    $artist_id = intval($_GET['artist_id'] ?? 0);
    $songs = [];
    $q = $conn->query("SELECT id, title, artist_id, cover_image FROM uploads");
    while($s = $q->fetch_assoc()) {
        $s['checked'] = ($s['artist_id'] == $artist_id) ? true : false;
        $songs[] = $s;
    }
    $response = [
        'success' => true,
        'songs' => $songs
    ];
}
if ($action == 'assign_songs') {
    $artist_id = intval($_POST['artist_id'] ?? 0);
    $song_ids = isset($_POST['song_ids']) ? $_POST['song_ids'] : [];
    // Bỏ gán các bài hát cũ khỏi nghệ sĩ này
    $conn->query("UPDATE uploads SET artist_id=NULL WHERE artist_id=$artist_id");
    // Gán lại các bài hát được chọn
    if(!empty($song_ids)) {
        foreach($song_ids as $sid) {
            $conn->query("UPDATE uploads SET artist_id=$artist_id WHERE id=".intval($sid));
        }
    }
    $response['success'] = true;
}

if ($action == 'add_comment') {
    $song_id = intval($_POST['song_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    $user_id = isset($_SESSION['login_id']) ? intval($_SESSION['login_id']) : null;
    $user_name = isset($_SESSION['login_name']) ? $_SESSION['login_name'] : (isset($_SESSION['login_firstname']) ? $_SESSION['login_firstname'] : 'Khách');
    if($song_id && $rating && $comment) {
        if($user_id) {
            $stmt = $conn->prepare("INSERT INTO song_comments (song_id, user_id, user_name, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('iisis', $song_id, $user_id, $user_name, $rating, $comment);
        } else {
            $stmt = $conn->prepare("INSERT INTO song_comments (song_id, user_id, user_name, rating, comment, created_at) VALUES (?, NULL, ?, ?, ?, NOW())");
            $stmt->bind_param('isis', $song_id, $user_name, $rating, $comment);
        }
        if($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Lỗi SQL: ' . $conn->error;
        }
    } else {
        $response['error'] = 'Thiếu thông tin.';
    }
}

if ($action == 'delete_comment') {
    $comment_id = intval($_POST['comment_id'] ?? 0);
    $user_id = isset($_SESSION['login_id']) ? intval($_SESSION['login_id']) : 0;
    $is_admin = isset($_SESSION['login_type']) && ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] === '1');
    if($user_id && $comment_id) {
        if($is_admin) {
            // Admin xóa bất kỳ bình luận nào
            $stmt = $conn->prepare("DELETE FROM song_comments WHERE id = ?");
            $stmt->bind_param('i', $comment_id);
            $stmt->execute();
            if($stmt->affected_rows > 0) {
                $response['success'] = true;
            } else {
                $response['error'] = 'Bình luận không tồn tại.';
            }
        } else {
            // User thường chỉ xóa bình luận của mình
            $check = $conn->prepare("SELECT id FROM song_comments WHERE id = ? AND user_id = ?");
            $check->bind_param('ii', $comment_id, $user_id);
            $check->execute();
            $result = $check->get_result();
            if($result && $result->num_rows > 0) {
                $stmt = $conn->prepare("DELETE FROM song_comments WHERE id = ? AND user_id = ?");
                $stmt->bind_param('ii', $comment_id, $user_id);
                $stmt->execute();
                if($stmt->affected_rows > 0) {
                    $response['success'] = true;
                } else {
                    $response['error'] = 'Không thể xóa bình luận.';
                }
            } else {
                $response['error'] = 'Bạn không có quyền xóa bình luận này.';
            }
        }
    } else {
        $response['error'] = 'Bạn cần đăng nhập và chọn bình luận để xóa.';
    }
}

echo json_encode($response);
