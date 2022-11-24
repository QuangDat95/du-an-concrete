const countOccurrences = (arr, val) => arr.reduce((a, v) => (v === val ? a + 1 : a), 0);

$(document).ready(function() {
    $('#select-all').click(function(event) {
        if(getChecked().length){
            showCheckAllIcon();
            var permissions = getAttrPermission();
            getPermission(permissions);
            if($('input[name=roles]').length){
                var roles = getAttrRole();
                getRoles(roles);
            }
            $('.dropdown-set-role').show();
        }else{
            removeAllPermission();
            $('.dropdown-set-role').hide();
        }
    });

    $('body').on("click", ".dataTable tbody tr", function(evt) {
        if(getChecked().length){
            var permissions = getAttrPermission();
            getPermission(permissions);
            if($('input[name=roles]').length){
                var roles = getAttrRole();
                getRoles(roles);
            }
        }else{
            removeAllPermission();
        }
    });
    function getAttrRole(){
        var roles = [];
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            var params = $.parseJSON($(this).attr('role'));
            $.each(params, function( index, value ) {
                roles.push(value);
            });
        });
        return roles;
    }
    function getAttrPermission(){
        var permissions = [];
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            var params = $.parseJSON($(this).attr('permission'));
            $.each(params, function( index, value ) {
                permissions.push(value);
            });
        });
        return permissions;
    }
    function removeAllPermission(){
        $('input[name=permission]').each(function(){
            this.checked = false;
            $('.vs-checkbox').removeClass('vs-checkbox-custom');
        });
    }

    function getRoles(roles){
        $('input[name=roles]').each(function(){
            var roleId = parseInt($(this).attr('role'), 10);
            var selectBox = checkSelectBox();
            var countParam = countOccurrences(roles, roleId);
            $('#vs-checkbox-role-'+roleId).removeClass('vs-checkbox-custom');
            if(roles.includes(roleId)){
                this.checked = true;
            }
            if(this.checked && selectBox >= 2){
                if(countParam == selectBox){
                    this.checked = true;
                    $('#vs-checkbox-role-'+roleId).removeClass('vs-checkbox-custom')
                }else{
                    this.checked = false;
                    $('#vs-checkbox-role-'+roleId).addClass('vs-checkbox-custom')
                }
            }
        });
    }

    function getPermission(permission){
        $('input[name=permission]').each(function(){
            var permissionId = parseInt($(this).attr('permission'), 10);
            var selectBox = checkSelectBox();

            $('#vs-checkbox-'+permissionId).removeClass('vs-checkbox-custom');
            if(permission.includes(permissionId)){
                this.checked = true;
            }
            var countParam = countOccurrences(permission, permissionId);

            if(this.checked && selectBox >= 2){
                if(countParam == selectBox){
                    this.checked = true;
                    $('#vs-checkbox-'+permissionId).removeClass('vs-checkbox-custom')
                }else{
                    this.checked = false;
                    $('#vs-checkbox-'+permissionId).addClass('vs-checkbox-custom')
                }
            }
        });
    }
    function checkSelectBox(){
        var checkBoxed = 0;
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            checkBoxed++;
        });
        return checkBoxed;
    }
    function getChecked(){
        var checkBoxeds = []
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            var value = $(this).val();
            if(value != 0)
                checkBoxeds.push(value);
        });
        return checkBoxeds;
    }
});
