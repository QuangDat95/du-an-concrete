$(document).ready(function(){
    window.loadOrganizationAjax = function() {
        let organizationLoadAjax = "/organizations/load/ajax";
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: organizationLoadAjax,
            type: "post",
        }).done(function (data) {
            $("#organization-parent-create").html("<option value=''>---root---</option>");
            $("#organization-parent-create").append(data);
        });
    }
});