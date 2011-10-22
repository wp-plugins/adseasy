=== Ads Easy ===
Contributors: tepelstreel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VRMSV3NXQDXSA
Tags: ads, sidebar, widget, multi widget, advertising, banner, banners
Requires at least: 2.7
Tested up to: 3.3
Stable tag: 2.0

Ads Easy is the most simple way to integrate some banners into your blog. It works with basically everything.

== Description ==

If you need to place some Advertisements on your blog, but you don't need other stats, than those from your adprovider and you don't want to have ads in your posts, this is the solution. You can define the widget style yourself or you can leave it to your theme. Show your ads on every type of page or just on the frontpage. Keep your registered users adfree when they are logged in if you want.

Ads Easy was tested up to WP 3.3. It should work with versions down to 2.7 but was never tested on those.

== Installation ==

1. Upload the `adseasy` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place and customize your widgets
4. Customize your links in the settings

== Frequently Asked Questions ==

= I styled the widget container myself and it looks bad. What do I do? =

The styling of the widget requires some knowledge of css. If you are not familiar with that, try adding

`padding: 10px;
margin-bottom: 10px;`
 
to the style section.

= My widget should have rounded corners, how do I do that? =

Add something like

`-webkit-border-top-left-radius: 5px;
-webkit-border-top-right-radius: 5px;
-moz-border-radius-topleft: 5px;
-moz-border-radius-topright: 5px;
border-top-left-radius: 5px;
border-top-right-radius: 5px;`
 
to the widget style. This is not supported by all browsers yet, but should work in almost all of them.

= My widget should have a shadow, how do I do that? =

Add something like

`-moz-box-shadow: 10px 10px 5px #888888;
-webkit-box-shadow: 10px 10px 5px #888888;
box-shadow: 10px 10px 5px #888888;`
 
to the widget style to get a nice shadow down right of the container. This is not supported by all browsers yet, but should work in almost all of them.

== Screenshots ==

1. The plugin's work on testsite, user not logged in
2. The plugin's work on testsite, user logged in
3. The widget's settings section

== Changelog ==

= 2.0 =

* Resizable Textareas work now. Check all function added.

= 1.0 =

* Stable version with Dutch and German language files.

== Upgrade Notice ==

= 1.0 =

Stable and clean version

= 2.0 =

* Small bugfix and more functionality.
