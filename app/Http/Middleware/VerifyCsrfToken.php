<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs yang dikecualikan dari CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'callback',
        // tambahkan URL lain jika perlu
    ];
}
