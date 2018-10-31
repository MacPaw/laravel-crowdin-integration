<?php

namespace MacPaw\LaravelCrowdinIntegration\Crowdin;

use ElKuKu\Crowdin\Crowdin;
use ElKuKu\Crowdin\Languagefile;
use Illuminate\Console\Command;

class AddFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdin:add {filename}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new file to Crowdin project';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));
        $pathInfo = $this->getPathInfo($this->argument('filename'));

        if ($langFile = $this->getLanguageFile($this->argument('filename'))) {
            $crowdin->file->add($langFile, $pathInfo['extension']);
        }
    }

    protected function getLanguageFile($fileName)
    {
        $pathInfo = $this->getPathInfo($fileName);
        $dirInCrowdinProject = config('crowdin.crowdin_dir', false);
        $crowdinPath = $dirInCrowdinProject ? DIRECTORY_SEPARATOR . $dirInCrowdinProject : DIRECTORY_SEPARATOR;

        return new Languagefile($pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'],
            $crowdinPath . DIRECTORY_SEPARATOR . $pathInfo['basename']);
    }

    protected function getPathInfo($fileName)
    {
        $thisLangDir = base_path('resources') . '/lang';
        $defaultLang = config('crowdin.defaultLang', 'en');

        $pathInfo = $fileName ? pathinfo($thisLangDir . '/' . $defaultLang . '/' . $fileName) : false;
        if (empty($pathInfo['extension'])) {
            throw new \RuntimeException('wrong file extension');
        }

        return $pathInfo;
    }
}
