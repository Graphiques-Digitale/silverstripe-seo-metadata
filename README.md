[![Build Status](https://travis-ci.org/Graphiques-Digitale/silverstripe-seo-metadata.svg?branch=master)](https://travis-ci.org/Graphiques-Digitale/silverstripe-seo-metadata) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Graphiques-Digitale/silverstripe-seo-metadata/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Graphiques-Digitale/silverstripe-seo-metadata/?branch=master)

## Overview ##

This is the core metadata module for the graphiques-digitale/silverstripe-seo-* module collection.

It enables enhanced **_title_** features, **_character set_** selection, **_canonical URLs_** and an enhanced fall-back **_description_** using `$Content.FirstParagraph()`.

Title inspired by: [http://moz.com/learn/seo/title-tag](http://moz.com/learn/seo/title-tag)

It is intended to be used with it's siblings:
* [`Graphiques-Digitale/silverstripe-seo-icons`][3]
* [`Graphiques-Digitale/silverstripe-seo-facebook-domain-insights`][4]
* [`Graphiques-Digitale/silverstripe-seo-open-graph`][5]

These are all optional and fragmented from the alpha version [`Graphiques-Digitale/SSSEO`][1], which is now redundant.

The whole module collection is based largely on [18 Meta Tags Every Webpage Should Have in 2013][6].

Also, a good overview: [5 tips for SEO with Silverstripe 3][7].

## Installation ##

#### Composer ####

* `composer require graphiques-digitale/silverstripe-seo-metadata`
* rebuild using `/dev/build/?flush`

#### From ZIP ####

* Place the extracted folder `silverstripe-seo-metadata-{version}` into `silverstripe-seo-metadata` in the SilverStripe webroot
* rebuild using `/dev/build/?flush`

## CMS Usage ##

See `/silverstripe-seo-metadata/_config/app.yml` for configuration.

Metadata is changed globally via `/admin/settings/` under the Metadata tab.

And also locally, per page, under their Metadata tab.

## Template Usage ##

Depending on your configuration, the general idea is to replace all header content relating to metadata with `$Metadata()` just below the opening `<head>` tag and `<% base_tag %>` include, e.g.:

```html
<head>
    <% base_tag %>
    $Metadata()
    <!-- further includes ~ viewport, etc. -->
</head>
```

This will output something along the lines of:

```html
<head>
    <base href="http://dev.seo.silverstripe.org/"><!--[if lte IE 6]></base><![endif]-->

    <!-- SEO -->
    <!-- Metadata -->
    <meta charset="UTF-8" />
    <link rel="canonical" href="http://dev.seo.silverstripe.org/" />
    <title>Your Site Name | Home - your tagline here</title>
    <meta name="description" content="Welcome to SilverStripe! This is the default home page. You can edit this page by opening the CMS. You can now access the developer documentation, or begin the tutorials." />
    <!-- END SEO -->

    <!-- further includes ~ viewport, etc. -->
</head>
```

## Advanced Usage ##

Please check the [`documentation`](https://github.com/Graphiques-Digitale/silverstripe-seo-metadata/tree/master/docs) folder for how to extend classes and more. 

## Issue Tracker ##

Issues are tracked on GitHub @ [Issue Tracker](https://github.com/Graphiques-Digitale/silverstripe-seo-metadata/issues)

## Development and Contribution ##

Please get in touch @ [`hello@graphiquesdigitale.net`](mailto:hello@graphiquesdigitale.net) if you have any extertise in any of these SEO module's areas and would like to help ~ they're a lot to maintain, they should be improved continually as HTML evolves and I'm sure they can generally be improved upon by field experts.

## License ##

BSD-3-Clause license

See @ [Why BSD?][8]


![Screenshot](screenshot-1.png)

![Screenshot](screenshot-2.png)

![Screenshot](screenshot-3.png)

![Screenshot](screenshot-4.png)


[1]: https://github.com/Graphiques-Digitale/SSSEO
[2]: https://github.com/Graphiques-Digitale/silverstripe-seo-metadata
[3]: https://github.com/Graphiques-Digitale/silverstripe-seo-icons
[4]: https://github.com/Graphiques-Digitale/silverstripe-seo-facebook-domain-insights
[5]: https://github.com/Graphiques-Digitale/silverstripe-seo-open-graph
[6]: https://www.iacquire.com/blog/18-meta-tags-every-webpage-should-have-in-2013
[7]: http://www.silverstripe.org/blog/5-tips-for-seo-with-silverstripe-3-/
[8]: https://www.silverstripe.org/blog/why-bsd/
