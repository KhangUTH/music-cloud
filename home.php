<?php include('db_connect.php') ?>

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

      <!-- Latest Songs Section (Responsive) -->
      <div class="music-list-grid" id="latest-songs-list">
        <?php 
        $latest_songs = $conn->query("SELECT u.*, g.genre FROM uploads u INNER JOIN genres g ON g.id = u.genre_id ORDER BY u.id DESC LIMIT 5");
        $js_song_arr = [];
        $js_index = 0;
        while($row = $latest_songs->fetch_assoc()):
          $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
          unset($trans['\"'], $trans['<'], $trans['>'], $trans['<h2']);
          $desc = strtr(html_entity_decode($row['description']),$trans);
          $desc = str_replace(array("<li>","</li>"), array("",", "), $desc);
          $js_song_arr[] = [
            'id' => $row['id'],
            'upath' => 'assets/uploads/'.$row['upath'],
            'title' => $row['title'],
            'artist' => $row['artist'],
            'cover' => 'assets/uploads/'.$row['cover_image']
          ];
        ?>
        <div class="music-card" data-id="<?php echo $row['id'] ?>" data-upath="<?php echo $row['upath'] ?>" data-index="<?php echo $js_index ?>">
          <a href="index.php?page=view_music&id=<?php echo $row['id'] ?>">
            <img src="assets/uploads/<?php echo $row['cover_image'] ?>" class="music-cover" alt="music Cover" onerror="this.onerror=null;this.src='assets/default_cover.png';">
          </a>
          <div class="music-info">
            <h4><?php echo ucwords($row['title']) ?></h4>
            <p>Artist: <span class="artist"><?php echo ucwords($row['artist']) ?></span></p>
            <div class="music-actions">
              <button class="play-btn" onclick="play_music({0:{id:'<?php echo $row['id'] ?>',upath:'assets/uploads/<?php echo $row['upath'] ?>',title:'<?php echo htmlspecialchars(addslashes($row['title'])) ?>',artist:'<?php echo htmlspecialchars(addslashes($row['artist'])) ?>',cover:'assets/uploads/<?php echo $row['cover_image'] ?>'}})" title="Play"><i class="fa fa-play"></i></button>
              <button class="add-playlist-btn" onclick="showAddToPlaylistModal('<?php echo $row['id'] ?>')"><i class="fa fa-plus"></i></button>
              <span class="genre">Genre: <?php echo $row['genre'] ?></span>
            </div>
          </div>
        </div>
        <?php $js_index++; endwhile; ?>
      </div>
      <script>
      // Danh sách bài hát dạng array giống playlist
      var latestSongsArr = <?php echo json_encode(array_values($js_song_arr)); ?>;
      $(document).ready(function(){
        $('#latest-songs-list').on('click', '.play-btn', function(e){
          e.preventDefault();
          var idx = $(this).closest('.music-card').index();
          play_music(latestSongsArr, idx, 1);
        });
      });
      </script>

      <!-- Artists Section -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-outline bg-black" style="border-color: #fd7e14;">
            <div class="card-header">
              <h3 class="card-title">Popular Artists</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <?php 
                $artists = $conn->query("SELECT a.id, a.name, a.avatar, COUNT(u.id) as song_count FROM artists a LEFT JOIN uploads u ON a.id = u.artist_id WHERE a.popular = 1 GROUP BY a.id ORDER BY song_count DESC LIMIT 5");
                while($row = $artists->fetch_assoc()):
                  $avatar = !empty($row['avatar']) && file_exists($row['avatar']) ? $row['avatar'] : 'assets/uploads/default_cover.png';
                ?>
                <div class="col-md-3">
                  <div class="card bg-dark">
                    <div class="card-body text-center">
                      <img src="<?php echo $avatar ?>" class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;border:3px solid #fd7e14;" alt="Artist Avatar" onerror="this.onerror=null;this.src='assets/uploads/default_cover.png';">
                      <h5 class="card-title text-white"><?php echo htmlspecialchars($row['name']) ?></h5>
                      <p class="card-text text-white"><?php echo $row['song_count'] ?> songs</p>
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
              </div>
            </div>
          </div>
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
      <script>
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
      </script>

