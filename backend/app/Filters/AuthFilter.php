<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Token tidak ditemukan atau tidak valid'
                ])
                ->setStatusCode(401);
        }
        
        helper('jwt_helper');
        $token = $matches[1];
        $decoded = validateJWT($token);
        
        if (!$decoded) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Token expired atau tidak valid'
                ])
                ->setStatusCode(401);
        }
        
        // Attach user data to request
        $request->user = $decoded['data'];
        
        return $request;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}