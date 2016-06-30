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


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(
		'MetaTitle' => 'Varchar(128)',
		'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
		'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
	);

	//// testing
	protected $MetaConfig;
	protected $MetaCharset;

	public function __construct()
	{
		//
		parent::__construct();
		//
		$this->MetaConfig = SiteConfig::current_site_config();
		$this->MetaCharset = $this->MetaConfig->Charset;
	}


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// variables
		$config = SiteConfig::current_site_config();
		$owner = $this->owner;

		// remove framework default fields
		$fields->removeByName(array('Metadata'));

		//// Metadata
		$tab = 'Root.Metadata.SEO';

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

		//// Full Output

		$tab = 'Root.Metadata.FullOutput';

		// monospaced, HTML SEO output
		$fields->addFieldsToTab($tab, array(
			LiteralField::create('HeaderMetadata', '<pre class="bold">$Metadata()</pre>'),
			LiteralField::create('LiteralMetadata', '<pre>' . nl2br(htmlentities(trim($owner->Metadata()), ENT_QUOTES)) . '</pre>')
		));

	}

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

		// begin SEO
		$metadata = PHP_EOL . $owner->MarkupComment('SEO');

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

			// ternary operation
			// @todo Check what is going here ?!
			$title = ($owner->MetaTitle) ? $owner->MetaTitle : $owner->GenerateTitle();
			//
			$metadata .= '<title>' . htmlentities($title, ENT_QUOTES, $config->Charset()) . '</title>' . PHP_EOL;

		}

		// description
		$metadata .= $owner->MarkupMeta('description', $owner->GenerateDescription(), true, $config->Charset());

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
	 *
	 * @return string
	 */
	public function MarkupMeta($name, $content, $encode = false) {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $this->owner->Charset);
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
	 *
	 * @return string
	 */
	public function MarkupFacebook($property, $content, $encode = true) {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $this->owner->Charset);
		// format & return
		return '<meta property="' . $property . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * Returns markup for a Twitter Cards meta element.
	 *
	 * @var $name
	 * @var $content
	 * @var $encode
	 *
	 * @return string
	 */
	public function MarkupTwitter($name, $content, $encode = true) {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $this->owner->Charset);
		// format & return
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * Returns markup for a Schema.org meta element.
	 *
	 * @var $itemprop
	 * @var $content
	 * @var $encode
	 *
	 * @return string
	 */
	public function MarkupSchema($itemprop, $content, $encode = true) {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $this->owner->Charset);
		// format & return
		return '<meta itemprop="' . $itemprop . '" content="' . $content . '" />' . PHP_EOL;
	}


	/* Meta Methods
	------------------------------------------------------------------------------*/

	/**
	 * Generates HTML title based on configuration settings.
	 *
	 * @return bool|string
	 */
	public function GenerateTitle() {

		// return SEO title or false
		return SiteConfig::current_site_config()->GenerateTitle($this->owner->Title);

	}

	/**
	 * Returns description from the page `MetaDescription`, or the first paragraph of the `Content` attribute.
	 *
	 * @return bool|string
	 */
	public function GenerateDescription() {

		if ($this->owner->MetaDescription) {
			return $this->owner->MetaDescription;
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

		if ($content = trim($this->owner->Content)) {

			// pillage first paragraph from page content
			if (preg_match( '/<p>(.*?)<\/p>/i', $content, $match)) {
				// is HTML
				$content = $match[0];
			} else {
				// is plain text
				$content = explode("\n", $content);
				$content = $content[0];
			}

			// decode (no harm done) & return
			return trim(html_entity_decode(strip_tags($content)));

		} else {
			// none
			return false;
		}

	}

}
