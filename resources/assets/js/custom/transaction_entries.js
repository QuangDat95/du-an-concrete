$(document).ready(function () {
    window.loadStationTransactionEntry = function () {
        $('#station_id').select2({
            placeholder: "Chọn",
            allowClear: true,
            dropdownAutoWidth: true,
            width: '100%'
        });
        let loadStationUrl = '/loadStation';
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: loadStationUrl,
            type: "POST",
        }).done(function (data) {
            $('select[name="station_id"]').html("<option value=''>---root---</option>");
            $('select[name="station_id"]').append(data);
        });
    }

    window.checkEditTransactionEntry = function (index, value) {
        if (index == 'station_id') {
            $('#station_old').val(value);
            let editStationUrl = '/edit/Station/Url';
            $.ajax({
                url: editStationUrl,
                type: "POST",
                data: {
                    id: value
                }
            }).done(function (data) {
                $('select[name="station_id"]').html('');
                $('select[name="station_id"]').html(data);
                $('select[name="station_id"]').val(value).trigger('change.select2');
            });
        }
    }
});