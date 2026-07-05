<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // registers and activates your api.php endpoints
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ── STEP 3: CONFIGURE CORS FOR PRODUCTION CLIENTS ──
        $middleware->statefulApi();

        $middleware->validateCsrfTokens(except: [
            'api/*', // Bypasses token requirements for headless incoming payloads (bookings, inquiries, status updates)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();