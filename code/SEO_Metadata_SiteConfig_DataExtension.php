<?php

/**
 * Extends SiteConfig with basic metadata and status switches.
 *
 * @package SEO
 * @subpackage Metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 */

class SEO_Metadata_SiteConfig_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(

		//// Metadata Configuration
		// Charset
		'CharsetStatus' => 'Enum(array("off", "UTF-8", "ISO-8859-1"), "UTF-8")', // default: UTF-8
		// Canonical
		'CanonicalStatus' => 'Enum(array("off", "on"), "on")', // default: on
		// Title
		'TitleStatus' => 'Enum(array("off", "on"), "on")', // default: on
		// ExtraMeta
		'ExtraMetaStatus' => 'Enum(array("off", "on"), "off")', // default: off

		//// Metadata Values
		// Charset
		'Charset' => 'Enum(array("UTF-8"), "UTF-8")',
		// Title
		'Title' => 'Text', // redundant, but included for backwards-compatibility
		'TitleSeparator' => 'Varchar(1)',
		'Tagline' => 'Text', // redundant, but included for backwards-compatibility
		'TaglineSeparator' => 'Varchar(1)',
		'TitlePosition' => 'Enum(array("first", "last"), "first")',

	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// owner
		$owner = $this->owner;

		// SEO Tabset
		$fields->addFieldToTab('Root', new TabSet('SEO'));

		//// Configuration
		$tab = 'Root.SEO.Configuration';

		// header
		$fields->addFieldToTab($tab, HeaderField::create('MetadataHeader', 'Metadata'));

		// fields
		$fields->addFieldsToTab($tab, array(
			// Charset
			DropdownField::create('CharsetStatus', 'Character Set', $owner->dbObject('CharsetStatus')->enumValues())
				->setDescription('output: meta charset'),
			// Canonical
			DropdownField::create('CanonicalStatus', 'Canonical Pages', $owner->dbObject('CanonicalStatus')->enumValues())
				->setDescription('output: link rel="canonical"'),
			// Title
			DropdownField::create('TitleStatus', 'Title', $owner->dbObject('TitleStatus')->enumValues())
				->setDescription('output: meta title'),
			// ExtraMeta
			DropdownField::create('ExtraMetaStatus', 'Custom Metadata', $owner->dbObject('ExtraMetaStatus')->enumValues())
				->setDescription('allow custom metadata on pages<br />please ensure metadata content is entity encoded!') // @todo entity encode content="%s"
		));

		//// Title

		if ($this->TitleEnabled()) {

			// remove
			// @todo move existing fields, don't recreate them
// 			$fields->removeByName(array('Title', 'Tagline'));

			$tab = 'Root.SEO.Title';

			// add
			$fields->addFieldsToTab($tab, array(
				// Title
				TextField::create('Title', 'Title'),
				// TitleSeparator
				TextField::create('TitleSeparator', 'Title Separator')
					->setAttribute('placeholder', $this->titleSeparatorDefault())
					->setAttribute('size', 1)
					->setMaxLength(1)
					->setDescription('character limit: 1'),
				// Tagline
				TextField::create('Tagline', 'Tagline')
					->setDescription('optional'),
				// TaglineSeparator
				TextField::create('TaglineSeparator', 'Tagline Separator')
					->setAttribute('placeholder', $this->taglineSeparatorDefault())
					->setAttribute('size', 1)
					->setMaxLength(1)
					->setDescription('character limit: 1'),
				// TitlePosition
				DropdownField::create('TitlePosition', 'Title Position', $owner->dbObject('TitlePosition')->enumValues())
					->setDescription('first: <u>Title</u> | Page - Tagline' . '<br />' . 'last: Page - Tagline | <u>Title</u>')
			));
		}

	}


	/* Static Variables
	------------------------------------------------------------------------------*/

	private static $TitleSeparatorDefault = '|';
	private static $TaglineSeparatorDefault = '-';
	private static $FaviconBGDefault = 'FFFFFF';


	/* Static Accessors
	------------------------------------------------------------------------------*/

	//
	public function titleSeparatorDefault() {
		return self::$TitleSeparatorDefault;
	}

	//
	public function taglineSeparatorDefault() {
		return self::$TaglineSeparatorDefault;
	}

	//
	public function faviconBGDefault() {
		return self::$FaviconBGDefault;
	}


	/* Accessor Methods
	------------------------------------------------------------------------------*/

	//
	public function CharsetEnabled() {
		return ($this->owner->CharsetStatus == 'off') ? false : true;
	}

	//
	public function CanonicalEnabled() {
		return ($this->owner->CanonicalStatus == 'off') ? false : true;
	}

	//
	public function TitleEnabled() {
		return ($this->owner->TitleStatus == 'off') ? false : true;
	}

	//
	public function ExtraMetaEnabled() {
		return ($this->owner->ExtraMetaStatus == 'off') ? false : true;
	}

}
