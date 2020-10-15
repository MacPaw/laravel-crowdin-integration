<?php

namespace MacPaw\LaravelCrowdinIntegration\Commands;

use ElKuKu\Crowdin\Crowdin;
use MacPaw\LaravelCrowdinIntegration\BaseCommand;

class Build extends BaseCommand
{
    const BUILD_STATUS_BUILT = 'built';
    const BUILD_STATUS_SKIPPED = 'skipped';

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
     */
    public function handle(): void
    {
        $crowdin = new Crowdin(config('crowdin.project_id'), config('crowdin.api_key'));

        $this->info('Please wait. Building a translation package in progress...');

        $response = $crowdin->translation->export();
        $body = $this->xml2array(simplexml_load_string($response->getBody()->getContents()));
        $status = isset($body['@attributes']) && isset($body['@attributes']['status']) ? $body['@attributes']['status'] : null;

        if (!in_array($status, [self::BUILD_STATUS_BUILT, self::BUILD_STATUS_SKIPPED])) {
            $this->error("Something went wrong!\n The package cannot be built now...");
        }

        $this->info("\nCompleted with status: " . $status);
    }

    protected function xml2array($xmlObject, $out = []): array
    {
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? xml2array($node) : $node;
        }

        return $out;
    }
}