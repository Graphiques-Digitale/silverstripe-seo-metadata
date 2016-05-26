<?php

/**
 * Extends SiteTree with basic metadata fields, as well as the main `Metadata()` method.
 *
 * @package silverstripe-seo
 * @subpackage metadata
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 */

class SEO_Metadata_SiteTree_DataExtension extends DataExtension {


	/* Overload Variable
	------------------------------------------------------------------------------*/

	private static $db = array(
		'MetaTitle' => 'Varchar(128)',
		'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
		'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// variables
		$config = SiteConfig::current_site_config();
		$owner = $this->owner;

		// remove framework default fields
		$fields->removeByName(array('Metadata'));

		//// Full Output

		$tab = 'Root.SEO.FullOutput';

		/**
		 * @todo Schema.org integration
		 */
// 		if ($owner->hasExtension('SEO_SchemaDotOrg_SiteTree_DataExtension')) {
// 			if ($head = $owner->Metahead()) {
// 				$fields->addFieldsToTab($tab, array(
// 					LiteralField::create('HeaderMetahead', '<pre class="bold">$Metahead()</pre>'),
// 					LiteralField::create('LiteralMetahead', '<pre><span style="background-color: white;">' . htmlentities($head) . '</span></pre>')
// 				));
// 			}
// 		}

		// monospaced, HTML SEO output
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
	 * @todo Schema.org integration
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
	 * Main function to format & output metadata as an HTML string.
	 *
	 * Use the `updateMetadata($config, $owner, $metadata)` update hook when extending `DataExtension`s.
	 *
	 * @return string
	 */
	public function Metadata() {

		// variables
		$config = SiteConfig::current_site_config();
		$owner = $this->owner;
		$metadata = PHP_EOL . $owner->MarkupComment('SEO');

		//// basic
		$metadata .= $owner->MarkupComment('Metadata');

		// charset
		if ($config->CharsetEnabled()) {
			$metadata .= '<meta charset="' . $config->Charset . '" />' . PHP_EOL;
		}

		// canonical
		if ($config->CanonicalEnabled()) {
			$metadata .= $owner->MarkupLink('canonical', $owner->AbsoluteLink());
		}

		// title
		if ($config->TitleEnabled()) {

			// ternary operation
			$title = ($owner->MetaTitle) ? $owner->MetaTitle : $owner->GenerateTitle();
			//
			$metadata .= '<title>' . htmlentities($title, ENT_QUOTES, $config->Charset) . '</title>' . PHP_EOL;

		}

		// description
		$metadata .= $owner->MarkupMeta('description', $owner->GenerateDescription(), true, $config->Charset);

		//// ExtraMeta

		if ($config->ExtraMetaEnabled()) {
			if ($extraMeta = $owner->ExtraMeta != '') {
				$metadata .= $owner->MarkupComment('Extra Metadata');
				$metadata .= $owner->ExtraMeta . PHP_EOL;
			}
		}

		//// extension update hook
		$owner->extend('updateMetadata', $config, $owner, $metadata);

		// end
		$metadata .= $owner->MarkupComment('END SEO');

		// return
		return $metadata;

	}


	/* Helper Methods
	------------------------------------------------------------------------------*/

	/**
	 * Returns a given string as a HTML comment.
	 *
	 * @var string $comment
	 *
	 * @return string
	 */
	public function MarkupComment($comment) {
		// return
		return '<!-- ' . $comment . ' -->' . PHP_EOL;
	}

	/**
	 * Returns markup for a HTML meta element.
	 *
	 * @var string $name
	 * @var string $content
	 * @var bool $encode
	 * @var string $charset
	 *
	 * @return string
	 */
	public function MarkupMeta($name, $content, $encode = false, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
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
	public function MarkupLink($rel, $href, $type = '', $sizes = '') {
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

	/**
	 * Returns markup for an Open Graph meta element.
	 *
	 * @var $property
	 * @var $content
	 * @var $encode
	 * @var $charset
	 *
	 * @return string
	 */
	public function MarkupFacebook($property, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		//
		return '<meta property="' . $property . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * Returns markup for a Twitter Cards meta element.
	 *
	 * @var $name
	 * @var $content
	 * @var $encode
	 * @var $charset
	 *
	 * @return string
	 */
	public function MarkupTwitter($name, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * Returns markup for a Schema.org meta element.
	 *
	 * @var $itemprop
	 * @var $content
	 * @var $encode
	 * @var $charset
	 *
	 * @return string
	 */
	public function MarkupSchema($itemprop, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta itemprop="' . $itemprop . '" content="' . $content . '" />' . PHP_EOL;
	}


	/* Meta Methods
	------------------------------------------------------------------------------*/

	/**
	 * Generates HTML title based on configuration settings.
	 *
	 * @return string
	 */
	public function GenerateTitle() {

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
	 * Returns description from the page `MetaDescription`, or the first paragraph of the `Content` attribute.
	 *
	 * @return bool|string
	 */
	public function GenerateDescription() {

		//
		if ($this->owner->MetaDescription) {
			return $description = $this->owner->MetaDescription;
		} else {
			return $this->owner->GenerateDescriptionFromContent();
		}

	}

	/**
	 * Generates description from the first paragraph of the `Content` attribute.
	 *
	 * @return bool|string
	 */
	public function GenerateDescriptionFromContent() {

		// pillage content
		if ($content = trim($this->owner->Content)) {
			if (preg_match( '/<p>(.*?)<\/p>/i', $content, $match)) {
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
