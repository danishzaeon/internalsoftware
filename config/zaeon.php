<?php

$documents = [
    'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt'],
    'mimes' => [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/csv', 'text/plain',
    ],
];

return [

    'name' => 'ZAEON Manage',

    'currency' => 'INR',

    /*
    | First super admin, used by SuperAdminSeeder only. Read here (not with env() in the seeder)
    | so seeding keeps working when the configuration is cached.
    */
    'super_admin' => [
        'name' => env('SEED_SUPERADMIN_NAME'),
        'email' => env('SEED_SUPERADMIN_EMAIL'),
        'password' => env('SEED_SUPERADMIN_PASSWORD'),
    ],

    /*
    | Upload rules, used from Phase 5/6. Files are stored on a PRIVATE disk (storage/app/private)
    | and served only through authorized controllers. SVG, HTML, PHP and executables are never allowed.
    | Remember Hostinger's PHP upload_max_filesize / post_max_size must be >= these limits.
    */
    'uploads' => [
        'disk' => env('ZAEON_UPLOAD_DISK', 'local'),

        'task' => [
            'max_kb' => (int) env('ZAEON_TASK_UPLOAD_MAX_KB', 10240),      // 10 MB
            'extensions' => $documents['extensions'],
            'mimes' => $documents['mimes'],
        ],

        'content' => [
            'max_kb' => (int) env('ZAEON_CONTENT_UPLOAD_MAX_KB', 51200),   // 50 MB
            'extensions' => array_merge($documents['extensions'], ['mp4', 'mov']),
            'mimes' => array_merge($documents['mimes'], ['video/mp4', 'video/quicktime']),
        ],
    ],

];
