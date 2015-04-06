=== Plugin Name ===
Contributors: DeBAAT
Donate link: https://www.de-baat.nl/WP_MCM
Tags: media library, bulk action, bulk toggle, toggle category, taxonomy, taxonomies, attachment, media category, media categories, media tag, media tags, media taxonomy, media taxonomies, media filter, media organizer, media types, media uploader, custom, media management, attachment management, files management, user experience, wp-admin, admin
Requires at least: 4.0
Tested up to: 4.1.1
Stable tag: 1.4.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A plugin to provide bulk category management functionality for media in WordPress sites.

== Description ==
This WordPress plugin will ease the management of media categories, including bulk actions.
It supports categories for media using either the existing post categories or a dedicated media_category custom taxonomy.
The plugin supports easy category toggling on the media list page view and also bulk toggling for multiple media at once.
It now also supports media taxonomies defined by other plugins.

= Main Features =

* Use post categories or dedicated media categories.
* Control your media categories via admin the same way as post categories.
* Bulk toggle any media taxonomy assignment from Media Library via admin.
* Filter media files in Media Library by your custom taxonomies, both in list and grid view.
* Use new or existing shortcode to filter the media on galleries in posts.

== Installation ==

1. Upload plugin folder to '/wp-content/plugins/' directory
1. Activate the plugin through 'Plugins' menu in WordPress admin
1. Adjust plugin's settings on **WP MCM -> Settings**
1. Enjoy WordPress Media Category Management!
1. Use shortcode `[wp_mcm taxonomy="<slug>" category="<slugs>"]` in your posts or pages, see also **WP MCM -> Shortcodes**

== Frequently Asked Questions ==

= How do I use this plugin? =

On the options page, define which taxonomy to use for media: either use the standard post taxonomy, a dedicated media taxonomy or a custom media taxonomy.
Define the categories to be used for media.
Toggle category assignments to media, either individually or in bulk.
Use category filter when adding media to posts or pages.

= How do I use this plugin to support the media taxonomy of another plugin? =

There are a number of plugins available for managing media categories.
This plugin now supports the settings previously defined to support those media categories.

Check out the **MCM Settings** page which shows an option "Media Taxonomy To Use".
The dropdown list of this option shows a list of all taxonomies currently used by this WordPress installation.
The option "**(P) Categories**" is the taxonomy defined by default for posts.
The option "**MCM Categories**" is the taxonomy previously defined as "**Media Categories**" by version 1.1 and earlier of this plugin.
If there are other taxonomies currently assigned to attachments, the list shows the corresponding taxonomy slug prefixed with **(*)**.
When such a taxonomy is selected to be used, the taxonomy will be registered anew with the indication "**(*) Custom MCM Categories**".
As long as this taxonomy is selected, the functionality available for "**MCM Categories**" is now available for these "**(*) Custom MCM Categories**", i.e. toggling and filtering.
The name shown for the "**(*) Custom MCM Categories**" can be changed using the option "**Name for Custom MCM Taxonomy**" on the **MCM Settings** page.

= How can I use the "Default Media Category"? =

First enable the option "**Use Default Category**" on the **MCM Settings** page.
When enabled and a media attachment has no category defined yet, the value of "**Default Media Category**" will be assigned automatically when a media attachment is added or edited.
The default value is also used in the `[wp_mcm]` shortcode to automatically filter the attachments to be shown.

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

= 1.4.4 =
* Added French translation (thanks Pierre).

= 1.4.3 =
* Tested up to WP 4.1.1.
* Added filter support for MCM categories when adding media to new post.

= 1.4.2 =
* Fixed issue with display of filter when adding media in posts and pages.
* Changed menu icon into a dashicon to improve visibility.
* Changed urls to https.

= 1.4.1 =
* Tested up to WP 4.1.
* Changed row actions text to only show 'Toggle' for first category to save space.

= 1.4.0 =
* Added filter to view uncategorized media files only.

= 1.3.1 =
* Fixed issue with finding taxonomies to use.

= 1.3.0 =
* Fixed issue with updating options.
* Improved support for MCM categories in modal edit mode.
* Improved support for Custom MCM names.
* Improved support for use of POST categories.
* Added support for new shortcode parameter "alternative_shortcode".

= 1.2.0 =
* Renamed "Media Categories" to "MCM Categories" for easier distinction from other taxonomies.
* Added support for media categories as defined by other plugins.
* Updated MCM Settings page to reflect support for other media categories.
* Added support for new shortcode parameter "taxonomy".

= 1.1.0 =
* Create default options when activating.
* Limit dark table header to WP_MCM shortcode screen only.

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

= 1.4.3 =
* Fixed some issues, see change log.

= 1.4.2 =
* Fixed some issues, see change log.

= 1.4.1 =
* Fixed some issues, see change log.

= 1.4.0 =
* Added new functionality and fixed some issues, see change log.

= 1.3.1 =
* Fixed some issues, see change log.

= 1.3.0 =
* Added new functionality and fixed some issues, see change log.

= 1.2.0 =
* Added new functionality, see change log.

= 1.1.0 =
* Added new functionality, see change log.

= 1.0.0 =
* Added new functionality, see change log.

= 0.2.0 =
* Fixed bug managing post categories in combination with "Use Post Taxonomy" flag.
* Improved asset images for repository.

= 0.1.0 =
As this is the first version, there is no upgrade info yet.
