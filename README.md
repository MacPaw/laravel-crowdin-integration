# laravel-crowdin-integration
Automate translations uploading/downloading
## Installation

Add repository rout to composer.json: 

```
"repositories": [
   {
       "type": "git",
       "url": "https://github.com/MacPaw/laravel-crowdin-integration.git"
   }
],
```

Install the package via composer:

```composer macpaw/laravel-crowdin-integration```
  
## Config Files

In order to edit the default configuration for this package you may execute:

```
php artisan vendor:publish --provider="MacPaw\LaravelCrowdinIntegration\CrowdinServiceProvider"
```

After that, `config/crowdin.php` will be created. Inside this file you will find all the fields that can be edited in this package.