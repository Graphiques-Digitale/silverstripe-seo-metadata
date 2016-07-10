<?php

class MySEO_Metadata_SiteConfig_DataExtension extends SEO_Metadata_SiteConfig_DataExtension {

    /**
     * It'd be far better to override config variables via config.yml,
     * but it can be done here
     */
    private static $ExtraMetaStatus = true;

    /**
     * Override the GenerateTitle function
     *
     * @return string
     */
    public function GenerateTitle($pageTitle = "My Static App Title Failed") {
        // i.e. return a static title
        return 'My Static App Title';
    }

}
