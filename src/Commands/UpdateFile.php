<?php

namespace MacPaw\LaravelCrowdinIntegration\Commands;

use ElKuKu\Crowdin\Crowdin;
use MacPaw\LaravelCrowdinIntegration\BaseCommand;

class UpdateFile extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdin:update {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existed file in Crowdin project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));

        $crowdin->file->update($this->getLanguageFile($this->argument('filename')));
    }
}
