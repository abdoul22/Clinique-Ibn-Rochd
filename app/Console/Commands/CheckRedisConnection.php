<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;


class CheckRedisConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-redis-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Redis::connect();
            $this->info('Redis connected successfully');
            $this->info('Memory usage: ' . Redis::info()['used_memory_human']);
        } catch (\Exception $e) {
            $this->error('Redis connection failed: ' . $e->getMessage());
            // Fallback automatique
            config(['cache.default' => 'file']);
            $this->warn('Fallback to file cache activated');
        }
    }
}
