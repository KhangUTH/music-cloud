<?php
if (!isset($GLOBALS['__session_started'])) {
    if (session_status() == PHP_SESSION_NONE) session_start();
    $GLOBALS['__session_started'] = true;
}
if (!isset($GLOBALS['__db_connected'])) {
    include_once 'db_connect.php';
    $GLOBALS['__db_connected'] = true;
}
$qry = $conn->query("SELECT u.*,g.genre FROM uploads u inner join genres g on g.id = u.genre_id where u.id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
    if($k=='title')
        $k = 'mtitle';
    $$k = $v;
}

// Lấy trạng thái yêu thích cho bài hát này
$is_fav = false;
if(isset($_SESSION['login_id'])) {
    $uid = $_SESSION['login_id'];
    $fav_q = $conn->query("SELECT 1 FROM favorites WHERE user_id = $uid AND music_id = $id");
    if($fav_q && $fav_q->num_rows > 0) $is_fav = true;
}

// Lấy đánh giá cho bài hát này
$song_id = $id;
$rt_res = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM song_comments WHERE song_id = $song_id");
$rt = $rt_res->fetch_assoc();
$avg_rating = $rt['avg_rating'] ? round($rt['avg_rating'],1) : 0;
$total_rating = $rt['total'] ?? 0;

// Lấy bình luận của bài hát
$song_comments = [];
$comments_res = $conn->query("SELECT c.*, CONCAT(u.firstname, ' ', u.lastname) as user_name, u.id as user_id FROM song_comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.song_id = $song_id ORDER BY c.created_at DESC");
while($row = $comments_res->fetch_assoc()) {
    $song_comments[] = $row;
}
?>
<style>
.description-box, .description-box * {
    color: white !important;
}
.rating-stars { color: #ffc107; font-size: 1.2rem; }
.comment-box { background: #23272b; border-radius: 0.5rem; padding: 0.7rem 1rem; margin-bottom: 0.5rem; }
.comment-meta { color: #8ecae6; font-size: 0.95rem; }
.comment-content { color: #fff; font-size: 1rem; }
</style>
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4">
            <center>
                <div class="d-flex img-thumbnail bg-gradient-1 position-relative" style="width: 12rem">
<img src="assets/uploads/<?php echo $cover_image ?>" alt="" style="object-fit: cover;max-width: 100%;height:14rem" onerror="this.onerror=null;this.src='assets/default_cover.png';">
                </div>
            </center>
            <div>
            </div>
        </div>
        <div class="col-md-8">
            <h5 class="text-white">Title: <?php echo ucwords($mtitle); ?></h5>
            <h6 class="text-white">Artist: <?php echo ucwords($artist); ?></h6>
            <h6 class="text-white">Genre: <?php echo ucwords($genre); ?></h6>
            <h6 class="text-white border-bottom border-primary"><b class="text-white">Description:</b></h6>
            <div class="text-white description-box" style="background-color: #495057; padding: 1rem; border-radius: 0.5rem;">
                <?php echo html_entity_decode($description) ?>
            </div>
            <div class="d-flex align-items-center mt-2">
    <span class="bg-green rounded-circle d-flex justify-content-center align-items-center mr-2" style="width: 2.5rem; height: 2.5rem; cursor: pointer;" onclick="play_music('assets/uploads/<?php echo $upath ?>')">
        <i class="fa fa-play" style="font-size: 1.2rem; color: white;"></i>
    </span>
    <button class="btn btn-fav mr-2" data-music-id="<?php echo $id ?>" style="background: none; border: none; color: #fd475d; font-size: 1.5rem;" title="Yêu thích">
        <i class="fa <?php echo $is_fav ? 'fa fa-heart' : 'fa fa-heart-o' ?>" id="fav-icon-<?php echo $id ?>"></i>
    </button>
<?php if(isset($_SESSION['login_type']) && $_SESSION['login_type']==1): ?>
    <button class="btn btn-danger ml-2" id="btn-delete-song" data-music-id="<?php echo $id ?>"><i class="fa fa-trash"></i> Xóa bài hát</button>
<?php endif; ?>
<?php if(isset($_SESSION['login_id'])) { ?>
<!-- Modal chọn playlist -->
<div class="modal fade" id="playlistModal" tabindex="-1" role="dialog" aria-labelledby="playlistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="playlistModalLabel">Chọn playlist để thêm bài hát</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select class="form-control" id="playlistSelect">
          <option value="">-- Chọn playlist --</option>
          <?php
          $uid = $_SESSION['login_id'];
          $pls = $conn->query("SELECT id, title FROM playlist WHERE user_id = $uid order by title asc");
          while($pl = $pls->fetch_assoc()): ?>
            <option value="<?php echo $pl['id'] ?>"><?php echo htmlspecialchars($pl['title']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" id="confirmAddToPlaylist">Thêm</button>
      </div>
    </div>
  </div>
</div>
    <button class="btn btn-warning d-flex justify-content-center align-items-center p-0 btn-add-to-playlist" style="width: 2.5rem; height: 2.5rem; border-radius: .5rem; font-size: 1.3rem; margin-left: 0.25rem;" title="Thêm vào playlist yêu thích" data-music-id="<?php echo $id ?>">
        <i class="fa fa-plus"></i>
    </button>
<?php } ?>
</div>
<!-- Đánh giá và bình luận -->
<div class="mt-4">
    <h6 class="text-white border-bottom border-primary pb-2 mb-3"><b class="text-white">Đánh giá và bình luận:</b></h6>
    <div id="commentsSection"></div>
    <div class="mt-3">
        <div class="rating-stars mb-1" id="avgRatingSection">
            <?php for($i=1;$i<=5;$i++) echo '<i class="fa'.($i<=$avg_rating? ' fa-star':' fa-star-o').'" ></i>'; ?>
            <span style="color:#fff;font-size:0.95rem;">(<?php echo $total_rating; ?> đánh giá)</span>
        </div>
        <?php if(isset($_SESSION['login_id'])): ?>
        <form class="song-comment-form mt-2" data-song-id="<?php echo $song_id; ?>">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <select class="form-control form-control-sm" name="rating" required>
                        <option value="">Sao</option>
                        <?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i ★</option>"; ?>
                    </select>
                </div>
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="comment" placeholder="Viết bình luận..." required>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-info">Gửi</button>
                </div>
            </div>
            <input type="hidden" name="song_id" value="<?php echo $song_id; ?>">
        </form>
        <?php else: ?>
        <div class="alert alert-warning mt-2">Bạn cần <a href="login.php">đăng nhập</a> để bình luận và đánh giá.</div>
        <?php endif; ?>
    </div>
</div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#btn-delete-song').click(function(){
        if(!confirm('Bạn có chắc chắn muốn xóa bài hát này?')) return;
        var musicId = $(this).data('music-id');
        $.ajax({
            url: 'ajax.php?action=delete_music',
            method: 'POST',
            data: {id: musicId},
            success: function(resp){
                if(resp == '1' || resp == 1){
                    alert('Đã xóa bài hát!');
                    window.location.href = 'index.php';
                } else {
                    alert('Lỗi khi xóa bài hát!');
                }
            },
            error: function(){
                alert('Lỗi kết nối server!');
            }
        });
    });
    // Tải lại bình luận khi vừa load trang
    reloadComments();
    $('.btn-fav').click(function(e){
        e.stopPropagation();
        var btn = $(this);
        var musicId = btn.data('music-id');
        $.ajax({
            url: 'ajax.php?action=toggle_favorite',
            method: 'POST',
            data: {music_id: musicId},
            success: function(resp){
                if(resp == 'added') {
                    $('#fav-icon-' + musicId).removeClass('fa-heart-o').addClass('fa-heart');
                } else if(resp == 'removed') {
                    $('#fav-icon-' + musicId).removeClass('fa-heart').addClass('fa-heart-o');
                }
            }
        });
    });
    $('.btn-add-to-playlist').click(function(e){
        e.stopPropagation();
        $('#playlistModal').modal('show');
    });
    $('#confirmAddToPlaylist').click(function(){
        var playlistId = $('#playlistSelect').val();
        if(!playlistId) {
            alert_toast('Vui lòng chọn playlist!','warning');
            return;
        }
        var musicId = $('.btn-add-to-playlist').data('music-id');
        $.ajax({
            url: 'ajax.php?action=add_to_playlist',
            method: 'POST',
            data: {music_id: musicId, playlist_id: playlistId},
            success: function(resp){
                if(resp.includes('success')) {
                    alert_toast('Đã thêm vào playlist!','success');
                } else {
                    alert_toast(resp,'warning');
                }
                $('#playlistModal').modal('hide');
            }
        });
    });
    function renderComments(comments) {
        console.log('renderComments input:', comments);
        var html = '';
        comments.forEach(function(comment) {
            var stars = '';
            for(var i=1;i<=5;i++) stars += '<i class="fa '+(i<=comment.rating?'fa-star':'fa-star-o')+'"></i>';
            var displayName = comment.user_name ? $('<div>').text(comment.user_name).html() : 'Khách';
            var time = comment.created_at ? new Date(comment.created_at.replace(' ','T')).toLocaleString('vi-VN') : '';
            html += '<div class="comment-box">'+
                '<div class="d-flex justify-content-between align-items-center mb-2">'+
                '<div class="mr-2"><span class="comment-meta">'+displayName+' - '+time+'</span></div>'+
                '<div class="d-flex align-items-center">'+
                '<span class="rating-stars mr-2">'+stars+'</span>'+
                (comment.can_delete ? '<button class="btn btn-sm btn-danger btn-delete-comment" data-comment-id="'+comment.id+'">Xóa</button>' : '')+
                '</div></div>'+
                '<div class="comment-content">'+$('<div>').text(comment.comment).html().replace(/\n/g,'<br>')+'</div>'+
                '</div>';
        });
        $('#commentsSection').html(html);
    }
    function reloadComments() {
        $.post('ajax.php', {action: 'get_comments', song_id: <?php echo $song_id; ?>}, function(resp) {
            console.log('API get_comments resp:', resp);
            var comments = [];
            if (typeof resp === 'string') {
                try { comments = JSON.parse(resp); } catch(e){
                    console.error('Lỗi parse JSON:', e, resp);
                }
            } else if (Array.isArray(resp)) {
                comments = resp;
            }
            // Đánh dấu quyền xóa cho admin
            var isAdmin = <?php echo (isset($_SESSION['login_type']) && $_SESSION['login_type']==1) ? 'true':'false'; ?>;
            comments.forEach(function(c){ c.can_delete = isAdmin; });
            console.log('Comments sau khi parse:', comments);
            renderComments(comments);
        });
    }
    $(document).on('submit', '.song-comment-form', function(e){
        e.preventDefault();
        var form = $(this);
        var data = form.serialize()+'&action=add_comment';
        $.post('ajax.php?action=add_comment', data, function(resp){
            console.log('add_comment resp:', resp);
            if(resp.success){
                alert('Đã gửi bình luận!');
                // Nếu có comments mới nhất trả về thì render luôn
                if(resp.comments) {
                    // Đánh dấu quyền xóa cho admin
                    var isAdmin = <?php echo (isset($_SESSION['login_type']) && $_SESSION['login_type']==1) ? 'true':'false'; ?>;
                    resp.comments.forEach(function(c){ c.can_delete = isAdmin; });
                    renderComments(resp.comments);
                } else {
                    reloadComments();
                }
                form[0].reset();
            } else if(resp.error === 'not_logged_in') {
                alert('Bạn cần đăng nhập để bình luận!');
            } else {
                alert(resp.error || 'Lỗi khi gửi bình luận!');
            }
        }, 'json');
    });
    $(document).on('click', '.btn-delete-comment', function(){
        if(!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
        var commentId = $(this).data('comment-id');
        $.post('ajax.php?action=delete_comment', {comment_id: commentId}, function(resp){
            if(resp.success){
                alert('Đã xóa bình luận!');
                if(resp.comments) {
                    var isAdmin = <?php echo (isset($_SESSION['login_type']) && $_SESSION['login_type']==1) ? 'true':'false'; ?>;
                    resp.comments.forEach(function(c){ c.can_delete = isAdmin; });
                    renderComments(resp.comments);
                } else {
                    reloadComments();
                }
            } else if(resp.error === 'not_admin') {
                alert('Chỉ admin mới được xóa bình luận!');
            } else {
                alert(resp.error || 'Lỗi khi xóa bình luận!');
            }
        }, 'json');
    });
    // Tải lại bình luận khi trang load
    reloadComments();
});
</script>