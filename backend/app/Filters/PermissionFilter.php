<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredPermission = $arguments[0] ?? null;
        
        if (!$requiredPermission) {
            return $request;
        }
        
        helper('jwt_helper');
        $userPermissions = getUserPermissions($request->user->id);
        
        if (!in_array($requiredPermission, $userPermissions)) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk mengakses resource ini'
                ])
                ->setStatusCode(403);
        }
        
        return $request;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}