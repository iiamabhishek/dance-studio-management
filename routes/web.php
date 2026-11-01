<?php

use App\Http\Controllers\CronController;
use App\Http\Controllers\InvoiceDocumentController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

// Vercel Cron: serverless has no daemons; hit this once per day.
Route::get('/v/cron', [CronController::class, 'run'])->name('cron.run');

Route::middleware([Authenticate::class])
    ->group(function (): void {
        Route::get('/invoices/{invoice}/preview', [InvoiceDocumentController::class, 'preview'])
            ->name('invoices.preview');

        Route::get('/invoices/{invoice}/download', [InvoiceDocumentController::class, 'download'])
            ->name('invoices.download');
    });
