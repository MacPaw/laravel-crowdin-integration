<?php

namespace MacPaw\LaravelCrowdinIntegration\Commands;

use ElKuKu\Crowdin\Crowdin;
use MacPaw\LaravelCrowdinIntegration\BaseCommand;

class AddFile extends BaseCommand
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
     */
    public function handle(): void
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));
        $pathInfo = $this->getPathInfo($this->argument('filename'));

        $crowdin->file->add($this->getLanguageFile($this->argument('filename')), $pathInfo['extension']);
    }
}
