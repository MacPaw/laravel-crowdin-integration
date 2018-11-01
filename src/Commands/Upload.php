<?php

namespace MacPaw\LaravelCrowdinIntegration\Crowdin;

use Illuminate\Console\Command;

class Upload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdin:upload';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding or Updating all files to Crowdin project';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $langFiles = $this->getFilesNameFromDir(base_path('resources') . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'en');

        $bar = $this->output->createProgressBar(count($langFiles));
        $updated = 0;
        $added = 0;

        foreach ($langFiles as $file) {
            try {
                $this->callSilent('crowdin:add', [
                    'filename' => $file
                ]);
                $added++;
            } catch (\Exception $exception) {
                try {
                    $this->callSilent('crowdin:update', [
                        'filename' => $file
                    ]);
                    $updated++;
                } catch (\Exception $exception) {

                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line("\n");

        if ($added > 0 || $updated > 0) {
            $this->table(['added', 'updated'], [[$added, $updated]]);
        } else {
            $this->info('Nothing to export');
        }
        $this->line("\n");

    }

    protected function getFilesNameFromDir($dir): array
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException('I\'s not a dir:' . $dir);
        }

        return array_diff(scandir($dir, SCANDIR_SORT_NONE), ['..', '.']);
    }
}