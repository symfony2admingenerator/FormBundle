FormBundle
==========

This bundle introduces new twig blocks: `form_js`, `form_js_prototype` and `form_css`.

These blocks seperate form type's HTML structure from CSS decorating the form and JS enhancing it's functionalities. Also, the JS block includes `form_js_prototype` block, which holds any initialization logic, which should be called when adding a new field of that type.

If used properly, they make embeding collections of JS-enhanced form types very easy.

# 1. Installation

#### 1.1 Add dependency to your `composer.json`:

```json
"require": {
    "symfony2admingenerator/form-bundle": "@stable"
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
?>
```

# 2. Usage

This bundle adds new twig blocks for your form. In Symfony2 for form type aliased `my_form` you have one block `my_form_widget` where you put everything related to your form type. And that's totally fine for default Symfony2 form types, as they include only basic form elements.

To introduce nice GUI form widgets, we need to style them with CSS and enhance their functionality with JS. So, for these purposes, this bundle introduces new blocks. In our example, that would be `my_form_css` and `my_form_js`.

### Javascript enhanced forms in collections

To make implementing add/remove items to collection of forms easier, we've also added a javascript prototype block, which resides inside the form javascript block. Example:

```html+django
{% block my_form_js %}
<script type="text/javascript">
	// note: this example uses jQuery
	$field = $("#{{ id }}");

	{% block my_form_js_prototype %}
		// add thick red border to the field
		$field.css({
			'border-color': 'red',
			'border-width': '10px',
			'border-style': 'solid'
		});
	{% block}
</script>
{% endblock %}
```

This way we've seperated the javascript selector code from widget initialization code, which now can be used in our collection widget:

```html+django
{% block my_collection_form_js %}
<script type="text/javascript">
	var $collection = $('#{{ id }}');
	var $addButton 	= $('#{{ id ~ "_add_button" }}');

	var initJS = function($field) {
		// include js prototype code
		{{ form_js(prototype, true) }}
	};

	$addButton.on('click', function(e){
		// here create $newItem and add it to the page
		initJS($newItem);
	});
</script>
{% endblock %}
```

> **Note**: this snippet does not include code to add new item, as this is already covered by [How to Embed a Collection of Forms][sf2-cookbook-collection-add] cookbook.

For example usage study the [AdmingeneratorFormExtensionsBundle][s2a-form-extensions] (templates in `Resources/views/Form` directory).

# 3. Acknowledgements

The twig extension is copied from [GenemuFormBundle](https://github.com/genemu/GenemuFormBundle/). The names of twig blocks have been changed to not cause conflicts.

The bundle is great, however for our purposes, we only need the Twig Extension, so we decided to extract it and put it into a seperate bundle.

# 4. Versioning

Releases will be numbered with the format `major.minor.patch`.

And constructed with the following guidelines.

* Breaking backwards compatibility bumps the major.
* New additions without breaking backwards compatibility bumps the minor.
* Bug fixes and misc changes bump the patch.

For more information on SemVer, please visit [semver.org][semver] website.

# 5. Contributing

This bundle follows branching model described in [A successful git branching model][branching-model-post] blog post by Vincent Driessen.

* The `master` branch is used to tag stable releases.
* The `develop` branch is used to develop small changes and merge feature branches into it.
* The `feature-` branches are used to develop features. When ready, submit a PR to `develop` branch.
* The `hotfixes` branch is used to develop fixes to severe bugs in stable releases. When ready, the fix is merged both to `develop` and `master` branches.
* The release branches (eg. `1.1`) are created for each minor release and only patches will be merged into them.

![Branching model](https://github.com/symfony2admingenerator/FormBundle/raw/master/Resources/doc/branching-model.png)

# 6. License

This bundle is released under the [MIT License](LICENSE) except for the file: `Resources/doc/branching-model.png` by Vincent Driessen, which is released under `Creative Commons BY-SA`.

[sf2-cookbook-collection-add]: http://symfony.com/doc/current/cookbook/form/form_collections.html
[s2a-form-extensions]: http://github.com/symfony2admingenerator/FormExtensionsBundle
[semver]: http://semver.org
[branching-model-post]: http://nvie.com/posts/a-successful-git-branching-model/