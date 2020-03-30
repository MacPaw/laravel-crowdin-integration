<?php

namespace MacPaw\LaravelCrowdinIntegration\Commands;

use ElKuKu\Crowdin\Crowdin;
use Exception;
use Illuminate\Support\Str;
use MacPaw\LaravelCrowdinIntegration\BaseCommand;
use RuntimeException;
use ZanySoft\Zip\Zip;

class DownloadAll extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdin:download';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download a zip file containing all language files from Crowdin';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));
        $mapping = config('crowdin.mapping', null);
        $thisLangDir = base_path('resources') . '/lang';
        $dirInCrowdinProject = config('crowdin.crowdin_dir', false);

        $destination = $thisLangDir . 'all.' . Str::random();

        $this->call('crowdin:build');

        $crowdin->translation->download('all.zip', $destination . '.zip');

        try {
            $zip = Zip::open($destination . '.zip');
            $zip->extract($destination);
            $zip->close();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        foreach ($mapping as $dirInProject => $langDir) {
            $langDirFull = $thisLangDir . DIRECTORY_SEPARATOR . $dirInProject;
            $crowdinLangDir = $destination . DIRECTORY_SEPARATOR . $langDir;
            $crowdinDirFull = $dirInCrowdinProject ? $crowdinLangDir . DIRECTORY_SEPARATOR . $dirInCrowdinProject : $crowdinLangDir;

            if (is_dir($langDirFull) || mkdir($langDirFull) || is_dir($langDirFull)) {
                $langFiles = $this->getFilesNameFromDir($crowdinDirFull);
                $this->info("Processing lang: " . $dirInProject . "\n");
                $bar = $this->output->createProgressBar(count($langFiles));

                foreach ($langFiles as $langFile) {
                    if (is_file($crowdinDirFull . DIRECTORY_SEPARATOR . $langFile)) {
                        copy(
                            $crowdinDirFull . DIRECTORY_SEPARATOR . $langFile,
                            $langDirFull . DIRECTORY_SEPARATOR . $langFile
                        );
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->line("\n");
            }
        }

        unlink($destination . '.zip');
        $this->rrmdir($destination);
    }

    protected function rrmdir($dir): void
    {
        $objects = $this->getFilesNameFromDir($dir);

        foreach ($objects as $object) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
            } else {
                unlink($dir . DIRECTORY_SEPARATOR . $object);
            }
        }

        reset($objects);
        rmdir($dir);
    }
}
