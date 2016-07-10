## Extending SEO_Metadata_SiteConfig_DataExtension ##

Classes can be manipulated by extending them and using `Injector` to replace the original.

### 1. Create An Extension ###

In `/mysite/code/MySEO_Metadata_SiteConfig_DataExtension.php`

```php
class MySEO_Metadata_SiteConfig_DataExtension extends SEO_Metadata_SiteConfig_DataExtension {

    /**
     * It'd be far better to override config variables via config.yml,
     * but it can be done here
     */
    private static $ExtraMetaStatus = true;

    /**
     * Override the GenerateTitle function
     *
     * @return string
     */
    public function GenerateTitle($pageTitle = "My Static App Title Failed") {
        // i.e. return a static title
        return 'My Static App Title';
    }

}
```

### 2. Replace Existing Class With Extension Using Injector ###

In `/mysite/code/_config/config.yml`

```yml
Injector:
  SEO_Metadata_SiteConfig_DataExtension
    class: MySEO_Metadata_SiteConfig_DataExtension
```
