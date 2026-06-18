<?php

namespace App\Console\Commands;

use App\Services\CameraHealthService;
use Illuminate\Console\Command;

/**
 * Class CheckCameraHealth
 * 
 * Command to check health of all active cameras.
 * 
 * @package App\Console\Commands
 */
class CheckCameraHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:check-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings go2rtc API to check active camera streams and update status';

    /**
     * Execute the console command.
     * 
     * @param CameraHealthService $healthService
     * @return int
     */
    public function handle(CameraHealthService $healthService): int
    {
        $this->info('Starting camera health check...');
        $healthService->checkHealth();
        $this->info('Camera health check completed.');
        
        return 0;
    }
}
