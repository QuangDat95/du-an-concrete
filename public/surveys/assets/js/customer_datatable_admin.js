var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
script.type = 'text/javascript';
const url_setRole = location.origin+'/admin/roles/set';
const url_setPermission = location.origin+'/admin/permissions/set';
const url_removeRole = location.origin+'/admin/roles/remove';
const url_removePermission = location.origin+'/admin/permissions/remove';
// function checkLoadAjaxSetRoleOrPermission()
// {
//   var arr_users = [];
//   $("input:checkbox[name=user_checkbox]:checked").each(function(){
//     arr_users.push($(this).val());
//   });
//   if(arr_users.length === 0)
//   {
//     alert('Bạn phải chọn người dùng');
//     return false;
//   }
//   return true;
// }
$(document).ready(function(){
  var checkRoleManager = $('#check-role-manager').val();
  if(checkRoleManager =='0'){
    $('#parent-selectbox-permission').hide();
  }
});
$('#button-confirm-set-role').click(function(){
  var role_value = $('.radio-role-value:checked').val();
  if(role_value!=undefined){
    Swal.fire({
      title: 'Bạn có chắc chắn không?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Chắc chắn'
    }).then((result) => {
      if (result.isConfirmed) {
        loadAjaxSetRole(role_value);
      }
    });
  }
  else{
    Swal.fire(
      'Thông báo!',
      'Bạn phải chọn vai trò',
      'warning'
    );
  }
});
$('#button-confirm-set-permission').on('click',function(){
  var arrPermissionValues = [];
  $('.checkbox-permission-value:checked').each(function(){
    arrPermissionValues.push($(this).val());
  });
  if(arrPermissionValues.length==0){
    Swal.fire(
      'Thông báo!',
      'Bạn phải chọn phiếu khảo sát',
      'warning'
    );
  }
  else{
    Swal.fire({
      title: 'Bạn có chắc chắn không?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Chắc chắn'
    }).then((result) => {
      if (result.isConfirmed) {
        loadAjaxSetPermission(arrPermissionValues);
      }
    });
  }
});
function loadAjaxSetRole(role_value)
{
  var arr_users = [];
  $("input:checkbox[name=user_checkbox]:checked").each(function(){
    arr_users.push($(this).val());
  });
  
  $.ajax({
    url: url_setRole,
    type: 'get',
    data: {
      role_value : role_value,
      arr_users : arr_users,
    },
    success: function(data){
      if(data==1){
        if(role_value=='manager'){
          $('#parent-selectbox-permission').show();
        }
        else{
          $('#parent-selectbox-permission').hide();
        }
      }
      Swal.fire(
        'Chúc mừng',
        'Bạn đã phân quyền thành công',
        'success'
      );
      $('#table-set-role-users').DataTable().ajax.reload();
      uncheckCheckboxCheckAll();
    },
    error:function(xhr,status,error){
      Swal.fire(
        'Lỗi',
        'Phân quyền thất bại',
        'error'
      )
    },
    dataType: 'text',
  });
  
}
function loadAjaxSetPermission(arrPermissionValues)
{
  var arr_users = [];
  $("input:checkbox[name=user_checkbox]:checked").each(function(){
    arr_users.push($(this).val());
  });
  $.ajax({
    url: url_setPermission,
    type: 'get',
    data: {
      arrPermissionValues : arrPermissionValues,
      arr_users : arr_users,
    },
    success: function(data){
      Swal.fire(
        'Chúc mừng',
        'Bạn đã phân quyền phiếu thành công',
        'success'
      );
      reloadTableSetRoleUsers(); 
      uncheckCheckboxCheckAll();
    },
    error:function(xhr,status,error){
      Swal.fire(
        'Lỗi',
        'Phân quyền phiếu thất bại',
        'error'
      );
    },
    dataType: 'text',
  });
}
$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
var tableSetRoleUsers = $('#table-set-role-users').DataTable({
    "maxLength": 10,
    "lengthMenu": [10, 25, 50, 100],
    "processing": true,
    "serverSide": true,
    "columnDefs": [
      { "width": "5%", "targets": 0 },
      { "width": "5%", "targets": 1 },
      { "width": "20%", "targets": 2 },
      { "width": "15%", "targets": 3 },
      { "width": "15%", "targets": 4 },
      { "width": "10%", "targets": 5 },
      { "width": "30%", "targets": 6 },
    ],
    ajax: {
        url: "/ajax/list/users",
        type: 'post',
    },
    columns:[
        {data: 'checkbox', name: 'checkbox',orderable:false,searchable:false},
        {data : 'DT_RowIndex', name: 'DT_RowIndex'},
        {data: 'hovaten', name: 'hovaten',orderable:true,searchable:true},
        {data: 'email', name: 'email',orderable:true,searchable:true},
        {data: 'phongban', name: 'phongban',orderable:true,searchable:true},
        {data: 'role_name', name: 'role_name'},
        {data: 'permission_name', name: 'permission_name'},
    ]
});
function reloadTableSetRoleUsers()
{
  tableSetRoleUsers.ajax.reload();
}
$('#role-checkAll').click(function(){
  $(".role-check-user").prop('checked',this.checked);
  showCheckAllIcon();
  if($('.role-check-user').is(':checked')){
    $('#button-confirm-set-role').show();
    $('#dropdown-permission-list').show();
    $('#dropdown-role-list').show();
  }
  else{
    $('#button-confirm-set-role').hide();
    $('#dropdown-permission-list').hide();
    $('#dropdown-role-list').hide();
  }
});
function uncheckCheckboxCheckAll()
{
  $('#role-checkAll').prop('checked',false);
}
