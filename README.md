FormBundle
==========

This bundle introduces new twig blocks: `form_js`, `form_js_prototype` and `form_css`.

These blocks seperate form type's HTML structure from CSS decorating the form and JS enhancing it's functionalities. Also, the JS block includes `form_js_prototype` block, which holds any initialization logic, which should be called when adding a new field of that type.

If used properly, they make embeding collections of JS-enhanced form types very easy.

# 1. Installation

#### 1.1 Add dependency to your `composer.json`:

```json
"require": {
    "admingenerator/form-bundle": "@stable"
}
```

And run `composer update` in your project root directory.

#### 1.2 Enable the bundle in your `AppKernel.php`:

```php
<?php
// AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Admingenerator\FormBundle\AdmingeneratorFormBundle(),
    );
}
```

# 2. Usage

TODO

# 3. Acknowledgements

The twig extension is copied from [GenemuFormBundle](https://github.com/genemu/GenemuFormBundle/). The names of twig blocks have been changed to not cause conflicts.

The bundle is great, however for our purposes, we only need the Twig Extension, so we decided to extract it and put it into a seperate bundle.

# 4. License

This bundle is released under the [MIT License](LICENSE).
