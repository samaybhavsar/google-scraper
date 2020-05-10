# Google-Scraper
This class can retrieve search results from Google. 


## Install the package using composer

```
composer require samay/google-scraper
```

## Usage

```
<?php

require_once __DIR__ . '/./vendor/autoload.php';

use Scraper\GoogleScraper;

$obj = new GoogleScraper();

$arr=$obj->getUrlList(urlencode('car'),'');

print_r($arr);
```
