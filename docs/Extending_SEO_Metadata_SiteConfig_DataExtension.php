<?php

class MySEO_Metadata_SiteConfig_DataExtension extends SEO_Metadata_SiteConfig_DataExtension {

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
