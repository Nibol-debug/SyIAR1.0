<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CORSOptionsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'options') {
            $response = service('response');
            $response->setStatusCode(200);
            $response->setHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
            $response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With');
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Max-Age', '86400');
            $response->setBody('');
            return $response;
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add CORS headers to all responses
        $response->setHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        return $response;
    }
}