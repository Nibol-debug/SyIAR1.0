<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    /**
     * Array of allowed origins
     * 
     * @var array<int, string>
     */
    public array $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:3001',
    ];

    /**
     * Allowed origins patterns (regex)
     * 
     * @var array<int, string>
     */
    public array $allowedOriginsPatterns = [];

    /**
     * Allowed headers
     * 
     * @var array<int, string>
     */
    public array $allowedHeaders = [
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'Accept',
        'Origin'
    ];

    /**
     * Allowed methods
     * 
     * @var array<int, string>
     */
    public array $allowedMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS'
    ];

    /**
     * Whether to allow credentials
     * 
     * @var bool
     */
    public bool $allowCredentials = true;

    /**
     * Exposed headers
     * 
     * @var array<int, string>
     */
    public array $exposedHeaders = [];

    /**
     * Max age of preflight request (seconds)
     * 
     * @var int
     */
    public int $maxAge = 86400; // 24 jam
}