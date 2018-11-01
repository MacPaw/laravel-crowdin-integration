<?php

namespace MacPaw\LaravelCrowdinIntegration\Crowdin;

use Illuminate\Console\Command;
use ElKuKu\Crowdin\Crowdin;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdin:build';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build ZIP archive with the latest translations.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));

        $res = $crowdin->translation->export();
        $status = $crowdin->translation->getStatus();

        var_dump($res);
        var_dump($status);
    }
}