<?php

namespace MacPaw\LaravelCrowdinIntegration;

use ElKuKu\Crowdin\Languagefile;
use Illuminate\Console\Command;
use RuntimeException;

class BaseCommand extends Command
{
    protected function getFilesNameFromDir($dir): array
    {
        if (!is_dir($dir)) {
            throw new RuntimeException('Invalid directory provided:' . $dir);
        }

        return array_diff(scandir($dir, SCANDIR_SORT_NONE), ['..', '.']);
    }

    protected function getPathInfo($fileName)
    {
        $thisLangDir = base_path('resources') . '/lang';
        $defaultLang = config('crowdin.defaultLang', 'en');

        $pathInfo = $fileName ? pathinfo($thisLangDir . '/' . $defaultLang . '/' . $fileName) : false;
        if (empty($pathInfo['extension'])) {
            throw new RuntimeException('Invalid file extension');
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
