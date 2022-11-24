const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);
//-----------------Font Mix---------------------

//-----------------CSS Mix---------------------
mix.styles([
    'resources/app-assets/vendors/css/vendors.min.css',
    'resources/app-assets/css/bootstrap.css',
    'resources/app-assets/css/bootstrap-extended.css',
    'resources/app-assets/css/components.css',
    'resources/app-assets/css/colors.css',
    'resources/app-assets/css/themes/dark-layout.css',
    'resources/app-assets/css/themes/semi-dark-layout.css',
    'resources/app-assets/css/core/menu/menu-types/horizontal-menu.css',
    'resources/app-assets/css/core/menu/menu-types/vertical-menu.css',
    'resources/app-assets/css/core/colors/palette-gradient.css',
    'resources/app-assets/vendors/css/animate/animate.css',
    'resources/app-assets/vendors/css/extensions/sweetalert2.min.css',
    'resources/app-assets/vendors/css/pickers/pickadate/pickadate.css',
    'resources/app-assets/css/plugins/extensions/toastr.css',
    'resources/app-assets/vendors/css/extensions/toastr.css'
], 'public/dashboards/css/main.css');

mix.styles([
    'resources/app-assets/vendors/css/tables/datatable/datatables.min.css',
    'resources/app-assets/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
    'resources/assets/css/custom.css'
], 'public/dashboards/css/datatables.min.css');

mix.styles(['resources/assets/css/custom.css'],
'public/dashboards/css/custom.css');

mix.styles(['resources/app-assets/css/index.css'],
'public/dashboards/css/index.css');

mix.copy('resources/app-assets/vendors/css/vendors.min.css','public/dashboards/css/vendors.min.css');
mix.copy('resources/app-assets/css/pages/data-list-view.css','public/dashboards/css/data-list-view.css');
mix.copy('resources/app-assets/vendors/css/pickers/pickadate/pickadate.css','public/dashboards/css/pickadate.css');
mix.copy('resources/app-assets/vendors/css/forms/select/select2.min.css','public/dashboards/css/select2.min.css');
mix.copy('resources/app-assets/vendors/css/file-uploaders/dropzone.min.css','public/dashboards/css/dropzone.min.css');
mix.copy('resources/app-assets/css/plugins/file-uploaders/dropzone.css','public/dashboards/css/dropzone.css');
//----------------JS Mix----------------
mix.copy('resources/app-assets/js/scripts/modal/components-modal.min.js','public/dashboards/js/components-modal.min.js');
mix.copy('resources/app-assets/js/scripts/modal/components-modal.js','public/dashboards/js/components-modal.js');
mix.copy('resources/app-assets/vendors/js/extensions/dropzone.min.js','public/dashboards/js/dropzone.min.js');
mix.copy('resources/app-assets/js/scripts/extensions/dropzone.js','public/dashboards/js/dropzone.js');
mix.copy('resources/app-assets/js/scripts/jquery.blockUI.js','public/dashboards/js/jquery.blockUI.js');
mix.copy('resources/app-assets/js/core/sum().js','public/dashboards/js/sum().js');

mix.scripts(['resources/assets/js/custom/data-table-list.js'],'public/dashboards/js/data-table-list.js');
mix.scripts(['resources/assets/js/custom/reorder-form.js'],'public/dashboards/js/reorder-form.js');
mix.scripts(['resources/assets/js/custom/permission.js'],'public/dashboards/js/permission.js');
mix.scripts(['resources/assets/js/custom/export.js'],'public/dashboards/js/export.js');
mix.scripts(['resources/assets/js/custom/volume_trackings/index.js'],'public/dashboards/js/volume_trackings/index.js');
mix.scripts(['resources/assets/js/custom/delete_tree.js'],'public/dashboards/js/delete_tree.js');
mix.scripts(['resources/assets/js/custom/gl_accounts.js'],'public/dashboards/js/gl_accounts.js');
mix.scripts(['resources/assets/js/custom/organizations.js'],'public/dashboards/js/organizations.js');
mix.scripts(['resources/assets/js/custom/print.js'],'public/dashboards/js/print.js');
mix.scripts(['resources/assets/js/custom/transaction_entries.js'],'public/dashboards/js/transaction_entries.js');
mix.scripts(['resources/assets/js/custom/jquery.treegrid.js'],'public/dashboards/js/jquery.treegrid.js');

mix.scripts(['resources/assets/js/finances/define-chart.js'],'public/dashboards/js/finances/defince-chart.js');
mix.scripts(['resources/assets/js/finances/overview.js'],'public/dashboards/js/finances/overview.js');
mix.scripts(['resources/assets/js/finances/detail.js'],'public/dashboards/js/finances/detail.js');
mix.scripts(['resources/assets/js/finances/turnover.js'],'public/dashboards/js/finances/turnover.js');
mix.scripts(['resources/assets/js/finances/over-due.js'],'public/dashboards/js/finances/over-due.js');
mix.scripts(['resources/assets/js/finances/debt-structure.js'],'public/dashboards/js/finances/debt-structure.js');
mix.scripts(['resources/assets/js/finances/debt-collection.js'],'public/dashboards/js/finances/debt-collection.js');
mix.scripts(['resources/assets/js/finances/detail-debt-collection.js'],'public/dashboards/js/finances/detail-debt-collection.js');
mix.scripts(['resources/assets/surveys/js/scripts.js'],'public/dashboards/surveys/js/scripts.js');
mix.scripts(['resources/assets/surveys/js/form-select2.js'],'public/dashboards/surveys/js/form-select2.js');
mix.scripts(['resources/assets/surveys/js/datatable.js'],'public/dashboards/surveys/js/datatable.js');
mix.scripts(['resources/assets/surveys/js/chart-chartjs.js'],'public/dashboards/surveys/js/chart-chartjs.js');

mix.scripts([
    'resources/app-assets/vendors/js/vendors.min.js',
    'resources/app-assets/vendors/js/extensions/toastr.min.js',
    'resources/app-assets/js/core/app-menu.js',
    'resources/app-assets/js/core/app.js',
    'resources/app-assets/js/scripts/jquery-ui.min.js',
    'resources/app-assets/js/scripts/components.js',
    'resources/app-assets/vendors/js/extensions/sweetalert2.all.min.js',
    'resources/app-assets/js/scripts/alertify.min.js',
    'resources/assets/js/custom.js'
],'public/dashboards/js/main.js');

mix.scripts([
    'resources/app-assets/vendors/js/charts/chart.min.js',
    'resources/app-assets/vendors/js/charts/apexcharts.min.js',
    'resources/app-assets/vendors/js/charts/chartjs-plugin-datalabels.min.js'
],'public/dashboards/js/charts.js');

mix.scripts([
    'resources/app-assets/vendors/js/tables/datatable/datatables.min.js',
    'resources/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js',
    'resources/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js',
    'resources/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js',
    'resources/app-assets/vendors/js/tables/datatable/dataTables.select.min.js',
    'resources/app-assets/vendors/js/tables/datatable/datatables.checkboxes.min.js',
    'resources/app-assets/vendors/js/tables/datatable/buttons.print.min.js',
    'resources/app-assets/vendors/js/tables/datatable/buttons.html5.min.js',
    'resources/app-assets/vendors/js/charts/dataTables.rowGroup.min.js',
    'resources/app-assets/vendors/js/charts/dataTables.fixedHeader.min.js',
    'resources/app-assets/vendors/js/tables/datatable/vfs_fonts.js',
    'resources/app-assets/js/scripts/datatables/datatable.js',
],'public/dashboards/js/datatables.min.js');

mix.scripts([
    'resources/app-assets/vendors/js/pickers/pickadate/legacy.js',
    'resources/app-assets/vendors/js/pickers/pickadate/picker.js',
    'resources/app-assets/vendors/js/pickers/pickadate/picker.date.js',
    'resources/app-assets/vendors/js/pickers/pickadate/picker.time.js',
    'resources/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js',
],'public/dashboards/js/picker.date.js');

mix.scripts([
    'resources/app-assets/vendors/js/forms/select/select2.full.min.js',
    'resources/app-assets/js/scripts/forms/select/form-select2.js',
],'public/dashboards/js/select2.full.min.js');

if (mix.inProduction()) {
    mix.version();
}
// mix.browserSync('http://127.0.0.1:8000');