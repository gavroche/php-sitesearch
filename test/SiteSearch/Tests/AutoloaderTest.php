<?php

namespace SiteSearch\Tests;
use PHPUnit_Framework_TestCase;
use SiteSearch;
use SiteSearch\Autoloader;

class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $this->assertNull(Autoloader::autoload('Foo'), 'SiteSearch\\Autoloader::autoload() is trying to load classes outside of the SiteSearch namespace');
        $this->assertTrue(Autoloader::autoload('SiteSearch'), 'SiteSearch\\Autoloader::autoload() failed to autoload the SiteSearch class');
    }
}
