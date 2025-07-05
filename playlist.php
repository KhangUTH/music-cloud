<?php include'db_connect.php' ?>
<style>
	.playlist-item:hover{
		background: rgb(110,109,109);
		background: radial-gradient(circle, rgba(110,109,109,1) 0%, rgba(55,54,54,1) 23%, rgba(28,27,27,1) 56%);
	}
</style>
<div class="col-lg-12">
	<div class="d-flex justify-content-between align-items-center w-100 mb-3">
		<div class="form-group" style="width:calc(50%) ">
			<div class="input-group">
			  <input type="search" id="filter" class="form-control form-control-sm" placeholder="Search playlist using keyword">
			  <div class="input-group-append">
				  <button type="button" id="search" class="btn btn-sm btn-dark">
					  <i class="fa fa-search"></i>
				  </button>
			  </div>
		  </div>
		</div>
		<button class="btn btn-primary" id="add-playlist-btn"><i class="fa fa-plus"></i> Thêm Playlist</button>
	</div>
  <div class="music-list-grid" id="playlist-list">
		<?php 
			$uid = $_SESSION['login_id'];
			$playlist= $conn->query("SELECT * FROM playlist WHERE user_id = $uid order by title asc");
			while($row=$playlist->fetch_assoc()):
		?>
	<div class="music-card playlist-item" data-id="<?php echo $row['id'] ?>">
	  <div class="music-cover position-relative" style="height:170px;">
		<?php if($_SESSION['login_type'] == 1 || $_SESSION['login_id'] == $row['user_id'] ): ?>
		  <div class="dropdown position-absolute" style="right:.5em;top:.5em;z-index:2;">
			<button type="button" class="btn btn-tool py-1" data-toggle="dropdown" title="Manage" style="background: #000000ab;">
			  <i class="fa fa-ellipsis-v"></i>
			</button>
			<div class="dropdown-menu bg-dark">
			  <button class="dropdown-item manage_playlist bg-dark" data-id="<?php echo $row['id'] ?>" type="button">Manage List</button>
			  <button class="dropdown-item edit_playlist bg-dark" data-id="<?php echo $row['id'] ?>" type="button">Edit</button>
			  <button class="dropdown-item delete_playlist bg-dark" data-id="<?php echo $row['id'] ?>" type="button">Delete</button>
			</div>
		  </div>
		<?php endif; ?>
		<a href="index.php?page=view_playlist&id=<?php echo $row['id'] ?>"> 
		  <img src="assets/uploads/<?php echo $row['cover_image'] ?>" class="music-cover" alt="playlist Cover" onerror="this.onerror=null;this.src='assets/default_cover.png';">
		</a>
	  </div>
	  <div class="music-info" style="min-height: 12vh;">
		<h4 class="card-title"><?php echo ucwords($row['title']) ?></h4>
		<p class="card-text truncate text-white"><?php echo $row['description'] ?></p>
	  </div>
	</div>
		<?php endwhile; ?>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('.delete_playlist').click(function(){
	_conf("Are you sure to delete this Playlist?","delete_playlist",[$(this).attr('data-id')])
	})
	$('#manage_playlist').click(function(e){
		e.preventDefault()
		uni_modal("New Playlist",'manage_playlist.php')
	})
	$('.edit_playlist').click(function(e){
		e.preventDefault()
		uni_modal("Edit Playlist",'manage_playlist.php?id='+$(this).attr('data-id'))
	})
	$('.manage_playlist').click(function(e){
		e.preventDefault()
		uni_modal("Mange Playlist Music",'manage_playlist_items.php?pid='+$(this).attr('data-id'))
	})
	$('#add-playlist-btn').click(function(){
	uni_modal("Tạo Playlist mới",'manage_playlist.php');
});
	})
	check_list()
	function delete_playlist($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_playlist',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					$('.modal').modal('hide')
					_redirect(document.href)
					end_load()

				}
			}
		})
	}
	function _filter(){
		var _ftxt = $('#filter').val().toLowerCase()
		$('.playlist-item').each(function(){
			var _content = $(this).text().toLowerCase()
			if(_content.includes(_ftxt) == true){
				$(this).toggle(true)
			}else{
				$(this).toggle(false)
			}
		})
		check_list()
	}
	function check_list(){
		var count = $('.playlist-item:visible').length
		if(count > 0){
			if($('#ns').length > 0)
				$('#ns').remove()
		}else{
			var ns = $('<div class="col-md-12 text-center text-white" id="ns"><b><i>No data to be display.</i></b></b></div>')
			$('#playlist-list').append(ns)
		}
	}
	$('#search').click(function(){
		_filter()
	  })
	  $('#filter').on('keypress',function(e){
		if(e.which ==13){
		_filter()
		 return false; 
		}
	  })
	  $('#filter').on('search', function () {
		  _filter()
	  })
</script>