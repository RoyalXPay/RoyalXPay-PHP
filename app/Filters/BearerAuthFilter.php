<?php

namespace App\Filters;

use App\Models\AccessTokenModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BearerAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return service('response')->setJSON([
                'statusCode' => 401,
                'message' => 'Authorization token required',
                'data' => null
            ])->setStatusCode(401);
        }
        
        $token = str_replace('Bearer ', '', $authHeader);
        
        if (empty($token)) {
            return service('response')->setJSON([
                'statusCode' => 401,
                'message' => 'Invalid token format',
                'data' => null
            ])->setStatusCode(401);
        }
        
        $tokenModel = new AccessTokenModel();
        $tokenData = $tokenModel->validateToken($token);
        
        if (!$tokenData) {
            return service('response')->setJSON([
                'statusCode' => 401,
                'message' => 'Invalid or expired token',
                'data' => null
            ])->setStatusCode(401);
        }
        
        $request->userId = $tokenData['user_id'];
        $request->username = $tokenData['username'];
        $request->walletNumber = $tokenData['wallet_number'] ?? null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
