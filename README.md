PHP Google Site Search
======================

[![Build Status](https://secure.travis-ci.org/gabrielbull/php-sitesearch.png?branch=master)](http://travis-ci.org/gabrielbull/php-sitesearch)

Search engine for websites using Google Custom Search Engine with a Google Site Search account.

## Google Site Search API

To use the Google Site Search API, you have to [obtain a search engine ID from Google](https://www.google.com/cse/).


## Requirements

This library uses PHP 5.3+.

## Installation

It is recommended that you install the PHP Google Site Search library [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "gabrielbull/sitesearch": "dev-master"
    }
}
```

## Search

The SiteSearch class allows you to search Google Custom Search Engine. All you have to provide is a search engine ID and a query.

### Examples

```php
$id = "YOUR_SEARCH_ENGINE_ID";

$siteSearch = new SiteSearch($id);
$results = $siteSearch->search('kittens');

foreach($results as $result) {
    echo $result['title'];
}
```

or:

```php
$id = "YOUR_SEARCH_ENGINE_ID";

foreach(SiteSearch::create($id)->search('cats') as $result) {
    echo $result['title'];
}
```

### Parameters

The search method parameters are:

 * `value` The search query.
 * `start` The offset of the first result to return.
 * `limit` The number of results to return.

### Results

The search results will contain the following parameters:

 * `link` The link of the page.
 * `title` The title of the page.
 * `description` The description of the page.
 * `lang` The language of the page.
 * `image` An image associated with the page (src).
 * `thumbnail` A thumbnail of the image (src, width and height).
