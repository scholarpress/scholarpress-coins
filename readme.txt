=== Plugin Name ===
Contributors: stakats, jeremyboggs, dan-jones, boonebgorges, r-a-y, cuny-academic-commons
Donate link: http://scholarpress.net/coins/
Tags: metadata, coins, posts
Requires at least: 2.8
Tested up to: 4.8.3
Stable tag: 2.2

ScholarPress Coins adds COinS metadata to your blog posts, which will allow other tools that can read/parse COinS to ingest data about your posts. 

== Installation ==

1. Upload `scholarpress-coins` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enjoy! The plugin will automatically add an empty `<span>` to each post and page with COinS data.

== Screenshots ==
1. The metadata is managed via the "Bibliographic Information" meta box on Post and Page edit screens.

== Changelog ==

= 2.2 =
* Allows for multiple authors on one post or page
* Fixed issues with i18n
* Fixed security issues

= 2.1 =
* Fix JS error on non-post/page edit screens

= 2.0 =
* Allows users to customize COinS metadata values for title, author's name, subject, source, date, and identifier.
* Users can lock the title, author, subject, and identifier fields to the defaults taken from the information about the post.
* Fully backwards-compatible with older versions of the plugin.

= 1.3 =

* Adds 'scholarpress_coins_span_title' filter.
* Adds checks for empty author first and last names.

= 1.2 =

* URL encodes values where necessary.

= 1.1 =

* Adds support for static pages.

= 1.0 =

* First release.
