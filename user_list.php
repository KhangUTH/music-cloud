<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header d-flex justify-content-between align-items-center">
			<h3 class="card-title mb-0">User List</h3>
			<a class="btn btn-sm btn-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
		</div>
		<div class="card-body">
			<div class="table-responsive">
			<table class="table table-hover table-bordered table-striped align-middle" id="list">
				<thead class="thead-dark">
					<tr>
						<th class="text-center">#</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Contact #</th>
						<th>Role</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$type = array('',"Admin","Subscriber");
					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");
					while($row= $qry->fetch_assoc()):
						$avatar = !empty($row['avatar']) && file_exists('assets/uploads/'.$row['avatar']) ? 'assets/uploads/'.$row['avatar'] : 'assets/uploads/default.jpg';
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td class="text-center"><img src="<?php echo $avatar ?>" alt="avatar" style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:1px solid #ccc;"></td>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['contact'] ?></b></td>
						<td><span class="badge badge-<?php echo $row['type']==1?'success':'secondary' ?>"><?php echo $type[$row['type']] ?></span></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td class="text-center">
							<div class="btn-group">
							  <button type="button" class="btn btn-info btn-sm view_user" data-id="<?php echo $row['id'] ?>" title="View"><i class="fa fa-eye"></i></button>
							  <a class="btn btn-warning btn-sm" href="./index.php?page=edit_user&id=<?php echo $row['id'] ?>" title="Edit"><i class="fa fa-edit"></i></a>
							  <button class="btn btn-danger btn-sm delete_user" data-id="<?php echo $row['id'] ?>" title="Delete"><i class="fa fa-trash"></i></button>
							</div>
						</td>
					</tr>    
				<?php endwhile; ?>
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	if ( $.fn.DataTable.isDataTable('#list') ) {
		$('#list').DataTable().destroy();
	}
	$('#list').DataTable({
		"pageLength": 10,
		"lengthChange": false,
		"responsive": true,
		"order": [[2, 'asc']],
		"columnDefs": [
			{ "orderable": false, "targets": [1,6] }
		],
		"language": {
			"search": "🔍 Tìm kiếm:",
			"zeroRecords": "Không tìm thấy user nào",
			"info": "Hiển thị _START_ đến _END_ trong _TOTAL_ user",
			"infoEmpty": "Không có user nào",
			"paginate": {
				"previous": "Trước",
				"next": "Sau"
			}
		}
	});
	// Xem chi tiết user
	$(document).on('click', '.view_user', function(){
		uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
	});
	// Xóa user
	$(document).on('click', '.delete_user', function(){
		var id = $(this).attr('data-id');
		if(confirm('Bạn có chắc chắn muốn xóa user này?')) {
			delete_user(id);
		}
	});
});
function delete_user($id){
	start_load();
	$.ajax({
		url:'ajax.php?action=delete_user',
		method:'POST',
		data:{id:$id},
		success:function(resp){
			if(resp==1){
				alert_toast("Xóa user thành công!",'success');
				setTimeout(function(){
					location.reload();
				},1000);
			} else {
				alert_toast("Xóa user thất bại!",'error');
			}
		},
		error: function(){
			alert_toast("Lỗi kết nối server!",'error');
		}
	});
}
</script>