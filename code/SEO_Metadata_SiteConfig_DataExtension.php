<?php

/**
 * Adds enhanced HTML SEO metadata.
 *
 * @package SEO
 * @subpackage Metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 */
class SEO_Metadata_SiteConfig_DataExtension extends DataExtension
{


    /* Static Variables
    ------------------------------------------------------------------------------*/

    // status variables
    private static $CharsetStatus = false;
    private static $CanonicalStatus = false;
    private static $TitleStatus = false;
    private static $ExtraMetaStatus = false;

    // defaults
    private static $Charset = 'UTF-8';
    private static $TitleSeparatorDefault = '|';
    private static $TaglineSeparatorDefault = '-';


    /* Status Methods
    ------------------------------------------------------------------------------*/

    /**
     * @return bool
     */
    public function CharsetEnabled()
    {
        return ($this->owner->config()->CharsetStatus === true) ? true : self::$CharsetStatus;
    }

    /**
     * @return bool
     */
    public function CanonicalEnabled()
    {
        return ($this->owner->config()->CanonicalStatus === true) ? true : self::$CanonicalStatus;
    }

    /**
     * @return bool
     */
    public function TitleEnabled()
    {
        return ($this->owner->config()->TitleStatus === true) ? true : self::$TitleStatus;
    }

    /**
     * @return bool
     */
    public function ExtraMetaEnabled()
    {
        return ($this->owner->config()->ExtraMetaStatus === true) ? true : self::$ExtraMetaStatus;
    }


    /* Config Methods
    ------------------------------------------------------------------------------*/

    /**
     * @return string
     */
    public function Charset()
    {
        return ($this->owner->config()->Charset) ? $this->owner->config()->Charset : self::$Charset;
    }


    /* Overload Model
    ------------------------------------------------------------------------------*/

    private static $db = array(
        'TitleOrder' => 'Enum(array("first", "last"), "first")',
        'Title' => 'Text', // redundant, but included for backwards-compatibility
        'TitleSeparator' => 'Varchar(1)',
        'Tagline' => 'Text', // redundant, but included for backwards-compatibility
        'TaglineSeparator' => 'Varchar(1)',
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // CMS Fields
    public function updateCMSFields(FieldList $fields)
    {

        // Tab Set
        $fields->addFieldToTab('Root', new TabSet('Metadata'), 'Access');

        //// Title

        if ($this->TitleEnabled()) {

            // Tab
            $tab = 'Root.Metadata.Title';

            // Title Order Options
            $titleOrderOptions = array(
                'first' => 'Page Title | Website Name - Tagline',
                'last' => 'Website Name - Tagline | Page Title'
            );

            // Fields
            $fields->addFieldsToTab($tab, array(
                // Information
                LabelField::create('FaviconDescription', 'A title tag is the main text that describes an online document. Title elements have long been considered one of the most important on-page SEO elements (the most important being overall content), and appear in three key places: browsers, search engine results pages, and external websites.<br />@ <a href="https://moz.com/learn/seo/title-tag" target="_blank">Title Tag - Learn SEO - Mozilla</a>')
                    ->addExtraClass('information'),
                // Title Order
                DropdownField::create('TitleOrder', 'Page Title Order', $titleOrderOptions),
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
            ));
        }

    }


    /* Custom Methods
    ------------------------------------------------------------------------------*/

    /**
     * Fetches the title separator, falls back to default
     *
     * @return string
     */
    public function FetchTitleSeparator()
    {

        return ($this->owner->TitleSeparator) ? $this->owner->TitleSeparator : self::$TitleSeparatorDefault;

    }

    /**
     * Fetches the tagline separator, falls back to default
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
     * @param string $pageTitle
     *
     * @return string
     */
    public function GenerateTitle($pageTitle = 'Title Error')
    {

        // variables
        $owner = $this->owner;

        if ($owner->Title) {

            // title parts, begin with name/title
            $titles = array($owner->Title);

            // tagline
            if ($owner->Tagline) {
                array_push($titles, $owner->FetchTaglineSeparator());
                array_push($titles, $owner->Tagline);
            }

            // page title
            if ($owner->TitleOrder == 'first') {
                // add to the beginning
                array_unshift($titles, $owner->FetchTitleSeparator());
                array_unshift($titles, $pageTitle);
            } else {
                // add to the end
                array_push($titles, $owner->FetchTitleSeparator());
                array_push($titles, $pageTitle);

            }

            // implode to create title
            $title = implode(' ', $titles);

            // @todo remove whitespace before certain characters e.g. `,` `.` `;` `:`
            //
            $title = preg_replace('/\s*[,.:]/', '', $title);

            // return
            return $title;

        } else {

            // just return the page title if there is no name
            return $pageTitle;

        }

    }

}
