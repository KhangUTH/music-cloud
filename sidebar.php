<aside class="main-sidebar sidebar-dark-navy bg-black elevation-4">
    <div class="dropdown">
     <a href="/index.php?page=home" class="brand-link bg-black">
        <span class="brand-image img-circle elevation-3 d-flex justify-content-center align-items-center text-white font-weight-500" style="width: 38px;height:50px;font-size: 2rem"><b><i class="fa fa-headphones-alt" style="color: #fd7e14;"></i></b></span>
        <span class="brand-text font-weight-light" style="color: #fd7e14;"><i>Music Cloud</i></span>
      </a>
      <div class="dropdown-menu" style="">
        <a class="dropdown-item d-flex justify-content-between align-items-center manage_account" href="javascript:void(0)" data-id="<?php echo $_SESSION['login_id'] ?>">
          <span><i class="fa fa-id-card"></i> Profile</span>
          <span class="edit_profile" data-id="<?php echo $_SESSION['login_id'] ?>" style="cursor:pointer;"><i class="fa fa-cog"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="ajax.php?action=logout">Logout</a>
      </div>
    </div>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="/index.php?page=home" class="nav-link nav-home">
              <i class="nav-icon fas fa-home" style="color: #fd7e14;"></i>
              <p>
                Home
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/index.php?page=music_list" class="nav-link nav-music_list">
              <i class="fa fa-music nav-icon" style="color: #fd7e14;"></i>
              <p>Musics</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/index.php?page=artists" class="nav-link nav-artists">
              <i class="fas fa-user nav-icon" style="color: #fd7e14;"></i>
              <p>Artists</p>
            </a>
          </li>
          <li class="nav-item">
                <a href="/index.php?page=playlist" class="nav-link nav-playlist tree-item">
                  <i class="fas fa-list nav-icon" style="color: #fd7e14;"></i>
                  <p>Playlist</p>
                </a>
          </li> 
          <li class="nav-item">
                <a href="/index.php?page=genre_list" class="nav-link nav-genre_list tree-item">
                  <i class="fas fa-th-list nav-icon" style="color: #fd7e14;"></i>
                  <p>Genre</p>
                </a>
          </li>  
          <?php if($_SESSION['login_type'] == 1): ?>
          <li class="nav-item">
                <a href="/index.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-users nav-icon" style="color: #fd7e14;"></i>
                  <p>Users</p>
                </a>
          </li> 
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
    $(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      if($('.nav-link.nav-'+page).length > 0){
        $('.nav-link.nav-'+page).addClass('active')
          console.log($('.nav-link.nav-'+page).hasClass('tree-item'))
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
          $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

      }
      $('.manage_account').click(function(e){
        // Nếu click vào icon bánh răng thì không mở modal xem profile
        if($(e.target).closest('.edit_profile').length > 0) return;
        uni_modal('Profile','view_user.php?id='+$(this).attr('data-id'))
      })
      $('.edit_profile').click(function(e){
        e.stopPropagation();
        uni_modal('Chỉnh sửa Profile','manage_account.php','large')
      })
    })
  </script>