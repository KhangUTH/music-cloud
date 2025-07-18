<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
	$type_arr = array('',"Admin","Subscriber");
	$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
}
?>
<div class="container-fluid">
	<div class="card card-widget widget-user shadow">
	  <div class="widget-user-header bg-dark">
		<h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
		<h5 class="widget-user-desc"><?php echo $email ?></h5>
	  </div>
	  <div class="widget-user-image">
		<?php if(empty($profile_pic) || (!empty($profile_pic) && !is_file('assets/uploads/'.$profile_pic))): ?>
		<span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px"><h4><?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?></h4></span>
		<?php else: ?>
		<img class="img-circle elevation-2"  style="width: 90px;height:90px" src="assets/uploads/<?php echo $profile_pic ?>" alt="User Avatar" onerror="this.onerror=null;this.src='assets/default_cover.png';">
		<?php endif ?>
	  </div>
	  <div class="card-footer">
		<div class="container-fluid">
			<dl>
				<dt>Address</dt>
				<dd><?php echo $address ?></dd>
			</dl>
			<dl>
				<dt>Gender</dt>
				<dd><?php echo $gender ?></dd>
			</dl>
			<dl>
				<dt>Contact</dt>
				<dd><?php echo $contact ?></dd>
			</dl>
			<dl>
				<dt>Gender</dt>
				<dd><?php echo $gender ?></dd>
			</dl>
			<dl>
				<dt>User Type</dt>
				<dd><?php echo $type_arr[$type] ?></dd>
			</dl>
		</div>
	</div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
	<button type="button" class="btn btn-primary mr-2" id="edit_profile_btn"><i class="fa fa-edit"></i> Edit</button>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<script>
$('#edit_profile_btn').click(function(){
	uni_modal('Chỉnh sửa Profile','manage_account.php','large');
});
</script>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>