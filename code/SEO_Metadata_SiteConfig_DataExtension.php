<?php
/**
 * Adds enhanced HTML SEO metadata.
 *
 * @package SEO
 * @subpackage Metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 */

/**
 * Class SEO_Metadata_SiteConfig_DataExtension
 *
 * Adds additional statuses and defaults to control metadata output.
 */
class SEO_Metadata_SiteConfig_DataExtension extends DataExtension
{

    /* Attributes
    ------------------------------------------------------------------------------*/

    //// statuses

    /**
     * Character set status.
     *
     * Boolean value governing whether the character set is output.
     *
     * @var bool $CharsetStatus
     */
    private static $CharsetStatus = false;

    /**
     * `rel="canonical"` status.
     *
     * Boolean value governing whether canonical links are output.
     *
     * @var bool $CanonicalStatus
     */
    private static $CanonicalStatus = false;

    /**
     * Title status.
     *
     * Boolean value governing whether the page title should be output.
     *
     * @var bool $TitleStatus
     */
    private static $TitleStatus = false;

    /**
     * Extra metadata status.
     *
     * Boolean value governing whether additional (arbitrary) metadata can be added to pages.
     *
     * @var bool $ExtraMetaStatus
     */
    private static $ExtraMetaStatus = false;

    //// defaults

    /**
     * Character set.
     *
     * The character set to be used. Should always be `UTF-8` except for fringe configurations.
     *
     * @var string
     */
    private static $Charset = 'UTF-8';

    /**
     * Default title separator.
     *
     * The default title (primary) separator.
     *
     * @var string
     */
    private static $TitleSeparatorDefault = '|';

    /**
     * Default tagline separator.
     *
     * The default tagline (secondary) separator.
     *
     * @var string
     */
    private static $TaglineSeparatorDefault = '-';

    /**
     * Title ordering options.
     *
     * @var array
     */
    protected static $TitleOrderOptions = array(
        'first' => 'Page Title | Website Name - Tagline',
        'last' => 'Website Name - Tagline | Page Title'
    );


    /* Status Methods
    ------------------------------------------------------------------------------*/

    /**
     * Character set enabled.
     *
     * Gets whether the character set should be output.
     *
     * @return bool
     */
    public function CharsetEnabled()
    {
        return ($this->owner->config()->CharsetStatus === true) ? true : self::$CharsetStatus;
    }

    /**
     * Canonical links enabled.
     *
     * Gets whether the canonical link should be output.
     *
     * @return bool
     */
    public function CanonicalEnabled()
    {
        return ($this->owner->config()->CanonicalStatus === true) ? true : self::$CanonicalStatus;
    }

    /**
     * Title enabled.
     *
     * Gets whether the title should be output.
     *
     * @return bool
     */
    public function TitleEnabled()
    {
        return ($this->owner->config()->TitleStatus === true) ? true : self::$TitleStatus;
    }

    /**
     * Extra metadata enabled.
     *
     * Gets whether additional (arbitrary) metadata should be output.
     *
     * @return bool
     */
    public function ExtraMetaEnabled()
    {
        return ($this->owner->config()->ExtraMetaStatus === true) ? true : self::$ExtraMetaStatus;
    }


    /* Config Methods
    ------------------------------------------------------------------------------*/

    /**
     * Character set.
     *
     * Gets the character set from configuration, or uses the class-defined default.
     *
     * @return string
     */
    public function Charset()
    {
        return ($this->owner->config()->Charset) ? $this->owner->config()->Charset : self::$Charset;
    }


    /* Overload Model
    ------------------------------------------------------------------------------*/

    /**
     * Database fields.
     *
     * An associative array of database fields: `name` => `type`.
     *
     * @var array $db
     */
    private static $db = array(
        'TitleOrder' => 'Enum(array("first", "last"), "first")',
        'Title' => 'Text', // redundant, but included for backwards-compatibility
        'TitleSeparator' => 'Varchar(1)',
        'Tagline' => 'Text', // redundant, but included for backwards-compatibility
        'TaglineSeparator' => 'Varchar(1)',
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // @todo @inheritdoc ?? or does it happen automagically as promised?
    public function updateCMSFields(FieldList $fields)
    {
        // Tab Set
        $fields->addFieldToTab('Root', new TabSet('Metadata'), 'Access');

        // Title
        if ($this->TitleEnabled()) {
            $fields->addFieldsToTab('Root.Metadata.Title', $this->owner->getTitleFields());
        }
    }


    /* Custom Methods
    ------------------------------------------------------------------------------*/

    /**
     * Gets the title fields.
     *
     * This approach for getting fields for updateCMSFields is to be duplicated through all other modules to reduce complexity.
     *
     * @TODO i18n implementation
     *
     * @return array
     */
    public function getTitleFields() {
        return array(
            // Information
            LabelField::create('FaviconDescription', 'A title tag is the main text that describes an online document. Title elements have long been considered one of the most important on-page SEO elements (the most important being overall content), and appear in three key places: browsers, search engine results pages, and external websites.<br />@ <a href="https://moz.com/learn/seo/title-tag" target="_blank">Title Tag - Learn SEO - Mozilla</a>')
                ->addExtraClass('information'),
            // Title Order
            DropdownField::create('TitleOrder', 'Page Title Order', self::$TitleOrderOptions),
            // Title Separator
            TextField::create('TitleSeparator', 'Page Title Separator')
                ->setAttribute('placeholder', self::$TitleSeparatorDefault)
                ->setAttribute('size', 1)
                ->setMaxLength(1)
                ->setDescription('max 1 character'),
            // Title
            TextField::create('Title', 'Website Name'),
            // Tagline Separator
            TextField::create('TaglineSeparator', 'Tagline Separator')
                ->setAttribute('placeholder', self::$TaglineSeparatorDefault)
                ->setAttribute('size', 1)
                ->setMaxLength(1)
                ->setDescription('max 1 character'),
            // Tagline
            TextField::create('Tagline', 'Tagline')
                ->setDescription('optional')
        );
    }


    /* Custom Methods
    ------------------------------------------------------------------------------*/

    /**
     * Fetch title separator.
     *
     * Fetches the title (primary) separator, falls back to default.
     *
     * @return string
     */
    public function FetchTitleSeparator()
    {
        return ($this->owner->TitleSeparator) ? $this->owner->TitleSeparator : self::$TitleSeparatorDefault;
    }

    /**
     * Fetch tagline separator.
     *
     * Fetches the tagline (secondary) separator, falls back to default.
     *
     * @return string
     */
    public function FetchTaglineSeparator()
    {
        return ($this->owner->TaglineSeparator) ? $this->owner->TaglineSeparator : self::$TaglineSeparatorDefault;
    }

    /**
     * Generates HTML title based on configuration settings.
     *
     * @dev Override this function for any custom title functionality.
     *
     * @param string $pageTitle
     *
     * @return string
     */
    public function GenerateTitle($pageTitle = 'Title Error')
    {
        // if there is a site name
        if ($this->owner->Title) {

            // title parts, begin with name/title
            $titles = array($this->owner->Title);

            // tagline
            if ($this->owner->Tagline) {
                array_push($titles, $this->owner->FetchTaglineSeparator());
                array_push($titles, $this->owner->Tagline);
            }

            // page title
            if ($this->owner->TitleOrder == 'first') {
                // add to the beginning
                array_unshift($titles, $this->owner->FetchTitleSeparator());
                array_unshift($titles, $pageTitle);
            } else {
                // add to the end
                array_push($titles, $this->owner->FetchTitleSeparator());
                array_push($titles, $pageTitle);
            }

            // implode to create title
            $title = implode(' ', $titles);

            // removes whitespace before punctuation marks: `,.;:!?`
            // @todo isn't this a little bit random ?
            $title = preg_replace('/\s*[,.;:!?]/', '', $title);

            // return
            return $title;

        } else {
            // just return the page title if there is no site name
            return $pageTitle;
        }
    }

}
