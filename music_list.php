</script>
<script>
// Bổ sung xử lý submit form upload nhạc trong modal

$('#manage-music-modal').submit(function(e){
  console.log('Bắt đầu submit form upload nhạc');
  e.preventDefault();
  var form = $(this)[0];
  var formData = new FormData(form);
  formData.append('action', 'save_music');
  $.ajax({
    url: 'ajax.php',
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    method: 'POST',
    type: 'POST',
    success: function(resp){
      console.log('Đã nhận response từ server:', resp);
      if(resp == 1){
        toastr.success('Upload thành công!');
        setTimeout(function(){
          location.reload();
        }, 1500);
      } else if(typeof resp === 'object' && resp.error) {
        toastr.error(resp.error);
      } else {
        toastr.error('Lỗi không xác định!\n' + resp);
        console.error('Upload response:', resp);
      }
    },
    error: function(xhr, status, error){
      toastr.error('Lỗi kết nối server!');
      console.error('AJAX error:', status, error, xhr.responseText);
    }
  });
  console.log('Đã gửi AJAX upload nhạc');
});
</script>
<?php include'db_connect.php' ?>
<?php
// Lấy danh sách playlist của user cho modal
$playlist_options = '';
if(isset($_SESSION['login_id'])) {
    $uid = $_SESSION['login_id'];
    $pls = $conn->query("SELECT id, title FROM playlist WHERE user_id = $uid order by title asc");
    while($pl = $pls->fetch_assoc()) {
        $playlist_options .= '<option value="'.$pl['id'].'">'.htmlspecialchars($pl['title']).'</option>';
    }
}
?>
<style>
.music-list-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem 1.5rem;
  justify-content: flex-start;
}
.music-card {
  background: #111;
  border-radius: 8px;
  width: 240px;
  margin-bottom: 1.5rem;
  color: #fff;
  border: 1px solid #222;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.music-cover {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 8px 8px 0 0;
  background: #222;
}
.music-info {
  padding: 0.7rem 1rem 1rem 1rem;
}
.music-info h4 {
  color: #fff;
  font-size: 1.05rem;
  margin-bottom: 0.2rem;
  font-weight: 600;
  line-height: 1.2;
}
.music-info p {
  margin-bottom: 0.12rem;
  color: #bdbdbd;
  font-size: 0.98rem;
}
.music-info .artist {
  color: #fd7e14;
  font-weight: 500;
}
.music-actions {
  display: flex;
  align-items: center;
  margin-top: 0.5rem;
  gap: 0.5rem;
}
.play-btn {
  background: #28a745;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  cursor: pointer;
}
.add-playlist-btn {
  background: #ffc107;
  color: #222;
  border: none;
  border-radius: 5px;
  padding: 0.35rem 0.8rem;
  font-weight: 500;
  cursor: pointer;
  font-size: 0.98rem;
}
.genre {
  color: #fd7e14;
  font-size: 0.97rem;
  margin-left: auto;
  font-weight: 500;
}
</style>
<div class="col-lg-12">
  <div class="d-flex justify-content-between align-items-center w-100">
    <div class="form-group" style="width:calc(50%) ">
      <div class="input-group">
        <input type="search" id="filter" name="filter" class="form-control form-control-sm" placeholder="Search music using keyword">
        <div class="input-group-append">
          <button type="button" id="search" class="btn btn-sm btn-dark">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
    </div>
    <?php if($_SESSION['login_type'] == 1): ?>
    <button class="btn btn-sm btn-primary bg-gradient-primary" data-toggle="modal" data-target="#newMusicModal"><i class="fa fa-plus"></i> Add New</button>
    <?php endif; ?>
  </div>
  <button class="btn btn-primary" id="play_all">Play All</button>
  <p></p>
  <div class="music-list-grid" id="music-list">
    <?php 
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      if($search != '') {
        $musics = $conn->query("SELECT u.*,g.genre FROM uploads u inner join genres g on g.id = u.genre_id WHERE u.title LIKE '%".$conn->real_escape_string($search)."%' OR u.artist LIKE '%".$conn->real_escape_string($search)."%' OR g.genre LIKE '%".$conn->real_escape_string($search)."%' order by u.title asc");
      } else {
        $musics = $conn->query("SELECT u.*,g.genre FROM uploads u inner join genres g on g.id = u.genre_id order by u.title asc");
      }
      while($row=$musics->fetch_assoc()):
        $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
        unset($trans["\""], $trans["<"], $trans[">"]);
        $desc = strtr(html_entity_decode($row['description']),$trans);
        $desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
    ?>
    <div class="music-card">
      <a href="index.php?page=view_music&id=<?php echo $row['id'] ?>">
<img src="assets/uploads/<?php echo $row['cover_image'] ?>" class="music-cover" alt="cover" onerror="this.onerror=null;this.src='assets/default_cover.png';">
      </a>
      <div class="music-info">
        <h4><?php echo ucwords($row['title']) ?></h4>
        <p>Artist: <span class="artist"><?php echo ucwords($row['artist']) ?></span></p>
        <div class="music-actions">
          <button class="play-btn" onclick="play_music({0:{id:'<?php echo $row['id'] ?>',upath:'assets/uploads/<?php echo $row['upath'] ?>'}})"><i class="fa fa-play"></i></button>
          <button class="add-playlist-btn" onclick="showAddToPlaylistModal('<?php echo $row['id'] ?>')"><i class="fa fa-plus"></i></button>
          <span class="genre">Genre: <?php echo $row['genre'] ?></span>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>
<!-- Modal Thêm vào Playlist -->
<div class="modal fade" id="addToPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="addToPlaylistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addToPlaylistModalLabel">Thêm vào Playlist</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addToPlaylistForm">
          <input type="hidden" name="music_id" id="music_id">
          <div class="form-group">
            <label for="playlist_id">Chọn playlist</label>
            <select class="form-control" name="playlist_id" id="playlist_id" required>
              <?php echo $playlist_options; ?>
            </select>
          </div>
        </form>
        <div id="addToPlaylistMsg" class="mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" onclick="submitAddToPlaylist()">Thêm</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for New Music -->
<div class="modal fade" id="newMusicModal" tabindex="-1" role="dialog" aria-labelledby="newMusicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="newMusicModalLabel">Add New Music</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="manage-music-modal" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group text-dark">
              <label for="genre_id" class="control-label">Genre</label>
              <select name="genre_id" id="genre_id" class="form-control select2 text-dark" required>
                <option value=""></option>
                <?php
                  $genres = $conn->query("SELECT * FROM genres order by genre asc");
                  while($row = $genres->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>"><?php echo ucwords($row['genre']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="title" class="control-label">Title</label>
              <input type="text" class="form-control form-control-sm" name="title" id="title" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="artist" class="control-label">Artist</label>
              <input type="text" class="form-control form-control-sm" name="artist" id="artist" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-10">
            <div class="form-group">
              <label for="description" class="control-label">Description</label>
              <textarea name="description" id="description" cols="30" rows="4" class="summernote form-control"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="audio" class="control-label">Upload Music</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="audioFile" name="audio" accept="audio/*" required>
                  <label class="custom-file-label" for="audioFile">Choose file</label>
                </div>
            </div>
          </div>
          <div class="col-md-6">
           <div class="form-group">
              <label for="cover" class="control-label">Cover Image</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="coverFile" name="cover" accept="image/*" onchange="displayImgCover(this,$(this))">
                  <label class="custom-file-label" for="coverFile">Choose file</label>
                </div>
            </div>
            <div class="form-group d-flex justify-content-center">
              <img src="" alt="" id="cover" class="img-fluid img-thumbnail" style="max-height:200px;display:none;">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top border-info">
        <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">Save</button>
        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" data-dismiss="modal">Cancel</button>
      </div>
      </form>
    </div>
  </div>
</div>
<script>
function add_to_playlist(musicId) {
  $.ajax({
    url: 'ajax.php',
    method: 'POST',
    data: {
      action: 'add_to_playlist',
      music_id: musicId
    },
    success: function(resp) {
      if(resp === 'success') {
        toastr.success('Đã thêm vào playlist!');
      } else if(resp === 'exists') {
        toastr.info('Bài hát đã có trong playlist!');
      } else {
        toastr.error('Thêm vào playlist thất bại!');
      }
    },
    error: function() {
      toastr.error('Lỗi kết nối máy chủ!');
    }
  });
}
function showAddToPlaylistModal(musicId) {
  document.getElementById('music_id').value = musicId;
  document.getElementById('addToPlaylistMsg').innerHTML = '';
  $('#addToPlaylistModal').modal('show');
}
function submitAddToPlaylist() {
  var form = $('#addToPlaylistForm');
  $.ajax({
    url: 'ajax.php',
    method: 'POST',
    data: form.serialize() + '&action=add_to_playlist',
    success: function(resp) {
      if(resp == 'success') {
        $('#addToPlaylistMsg').html('<span class="text-success">Đã thêm vào playlist!</span>');
        setTimeout(function(){ $('#addToPlaylistModal').modal('hide'); }, 1000);
      } else {
        $('#addToPlaylistMsg').html('<span class="text-danger">' + resp + '</span>');
      }
    },
    error: function() {
      $('#addToPlaylistMsg').html('<span class="text-danger">Lỗi kết nối máy chủ!</span>');
    }
  });
}
// ================== PLAYER BAR HTML ==================
// Thêm thanh player bar giống index.php, dùng append thay vì document.write để tránh trắng trang
$(function() {
  $('body').append(`
<div id="audio-player" class="music-player-bar" style="display:none; visibility:hidden; height:0; min-height:0; overflow:hidden;z-index:9999;position:fixed;bottom:0;left:0;width:100%;background:#181818;box-shadow:0 -2px 10px #0008;">
  <div class="mpb-left">
    <img id="player-cover" src="assets/uploads/default.png" alt="cover" class="mpb-cover">
    <div class="mpb-info">
      <div id="player-title" class="mpb-title">Tên bài hát</div>
      <div id="player-artist" class="mpb-artist">Nghệ sĩ</div>
    </div>
  </div>
  <div class="mpb-controls">
    <button class="btn prev-player audio-control-btn" onclick="_prev()" data-type="play" title="Bài trước"><i class="fa fa-step-backward"></i></button>
    <button class="btn p-player audio-control-btn" onclick="_player($(this))" data-queue="0" data-type="play" title="Phát / Dừng"><i class="fa fa-play"></i></button>
    <button class="btn next-player audio-control-btn" onclick="_next()" data-type="play" title="Bài tiếp"><i class="fa fa-step-forward"></i></button>
    <input type="range" id="player-volume" min="0" max="1" step="0.01" value="1" title="Âm lượng" style="width: 90px; margin-left: 10px; vertical-align: middle;">
  </div>
  <div class="mpb-center">
    <div id="waveform-playerbar" class="mpb-waveform"></div>
    <div class="mpb-progress-wrap">
      <span id="player-current-time" class="mpb-time">0:00</span>
      <input type="range" id="player-progress" min="0" max="100" value="0" step="0.1" class="mpb-progress">
      <span id="player-duration" class="mpb-time">0:00</span>
    </div>
  </div>
  <button id="player-close" class="btn btn-sm mpb-close" title="Đóng player" onclick="$('#audio-player').hide();"><i class="fa fa-times"></i></button>
</div>
  `);
});
</script>
<script src="https://unpkg.com/wavesurfer.js@7"></script>
<script src="assets/plugins/wavesurfer-playerbar.js"></script>
<script>
// ================== PLAYER LOGIC ==================
var src_arr = [];
function play_music(list, idx=0, autoPlay=1) {
  // Nếu chỉ truyền 1 bài thì chuyển thành mảng
  if(!Array.isArray(list)) {
    src_arr = Object.values(list);
  } else {
    src_arr = list;
  }
  createNewAudio(idx, autoPlay);
  $('#audio-player').css({display:'flex',visibility:'visible',height:'',minHeight:'',overflow:''});
}
function _player(_this){
  var type = _this.attr('data-type')
  if($('#mplayer source').length <= 0) return false;
  if(type == 'play'){
    _this.attr('data-type','pause')
    _this.html('<i class="fa fa-pause"></i>')
    document.getElementById('mplayer').play()
  }else{
    _this.attr('data-type','play')
    _this.html('<i class="fa fa-play"></i>')
    document.getElementById('mplayer').pause()
  }
}
function _prev(){
  if(!src_arr.length) return;
  var cur = parseInt($('#mplayer').attr('data-queue')) || 0;
  var prev = (cur - 1 + src_arr.length) % src_arr.length;
  createNewAudio(prev, true);
}
function _next(){
  if(!src_arr.length) return;
  var cur = parseInt($('#mplayer').attr('data-queue')) || 0;
  var next = (cur + 1) % src_arr.length;
  createNewAudio(next, true);
}
function createNewAudio(index, autoPlay = true) {
  if(!src_arr.length || !src_arr[index]) return;
  if(window._wavePlayerBarInstance) {
    window._wavePlayerBarInstance.destroy();
    window._wavePlayerBarInstance = null;
  }
  document.getElementById('waveform-playerbar').innerHTML = '';
  var audio = $('<audio class="bg-black d-none" id="mplayer" data-queue = "'+index+'"></audio>');
  audio.append('<source src="'+src_arr[index].upath+'">');
  var player = $('#audio-player');
  player.find('audio').remove();
  player.append(audio);
  $('#mplayer').addClass('d-none');
  if(window.initWaveSurferPlayerBar) window.initWaveSurferPlayerBar('#mplayer', '#waveform-playerbar');
  // Cập nhật info
  if(src_arr[index]) {
    $('#player-title').text(src_arr[index].title||'Tên bài hát');
    $('#player-artist').text(src_arr[index].artist||'Nghệ sĩ');
    $('#player-cover').attr('src',src_arr[index].cover||'assets/uploads/default.png');
  }
  setTimeout(function(){
    var audioEl = document.getElementById('mplayer');
    if(audioEl) {
      var v = parseFloat(document.getElementById('player-volume').value);
      if(isNaN(v) || v < 0 || v > 1) v = 1;
      audioEl.volume = v;
      audioEl.muted = (v === 0);
      if(autoPlay) {
        audioEl.play().catch(function(error){console.log('Auto play failed:', error);});
        $('.p-player').attr('data-type','pause');
        $('.p-player').html('<i class="fa fa-pause"></i>');
      } else {
        $('.p-player').attr('data-type','play');
        $('.p-player').html('<i class="fa fa-play"></i>');
      }
      m_end();
    }
  }, 10);
}
function m_end(){
  var mplayer = document.getElementById('mplayer');
  if(!mplayer) return;
  mplayer.onended = null;
  mplayer.addEventListener('ended', function onEnded(){
    var cur = parseInt(mplayer.getAttribute('data-queue')) || 0;
    var next = (cur + 1) % src_arr.length;
    createNewAudio(next, true);
  }, { once: true });
}
// Sự kiện volume
$(document).on('input change', '#player-volume', function() {
  var audio = document.getElementById('mplayer');
  if(audio) audio.volume = this.value;
});
// Nút Play All
$('#play_all').on('click', function(){
  var arr = [];
  $('#music-list .music-card').each(function(){
    var $card = $(this);
    var upath = $card.find('.play-btn').attr('onclick').match(/upath:'([^']+)'/);
    var id = $card.find('.play-btn').attr('onclick').match(/id:'([^']+)'/);
    var title = $card.find('h4').text();
    var artist = $card.find('.artist').text();
    var cover = $card.find('img').attr('src');
    if(upath && id) arr.push({id:id[1],upath:upath[1],title:title,artist:artist,cover:cover});
  });
  play_music(arr,0,1);
});
// Sửa nút play từng bài để play cả danh sách
$('#music-list').on('click','.play-btn',function(e){
  e.preventDefault();
  var arr = [];
  $('#music-list .music-card').each(function(){
    var $card = $(this);
    var upath = $card.find('.play-btn').attr('onclick').match(/upath:'([^']+)'/);
    var id = $card.find('.play-btn').attr('onclick').match(/id:'([^']+)'/);
    var title = $card.find('h4').text();
    var artist = $card.find('.artist').text();
    var cover = $card.find('img').attr('src');
    if(upath && id) arr.push({id:id[1],upath:upath[1],title:title,artist:artist,cover:cover});
  });
  var idx = $(this).closest('.music-card').index();
  play_music(arr,idx,1);
});
</script>