## Extending SEO_Metadata_SiteTree_DataExtension ##

Classes can be manipulated by extending them and using `Injector` to replace the original.

### 1. Create An Extension ###

In `/mysite/code/MySEO_Metadata_SiteTree_DataExtension.php`

```php
class MySEO_Metadata_SiteTree_DataExtension extends SEO_Metadata_SiteTree_DataExtension {

    /**
     * Override the GenerateDescription function
     *
     * @return string
     */
    public function GenerateDescription()
    {

        if ($this->owner->MetaDescription) {
            return $this->owner->MetaDescription;
        } else {
            return "Could not find meta description.";
        }

    }

}
```

### 2. Replace Existing Class With Extension Using Inject ###

In `/mysite/code/_config/config.yml`

```yml
Injector:
  SEO_Metadata_SiteTree_DataExtension
    class: MySEO_Metadata_SiteTree_DataExtension
```
