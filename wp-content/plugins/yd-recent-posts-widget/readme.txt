=== YD Recent Posts Widget ===
Contributors: ydubois
Donate link: http://www.yann.com/
Tags: widget, recent posts, posts, plugin, sidebar, thumbnail, images, cache, tags, automatic, custom, Post, admin, image, previous posts, template function, template, page, administration, timthumb, English, French, Russian, Dutch, German, shortcode, excerpt, abstract, featured image
Requires at least: 2.0.0
Tested up to: 3.0.1
Stable tag: trunk

Highly customizable sidebar widget: displays latest or previous posts with automatic thumbnail images.
Optionaly skips posts already on the home page.

== Description ==

= Recent posts with thumbnails! =

This Wordpress plugin installs a **new sidebar widget** that can display the **latest posts** with **automatic thumbnail images**.
It also creates **new PHP functions** that can be included in any template to **display a posts list with thumbnails and excerpts**.
It uses pre-resized thumbnail images already generated in WP2.0+, WP2.5+, WP2.6+ WP2.7+, WP2.9+, including WP3.x (featured images).
It can auto-generate text excertps of the desired length, or use Wordpress' built-in manual excerpt field.
It also works perfectly with "old" versions of Wordpress that did not support the automatic multiple-format image resizing.
You can choose to list all recent posts, or **only list recent posts marked with a specific tag**.
You can display a **different selection on the home page** and on other pages.
Posts already listed on the home page are automatically "skipped" when the widget is displayed on the home page.

If you don't like the widget or don't use sidebars, you can also **include the list in the content of any page or post** of your blog, 
by simply using the special `[yd_list_posts]` shortcode, or **include it in a template** with the `<?php display_yd_recent_posts_here() ?>` function.
The list design is **highly customizable** allowing different settings when displayed as a widget on the home page and other blog pages, and when used inside templates. 

The plugin uses **cache** to avoid multiple database query.
It has its own widget control pannel and admin options / settings page.
It is **fully internationalized**.
You can use the provided additional stylesheet, or customize your own.
Base package includes .pot file for translation of the interface, and **English**, **French**, **Russian**, **Dutch** and **German** versions.
The plugin can be (and is) used to display posts in any Wordpress-compatible language and charset, including Chinese.

= Translation =

If you want to contribute to a translation of this plugin, please drop me a line by e-mail or leave a comment on the [plugin's page](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget "Yann Dubois' Recent Post Widget for Wordpress").
You will get credit for your translation in the plugin file and this documentation, as well as a link on this page and on my developers' blog.

= Translation credits =

* Thanks to [FatCow](http://www.fatcow.com/ "FatCow") for the Russian translation file.
* Thanks to [Rene @ Fethiye Hotels](http://www.fethiyehotels.com "Fethiye Hotels") for the Dutch translation file.
* Thanks to [Rian @ Pangaea](http://www.pangaea.nl/diensten/exact-webshop "Pangaea.nl") for the German translation file.

= Previous posts with thumbnails now also available =

Version 0.7 adds a new template function which displays a list of the previous posts with thumbnails:

Try it on your homepage (`index.php`) template!

Syntax: `<?php display_yd_previous_posts_here() ?>`
Option: you can specify number of posts to shows like this: `<?php display_yd_previous_posts_here( 5 ) ?>`

= Timthumb-compatibility =

The plugin can use Timthumb (as an alternative or complement to recent Wordpress built-in thumbnail/featured image generation functions) 
to generate images of the exact right size.

= Active support =

Drop me a line on my [YD Recent Posts Widget plugin support site](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget "Yann Dubois' Recent Post Widget for Wordpress") to report bugs, ask for specific feature or improvement, or just tell me how you're using the plugin.
It's still in an active development stage, with new features coming out on a regular basis.

The [official FAQ page for this plugin is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/faq).

The [official installation and usage guide is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/usage-guide).

= Description en Français : =

Cette extension Wordpress installe un nouveau widget dans votre barre latérale qui peut afficher les billets récents assortis automatiquement d'une image vignette.
Il fonctionne parfaitement avec les anciennes versions de Wordpress n'intégrant pas la génération automatique d'images multi-formats.
Vous pouvez choisir de lister tous les billets récents, ou de seulement lister ceux qui sont marqués d'un tag précis.
Vous pouvez afficher une sélection distincte sur la page d'accueil et sur les autres pages.
Les billets qui sont déjà affichés sur la page d'accueil n'apparaissent pas dans la liste quand le widget s'affiche en page d'accueil.
Si vous n'aimez pas le principe du widget ou n'utilisez pas de barres latérales, vous pouvez inclure la liste des billets récents n'impore où dans le contenu des pages et billets de votre blog,
simplement en insérant un "shortcode" spécial.
Le widget est entièrement paramétrable, autorisant des réglages différents entre la page d'accueil et les autres pages du blog.
Il utilise un système de cache pour éviter les requêtes de base de données redondantes.
Il utilise les images thumbnail pré-générées par WP2.0+, WP2.5+ WP2.7+, WP2.9+ et WP3.x (image à la une).
Il a son propre panneau de contrôle et sa page de réglages dans l'administration.
Il est entièrement internationalisé.
Vous pouvez au choix utiliser la feuille de style fournie, ou personnaliser l'apparence de la liste avec vos propres styles 
La distribution standard inclut le fichier de traduction .pot et les versions française, anglaise, russe, hollandaise et allemande.
L'extension peut fonctionner avec n'importe quelle langue ou jeu de caractères y compris le chinois.
Pour toute aide ou information en français, laissez-moi un commentaire sur le [site de support du plugin YD Recent Posts Widget](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget "Yann Dubois' Recent Post Widget for Wordpress").

== Installation ==

1. Unzip yd-recent-posts-widget.zip
1. Upload the `yd-recent-posts-widget` directory and all its contents into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use the widget admin page to add the widget to one of your sidebars and configure it
1. Use the 'YD Recent Posts' settings page to clear the cache when you make changes.
1. If you want to include the list in your page content, use the `[yd_list_posts]` short code.
1. If you want to include it in your template, use the `<?php display_yd_recent_posts_here() ?>` function.
1. Use the `<?php display_yd_previous_posts_here() ?>` function to display a list of previous posts.
For specific installations, some more information might be found on the [YD Recent Posts Widget plugin support page](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget "Yann Dubois' Recent Post Widget for Wordpress")


== Frequently Asked Questions ==

= Where should I ask questions? =

http://www.yann.com/en/wp-plugins/yd-recent-posts-widget

Use comments.

I will answer only on that page so that all users can benefit from the answer. 
So please come back to see the answer or subscribe to that page's post comments.

The [official FAQ page for this plugin is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/faq).

The [official installation and usage guide is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/usage-guide).

= Puis-je poser des questions et avoir des docs en français ? =

Oui, l'auteur est français.
("but alors... you are French?")

= What is your e-mail address? =

It is mentioned in the comments at the top of the main plugin file. However, please prefer comments on the plugin page (as indicated above) for all non-private matters.

= I made some changes but they do not show up on the site... =

Remember to use the "Clear cache" button of the YD Recent Posts settings page of the admin menu
if you want changes to appear right away on your blog.
Otherwise you will have to wait until content is added to the blog for the cache to expire 
(ie. when you write a new post or page - new comments don't make the cache expire).

= How to display this in the template without using a widget? =

Insert this code into your template:

`<?php display_yd_recent_posts_here() ?>`

= If I don’t want to use the widget, how can I display it in php? =

Same answer as above.

= Can I include the recent post list in my blog content? =

Yes you can include the list in the content of any page or post by using this shortcode:

`[yd_list_posts]`

The shortcode supports optional parameters to overload all basic plugin settings.

You can for example query posts using any custom WP Query using this syntax:

`[yd_list_posts spec_query='<your query parameters here>']`

Example:

`[yd_list_posts spec_query='showposts=3']` to show the last 3 posts of your blog.

= How do a display a list of previous posts on my homepage? =

Insert this code into your `index.php` homepage template, after the loop:

`<?php display_yd_previous_posts_here() ?>`

= How do I specify the number of previous posts to show in the "previous posts" list? =

Add the optional 'number of posts' parameter when calling the function, for example:

`<?php display_yd_previous_posts_here( 5 ) ?>` will display the previous 5 posts.

= How do i change the text formatting? =

Try to load the specially provided CSS stylesheet by checking the "load CSS" checkbox in the plugin settings page.
You can either customise this stylesheet which is inside the /css sub-folder of the plugin folder, or add styles to your main stylesheets
for elements of the `<div class="yd_rp_widget">` tag.

= What if I want / don't want to display the date? =

Just check or uncheck the "date" checkbox in the widget control pannel.
You can customize the date display style by using the usual PHP syntax.

= How do I restore the default settings? =

Click on the "Reset default settings" button in the YD Recent Posts settings page of the admin menu.


== Screenshots ==

1. An example of the sidebar widget in action
2. Another example of the recent post list rendering
3. The widget control pannel in Wordpress 2.3
4. The widget settings page in Wordpress 2.7


== Widget control pannel ==

The widget has its own control pannel for setting-up its look and feel. You can administer it from the widgets admin page.
Remember to clear the cache when you make changes, if you want to see them right away (see hereunder).

The [official installation and usage guide is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/usage-guide).


== Widget settings page ==

Use the widget's own settings page to clear the cache and reset default settings.
Otherwise, the cache expires only when content is added to the blog or widger control panel options are changed.

The [official installation and usage guide is here](http://www.yann.com/en/wp-plugins/yd-recent-posts-widget/usage-guide).


== Revisions ==

* 0.2. The cache did not always expire when new content was added -> Fixed.
* 0.2. I18n + .PO file + French .MO file.
* 0.3. Made date display and format optional.
* 0.4. Now Initializes / resets default options properly
* 0.4. Now gets default date format from WP options
* 0.4. Bugfix: in WP2.7 the widget was called when in admin mode, giving strange results
* 0.4. Created/Added a default thumbnail image + set it as default during init
* 0.5. Added function and special tag to display the list outside of a widget
* 0.5. Added feature to skip posts already displayed on home page
* 0.6. No warnings in debug mode (hopefully?)
* 0.6. Supports pre-existing WordPress 2.0+ and 2.5+ thumbnails
* 0.7. Now Supports an optional specific cache and settings for usage within templates + undocumented optional parameters
* 0.7. Added the new `display_yd_previous_posts_here()` function!
* 0.7. Fixed WP_query redefinition / `is_home()` status loss issue
* 0.7. Fixed the private post display issue
* 0.8. New setting to keep HTML formatting inside the post excerpts
* 0.8. New settings to get rid of [...] and/or {...} special tags
* 0.8. New (default) setting to display the list as a set of &lt;ul&gt;, &lt;li&gt; tags
* 0.8. New default stylesheet
* 0.8. Bugfixes
* 0.9. Timthumb compatibility
* 0.9. Option to display all posts on home page
* 3.0. Wordpress 3 compatibility / numerous bugfixes / a trunkload of new andlong-awaited features
* 3.0.1 Bugfixes; WP 3.0.1 compatibility

== Changelog ==

= 0.1 =
* Initial release
= 0.2 = 
* Bugfix: The cache did not always expire when new content was added -> Fixed.
* I18n: .PO file + French .MO file.
= 0.3 =
* Feature: Made date display and format optional.
= 0.4 =
* Bugfix: Now Initializes / resets default options properly
* Feature: Now gets default date format from WP options
* Bugfix: in WP2.7 the widget was called when in admin mode, giving strange results
* Feature: Created/Added a default thumbnail image + set it as default during init
= 0.5 =
* Feature: Added function and special tag to display the list outside of a widget
* Feature: Added feature to skip posts already displayed on home page
= 0.6 =
* Bugfix: No warnings in debug mode (hopefully?)
* Feature: Supports pre-existing WordPress 2.0+ and 2.5+ thumbnails
= 0.7 =
* Feature: Now Supports an optional specific cache and settings for usage within templates + undocumented optional parameters
* Feature: Added the new `display_yd_previous_posts_here()` function!
* Bugfix: Fixed WP_query redefinition / `is_home()` status loss issue
* Bugfix: Fixed the private post display issue
= 0.8 =
* Feature: New setting to keep HTML formatting inside the post excerpts
* Feature: New settings to get rid of [...] and/or {...} special tags
* Feature: New (default) setting to display the list as a set of &lt;ul&gt;, &lt;li&gt; tags
* Feature: New default stylesheet
* Bugfixes
= 0.9 =
* Feature: Timthumb compatibility
* Feature: Option to display all posts on home page
= 3.0 =
* Feature: new admin interface
* Feature: linkbackware
* Feature: strip shortcode by default
* Feature: tidy up file hierarchy
* Feature: Wordpress 3.0 full compatibility
* Feature: use WP thumbnail image (featured image)
* Bugfix: clear all caches
* Feature: use shortcode
* Feature: default stylesheet to get rid of bullets
* Feature: call stylesheet from head
* Feature: Updated css stylesheet
* Feature: use WP manual excerpt
* Feature: different setting on other pages is an option
* Feature: new widget admin interface
* Feature: display title / display abstract / display image options
* Feature: choose ellipsis string option
* Feature: show posts from category option -> custom wp_query string option
* Feature: function parameters overload (incl. title)
* Feature: multiple widget - Support multiple instances of the widget
* Bugfix: if you delete a post, the cache of the recent-post-widget is now renewed
* Feature: default timthumb location
* Feature: Timthumb cache write-permission check
* Feature: links in plugin list menu
* Feature: German version
* Updated French version
* Updated readme.txt doc (new format for revisions and upgrades)
= 3.0.1 =
* Bugfix: on-line image style option did not save
* Bugfix: default image URL option did not save
* Bugfix: translation of the js link disable alert
* Bugfix: array casting in http_build_query() function (yd-rpw-widget.inc.php)
* Bugfix: allow default empty param in widget_yd_rp() function
* Feature: Wordpress 3.0.1 compatibility

== Upgrade Notice ==

= 3.0.1 =
* For upgrading, completely removing the files of the previous version before installing the new version is recommended.
* Automatic upgrade will also work. 
* Resetting the plugin options in the admin panel to get the new default settings is also recommended.

== Did you like it? ==

Drop me a line on http://www.yann.com/en/wp-plugins/yd-recent-posts-widget

And... *please* rate this plugin --&gt;