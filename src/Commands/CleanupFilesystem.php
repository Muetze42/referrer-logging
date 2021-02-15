<?php

namespace NormanHuth\ReferrerLogging\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupFilesystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrer-logging:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old referrer log throttle files from filesystem';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cleanUp('referrer-cache', '', (60 * 60 * 8));

        return 0;
    }

    protected function cleanUp($disk, $folder, $seconds)
    {
        $this->line($disk.'->\\'.$folder.' -> Delete files older then '.number_format($seconds, 0, '.', ',').' seconds');

        collect(Storage::disk($disk)->listContents($folder, true))
            ->each(function ($file) use ($disk, $seconds) {
                if ($file['path'] != '.gitignore' && $file['type'] == 'file' && $file['timestamp'] < now()->subSeconds($seconds)->getTimestamp()) {
                    Storage::disk($disk)->delete($file['path']);
                }
            });
    }
}
