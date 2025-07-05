<?php
include 'db_connect.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$artist = null;
if($id) {
    $res = $conn->query("SELECT * FROM artists WHERE id = $id");
    if($res && $res->num_rows > 0) {
        $artist = $res->fetch_assoc();
    }
}
if(!$artist) {
    echo '<div class="alert alert-danger">Không tìm thấy nghệ sĩ.</div>';
    exit;
}

// Lấy danh sách bài hát của nghệ sĩ
$songs = [];
$song_res = $conn->query("SELECT u.*, g.genre FROM uploads u LEFT JOIN genres g ON g.id = u.genre_id WHERE u.artist_id = $id");
while($row = $song_res->fetch_assoc()) {
    $songs[] = $row;
}

// Lấy đánh giá và bình luận cho các bài hát
$song_comments = [];
$song_ratings = [];
$song_ids = array_column($songs, 'id');
if(count($song_ids)) {
    $ids_str = implode(',', array_map('intval', $song_ids));
    $cmt_res = $conn->query("SELECT * FROM song_comments WHERE song_id IN ($ids_str) ORDER BY created_at DESC");
    while($row = $cmt_res->fetch_assoc()) {
        $song_comments[$row['song_id']][] = $row;
    }
    $rt_res = $conn->query("SELECT song_id, AVG(rating) as avg_rating, COUNT(*) as total FROM song_comments WHERE song_id IN ($ids_str) GROUP BY song_id");
    while($row = $rt_res->fetch_assoc()) {
        $song_ratings[$row['song_id']] = $row;
    }
}
?>
<style>
.artist-detail-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #fff;
    margin-bottom: 0.5rem;
}
.artist-detail-section {
    background: #181a1b;
    border-radius: 1rem;
    padding: 2rem 2rem 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px #0002;
}
.artist-avatar-big {
    width: 260px;
    height: 260px;
    object-fit: cover;
    border-radius: 1rem;
    border: 4px solid #ffb703;
    background: #222;
    margin-bottom: 1.5rem;
}
.artist-info-label {
    font-size: 1.2rem;
    color: #ffb703;
    font-weight: 600;
}
.artist-info-value {
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 1rem;
}
.artist-description-box {
    background: #495057;
    padding: 1.2rem;
    border-radius: 0.5rem;
    color: #fff;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    min-height: 80px;
}
.song-list-section {
    margin-top: 2.5rem;
}
.song-card-view {
    background: #23272b;
    border-radius: 0.7rem;
    border: 1px solid #444;
    margin-bottom: 1.5rem;
    padding: 1.2rem 1.5rem;
    display: flex;
    align-items: center;
    min-height: 120px;
}
.song-cover-view {
    width: 110px;
    height: 110px;
    object-fit: cover;
    border-radius: 0.7rem;
    margin-right: 2rem;
    background: #222;
    border: 2px solid #ffb703;
}
.song-meta {
    flex: 1;
}
.song-title-view {
    font-size: 1.3rem;
    font-weight: bold;
    color: #fff;
    margin-bottom: 0.5rem;
}
.song-genre-view {
    font-size: 1.05rem;
    color: #8ecae6;
    margin-bottom: 0.3rem;
}
.song-desc-view {
    color: #fff;
    font-size: 1rem;
    background: #495057;
    border-radius: 0.4rem;
    padding: 0.7rem 1rem;
    margin-bottom: 0.2rem;
}
.play-btn-view {
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 2.7rem;
    height: 2.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    cursor: pointer;
    transition: background 0.2s;
    margin-left: 1.5rem;
}
.play-btn-view:hover { background: #218838; }
.rating-stars { color: #ffc107; font-size: 1.2rem; }
.comment-box { background: #23272b; border-radius: 0.5rem; padding: 0.7rem 1rem; margin-bottom: 0.5rem; }
.comment-meta { color: #8ecae6; font-size: 0.95rem; }
.comment-content { color: #fff; font-size: 1rem; }
</style>
<div class="container-fluid px-5 py-4">
    <div class="artist-detail-title mb-4">View Artist</div>
    <div class="artist-detail-section row align-items-center">
        <div class="col-md-4 text-center">
<img src="<?php echo $artist['avatar'] ? $artist['avatar'] : 'assets/uploads/default_cover.png'; ?>" class="artist-avatar-big" alt="Avatar" onerror="this.onerror=null;this.src='assets/default_cover.png';">
            <div class="mt-3">
                <button class="btn btn-primary mr-2" id="edit-artist-btn" data-id="<?php echo $artist['id']; ?>"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn btn-danger" id="delete-artist-btn" data-id="<?php echo $artist['id']; ?>"><i class="fa fa-trash"></i> Delete</button>
            </div>
        </div>
        <div class="col-md-8">
            <div class="artist-info-label">Artist Name:</div>
            <div class="artist-info-value"><?php echo htmlspecialchars($artist['name']); ?></div>
            <div class="artist-info-label">Description:</div>
            <div class="artist-description-box"><?php echo $artist['description'] ? nl2br(htmlspecialchars($artist['description'])) : '<i>Updating...</i>'; ?></div>
        </div>
    </div>
    <div class="song-list-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-white mb-0">Songs</h3>
            <button class="btn btn-success btn-sm" id="add-song-btn" data-artist-id="<?php echo $artist['id']; ?>"><i class="fa fa-plus"></i> Add/Delete Song</button>
        </div>
        <?php if(count($songs)): ?>
            <?php foreach($songs as $song): ?>
            <div class="song-card-view">
<img src="assets/uploads/<?php echo $song['cover_image'] ? $song['cover_image'] : 'default_cover.png'; ?>" class="song-cover-view" alt="cover" onerror="this.onerror=null;this.src='assets/default_cover.png';">
                <div class="song-meta">
                    <div class="song-title-view"><?php echo htmlspecialchars($song['title']); ?></div>
                    <div class="song-genre-view">Genre: <?php echo htmlspecialchars($song['genre']); ?></div>
                </div>
                <?php if(!empty($song['upath'])): ?>
                <!-- <button class="play-btn-view mr-2" onclick="play_music('assets/uploads/<?php echo $song['upath']; ?>')"><i class="fa fa-play"></i></button> -->
                <?php endif; ?>
                <!-- <button class="btn btn-primary btn-sm mr-2 edit-song-btn" data-id="<?php echo $song['id']; ?>"><i class="fa fa-edit"></i></button> -->
                <!-- <button class="btn btn-danger btn-sm delete-song-btn" data-id="<?php echo $song['id']; ?>"><i class="fa fa-trash"></i></button> -->
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-white-50">No songs found for this artist.</p>
        <?php endif; ?>
    </div>
</div>
<script>
if(typeof play_music !== 'function') {
  function play_music(src) {
    var audio = document.getElementById('audio-player');
    if(!audio) {
      audio = document.createElement('audio');
      audio.id = 'audio-player';
      audio.style.display = 'none';
      document.body.appendChild(audio);
    }
    audio.src = src;
    audio.play();
  }
}
// Ensure Bootstrap JS is loaded for modal functionality
if (typeof $().modal !== 'function') {
  var script = document.createElement('script');
  // Use Bootstrap 4 bundle (includes Popper)
  script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js';
  script.onload = function() {
    console.log('Bootstrap JS loaded dynamically.');
  };
  document.head.appendChild(script);
}

// Add debug for modal show
$(document).on('click', '#edit-artist-btn', function(){
    var id = $(this).data('id');
    console.log('Edit artist clicked, id:', id);
    $.ajax({
      url: 'artist_actions.php',
      type: 'GET',
      data: {action:'get', id:id},
      dataType: 'text',
      success: function(resp) {
        // Remove PHP warnings if present
        var jsonStart = resp.indexOf('{');
        if(jsonStart > 0) {
          resp = resp.substring(jsonStart);
        }
        try {
          resp = JSON.parse(resp);
        } catch(e) {
          console.error('JSON parse error:', e, resp);
          alert('Lỗi dữ liệu trả về từ server!');
          return;
        }
        console.log('Edit artist ajax resp:', resp);
        if(resp && resp.success && resp.data){
            var artist = resp.data;
            $('#edit_artist_id').val(artist.id);
            $('#edit_artist_name').val(artist.name);
            $('#edit_description').val(artist.description);
            if(artist.avatar) {
                $('#edit_avatarPreview').attr('src', artist.avatar).show();
            } else {
                $('#edit_avatarPreview').hide();
            }
            $('#editArtistModal').modal('show');
            setTimeout(function(){
                if (!$('#editArtistModal').hasClass('show')) {
                    console.error('Modal did not show!');
                }
            }, 500);
        } else {
            alert('Không lấy được thông tin nghệ sĩ!');
            if(resp && resp.error) console.error('Server error:', resp.error);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX error:', status, error, xhr.responseText);
        alert('Lỗi AJAX khi lấy thông tin nghệ sĩ!');
      }
    });
});
$(document).on('click', '#delete-artist-btn', function(){
    var id = $(this).data('id');
    if(confirm('Bạn có chắc muốn xóa nghệ sĩ này?')){
        $.post('artist_actions.php', {action:'delete', artist: '<?php echo addslashes($artist['name']); ?>'}, function(resp){
            if(resp.success){
                alert('Đã xóa nghệ sĩ!');
                location.reload();
            } else {
                alert(resp.error || 'Lỗi khi xóa nghệ sĩ!');
            }
        }, 'json');
    }
});
$(document).on('click', '.edit-song-btn', function(){
    var id = $(this).data('id');
    // TODO: Hiển thị form/modal chỉnh sửa bài hát
    alert('Chức năng Edit Song đang phát triển!');
});
$(document).on('click', '.delete-song-btn', function(){
    var id = $(this).data('id');
    if(confirm('Bạn có chắc muốn xóa bài hát này?')){
        $.post('ajax.php?action=delete_song', {id: id}, function(resp){
            if(resp.success){
                alert('Đã xóa bài hát!');
                location.reload();
            } else {
                alert(resp.error || 'Lỗi khi xóa bài hát!');
            }
        }, 'json');
    }
});
var allSongList = [];
$(document).on('click', '#add-song-btn', function(){
  var artistId = $(this).data('artist-id');
  console.log('Add song clicked, artistId:', artistId);
  $('#assign_song_artist_id').val(artistId);
  $.ajax({
    url: 'artist_actions.php',
    type: 'GET',
    data: {action:'list_songs', artist_id: artistId},
    dataType: 'text',
    success: function(resp) {
      // Remove PHP warnings if present
      var jsonStart = resp.indexOf('{');
      if(jsonStart > 0) {
        resp = resp.substring(jsonStart);
      }
      try {
        resp = JSON.parse(resp);
      } catch(e) {
        console.error('JSON parse error:', e, resp);
        alert('Lỗi dữ liệu trả về từ server!');
        return;
      }
      console.log('Add song ajax resp:', resp);
      if(resp && resp.success && resp.songs) {
        allSongList = resp.songs;
        renderSongList(allSongList, '');
        $('#assignSongModal').modal('show');
        setTimeout(function(){
          if (!$('#assignSongModal').hasClass('show')) {
            console.error('AssignSongModal did not show!');
          }
        }, 500);
      } else {
        $('#assign-song-list').html('<div class="text-danger">Không có bài hát nào để chọn.</div>');
        $('#assignSongModal').modal('show');
        if(resp && resp.error) console.error('Server error:', resp.error);
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX error:', status, error, xhr.responseText);
      alert('Lỗi AJAX khi lấy danh sách bài hát!');
    }
  });
});
$('#search-song-input').on('input', function(){
  var keyword = $(this).val().toLowerCase();
  renderSongList(allSongList, keyword);
});
function renderSongList(songList, keyword) {
  var html = '';
  songList.forEach(function(song){
    if(!keyword || song.title.toLowerCase().includes(keyword)) {
      html += '<div class="form-check d-flex align-items-center mb-2">';
      html += '<input class="form-check-input mt-0" type="checkbox" name="song_ids[]" value="'+song.id+'" id="song_'+song.id+'"'+(song.checked?' checked':'')+'>';
      html += '<label class="form-check-label d-flex align-items-center ml-2" for="song_'+song.id+'">';
html += '<img src="'+(song.cover_image ? 'assets/uploads/'+song.cover_image : 'assets/uploads/default_cover.png')+'" style="width:40px;height:40px;object-fit:cover;border-radius:5px;margin-right:10px;" onerror="this.onerror=null;this.src=\'assets/default_cover.png\';">';
      html += song.title;
      html += '</label></div>';
    }
  });
  if(!html) html = '<div class="text-warning">Không tìm thấy bài hát phù hợp.</div>';
  $('#assign-song-list').html(html);
}
$('#assign-song-form').submit(function(e){
  e.preventDefault();
  var formData = $(this).serialize();
  $.post('artist_actions.php', formData, function(resp){
    if(typeof resp === 'string') {
      try { resp = JSON.parse(resp); } catch(e) {}
    }
    if(resp && resp.success){
      // Đóng modal và xóa backdrop nếu bị kẹt
      $('#assignSongModal').modal('hide');
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      setTimeout(function(){ location.reload(); }, 600);
    } else {
      alert((resp && resp.error) || 'Lỗi khi gán bài hát!');
    }
  },'json');
});
// Sửa lại form và xử lý submit
$(document).off('click', '#save-artist-btn-new').on('click', '#save-artist-btn-new', function(){
    var name = $('#edit_artist_name').val().trim();
    if(!name) {
        alert('Tên nghệ sĩ không được để trống!');
        $('#edit_artist_name').focus();
        return;
    }
    var form = document.getElementById('editArtistForm');
    var formData = new FormData(form);
    formData.append('action', 'edit');
    var $btn = $(this);
    $btn.prop('disabled', true).text('Đang lưu...');
    $.ajax({
        url: 'artist_actions.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(resp) {
            if(resp && resp.success) {
                if(typeof alert_toast === 'function') {
                    alert_toast('Cập nhật nghệ sĩ thành công!','success');
                } else {
                    alert('Cập nhật nghệ sĩ thành công!');
                }
                $('#editArtistModal').modal('hide');
                $btn.prop('disabled', false).text('Lưu Nghệ Sĩ');
            } else {
                alert((resp && resp.error) || 'Lỗi khi cập nhật thông tin nghệ sĩ!');
                $btn.prop('disabled', false).text('Lưu Nghệ Sĩ');
            }
        },
        error: function(xhr, status, error) {
            alert('Lỗi AJAX khi cập nhật nghệ sĩ!');
            $btn.prop('disabled', false).text('Lưu Nghệ Sĩ');
        }
    });
});
$(document).on('submit', '.song-comment-form', function(e){
  e.preventDefault();
  var form = $(this);
  var data = form.serialize()+'&action=add_comment';
  $.post('artist_actions.php', data, function(resp){
    if(resp.success){
      alert('Đã gửi bình luận!');
      location.reload();
    } else {
      alert(resp.error || 'Lỗi khi gửi bình luận!');
    }
  },'json');
});
/*
-- Để thêm nghệ sĩ vào popular (set cột popular = 1) bằng SQL:
UPDATE artists SET popular = 1 WHERE id = <ID_NGHESI>;
-- Ví dụ: Muốn set nghệ sĩ có id=5 thành popular:
UPDATE artists SET popular = 1 WHERE id = 5;
*/
</script>

<!-- Modal chỉnh sửa nghệ sĩ -->
<div class="modal fade" id="editArtistModal" tabindex="-1" aria-labelledby="editArtistModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editArtistModalLabel">Chỉnh sửa nghệ sĩ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editArtistForm">
          <input type="hidden" id="edit_artist_id" name="id">
          <div class="form-group">
            <label for="edit_artist_name">Tên nghệ sĩ</label>
            <input type="text" class="form-control" id="edit_artist_name" name="name" required>
          </div>
          <div class="form-group">
            <label for="edit_description">Mô tả</label>
            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Ảnh đại diện</label>
            <div class="d-flex align-items-center">
              <img id="edit_avatarPreview" src="" alt="Avatar" class="artist-avatar-big mr-3" style="display:none;">
              <input type="file" class="form-control-file" id="edit_avatar" name="avatar">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success" id="save-artist-btn-new">Lưu Nghệ Sĩ</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Gán Bài Hát Cho Nghệ Sĩ (nâng cấp) -->
<div class="modal fade" id="assignSongModal" tabindex="-1" role="dialog" aria-labelledby="assignSongModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="assignSongModalLabel">Gán Bài Hát Đã Có Cho Nghệ Sĩ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="assign-song-form">
        <input type="hidden" name="action" value="assign_songs">
        <input type="hidden" name="artist_id" id="assign_song_artist_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Chọn bài hát muốn gán cho nghệ sĩ:</label>
            <input type="text" class="form-control mb-2" id="search-song-input" placeholder="Tìm kiếm bài hát...">
            <div id="assign-song-list" style="max-height:300px;overflow-y:auto;"></div>
          </div>
        </div>
        <div class="modal-footer border-top border-info">
          <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">Lưu</button>
          <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" data-dismiss="modal">Hủy</button>
        </div>
      </form>
    </div>
  </div>
</div>