## Overview

This is the base and basic metadata module for the graphiques-digitale/silverstripe-seo-* modules.

It enables enhanced **_title_** features, **_character set_** selection, **_canonical URLs_** and an enhanced fall-back **_description_** using `$Content.FirstParagraph`.

It is intended to be used with it's siblings:
* [`Icons`](https://github.com/Graphiques-Digitale/silverstripe-seo-icons)
* [`Authorship`](https://github.com/Graphiques-Digitale/silverstripe-seo-authorship)
* [`Facebook Insights`](https://github.com/Graphiques-Digitale/silverstripe-seo-facebook-insights)
* [`Open Graph`](https://github.com/Graphiques-Digitale/silverstripe-seo-open-graph)
* [`Twitter Cards`](https://github.com/Graphiques-Digitale/silverstripe-seo-twitter-cards)
* [`Schema.org`](https://github.com/Graphiques-Digitale/silverstripe-seo-schema-dot-org)

These are all optional and fragmented from the alpha version [`SSSEO`](https://github.com/Graphiques-Digitale/SSSEO), which is now redundant.

![Screenshot](screenshot-1.png)

## Installation ##

### Composer ###

* `composer require graphiques-digitale/silverstripe-seo-metadata`
* run `~/dev/build/?flush`

### From ZIP ###

* Place extracted folder "" `silverstripe-seo-metadata` in the SilverStripe webroot.
* run `~/dev/build/?flush`

## Template Usage ##

Depending on your configuration, the general idea is to replace all header content relating to metadata with the following `$Metadata()` just below the opening `<head>` tag and `$BaseHref()` function, e.g.:

```html
<head$Metahead()>
<% base_tag %>
$Metadata()
<!-- ++ any further includes ~ viewport, etc. -->
</head>
```

## Issue Tracker ##

Issues are tracked on GitHub @ [Issue Tracker](https://github.com/Graphiques-Digitale/silverstripe-seo-metadata/issues)

## Development and Contribution ##

Please get in touch @ [`hello@graphiquesdigitale.net`](mailto:hello@graphiquesdigitale.net) if you have any extertise in any of these SEO module's areas and would like to help. They're alot to maintain altogether.