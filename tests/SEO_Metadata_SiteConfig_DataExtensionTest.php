<?php

use \SilverStripe\Dev\SapphireTest;

//
class SEO_Metadata_SiteConfig_DataExtensionTest extends SapphireTest {

    //
    public static $fixture_file = 'SEO_Metadata_SiteConfig_DataExtensionTest.yml';

    public function testFetchTitleSeparator() {
        $siteConfig = $this->objFromFixture('SiteConfig', 'Main');
        $this->assertEquals('1', $siteConfig->FetchTitleSeparator());
    }

    public function testFetchTaglineSeparator() {
        $siteConfig = $this->objFromFixture('SiteConfig', 'Main');
        $this->assertEquals('2', $siteConfig->FetchTaglineSeparator());
    }

    public function testGenerateTitle() {
        $siteConfig = $this->objFromFixture('SiteConfig', 'Main');
        $this->assertEquals('Page Title 1 Test Site 2 test tagline', $siteConfig->GenerateTitle('Page Title'));
    }
}
