<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * AdminAuth Filter
 *
 * Ensures the current user is logged in AND has the 'admin' role.
 * Returns JSON 403 for API/AJAX requests, redirects to '/' for page requests.
 */
class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Must be logged in
        if (!session()->get('logged_in')) {
            if ($this->isApiRequest($request)) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu']);
            }
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu');
        }

        // Must be admin
        if (session()->get('role') !== 'admin') {
            if ($this->isApiRequest($request)) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 'error', 'message' => 'Akses ditolak: hanya admin']);
            }
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }

    private function isApiRequest(RequestInterface $request): bool
    {
        $uri = (string) $request->getUri()->getPath();
        // API routes start with /api/ or the request accepts JSON
        return str_contains($uri, '/api/') || $request->hasHeader('Accept') && str_contains($request->getHeaderLine('Accept'), 'application/json');
    }
}
