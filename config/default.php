<?php
return [
    'directory' => [
        'images' => [
            'inventory' =>'app-assets/images/inventory',
            'avatar' => 'app-assets/images/users/avatar',
            'categories' => 'app-assets/images/categories'
        ]
    ],
    'is_approve' => [
        'pending' => 0,
        'approved' => 1,
        'disapprove' => 2,
        'exported' => 3
    ],

    'supplier_type' => [
        1 => ['id'=>1,'name' => 'Cá nhân'],
        2 => ['id'=>2,'name' => 'Công ty']
    ],
    'type_id' => [
        ['id'=> 0, 'name' => 'Cá nhân'],
        ['id'=> 1, 'name' => 'Công ty'],
        ['id'=> 2, 'name' => 'Nội bộ']
    ],

    'status_id' => [
        ['id'=> 0, 'name' => 'Đang hoạt động'],
        ['id'=> 1, 'name' => 'Không hoạt động'],
        ['id'=> 2, 'name' => 'Không phát sinh'],
        ['id'=> 3, 'name' => 'Khởi kiện']
    ],
    'classify' => [
        'CÁ NHÂN' => 'Cá nhân',
        'DOANH NGHIỆP' => 'Doanh nghiệp',
        'NỘI BỘ' => 'Nội bộ'
    ],
    'category' => [
        'customers' => 'Khách hàng',
        'constructions' => 'Công trình',
        'areas' => 'Khu vực',
        'stations' => 'Trạm',
        'vehicles' => 'Phương tiện đổ',
        'slumps' => 'Độ sụt',
        'sampleages' => 'Tuổi mẫu',
        'concreteGrades' => 'Mác bê tông',
        'volumeTrackings' => 'Theo dõi khối lượng'
    ],
    'customer_flag' => [
        '1' => 'Có',
        '0' => 'Không'
    ],
    'nature_id' => [
        '1' => 'Dư nợ',
        '2' => 'Dư có',
        '3' => 'Lưỡng tính'
    ]
];