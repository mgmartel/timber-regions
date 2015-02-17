# Timber Regions, a Timber starter theme

This starter theme is an opinionated template system for Timber.

The main design principle is a strong separation of elements into regions, wrappers, layouts, templates and partials: every template (eg. `single.twig`) is divided into regions around the main content area - generally a header and a footer. Templates are agnostic of their layout or the content of their regions - this is set up in the controller.

> *Note:* This theme depends on the Timber template autoloader, which has not been merged into the main Timber branch (yet).

> *Further note:* as always, 'this is a work in progress'

## How it Works

Almost all template loading is based on the directory structure, so lets start there:

```
/timber-region-theme
    /views          // All twig files

        /default    // The base templates that are overridden by templates
                    // defined in the other directories. Do not change when
                    // creating your own theme.

        /layouts    // Main content layout - eg. sidebar-left or full-width

        /partials   // All template parts that can be included by other
                    // templates, eg. content-page.twig, comment.twig, etc.

        /regions    // Templates for regions - eg. header-topbar, footer-menu,
                    // etc.

        /wrappers   // Parent wrappers that contain everything from `<html>`
                    // to `</html>`
```

The `default` folder works like a WordPress parent theme. It includes all the templates that are part of the Timber Regions theme. Timber will always fallback to those templates if you don't override them.

### Regions
Regions are all areas around the main content area (header, footer, menu, etc.), and their contents are determined via a simple API in PHP. This makes building up pages using theme options or complicated logic very simple.

The class `TimberRegions_MyTheme` contains the default contents for the regions, wrapper and layout. Check out the file `lib/regions-theme.php` to see how that works. In the PHP you can set the layout (in the controller, page template, through filters, or however you want) with the API of `TimberRegions`:

```php
TimberRegions::add_{$region}( $template, 'before'|'after'|'replace' );
TimberRegions::set_{$region}( $template );
TimberRegions::set_layout( $template );
TimberRegions::set_wrapper( $template );
```

Make sure to replace `TimberRegions` with your TimberRegions child class name.
For example:

```php
// Add item to a region
TimberRegions_MyTheme::add_header( 'topbar', 'after' );
// Set the layout
TimberRegions_MyTheme::set_layout( 'full-width' );
```

By default only the `header` and `footer` region are defined. You can add extra regions by adding it as a key to the `TimberRegions_MyTheme::$_regions` array.

## Building a theme with Timber Regions

There are 3 ways of building on the Timber Regions theme:

### 1. Use it as a parent theme

The best way to quickly start hacking. You can always switch over to method 3 later. Make sure you add a `lib/regions-theme.php` to your child theme, that defines your child theme regions! See the example in the theme.

### 2. Modify the theme directly

You can check out the theme and start creating new views in the `views` directory. Do not overwrite the `views/default` directory - that one is where all the magic happens!

### 3. Include the theme in your own theme

This is the recommended way. This keeps Timber Regions updatable, without it having to be a parent theme. This means your theme will still be child-themeable.

Put the whole timber-regions-theme inside your theme folder, and include the `functions.php` of Timber Regions.

## The hierarchy: wrappers, regions, layouts and templates

Below is an explanation of how Timber Regions builds up your pages and how you can extend it to quickly create complex themes!

Here is a quick example of a very boring page:

![Example of regions structure](https://raw.github.com/mgmartel/timber-regions/master/regions-example.png)

### Wrappers

Wrappers contain everything from `<html>` to `</html>`. You can use different wrapper for when the base structure of your HTML *really* has to change. For example when you have to wrap your container for an offcanvas menu.

The easiest way of creating a wrapper is by extending the default wrapper like this:

`{% extends 'default/wrappers/default.twig' %}`

(Because your theme will probably include a `wrappers/default.twig`, we have to explicitly specify that we want the `default.twig` wrapper from `default/wrappers`)

Then you simply override the block you want to override.

In your wrapper, the available **regions** are defined. A region is any part of your theme other than the main content area: your headers, footers, menu's, etc.

In your *wrapper* you include them like this:

`{% include 'regions/_get_region.twig' with { region: 'header' } %}`

Just replace `header` with your region name. Under the hood, this will check what templates you have defined to display in your `header` region and load them. More info on that further down.

If you have multiple regions of the same type (for example a mobile header and a desktop header), you can pass a region id:

`{%- include 'regions/_get_region.twig' with { region: 'header', region_id: 'header-mobile' } -%}`

This will find what templates you set for the `header-mobile`, and load each of those `header-{{template}}`.

Lastly, `wrappers` should always define a `layout` block. This is where the main content area will go:

`{% block layout %}{% endblock %}`

If you're not overriding the `main` block, you do not have to worry about this.


### Regions

Region templates are defined in the `regions` folder. Using the PHP API described above, you can define what templates should be shown in your respective regions.

For example, if you want to enable footer widgets through theme option, this would be the way to go:

```php
// lib/regions-theme.php

/* First, define the default regions and templates
 * in your regions class.
 */
class TimberRegions_MyTheme extends TimberRegions
{
    protected static $_regions = array(
        'header' => array( 'default'),
        'footer' => array(
            'menu',
            'colophon'
        )
    );
}
```

Then, somewhere in your site class:

```php
if ( $this->get_theme_option( 'display_footer_widgets' ) ) {
    TimberRegions_MyTheme::add_footer( 'widgets', 'before' );
}
```

Now in your footer, regions theme will automatically look load:

1. `regions/footer-widgets.twig` or `regions/widgets.twig`
2. `regions/footer-menu.twig` or `regions/menu.twig`
3.  `regions/footer-colophon.twig` or `regions/colophon.twig`

That's it!

### Layouts

Layouts define what happens in your *main content region*. Layouts will generally coincide with traditional WordPress page templates: sidebar left/right, full width template, etc.

Layouts should *always* extend `layouts/base.twig` (or another layout that in turn extends `layouts/base.twig`), define a  and contain an (empty) content block:

`{% block content %}{% endblock %}`

A basic sidebar layout could look like this:

```twig
{# layouts/sidebar-right.twig #}
{% extends "layouts/base.twig" %}

{% block layout %}

    <div class="content-wrapper sidebar-right" id="content" role="main">
        {% block content %}{% endblock %}
    </div>

    {% include 'partials/sidebar.twig' %}

{% endblock %}
```

### Templates

Finally, the templates! The templates are loaded according to the Timber template hierarchy. So `page.twig` is loaded for single pages, `home.twig` for the home page, `single.twig` for single posts, etc.

Templates should always extend `base.twig` and include a `content` block where the content is displayed (finally!).

`base.twig` loads the layouts, regions and wrappers, so templates only have to worry about displaying the content itself. This is where the real power of the Timber Regions theme comes out. Have a look at the `default/single.twig` or `default/index.twig` templates in the theme to get an idea of how simple displaying content becomes with Timber Regions.

### Partials

The Timber Regions theme relies heavily on using partials for every bit of abstractable content. This is not necessary for productive use of the theme, but strongly recommended.

Again, have a look at the templates and you will see that all the real displaying happens in the partials.

## The PHP files

Timber Regions bootstraps itself in `functions.php`. There should be no need to change that file generally as your theme code should mostly live elsewhere. Below is the files you should use in your (child)theme. They are always loaded automatically during the bootstrap.

### `lib/regions-theme.php` - Define your regions class and default regions

As explained above, the `regions-theme.php` file should contain a class that extends `TimberRegions` and that defines the default wrapper, layout and regions. Check out the `regions-theme.php` in the repo for an example.

### `lib/site.php` - Your theme functions

All coding happens in `site.php`. Timber Regions comes with a base class you can extend to get rid of some boilerplate code. Check out `site-example.php` to get started.

The only thing to really take care of, is that `regions` makes it into context. The simplest way to do this is by defining the following method in your site class:

```php
public function get_theme_regions() {
    return new TimberRegions_MyTheme;
}
```

### `lib/init.php` - Your initialization code

This is where you can run your theme bootstrap. All files have been included, so you just have to worry about creating instances and perhaps setting up some globals (you shouldn't, though). It's most important role is **instantiating your site class** from `site.php`. In practice, it will just contain:

```php
<?php
new MySite();
```
