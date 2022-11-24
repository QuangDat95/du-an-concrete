<section id="breadcrumb-alignment">
    <div class="row-custom">
        <div class="col-sm-12">
            <div class="d-flex mt-2 justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @canany(['nhan vien','truong bo phan','thu kho','admin'])
                            <li class="breadcrumb-item">Nhân viên</li>
                        @endcanany
                        @canany(['truong bo phan','thu kho','admin'])
                            <li class="breadcrumb-item">Trưởng bộ phận</li>
                        @endcanany
                        @canany(['thu kho','admin'])
                            <li class="breadcrumb-item">Thủ kho</li>
                        @endcanany
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
