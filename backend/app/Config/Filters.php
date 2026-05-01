<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'permission'    => \App\Filters\PermissionFilter::class,
        'cors'          => \App\Filters\CORSOptionsFilter::class, // Tambahkan ini
    ];

    // Global filters yang akan berjalan di semua request
    public array $globals = [
        'before' => [
            'cors', // CORS harus di awal
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'cors',
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    // Filters untuk method tertentu
    public array $methods = [];

    // Filters untuk URI pattern tertentu
    public array $filters = [];
}