<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
  if(!isset($_SESSION['login_id']))
      header('location:login.php');


  include 'header.php' 
?>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <?php include 'topbar.php' ?>
  <?php include 'sidebar.php' ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper  bg-dark">
     <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body text-white">
      </div>
    </div>
    <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
     
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <?php $page = isset($_GET['page']) ? $_GET['page']:'home' ?>
      <div class="container-fluid text-dark viewer-panel" style="margin-bottom: 4rem">
         <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <?php
            $title = isset($_GET['page']) ? ucwords(str_replace("_", ' ', $_GET['page'])) : "Home";
             ?>
            <h1 class="m-0 text-light"><?php echo $title ?></h1>
          </div><!-- /.col -->

        </div><!-- /.row -->
            <hr class="border-primary">
      </div><!-- /.container-fluid -->
         <?php 
            if(!file_exists($page.".php")){
                include '404.html';
            }else{
            include $page.'.php';

            }
          ?>
          
        
      </div><!--/. container-fluid -->
      <style>
      /* Music Player Bar Modern UI */
      .music-player-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: fixed;
        left: 0; right: 0; bottom: 0;
        z-index: 9999;
        background: #181a1b;
        border-top: 1px solid #232526;
        min-height: 70px;
        padding: 0 1.5rem;
        gap: 1.2rem;
        box-shadow: 0 -2px 16px 0 rgba(0,0,0,0.18);
      }
      .mpb-left {
        display: flex;
        align-items: center;
        min-width: 180px;
        max-width: 320px;
        gap: 0.7rem;
      }
      .mpb-cover {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
      }
      .mpb-info {
        max-width: 180px;
        overflow: hidden;
      }
      .mpb-title {
        color: #fff;
        font-weight: bold;
        font-size: 1.08rem;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .mpb-artist {
        color: #b0b0b0;
        font-size: 0.97rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .mpb-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 120px;
      }
      .audio-control-btn {
        color: #b0b0b0;
        background: transparent;
        border: none;
        font-size: 1.35rem;
        transition: color 0.15s;
      }
      .audio-control-btn:hover {
        color: #fff;
      }
      .mpb-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1 1 0%;
        min-width: 220px;
        max-width: 700px;
        margin: 0 1.2rem;
      }
      .mpb-waveform {
        width: 100%;
        height: 48px;
        background: #232526;
        border-radius: 8px;
        margin-bottom: 2px;
      }
      .mpb-progress-wrap {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 0.5rem;
        height: 28px;
      }
      .mpb-progress {
        flex: 1 1 0%;
        margin: 0 8px;
        accent-color: #007bff;
        height: 4px;
        border-radius: 2px;
        background: #333;
      }
      .mpb-time {
        color: #b0b0b0;
        font-size: 0.98rem;
        min-width: 44px;
        text-align: center;
      }
      .mpb-close {
        color: #b0b0b0;
        background: transparent;
        border: none;
        font-size: 1.5rem;
        margin-left: 0.7rem;
        transition: color 0.15s;
      }
      .mpb-close:hover {
        color: #ff4d4f;
      }
      @media (max-width: 700px) {
        .music-player-bar {
          flex-direction: column;
          align-items: stretch;
          min-height: 110px;
          padding: 0.5rem 0.5rem 0.3rem 0.5rem;
          gap: 0.5rem;
        }
        .mpb-left, .mpb-controls, .mpb-center {
          margin-bottom: 2px;
        }
        .mpb-center {
          margin: 0;
        }
      }
      </style>
      <div id="audio-player" class="music-player-bar" style="display:none; visibility:hidden; height:0; min-height:0; overflow:hidden;">
        <div class="mpb-left">
          <img id="player-cover" src="assets/uploads/default.png" alt="cover" class="mpb-cover">
          <div class="mpb-info">
            <div id="player-title" class="mpb-title">Tên bài hát</div>
            <div id="player-artist" class="mpb-artist">Nghệ sĩ</div>
          </div>
        </div>
        <div class="mpb-controls">
          <button class="btn prev-player audio-control-btn" onclick="_prev($(this))" data-type="play" title="Bài trước"><i class="fa fa-step-backward"></i></button>
          <button class="btn p-player audio-control-btn" onclick="_player($(this))" data-queue="0" data-type="play" title="Phát / Dừng"><i class="fa fa-play"></i></button>
          <button class="btn next-player audio-control-btn" onclick="_next(-1,1)" data-type="play" title="Bài tiếp"><i class="fa fa-step-forward"></i></button>
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
        <button id="player-close" class="btn btn-sm mpb-close" title="Đóng player"><i class="fa fa-times"></i></button>
      </div>
    <script src="https://unpkg.com/wavesurfer.js@7"></script>
    <script src="assets/plugins/wavesurfer-playerbar.js"></script>
    <script>
    // Update player info (cover, title, artist) when song changes
    function updatePlayerInfo(resp) {
      if(resp) {
        var cover = (resp.cover_image && resp.cover_image.trim() && resp.cover_image !== 'null' && resp.cover_image !== 'undefined') ? resp.cover_image : 'default.jpg';
        document.getElementById('player-cover').src = 'assets/uploads/' + cover;
        document.getElementById('player-title').textContent = resp.title || '';
        document.getElementById('player-artist').textContent = resp.artist || '';
      }
    }
    // Patch get_details to update player info
    function get_details($id){
      $.ajax({
        url:"ajax.php?action=get_details",
        method:'POST',
        data:{id:$id},
        success:function(resp){
          if(resp){
            resp = JSON.parse(resp)
            updatePlayerInfo(resp);
          }
        }
      })
    }
    // Init waveform for player bar
    $(document).ready(function(){
      setTimeout(function(){
        if(window.initWaveSurferPlayerBar) window.initWaveSurferPlayerBar('#mplayer', '#waveform-playerbar');
        // Ẩn audio mặc định, chỉ show sóng nhạc và custom control
        $('#mplayer').addClass('d-none');
        // Volume control
        $(document).on('input change', '#player-volume', function() {
          var audio = document.getElementById('mplayer');
          if(audio) audio.volume = this.value;
        });
        // Set initial volume if audio exists
        var audio = document.getElementById('mplayer');
        if(audio) audio.volume = $('#player-volume').val();
      }, 500);
    });
    // Fix player bar alignment
    $(function(){
      $('#audio-player').removeClass('justify-content-end').addClass('justify-content-start');
      $('#audio-player').css({
        'gap':'1.2rem',
        'padding-left':'2.5rem',
        'padding-right':'2.5rem',
        'min-height':'70px',
        'align-items':'center',
        'position':'fixed',
        'bottom':'0',
        'left':'0',
        'right':'0',
        'z-index':'9999',
        'background':'#181a1b',
        'border-top':'1px solid #222',
      });
      $('#waveform-playerbar').css({
        'margin':'0 1.5rem',
        'background':'#232526',
        'border-radius':'8px',
      });
    });
    // Đảm bảo player bar không bị che sóng nhạc bởi các thành phần khác
    $('#waveform-playerbar').css({
      'z-index': '10001',
      'position': 'relative',
      'overflow': 'visible',
      'background': 'transparent',
      'min-height': '48px',
      'height': '48px',
      'box-shadow': 'none',
      'border': 'none',
      'padding': 0
    });
    // Xóa mọi background hoặc border không cần thiết của các thành phần cha
    $('#audio-player').css({
      'background': '#181a1b',
      'box-shadow': 'none',
      'border': 'none',
    });
    </script>
    <script>
    // Đóng player khi bấm nút X
    $(document).on('click', '#player-close', function() {
      // Ẩn player hoàn toàn
      $('#audio-player').css({
        'display': 'none',
        'visibility': 'hidden',
        'height': '0',
        'min-height': '0',
        'overflow': 'hidden'
      });
      // Ngắt nhạc nếu đang phát
      if(document.getElementById('mplayer')) {
        document.getElementById('mplayer').pause();
        document.getElementById('mplayer').currentTime = 0;
      }
    });
    </script>
    </section>
    <script>
      function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return "";
      }
var src_arr = [];
      $(document).ready(function(){
        // Luôn ẩn player khi load trang, chỉ hiện khi play bài hát
        $('#audio-player').css('display', 'none');
        // Không tự động play lại bài trước khi reload trang
        // Nếu muốn tự động play lại, hãy bỏ comment đoạn dưới
        // if(getCookie('src') != ''){
        //   var parsed = JSON.parse(getCookie('src'))
        //   var q = getCookie('pq') != '' ? getCookie('pq') : '';
        //   play_music(parsed,q,0)
        // }
      })
      function _player(_this){
        var type = _this.attr('data-type')
        if($('#mplayer source').length <= 0)
          return false;
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

      function play_music($src,$i=0,$p = 1){
        // Hiện player khi bắt đầu play bài hát
        $('#audio-player').css({
          'display': 'flex',
          'visibility': 'visible',
          'height': '',
          'min-height': '',
          'overflow': ''
        });
        // Xóa audio cũ trước khi tạo mới để tránh chồng lặp
        $('#mplayer').remove();
        var audio = $('<audio class="bg-black d-none" id="mplayer" data-queue = "'+$i+'"></audio>');
        if(typeof $src === 'object'){
            // Nếu là object (có thể là array hoặc object), ép về array
            if(Array.isArray($src)) {
                src_arr = $src;
            } else {
                // Nếu là object dạng {0: {...}, 1: {...}}, chuyển thành array
                src_arr = Object.values($src);
            }
            $src = src_arr[$i].upath;
        }
        document.cookie = "src="+JSON.stringify(src_arr);
        document.cookie = "pq="+$i;
        var csrc = $('#mplayer source').attr("src");
        if($src != csrc){
            // Xóa sóng nhạc cũ nếu có
            if(window._wavePlayerBarInstance) {
                window._wavePlayerBarInstance.destroy();
                window._wavePlayerBarInstance = null;
            }
            document.getElementById('waveform-playerbar').innerHTML = '';
            audio.append('<source src="'+$src+'">');
            var player=  $('#audio-player');
            player.append(audio);
            // Set volume ngay khi audio đã vào DOM, đảm bảo đúng element
            setTimeout(function(){
              var audioEl = document.getElementById('mplayer');
              if(audioEl) {
                var v = parseFloat(document.getElementById('player-volume').value);
                if(isNaN(v) || v < 0 || v > 1) v = 1;
                audioEl.volume = v;
                audioEl.muted = (v === 0);
                // Gắn lại sự kiện volume mỗi lần tạo audio mới
                $('#player-volume').off('.playerVol').on('input.playerVol change.playerVol', function() {
                  var vv = parseFloat(this.value);
                  if(isNaN(vv) || vv < 0 || vv > 1) vv = 1;
                  audioEl.volume = vv;
                  audioEl.muted = (vv === 0);
                });
              }
            }, 10);
            // Luôn ẩn audio mặc định để không hiện player trắng
            $('#mplayer').addClass('d-none');
            // Khởi tạo lại sóng nhạc cho audio mới
            if(window.initWaveSurferPlayerBar) window.initWaveSurferPlayerBar('#mplayer', '#waveform-playerbar');
            // Cập nhật thông tin player (cover, title, artist)
            get_details(src_arr[$i].id);
        }else{
            if(!document.getElementById('mplayer').paused == true){
                document.getElementById('mplayer').pause();
                return false;
            }
        }
        if($p == 1){
            document.getElementById('mplayer').play();
            $('.p-player').attr('data-type','pause');
            $('.p-player').html('<i class="fa fa-pause"></i>');
        } else {
            $('.p-player').attr('data-type','play');
            $('.p-player').html('<i class="fa fa-play"></i>');
        }
        m_end();
      }
      function _prev($i=-1){
        if(!src_arr.length) return;
        var cur = parseInt($('#mplayer').attr('data-queue')) || 0;
        var prev = (cur - 1 + src_arr.length) % src_arr.length;
        createNewAudio(prev, true);
      }
      function _next($i=-1,$p=1){
        if(!src_arr.length) return;
        var cur = parseInt($('#mplayer').attr('data-queue')) || 0;
        var next = (cur + 1) % src_arr.length;
        createNewAudio(next, true);
      }
      // Hàm helper tạo audio mới cho _next/_prev và auto-next
      function createNewAudio(index, autoPlay = true) {
        if(!src_arr.length || !src_arr[index]) return;
        if(window._wavePlayerBarInstance) {
            window._wavePlayerBarInstance.destroy();
            window._wavePlayerBarInstance = null;
        }
        document.getElementById('waveform-playerbar').innerHTML = '';
        var audio = $('<audio class="bg-black d-none" id="mplayer" data-queue = "'+index+'"></audio>');
        document.cookie = "pq="+index;
        audio.append('<source src="'+src_arr[index].upath+'">');
        var player = $('#audio-player');
        player.find('audio').remove();
        player.append(audio);
        $('#mplayer').addClass('d-none');
        if(window.initWaveSurferPlayerBar) window.initWaveSurferPlayerBar('#mplayer', '#waveform-playerbar');
        if(src_arr[index] && src_arr[index].id) {
          get_details(src_arr[index].id);
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
            // Đảm bảo luôn gắn lại sự kiện ended khi tạo audio mới
            m_end();
          }
        }, 10);
      }
      function m_end(){
        var mplayer = document.getElementById('mplayer');
        if(!mplayer) return;
        // Xóa event cũ để tránh lặp nhiều lần khi chuyển bài liên tục
        mplayer.onended = null;
        mplayer.addEventListener('ended', function onEnded(){
          var cur = parseInt(mplayer.getAttribute('data-queue')) || 0;
          var next = (cur + 1) % src_arr.length;
          // Chuyển bài như SoundCloud: luôn lặp lại playlist, chuyển mượt
          createNewAudio(next, true);
        }, { once: true });
      }
      window.addEventListener('popstate', function(e){
         var nl = new URLSearchParams(window.location.search);
            var page =nl.get('page')
        $.ajax({
          url:"controller.php"+window.location.search,
          success:function(resp){
           $('.viewer-panel').html(resp)
          }
        })
      });
      // Đã loại bỏ logic chèn lại #pdet, chỉ cập nhật info qua updatePlayerInfo
      _anchor()
      function _anchor(){
      $('a').click(function(e){
        e.preventDefault()
        var _h=  $(this).attr("href");
        if(document.href == _h){
          return false
        }
        window.history.pushState({}, null, $(this).attr("href"));
        var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
          var nl = new URLSearchParams(window.location.search);
          var page =nl.get('page')
          $('.nav-link').removeClass('active')
          if($('.nav-link.nav-'+page).length > 0){
            $('.nav-link.nav-'+page).addClass('active')
            if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
              $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
              $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
            }
            if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
              $('.nav-link.nav-'+page).parent().addClass('menu-open')
            }

          }
        $.ajax({
          url:"controller.php"+window.location.search,
          success:function(resp){
           $('.viewer-panel').html(resp)
           _anchor()
          }
        })
      })
      }
      function _redirect($url){
          window.history.pushState({}, null, $url);
          var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
            var nl = new URLSearchParams(window.location.search);
            var page =nl.get('page')
            $('.nav-link').removeClass('active')
            if($('.nav-link.nav-'+page).length > 0){
              $('.nav-link.nav-'+page).addClass('active')
              if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
                $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
                $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
              }
              if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
                $('.nav-link.nav-'+page).parent().addClass('menu-open')
              }

            }
          $.ajax({
            url:"controller.php"+window.location.search,
            success:function(resp){
             $('.viewer-panel').html(resp)
             _anchor()
            }
          })
      }
    </script>
    <!-- /.content -->
    <div class="text-dark">
    <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
  </div>
  <!-- /.content-wrapper -->
</div>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer bg-black">
    <strong>Copyright &copy; 2025 AE72.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Music Cloud - Music Stream.</b>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- Bootstrap -->
<?php include 'footer.php' ?>
<script>

</script>
</body>
</html>
