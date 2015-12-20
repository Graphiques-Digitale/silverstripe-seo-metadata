<?php

/**
 * Extends SiteTree with basic metadata fields, as well as the main `Metadata()` method.
 *
 * @package silverstripe-seo
 * @subpackage metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SEO_Metadata_SiteTree_DataExtension extends DataExtension
{


    /* Overload Variable
    ------------------------------------------------------------------------------*/

    private static $db = array(
        //
        'MetaTitle' => 'Varchar(128)',
        'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
        'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // CMS Fields
    public function updateCMSFields(FieldList $fields)
    {

        // variables
        $config = SiteConfig::current_site_config();
        $owner = $this->owner;

        // SEO Tabset
// 		$fields->addFieldToTab('Root', new TabSet('SEO'));

        // remove
        $fields->removeByName(array('Metadata'));

        //// Full Output

        $tab = 'Root.SEO.FullOutput';

// 		if ($owner->hasExtension('SEO_SchemaDotOrg_SiteTree_DataExtension')) {
// 			if ($head = $owner->Metahead()) {
// 				$fields->addFieldsToTab($tab, array(
// 					LiteralField::create('HeaderMetahead', '<pre class="bold">$Metahead()</pre>'),
// 					LiteralField::create('LiteralMetahead', '<pre><span style="background-color: white;">' . htmlentities($head) . '</span></pre>')
// 				));
// 			}
// 		}
        $fields->addFieldsToTab($tab, array(
            LiteralField::create('HeaderMetadata', '<pre class="bold">$Metadata()</pre>'),
            LiteralField::create('LiteralMetadata', '<pre>' . nl2br(htmlentities(trim($owner->Metadata()), ENT_QUOTES)) . '</pre>')
        ));

        //// Metadata

        $tab = 'Root.SEO.Metadata';

        // Canonical
        if ($config->CanonicalEnabled()) {
            $fields->addFieldsToTab($tab, array(
                ReadonlyField::create('ReadonlyMetaCanonical', 'link rel="canonical"', $owner->AbsoluteLink())
            ));
        }

        // Title
        if ($config->TitleEnabled()) {
            $fields->addFieldsToTab($tab, array(
                TextField::create('MetaTitle', 'meta title')
                    ->setAttribute('placeholder', $owner->GenerateTitle())
            ));
        }

        // Description
        $fields->addFieldsToTab($tab, array(
            TextareaField::create('MetaDescription', 'meta description')
                ->setAttribute('placeholder', $owner->GenerateDescriptionFromContent())
        ));

        // ExtraMeta
        if ($config->ExtraMetaEnabled()) {
            $fields->addFieldsToTab($tab, array(
                TextareaField::create('ExtraMeta', 'Custom Metadata')
            ));
        }
    }


    /* Template Methods
    ------------------------------------------------------------------------------*/

    /**
     * @name Metahead
     */
// 	public function Metahead() {

// 		$owner = $this->owner;
// 		$metadata = '';

// 		//// Schema.org

// 		if ($owner->hasExtension('SEO_SchemaDotOrg_SiteTree_DataExtension')) {
// 			$metadata .= $owner->SchemaDotOrgItemscope();
// 		}

// 		return $metadata;

// 	}

    /**
     * @name Metadata
     */
    public function Metadata()
    {

        // variables
        $config = SiteConfig::current_site_config();
        $owner = $this->owner;
        $metadata = PHP_EOL . $owner->MarkupHeader('SEO');

        //// Basic
        $metadata .= $owner->MarkupHeader('Metadata');

        // Charset
        if ($config->CharsetEnabled()) {
            $metadata .= '<meta charset="' . $config->Charset . '" />' . PHP_EOL;
        }

        // Canonical
        if ($config->CanonicalEnabled()) {
            $metadata .= $owner->MarkupRel('canonical', $owner->AbsoluteLink());
        }

        // Title
        if ($config->TitleEnabled()) {

            // ternary operation
            $title = ($owner->MetaTitle) ? $owner->MetaTitle : $owner->GenerateTitle();
            //
            $metadata .= '<title>' . htmlentities($title, ENT_QUOTES, $config->Charset) . '</title>' . PHP_EOL;
        }

        // Description
        $metadata .= $owner->Markup('description', $owner->GenerateDescription(), true, $config->Charset);

        //// ExtraMeta

        if ($config->ExtraMetaEnabled()) {
            if ($extraMeta = $owner->ExtraMeta != '') {
                $metadata .= $owner->MarkupHeader('Extra Metadata');
                $metadata .= $owner->ExtraMeta . PHP_EOL;
            }
        }

        ////

        $owner->extend('updateMetadata', $metadata, $owner, $config);

        // end
        $metadata .= $owner->MarkupHeader('END SEO');

        // return
        return $metadata;
    }


    /* Helper Methods
    ------------------------------------------------------------------------------*/

    /**
     * @name Markup (basic)
     */
    public function Markup($name, $content, $encode, $charset = 'UTF-8')
    {
        // encode content
        if ($encode) {
            $content = htmlentities($content, ENT_QUOTES, $charset);
        }
        // return
        return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
    }

    /**
     * @name Markup Header
     */
    public function MarkupHeader($title)
    {
        // return
        return '<!-- ' . $title . ' -->' . PHP_EOL;
    }

    /**
     * @name Markup Rel
     */
    public function MarkupRel($rel, $href, $type = null)
    {
        if ($type) {
            return '<link rel="' . $rel . '" href="' . $href . '" type="' . $type . '" />' . PHP_EOL;
        } else {
            return '<link rel="' . $rel . '" href="' . $href . '" />' . PHP_EOL;
        }
    }

    /**
     * @name Markup Facebook
     */
    public function MarkupFacebook($property, $content, $encode, $charset = 'UTF-8')
    {
        // encode content
        if ($encode) {
            $content = htmlentities($content, ENT_QUOTES, $charset);
        }
        //
        return '<meta property="' . $property . '" content="' . $content . '" />' . PHP_EOL;
    }

    /**
     * @name Markup Twitter
     */
    public function MarkupTwitter($name, $content, $encode, $charset = 'UTF-8')
    {
        // encode content
        if ($encode) {
            $content = htmlentities($content, ENT_QUOTES, $charset);
        }
        // return
        return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
    }

    /**
     * @name Markup Schema
     */
    public function MarkupSchema($itemprop, $content, $encode, $charset = 'UTF-8')
    {
        // encode content
        if ($encode) {
            $content = htmlentities($content, ENT_QUOTES, $charset);
        }
        // return
        return '<meta itemprop="' . $itemprop . '" content="' . $content . '" />' . PHP_EOL;
    }


    /* Meta Methods
    ------------------------------------------------------------------------------*/

    /**
     * @name MetaTitle
     */
    public function GenerateTitle()
    {
        $owner = $this->owner;

        // variables
        $config = SiteConfig::current_site_config();

        // collect title parts
        $titles = array();
        // Title WHERE TitlePosition = first
        if ($config->TitlePosition == 'first' && $config->Title) {
            $titleSeparator = ($config->TitleSeparator) ? $config->TitleSeparator : $config->titleSeparatorDefault();
            array_push($titles, $config->Title);
            array_push($titles, $titleSeparator);
        }
        // Title
        if ($owner->Title) {
            array_push($titles, $owner->Title);
        }
        // Tagline
        if ($config->Tagline) {
            $taglineSeparator = ($config->TaglineSeparator) ? $config->TaglineSeparator : $config->taglineSeparatorDefault();
            array_push($titles, $taglineSeparator);
            array_push($titles, $config->Tagline);
        }
        // Title WHERE TitlePosition = last
        if ($config->TitlePosition == 'last' && $config->Title) {
            $titleSeparator = ($config->TitleSeparator) ? $config->TitleSeparator : $config->titleSeparatorDefault();
            array_push($titles, $titleSeparator);
            array_push($titles, $config->Title);
        }

        // implode to create title
        $title = implode(' ', $titles);

        // return
        return $title;
    }

    /**
     * @name GenerateDescription
     * default limit: 155 characters
     */
    public function GenerateDescription()
    {

        //
        if ($this->owner->MetaDescription) {
            return $description = $this->owner->MetaDescription;
        } else {
            return $this->owner->GenerateDescriptionFromContent();
        }
    }

    /**
     * @name GenerateDescription
     * default limit: 155 characters
     */
    public function GenerateDescriptionFromContent()
    {

        // pillage content
        if ($content = trim($this->owner->Content)) {
            if (preg_match('/<p>(.*?)<\/p>/i', $content, $match)) {
                $content = $match[0];
            } else {
                $content = explode("\n", $content);
                $content = $content[0];
            }
            return trim(html_entity_decode(strip_tags($content)));
        } else {
            return false;
        }
    }
}
