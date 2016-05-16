=== Plugin Name ===
Contributors: stakats, jeremyboggs
Donate link: http://scholarpress.net/coins/
Tags: metadata, coins, posts
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 1.3

ScholarPress Coins adds a COinS metadata to your blog posts, which will allow other tools that can read/parse COinS to ingest data about your posts. 

== Installation ==

1. Upload `scholarpress-coins` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enjoy! The plugin will automatically add an empty `<span>` to each post and page with COinS data.

== Changelog ==

= 1.3 =

* Adds 'scholarpress_coins_span_title' filter.
* Adds checks for empty author first and last names.

= 1.2 =

* URL encodes values where necessary.

= 1.1 =

* Adds support for static pages.

= 1.0 =

* First release.
