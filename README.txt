=== Plugin Name ===
Contributors: DeBAAT
Donate link: http://www.de-baat.nl/WP_MCM
Tags: media library, bulk action, bulk toggle, toggle category, taxonomy, taxonomies, attachment, media category, media categories, media tag, media tags, media taxonomy, media taxonomies, media filter, media organizer, media types, media uploader, custom, media management, attachment management, files management, user experience, wp-admin, admin
Requires at least: 4.0
Tested up to: 4.0
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A plugin to provide bulk category management functionality for media in WordPress sites.

== Description ==
This WordPress plugin will ease the management of media categories, including bulk actions.
It supports categories for media using either the existing post categories or a dedicated media_category custom taxonomy.
The plugin supports easy category toggling on the media list page view and also bulk toggling for multiple media at once.

* use post categories or dedicated media categories,
* control your media categories via admin the same way as post categories,
* bulk toggle any media taxonomy assignment from Media Library via admin,
* filter media files in Media Library by your custom taxonomies, both in list and grid view
* use new shortcode to filter the media on galleries in posts,

== Installation ==

1. Upload plugin folder to '/wp-content/plugins/' directory
1. Activate the plugin through 'Plugins' menu in WordPress admin
1. Adjust plugin's settings on **WP MCM -> Settings**
1. Enjoy WordPress Media Category Management!
1. Use shortcode `[wp_mcm category="<slug>"]` in your posts or pages, see also **WP MCM -> Shortcodes**

== Frequently Asked Questions ==

= How do I use this plugin? =

On the options page, define which category to use for media: either use the standard post category or a dedicated media category.
Define the categories to be used for media.
Assign categories to media, either individually or in bulk.
Use category filter when adding media to posts or pages.

= How do I use the shortcode of this plugin? =

Use the `[wp_mcm]` shortcode. Various shortcode uses are explained in the **WP MCM -> Shortcodes** page.

== Screenshots ==

1. The admin page showing the options for this plugin.
2. Managing the new Media Category taxonomy.
3. Setting Media Category options for a media post.
4. Media List page view showing individual toggle options for first media post.
5. Media List page view showing bulk toggle actions for selected media post.
6. Media List page view showing filter options for Media Categories.
7. Media Grid page view showing filter options for Media Categories.
8. The admin page showing the shortcode explanations for this plugin.
9. The post edit page showing an example using the [wp-mcm category="mediafoto,medialogo"] shortcode.
10. The post page showing the results of the example using the [wp-mcm category="mediafoto,medialogo"] shortcode.

== Changelog ==

= 1.0.0 =
* Added category filter functionality when adding media to posts or pages.
* Added functionality to define default category when adding or editing a media file.
* Added a switch to enable and disable the assignment of default category.
* Added category filter functionality to the media grid view.
* Added a new screenshot showing filter in media grid view.

= 0.2.0 =
* Fixed bug managing post categories in combination with "Use Post Taxonomy" flag.
* Improved asset images for repository.

= 0.1.0 =
* First version starting the plugin.

== Upgrade Notice ==

= 1.0.0 =
* Added new functionality, see change log.

= 0.2.0 =
* Fixed bug managing post categories in combination with "Use Post Taxonomy" flag.
* Improved asset images for repository.

= 0.1.0 =
As this is the first version, there is no upgrade info yet.
