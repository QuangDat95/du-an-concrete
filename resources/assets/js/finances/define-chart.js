ajaxSetup();
function ajaxSetup() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}
//hiển thị table đầy đủ khi load dữ liệu
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $.fn.dataTable.tables({
        visible: true,
        api: true
    }).columns.adjust();
});
//refresh lại biểu đồ khi chuyển tab
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    window.dispatchEvent(new Event('resize'));
});
//lấy ngày trong giới hạn có của dữ liệu
const min = MINMAX_DATE[0]['MinDate'];
const max = MINMAX_DATE[0]['MaxDate'];
//chuyển đổi định dạng
const min_time = new Date(min);
const max_time = new Date();
//lấy giá trị các ngày
const min_date = min_time.getDate();
const min_month = min_time.getMonth();
const min_year = min_time.getFullYear();
const max_date = max_time.getDate();
const max_month = max_time.getMonth();
const max_year = max_time.getFullYear();
const date_first = new Date(max_year + "-" + (max_month + 1) + "-" + 1);
//định nghĩa màu sắc của các chart
const primary = '#7367F0';
const success = '#28C76F';
const danger = '#EA5455';
const warning = '#FF9F43';
const label_color = '#1E1E1E';
const grid_line_color = '#dae1e7';
const scatter_grid_color = '#f3f3f3';
const scatter_point_light = '#D1D4DB';
const scatter_point_dark = '#5175E0';
const blue = '#0045FE';
const white = '#fff';
const black = '#000';
const themeColorss = [primary, success, danger, grid_line_color, 'pink', success, danger, warning,
    primary, success, grid_line_color, warning, primary, success, danger, warning, primary,
    success, grid_line_color, warning, primary, success, danger, warning, primary, success,
    danger, warning, primary, success, danger, warning
];
const info = '#00cfe8',
    label_color_light = '#dae1e7';
    const themeColors = [primary, success, danger, warning, info];
    const linecolors = ['#2B2087'];
    const yaxis_opposite = false;
if ($('html').data('textdirection') == 'rtl') {
    yaxis_opposite = true;
}