<?php

namespace MacPaw\LaravelCrowdinIntegration\Crowdin;

use App\Services\Crowdin\Init;
use ElKuKu\Crowdin\Crowdin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use ZanySoft\Zip\Zip;

class DownloadAll extends Command
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
     *
     * @return mixed
     */
    public function handle()
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
        } catch (\Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }

        foreach ($mapping as $dirInProject => $langDir) {
            $langDirFull = $thisLangDir . DIRECTORY_SEPARATOR . $dirInProject;
            $crowdinLangDir = $destination . DIRECTORY_SEPARATOR . $langDir;
            $crowdinDirFull = $dirInCrowdinProject ? $crowdinLangDir . DIRECTORY_SEPARATOR . $dirInCrowdinProject : $crowdinLangDir;
            if (is_dir($langDirFull) || mkdir($langDirFull) || is_dir($langDirFull)) {
                $langFiles = $this->getFilesNameFromDir($crowdinDirFull);
                $this->info("Pricesing lang: " . $dirInProject . "\n");
                $bar = $this->output->createProgressBar(count($langFiles));

                foreach ($langFiles as $langFile) {
                    if (is_file($crowdinDirFull . DIRECTORY_SEPARATOR . $langFile)) {
                        copy($crowdinDirFull . DIRECTORY_SEPARATOR . $langFile,
                            $langDirFull . DIRECTORY_SEPARATOR . $langFile);
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

    protected function getFilesNameFromDir($dir): array
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException('I\'s not a dir:' . $dir);
        }

        return array_diff(scandir($dir, SCANDIR_SORT_NONE), ['..', '.']);
    }

    protected function rrmdir($dir)
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
