<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * GuruPiketAuth Filter
 *
 * Ensures the current user is logged in AND has the 'guru_piket' role.
 */
class GuruPiketAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('role') !== 'guru_piket') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
