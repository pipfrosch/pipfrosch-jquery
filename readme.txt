=== Pipfrosch jQuery ===
Contributors: PipfroschPress
Tags: jQuery
Donate link: https://pipfrosch.com/donate
Requires at least: 4.1.0
Tested up to: 5.4.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Use an updated version of jQuery with your WordPress powered website.

== Description ==
The jQuery that current ships as part of WordPress is (as of 5.4.1) an older
version of jQuery. As in ancient. This plugins allows you to instead use a much
more modern of jQuery, with optional compatibility for scripts that need the
older jQuery calls.

This plugin also optionally allows you to securely use the `code.jquery.com` CDN
for jQuery including the appropriate SRI and CrossOrigin attributes which speeds
up page load for users who already have jQuery from that CDN cached (most users
will likely already have it cached).

The updated jQuery will not replace the core jQuery for administration pages.

=== Compatibility Options ===
You may disable compatibility with scripts that require older versions of
jQuery. This is not recommended. Plugins and themes and quite possibly WP Core
scripts that use jQuery may require features that are no longer supported in the
current version of jQuery. However you can disable compatibility completely if
you desire.

You may specify compatibility with jQuery 1.9 through 3.0. This is the default
and provides compatibility with the version of jQuery that currently (WP 5.4.1)
ships in WordPress, which is version jQuery 1.12.4.

If some jQuery scripts break even with the default compatibility mode, they are
likely written for jQuery prior to 1.9. The only option is to not use this
plugin until you have upgraded that very old code.

It is highly recommended you update that code as soon as possible, whether or
not you plan to use this plugin.

Please note that WordPress has loaded jQuery by default for years, this results
in many plugins and themes using it when they really did not need to because
jQuery would be loaded by the browser anyway. Rumor is that a future release of
WordPress may only use native JavaScript for everything in WP Core. If hiring a
JavaScript developer to update old jQuery code, make sure the JavaScript
developer is wise enough to know when porting the code to native JavaScript
makes more sense than continuing to use jQuery and when using jQuery really is
the best approach. In other words, do not hire me, I pretty much only use jQuery
but that is not always the right approach even if it is the convenient approach.

=== CDN OPTIONS ===
By default, the updated jQuery scripts are served from within the plugin. This
is because responsible plugin and theme developers do not (in my opinion)
utilize third party resources by default.

It is recommended that you have the scripts served by the `code.jquery.com` CDN
as that CDN is heavily used, so those who visit your WordPress powered website
will almost always already have the scripts unexpired in their browser cache
reducing the time it takes to load your website in their browser. For users who
pay for their bandwidth, it also reduces their financial cost.

When you opt to use the CDN, by default this plugin will add the SRI attributes
(see https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity
if you do not know what that is) needed to allow the client browser to verify
the script has not been altered.

You can optionally turn that off. The only logical reason for turning it off
would be if you already have a plugin that adds SRI tags to scripts. In that
case, it is redundant to have this plugin run a filter that adds them as well.

== Plugin / Theme Compatibility ==
This plugin does not set the version for jQuery. Doing so greatly reduces the
odds that a browser will recognize it already has the scripts cached. Some
plugins and themes may query for the version of jQuery being used. They should
not do that, but some do. They will not get an answer if this plugin has run
before they run and they will get the wrong answer if they run before this
script has run.

== Updates ==
I will try to update this plugin when new versions of jQuery are released but
it may not be as fast as some may like. You can bug be by leaving a comment on
the donate page if a new version of jQuery is out and I have not updated.

For the included jQuery migration plugins, I am less likely to notice when new
versions are available but I do check whenever a new version of jQuery itself is
released. Again you can bug me if needed.

== Translators ==
Please fork https://github.com/pipfrosch/pipfrosch-jquery and make a pull
request.

