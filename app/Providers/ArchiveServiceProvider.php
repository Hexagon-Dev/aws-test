<?php

namespace App\Providers;

use App\Contracts\Services\ArchiveServiceInterface;
use App\Services\ArchiveService;
use Illuminate\Support\ServiceProvider;

class ArchiveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ArchiveServiceInterface::class, ArchiveService::class);
    }
}
