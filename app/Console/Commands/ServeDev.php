<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serve:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run npm run dev and php artisan serve concurrently.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $npmProcess = new Process(['npm', 'run', 'dev']);
        $artisanProcess = new Process(['php', 'artisan', 'serve']);

        $npmProcess->start();
        $artisanProcess->start();

        $this->info('Starting npm run dev...');
        $this->info('Starting php artisan serve...');

        while ($npmProcess->isRunning() || $artisanProcess->isRunning()) {
            if ($npmProcess->isRunning()) {
                echo $npmProcess->getIncrementalOutput();
                echo $npmProcess->getIncrementalErrorOutput();
            }

            if ($artisanProcess->isRunning()) {
                echo $artisanProcess->getIncrementalOutput();
                echo $artisanProcess->getIncrementalErrorOutput();
            }

            usleep(100000); // Sleep for 100 milliseconds to prevent excessive CPU usage
        }

        if (!$npmProcess->isSuccessful()) {
            $this->error('npm run dev failed: ' . $npmProcess->getErrorOutput());
        }

        if (!$artisanProcess->isSuccessful()) {
            $this->error('php artisan serve failed: ' . $artisanProcess->getErrorOutput());
        }

        return 0;
    }
}
