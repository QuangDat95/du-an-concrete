function exportdata() {
    if (TABLE_NAME == "volume_trackings") {
        var filterTableColumn = $(".filter-table-column").val();
        // var search = $('input[type="search"]').val();
        $.ajax({
            url: URL_EXPORT_VOLUME,
            method: "POST",
            data: { filter_table: filterTableColumn },
            type: "json",
            xhrFields: {
                responseType: "blob",
            },
        }).done(function (data) {
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(data);
            (filterTableColumn == 0) ? (link.download = "theo dõi khối lượng tất cả khu vực.xlsx") : (link.download = "theo dõi khối lượng khu vực " + filterTableColumn + ".xlsx");
            $.unblockUI();
            link.click();
        });
    }
    if (TABLE_NAME == "customers") {
        $.ajax({
            url: URL_EXPORT_CUSTOMER,
            method: "POST",
            type: "json",
            xhrFields: {
                responseType: "blob",
            },
        }).done(function (data) {
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(data);
            link.download = "khách hàng.xlsx";
            $.unblockUI();
            link.click();
        });
    }
    if (TABLE_NAME == "constructions") {
        $.ajax({
            url: URL_EXPORT_CONSTRUCTION,
            method: "POST",
            type: "json",
            xhrFields: {
                responseType: "blob",
            },
        }).done(function (data) {
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(data);
            link.download = "Công trình.xlsx";
            $.unblockUI();
            link.click();
        });
    }
}

$("body").on("click", "#export-btn-click", function (e) {
    $.blockUI({
        message: '<h1><div class="spinner-border text-primary" role="status">\
        <span class="sr-only">Loading...</span>\
      </div> Please wait </h1>', onBlock: exportdata()
    });
    
});