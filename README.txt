=== Post Grid Shortcode ===
Contributors: devnahin
Donate link: https://buymeacoffee.com/devnahin
Tags: grid, posts, shortcode, post grid, featured image
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple and flexible shortcode to display a grid of posts with customizable options.

== Description ==

The **Post Grid Shortcode** plugin allows you to create a responsive grid of posts anywhere on your site using a simple shortcode. It provides several options to control how the posts are displayed, including the number of posts, order, post types, and the option to show featured images and excerpts.

**Features include:**
- Customizable grid of posts
- Control over the number of posts to display
- Choose post type, order, and post status
- Display featured images and excerpts
- Option to add a "Read More" button with customizable text

Use the `[post-grid]` shortcode in any post, page, or widget to display a grid of posts.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/post-grid-shortcode` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to "Settings" > "Post Grid Shortcode" to configure your options.
4. Add the `[post-grid]` shortcode to any post or page where you want the post grid to appear.

== Frequently Asked Questions ==

= How do I use the shortcode? =

Simply add `[post-grid]` in any post, page, or widget. You can customize the display by using the following attributes:

- `posts_per_page`: (int) Number of posts to display. Default is 5.
- `orderby`: (string) How to order the posts (e.g., date, title). Default is 'date'.
- `order`: (string) Ascending ('ASC') or Descending ('DESC'). Default is 'ASC'.
- `post_type`: (string) Type of posts to display (e.g., 'post', 'page'). Default is 'post'.
- `post_status`: (string) Status of posts to display (e.g., 'publish', 'draft'). Default is 'publish'.
- `show_featured_image`: (bool) Whether to show featured images. Default is true.
- `excerpt_length`: (int) Number of words in the post excerpt. Default is 20.
- `enable_read_more_button`: (bool) Show a "Read More" button. Default is false.
- `read_more_text`: (string) Customize the "Read More" button text. Default is "Read More".

Example usage: 
`[post-grid posts_per_page="3" orderby="title" order="DESC" show_featured_image="1"]`

= Where do I configure the options for the plugin? =

Go to "Settings" > "Post Grid Shortcode" in the WordPress admin dashboard.

= Can I use this with custom post types? =

Yes, you can specify the `post_type` attribute in the shortcode to display custom post types. For example:
`[post-grid post_type="product"]`

== Screenshots ==

1. **Example of a Post Grid**: A simple post grid display.
2. **Settings Page**: The settings page where you can configure the default values for the shortcode.

== Changelog ==

= 1.0.0 =
* Initial release of Post Grid Shortcode.
* Basic settings for post grid display and shortcode attributes.

== Upgrade Notice ==

= 1.0.0 =
Initial release, no upgrade issues.

== License & Warranty ==

This plugin is licensed under the GPLv2 or later. You are free to use, modify, and distribute it as long as you comply with the terms of the GPLv2 license.

== Credits ==

Post Grid Shortcode was developed by devnahin. If you enjoy using this plugin, consider supporting us by donating via our [donation page](https://buymeacoffee.com/devnahin).
