<?php include'db_connect.php' ?>
<style>
  .artist-item:hover{
    background: rgb(110,109,109);
    background: radial-gradient(circle, rgba(110,109,109,1) 0%, rgba(55,54,54,1) 23%, rgba(28,27,27,1) 56%);
  }
</style>
<div class="col-lg-12">
  <div class="d-flex justify-content-between align-items-center w-100">
    <div class="form-group" style="width:calc(50%) ">
      <div class="input-group">
              <input type="search" id="filter-artist" class="form-control form-control-sm" placeholder="Tìm nghệ sĩ theo tên">
              <div class="input-group-append">
                  <button type="button" id="search-artist" class="btn btn-sm btn-dark">
                      <i class="fa fa-search"></i>
                  </button>
              </div>
          </div>
    </div>
    <?php if($_SESSION['login_type'] == 1): ?>
    <button class="btn btn-sm btn-primary bg-gradient-primary" data-toggle="modal" data-target="#newArtistModal"><i class="fa fa-plus"></i> Thêm Nghệ Sĩ</button>
    <?php endif; ?>
  </div>
  <div class="music-list-grid" id="artist-list">
    <?php 
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      if($search != '') {
        $artists = $conn->query("SELECT * FROM artists WHERE name LIKE '%".$conn->real_escape_string($search)."%' order by name asc");
      } else {
        $artists = $conn->query("SELECT * FROM artists order by name asc");
      }
      while($row=$artists->fetch_assoc()):
    ?>
    <div class="music-card artist-item" data-id="<?php echo $row['id'] ?>" style="cursor:pointer;">
      <div class="music-cover d-flex align-items-center justify-content-center" style="background:#222; height:170px;">
        <img src="<?php echo $row['avatar'] ? $row['avatar'] : 'assets/uploads/default_cover.png' ?>" class="rounded-circle mx-auto" style="object-fit: cover; width: 120px; height: 120px; background:#222;" alt="Artist Avatar">
      </div>
      <div class="music-info border-top border-primary text-center" style="min-height:15vh">
        <h4 class="card-title w-100"><?php echo ucwords($row['name']) ?></h4>
        <p class="card-text truncate text-white"><small><?php echo strip_tags($row['description']) ?></small></p>
        <div class="d-flex justify-content-center align-items-center mt-2">
          <button class="btn btn-warning btn-sm toggle-popular-btn" data-id="<?php echo $row['id'] ?>" data-popular="<?php echo $row['popular'] ?? 0 ?>">
            <i class="fa fa-star<?php echo ($row['popular'] ?? 0) ? '' : '-o' ?>"></i> Popular
          </button>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>
<!-- Modal for New Artist -->
<div class="modal fade" id="newArtistModal" tabindex="-1" role="dialog" aria-labelledby="newArtistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="newArtistModalLabel">Thêm Nghệ Sĩ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="manage-artist-modal" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-group">
          <label for="artist_name" class="control-label">Tên nghệ sĩ</label>
          <input type="text" class="form-control form-control-sm" name="name" required>
        </div>
        <div class="form-group">
          <label for="description" class="control-label">Mô tả</label>
          <textarea name="description" cols="30" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group">
          <label for="avatar" class="control-label">Ảnh đại diện</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="avatarFile" name="avatar" accept="image/*">
            <label class="custom-file-label" for="avatarFile">Chọn file</label>
          </div>
          <div class="form-group d-flex justify-content-center mt-2">
            <img src="" alt="" id="avatarPreview" class="img-fluid img-thumbnail" style="max-height:120px;display:none;">
          </div>
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
<!-- Modal Edit Artist -->
<div class="modal fade" id="editArtistModal" tabindex="-1" role="dialog" aria-labelledby="editArtistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="editArtistModalLabel">Chỉnh sửa Nghệ Sĩ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit-artist-form" enctype="multipart/form-data">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_artist_id">
      <div class="modal-body">
        <div class="form-group">
          <label for="edit_artist_name" class="control-label">Tên nghệ sĩ</label>
          <input type="text" class="form-control form-control-sm" name="name" id="edit_artist_name" required>
        </div>
        <div class="form-group">
          <label for="edit_description" class="control-label">Mô tả</label>
          <textarea name="description" id="edit_description" cols="30" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group">
          <label for="edit_avatar" class="control-label">Ảnh đại diện</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="edit_avatar" name="avatar" accept="image/*">
            <label class="custom-file-label" for="edit_avatar">Chọn file</label>
          </div>
          <div class="form-group d-flex justify-content-center mt-2">
            <img src="" alt="" id="edit_avatarPreview" class="img-fluid img-thumbnail" style="max-height:120px;display:none;">
          </div>
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
<!-- Section hiển thị chi tiết nghệ sĩ -->
<div id="artist-detail-section" style="display:none;"></div>
<script>
$(document).ready(function(){
  // Tìm kiếm nghệ sĩ
  $('#search-artist').click(function(){ _filterArtist(); });
  $('#filter-artist').on('keypress',function(e){ if(e.which ==13){ _filterArtist(); return false; } });
  $('#filter-artist').on('search', function () { _filterArtist(); });
  function _filterArtist(){
    var _ftxt = $('#filter-artist').val().toLowerCase();
    $('.artist-item').each(function(){
      var _content = $(this).text().toLowerCase();
      $(this).toggle(_content.includes(_ftxt));
    });
    checkArtistList();
  }
  function checkArtistList(){
    var count = $('.artist-item:visible').length;
    if(count > 0){ if($('#ns-artist').length > 0) $('#ns-artist').remove(); }
    else {
      var ns = $('<div class="col-md-12 text-center text-white" id="ns-artist"><b><i>Không có nghệ sĩ nào.</i></b></div>');
      $('#artist-list').append(ns);
    }
  }
  // Xem trước ảnh đại diện
  $(document).on('change', '#avatarFile', function (event) {
    var inputFile = event.currentTarget;
    if(inputFile.files && inputFile.files[0]){
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#avatarPreview').attr('src', e.target.result).show();
      }
      reader.readAsDataURL(inputFile.files[0]);
    }
    $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
  });
  // Submit thêm nghệ sĩ
  $('#manage-artist-modal').submit(function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url:'artist_actions.php',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      method: 'POST',
      success:function(resp){
        if(resp.success){
          $('#newArtistModal').modal('hide');
          setTimeout(function(){
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            alert('Thêm nghệ sĩ thành công!');
            location.reload();
          }, 400);
        } else {
          alert(resp.error || 'Lỗi khi thêm nghệ sĩ!');
        }
      }
    });
  });
  // Xem trước ảnh đại diện khi chọn file mới
  $(document).on('change', '#edit_avatar', function (event) {
    var inputFile = event.currentTarget;
    if(inputFile.files && inputFile.files[0]){
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#edit_avatarPreview').attr('src', e.target.result).show();
      }
      reader.readAsDataURL(inputFile.files[0]);
    }
    $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
  });
  // Submit form chỉnh sửa nghệ sĩ
  $('#edit-artist-form').submit(function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'edit');
    $.ajax({
      url:'artist_actions.php',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      method: 'POST',
      success:function(resp){
        if(resp.success){
          $('#editArtistModal').modal('hide');
          alert('Cập nhật nghệ sĩ thành công!');
          setTimeout(function(){ location.reload(); }, 1200);
        } else {
          alert(resp.error || 'Lỗi khi cập nhật nghệ sĩ!');
        }
      }
    });
  });
  // Play nhạc của nghệ sĩ
  $('.play-artist-music').click(function(){
    var artist_id = $(this).data('id');
    $.get('ajax.php?action=get_artist_music&id='+artist_id, function(data){
      if(data && data.length > 0){
        var _src = {};
        for(var i=0;i<data.length;i++){
          _src[i] = {id:data[i].id, upath:'assets/uploads/'+data[i].upath};
        }
        play_music(_src);
      } else {
        alert('Nghệ sĩ này chưa có bài hát nào!');
      }
    },'json');
  });

  // Toggle popular artist
  $(document).on('click', '.toggle-popular-btn', function(e){
    e.stopPropagation();
    var btn = $(this);
    var artist_id = btn.data('id');
    var current = btn.data('popular') == 1 ? 1 : 0;
    var new_popular = current ? 0 : 1;
    $.post('artist_actions.php', {action: 'toggle_popular', artist_id: artist_id, popular: new_popular}, function(resp){
      if(resp.success){
        btn.data('popular', new_popular);
        btn.find('i').toggleClass('fa-star fa-star-o');
        // Reload trang chủ nếu có, hoặc reload toàn bộ nếu đang ở trang home
        if(window.location.pathname.indexOf('artists.php') !== -1){
          setTimeout(function(){ window.location.reload(); }, 500);
        } else if(window.location.pathname.indexOf('home.php') !== -1){
          setTimeout(function(){ window.location.reload(); }, 500);
        }
      } else {
        alert(resp.error || 'Lỗi khi cập nhật trạng thái popular!');
      }
    }, 'json');
  });
  // Click vào thẻ nghệ sĩ để xem chi tiết (show section, không chuyển trang)
  $(document).on('click', '.artist-item', function(e){
    if($(e.target).closest('.play-artist-music').length) return;
    var id = $(this).data('id');
    if(id) {
      $.get('view_artist.php?id='+id, function(html){
        $('#artist-list').hide();
        $('#artist-detail-section').html(html).show();
        // Thêm nút quay lại
        if($('#back-to-artist-list').length === 0) {
          $('#artist-detail-section').prepend('<button id="back-to-artist-list" class="btn btn-warning mb-3">&larr; Quay lại danh sách</button>');
        }
      });
    }
  });
  // Nút quay lại danh sách nghệ sĩ
  $(document).on('click', '#back-to-artist-list', function(){
    $('#artist-detail-section').hide().html('');
    $('#artist-list').show();
  });
  // Patch dashboard2.js lỗi getContext nếu không có canvas
  if (typeof document !== 'undefined') {
    var allCanvas = document.querySelectorAll('canvas');
    allCanvas.forEach(function(canvas) {
      if (canvas && typeof canvas.getContext === 'function') {
        // Chỉ chạy code dashboard2.js nếu có canvas hợp lệ
      }
    });
  }
  // Sửa các thẻ a có href='javascript:void(0)' thành href='#'
  $("a[href='javascript:void(0)']").attr('href', '#');
});
</script>