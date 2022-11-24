$(document).ready(function(){
    window.loadGlAccountAjax = function() {
        let GlAccountUrl = "/gl_accounts/load/ajax";
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: GlAccountUrl,
            type: "post",
        }).done(function (data) {
            $("#gl-account-parent-create").html("<option value=''>---root---</option>");
            $("#gl-account-parent-create").append(data);
        });
    }

    window.checkVatflagGlaccount = function(index, value){
        if (index == "account_code") {
            $("#account-code-old").val(value);
        }
        if (index == "description") {
            $("#description").val(value);
        }
        if (index == "nature_id") {
            $("#select2-nature_id").val(value).trigger("change.select2");
        }
        if(index == 'parent_id'){
            $("#gl-account-parent-create").val(value).trigger("change.select2");
        }
        if (index == "customer_flag") {
            if (value == 1) {
                $("#customer_flag").attr("checked", true);
            } else {
                $("#customer_flag").attr("checked", false);
            }
        }
    }
});