<?php

namespace SiteSearch\Tests;
use PHPUnit_Framework_TestCase;
use SiteSearch;

class SiteSearchTest extends PHPUnit_Framework_TestCase
{
    protected $googleSiteSearchID;

    protected function setUp()
    {
        if (isset($GLOBALS['GOOGLE_SITE_SEARCH_IDENTIFIER'])) {
            $this->googleSiteSearchID = $GLOBALS['GOOGLE_SITE_SEARCH_IDENTIFIER'];
        }
    }

    public function testSearch()
    {
        // No point in automatically testing these as we need a private key
        if ($this->googleSiteSearchID !== 'xxxxxxxxxxxxxx') {
            $result = SiteSearch::create($this->googleSiteSearchID)->search('test');

            $this->assertGreaterThan(0, count($result), 'SiteSearch::search() did get any result for the query "test"');
        }
    }

    public function testGetRawResponse()
    {
        // No point in automatically testing these as we need a private key
        if ($this->googleSiteSearchID !== 'xxxxxxxxxxxxxx') {
            $q = new SiteSearch($this->googleSiteSearchID);
            $q->search('hello');
            $response = $q->getResponse();

            $this->assertRegExp('/<Q>hello<\/Q>/', $response, 'SiteSearch::search() response is not a valid Google Custom Search response');
        }
    }
}
