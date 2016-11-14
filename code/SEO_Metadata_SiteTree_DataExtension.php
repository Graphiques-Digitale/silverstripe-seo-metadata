<?php
/**
 * Extends SiteTree with basic metadata fields, as well as the main `Metadata()` method.
 *
 * @package silverstripe-seo
 * @subpackage metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 */

/**
 * Class SEO_Metadata_SiteTree_DataExtension
 */
class SEO_Metadata_SiteTree_DataExtension extends DataExtension
{

    /* Overload Model
    ------------------------------------------------------------------------------*/

    /**
     * Database attributes.
     *
     * @var array $db
     */
    private static $db = array(
        'MetaTitle' => 'Varchar(128)',
        'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
        'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // @todo @inheritdoc ?? or does it happen automagically as promised?
    public function updateCMSFields(FieldList $fields)
    {
        // Variables
        $config = SiteConfig::current_site_config();

        // Remove framework default metadata group
        $fields->removeByName(array('Metadata'));

        //// SEO metadata
        $SEO = [];
        // Canonical
        if ($config->CanonicalEnabled()) {
            $SEO[] = ReadonlyField::create('ReadonlyMetaCanonical', 'link rel="canonical"', $this->owner->AbsoluteLink());
        }
        // Title
        if ($config->TitleEnabled()) {
            $SEO[] = TextField::create('MetaTitle', 'meta title')
                ->setAttribute('placeholder', $this->owner->GenerateTitle());
        }
        // Description
        $SEO[] = TextareaField::create('MetaDescription', 'meta description')
            ->setAttribute('placeholder', $this->owner->GenerateDescriptionFromContent());
        // ExtraMeta
        if ($config->ExtraMetaEnabled()) {
            $SEO[] = TextareaField::create('ExtraMeta', 'Custom Metadata');
        }
        // Add to fields
        $fields->addFieldsToTab('Root.Metadata.SEO', $SEO);

        //// Full output
        // monospaced, HTML SEO output
        $fields->addFieldsToTab('Root.Metadata.FullOutput', array(
            LiteralField::create('HeaderMetadata', '<pre class="bold">$Metadata()</pre>'),
            LiteralField::create('LiteralMetadata', '<pre>' . nl2br(htmlentities(trim($this->owner->Metadata()), ENT_QUOTES)) . '</pre>')
        ));
    }

    /**
     * Main function to format & output metadata as an HTML string.
     *
     * Use the `updateMetadata($config, $owner, $metadata)` update hook when extending `DataExtension`s.
     *
     * @return string
     */
    public function Metadata()
    {
        // variables
        $config = SiteConfig::current_site_config();
        $owner = $this->owner;

        // begin SEO
        $metadata = PHP_EOL . $owner->MarkupComment('SEO');

        // register extension update hook
        $owner->extend('updateMetadata', $config, $owner, $metadata);

        // end
        $metadata .= $owner->MarkupComment('END SEO');

        // return
        return $metadata;
    }


    /* Template Methods
    ------------------------------------------------------------------------------*/

    /**
     * Updates metadata fields.
     *
     * @param SiteConfig $config
     * @param SiteTree $owner
     * @param string $metadata
     *
     * @return void
     */
    public function updateMetadata(SiteConfig $config, SiteTree $owner, &$metadata)
    {
        // metadata
        $metadata .= $owner->MarkupComment('Metadata');

        // charset
        if ($config->CharsetEnabled()) {
            $metadata .= '<meta charset="' . $config->Charset() . '" />' . PHP_EOL;
        }

        // canonical
        if ($config->CanonicalEnabled()) {
            $metadata .= $owner->MarkupLink('canonical', $owner->AbsoluteLink());
        }

        // title
        if ($config->TitleEnabled()) {
            $metadata .= '<title>' . $owner->encodeContent($owner->GenerateTitle(), $config->Charset()) . '</title>' . PHP_EOL;
        }

        // description
        if ($description = $owner->GenerateDescription()) {
            $metadata .= $owner->MarkupMeta('description', $description, $config->Charset());
        }

        // extra metadata
        if ($config->ExtraMetaEnabled()) {
            $metadata .= $owner->MarkupComment('Extra Metadata');
            $metadata .= $owner->GenerateExtraMeta();
        }
    }


    /* Markup Methods
    ------------------------------------------------------------------------------*/

    /**
     * Returns a given string as a HTML comment.
     *
     * @var string $comment
     *
     * @return string
     */
    public function MarkupComment($comment)
    {
        return '<!-- ' . $comment . ' -->' . PHP_EOL;
    }

    /**
     * Returns markup for a HTML meta element. Can be flagged for encoding.
     *
     * @param string $name
     * @param string $content
     * @param null $encode
     *
     * @return string
     */
    public function MarkupMeta($name, $content, $encode = null)
    {
        if ($encode !== null) {
            return '<meta name="' . $name . '" content="' . $this->encodeContent($content, $encode) . '" />' . PHP_EOL;
        } else {
            return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
        }
    }

    /**
     * Returns markup for a HTML link element.
     *
     * @param string $rel
     * @param string $href
     * @param string $type
     * @param string $sizes
     *
     * @return string
     */
    public function MarkupLink($rel, $href, $type = '', $sizes = '')
    {
        // start fragment
        $return = '<link rel="' . $rel . '" href="' . $href . '"';
        // if type
        if ($type) {
            $return .= ' type="' . $type . '"';
        }
        // if sizes
        if ($sizes) {
            $return .= ' sizes="' . $sizes . '"';
        }
        // end fragment
        $return .= ' />' . PHP_EOL;
        // return
        return $return;
    }


    /* Generation Methods
    ------------------------------------------------------------------------------*/

    /**
     * Generates HTML title based on configuration settings.
     *
     * @return string|null
     */
    public function GenerateTitle()
    {
        if ($this->owner->MetaTitle) {
            return $this->owner->MetaTitle;
        } else {
            return SiteConfig::current_site_config()->GenerateTitle($this->owner->Title);
        }
    }

    /**
     * Generates description from the page `MetaDescription`, or the first paragraph of the `Content` attribute.
     *
     * @return string|null
     */
    public function GenerateDescription()
    {
        if ($this->owner->MetaDescription) {
            return $this->owner->MetaDescription;
        } else {
            return $this->owner->GenerateDescriptionFromContent();
        }
    }

    /**
     * Generates description from the first paragraph of the `Content` attribute.
     *
     * @return string|null
     */
    public function GenerateDescriptionFromContent()
    {
        // check for content
        if ($content = trim($this->owner->Content)) {
            // pillage first paragraph from page content
            if (preg_match('/<p>(.*?)<\/p>/i', $content, $match)) {
                // is HTML
                $content = $match[0];
            } else {
                // is plain text
                $content = explode(PHP_EOL, $content);
                $content = $content[0];
            }
            // decode (no harm done) & return
            return trim(html_entity_decode(strip_tags($content)));
        } else {
            return null;
        }
    }

    /**
     * Generates extra metadata.
     *
     * return string
     */
    public function GenerateExtraMeta()
    {
        if ($this->owner->ExtraMeta) {
            return $this->owner->ExtraMeta . PHP_EOL;
        } else {
            return $this->owner->MarkupComment('none');
        }
    }


    /* Utility Methods
    --------------------------------------------------------------------------*/

    /**
     * Returns a plain or HTML-encoded string according to the current charset & encoding settings.
     *
     * @param string $content
     * @param string $charset
     *
     * @return string
     */
    public function encodeContent($content, $charset)
    {
        return htmlentities($content, ENT_QUOTES, $charset);
    }

}
