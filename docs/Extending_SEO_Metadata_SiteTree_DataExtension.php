<?php

class MySEO_Metadata_SiteTree_DataExtension extends SEO_Metadata_SiteTree_DataExtension {

    /**
     * Override the GenerateDescription function
     *
     * @return string
     */
    public function GenerateDescription()
    {

        if ($this->owner->MetaDescription) {
            return $this->owner->MetaDescription;
        } else {
            return "Could not find meta description.";
        }

    }

}
