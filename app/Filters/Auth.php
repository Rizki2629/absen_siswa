<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            // Log for debugging
            log_message('error', 'Auth Filter: Session not found or expired. URI: ' . $request->getUri());
            log_message('error', 'Auth Filter: Session data: ' . json_encode(session()->get()));

            // If it's an API request, return JSON instead of redirect
            if (strpos($request->getUri()->getPath(), '/api/') !== false) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Unauthorized. Please login first.'
                    ]);
            }

            // Redirect to login page for web requests
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
