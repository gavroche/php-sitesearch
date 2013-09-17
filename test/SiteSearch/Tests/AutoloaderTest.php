<?php

namespace SiteSearch\Tests;
use PHPUnit_Framework_TestCase;

class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $this->assertNull(SiteSearch\Autoloader::autoload('Foo'), 'SiteSearch\\Autoloader::autoload() is trying to load classes outside of the SiteSearch namespace');
        $this->assertNotNull(SiteSearch\Autoloader::autoload('SiteSearch\\SiteSearch'), 'SiteSearch\Autoloader::autoload() failed to autoload the SiteSearch\\SiteSearch class');
    }
}
