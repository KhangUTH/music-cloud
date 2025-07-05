<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-lg navbar-dark bg-dark" style="border-color: #fd7e14;">
    <div class="container-fluid px-2">
      <a class="navbar-brand d-flex align-items-center" style="color: #fd7e14;" href="./"><b>Music Cloud</b></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav mr-auto align-items-lg-center d-lg-block d-none">
          <?php if(isset($_SESSION['login_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" style="color: #fd7e14;" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
          </li>
          <?php endif; ?>
        </ul>
        <!-- Mobile menu dropdown -->
        <ul class="navbar-nav d-lg-none w-100">
          <li class="nav-item"><a class="nav-link" href="index.php" style="color: #fd7e14;">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=music_list" style="color: #fd7e14;">Music List</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=playlist" style="color: #fd7e14;">Playlists</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=artists" style="color: #fd7e14;">Artists</a></li>
          <?php if(isset($_SESSION['login_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?page=user_list" style="color: #fd7e14;">Users</a></li>
          <?php endif; ?>
        </ul>
        <form class="form-inline my-2 my-lg-0 w-100 justify-content-center" style="max-width:600px;">
          <div class="input-group w-100">
            <input type="search" id="filter" class="form-control form-control-sm" placeholder="Search music using keyword" style="height:40px;font-size:1.1rem;">
            <div class="input-group-append">
              <button type="button" id="search" class="btn btn-sm btn-dark" style="height:40px;" tabindex="-1">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>
        </form>
        <ul class="navbar-nav ml-auto align-items-lg-center mt-2 mt-lg-0">

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
              <span>
                <div class="d-flex badge-pill align-items-center bg-gradient-primary p-1" style="background: #337cca47 linear-gradient(180deg,#268fff17,#007bff66) repeat-x!important;border:50px">
                  <?php if(isset($_SESSION['login_profile_pic']) && !empty($_SESSION['login_profile_pic'])): ?>
                    <div class="rounded-circle mr-1" style="width: 25px;height: 25px;top:-5px;left: -40px">
                      <img src="assets/uploads/<?php echo $_SESSION['login_profile_pic'] ?>" class="image-fluid image-thumbnail rounded-circle" alt="" style="max-width: calc(100%);height: calc(100%);">
                    </div>
                  <?php else: ?>
                  <span class="fa fa-user mr-2" ></span>
                  <?php endif; ?>
                  <span ><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
                  <span class="fa fa-angle-down ml-2"></span>
                </div>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="account_settings" style="left: auto; right: 0;">
              <button class="dropdown-item" type="button" id="my_profile"><i class="fa fa-id-card"></i> Profile</button>
              <button class="dropdown-item" onclick="location.href='ajax.php?action=logout'"><i class="fa fa-power-off"></i> Logout</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <style>
  @media (max-width: 991.98px) {
    .navbar-brand {
      font-size: 1.1rem;
      padding-left: 0.5rem;
    }
    .navbar-nav .nav-link, .navbar-nav .dropdown-toggle {
      padding: 0.5rem 1rem;
      font-size: 1rem;
    }
    .form-inline {
      width: 100% !important;
      margin: 0.5rem 0;
    }
    .input-group {
      width: 100%;
    }
  }
  </style>
  <script>
  $('#my_profile').click(function(){
    uni_modal("Profile","view_user.php?id=<?php echo $_SESSION['login_id'] ?>")
  })
  $('#search').on('click', function() {
    var keyword = $('#filter').val().trim();
    if(keyword.length > 0) {
      window.location.href = 'index.php?page=music_list&search=' + encodeURIComponent(keyword);
    }
  });
  $('#filter').on('keypress', function(e) {
    if(e.which == 13) {
      var keyword = $('#filter').val().trim();
      if(keyword.length > 0) {
        window.location.href = 'index.php?page=music_list&search=' + encodeURIComponent(keyword);
      }
      return false;
    }
  });
  // Fix Bootstrap dropdown for account menu
  $(function(){
    $('.dropdown-toggle').dropdown();
  });
  </script>
  <!-- /.navbar -->
