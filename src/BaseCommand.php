<?php

namespace MacPaw\LaravelCrowdinIntegration;

use ElKuKu\Crowdin\Languagefile;
use Illuminate\Console\Command;
use RuntimeException;

class BaseCommand extends Command
{
    protected function getPathInfo($fileName)
    {
        $thisLangDir = base_path('resources') . '/lang';
        $defaultLang = config('crowdin.defaultLang', 'en');

        $pathInfo = $fileName ? pathinfo($thisLangDir . '/' . $defaultLang . '/' . $fileName) : false;
        if (empty($pathInfo['extension'])) {
            throw new RuntimeException('wrong file extension');
        }

        return $pathInfo;
    }

    protected function getLanguageFile($fileName): Languagefile
    {
        $pathInfo = $this->getPathInfo($fileName);
        $dirInCrowdinProject = config('crowdin.crowdin_dir', false);
        $crowdinPath = $dirInCrowdinProject ? DIRECTORY_SEPARATOR . $dirInCrowdinProject . DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR;

        return new Languagefile(
            $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'],
            $crowdinPath . $pathInfo['basename']
        );
    }
}