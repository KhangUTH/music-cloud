<?php
session_start();
include 'db_connect.php';
if(!isset($_SESSION['login_id'])) {
    echo '<div class="alert alert-danger">Bạn cần đăng nhập để xem danh sách yêu thích.</div>';
    exit;
}
$user_id = $_SESSION['login_id'];
$sql = "SELECT m.* FROM favorites f JOIN music m ON f.song_id = m.id WHERE f.user_id = $user_id ORDER BY f.added_at DESC";
$result = $conn->query($sql);
?>
<div class="container py-4">
    <h2 class="mb-4">Danh sách bài hát yêu thích</h2>
    <?php if($result && $result->num_rows > 0): ?>
        <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card bg-black text-white h-100">
<img src="assets/uploads/<?php echo $row['cover_image'] ?>" class="card-img-top" style="object-fit: cover; height: 180px;" alt="cover" onerror="this.onerror=null;this.src='assets/default_cover.png';">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">Artist: <?php echo htmlspecialchars($row['artist']) ?></p>
                        <audio controls style="width:100%">
                            <source src="assets/uploads/<?php echo $row['upath'] ?>" type="audio/mpeg">
                            Trình duyệt của bạn không hỗ trợ audio.
                        </audio>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Bạn chưa có bài hát yêu thích nào.</div>
    <?php endif; ?>
</div>
