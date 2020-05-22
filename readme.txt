=== Pipfrosch jQuery ===
Contributors: pipfroschpress
Tags: jQuery
Donate link: https://pipfrosch.com/donate
Requires at least: 4.1.0
Tested up to: 5.4.1
Stable tag: trunk
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Use jQuery 3.5.1 and jQuery Migrate 3.3.0 with your WordPress powered website
with a third party CDN if so desired.

== Description ==

The jQuery that current ships as part of WordPress is (as of WP 5.4.1) an older
version of jQuery. As in ancient. This plugin allows you to instead use a much
more modern of jQuery, with optional compatibility for scripts that need the
older jQuery calls.

This plugin also optionally allows you to securely select a public CDN for
jQuery including the appropriate Subresource Integrity (SRI) and CrossOrigin
attributes. Using a CDN can speed up the loading of your website as there is a
good chance a client visiting your website already has the identical file
in their browser cache and does not need to fetch it again.

When a CDN is used, a small amount of code is added to your pages to provide a
fallback where jQuery and the jQuery Migrate plugin are served from your site
if either the CDN can not be reached by the client or if the SRI check fails.

The updated jQuery will not replace the core jQuery for administration pages.
This is to avoid potential breakage of administrative pages.

The options for this plugin are managed from the WordPress ‘Dashboard’ in the
‘Settings’ area, using the ‘jQuery Settings’ menu option within the ‘Settings’
area.


== Plugin Options ==

There are four configurable options you can customize.

= ‘Use Migrate Plugin’ option =

Enabled by default.

You may disable compatibility with scripts that require older versions of
jQuery. This is not recommended. Plugins and themes and quite possibly WP Core
scripts that use jQuery may require features that are no longer supported in the
current version of jQuery without the Migrate plugin.

You can disable compatibility completely if you so desire by disabling the
jQuery Migrate plugin.

The jQuery Migrate plugin provides compatibility with jQuery 1.9 through 3.0.
It is enabled by default and provides compatibility with the version of jQuery
that currently (WP 5.4.1) ships in WordPress, which is version jQuery 1.12.4.

If some jQuery scripts break even with the default compatibility mode, they are
likely written for jQuery prior to 1.9. The only option is to not use this
plugin until you have upgraded that very old code.

It is highly recommended you update that code as soon as possible, whether or
not you plan to use this plugin.

Please note that WordPress has loaded jQuery by default for years, this results
in many plugins and themes using jQuery out of convenience when they really did
not need to.

Rumor is that a future release of WordPress may only use native JavaScript for
everything in WP Core. If hiring a JavaScript developer to update old jQuery
code, make sure the JavaScript developer is wise enough to know when porting
the code to native JavaScript makes more sense than continuing to use jQuery
and when using jQuery really is the best approach. In other words, do not hire
someone like me, I pretty much only use jQuery but that is admittedly not
always the best approach, just the convenient approach.

= ‘Use Content Distribution Network’ option =

Disabled by default.

By default, the updated jQuery scripts are served from within this WordPress
plugin. This is because responsible plugin and theme developers do not (in my
opinion) utilize third party resources by default.

I recommend you enable this option however doing so will result in frontend
pages being served that reference a Third Party Resource. Links to those
services and their privacy policies follows the section on Plugin Options.

= ‘Use Subresource Integrity’ option =

Enabled by default.

This option only has meaning when the ‘Use Content Distribution Network’ option
is enabled.

This option will add a Subresource Integrity hash that browsers can use to
verify the resource retrieved is valid opposed to a modified possible trojan.

The only logical reason I can think of to disable this option is if you already
have a different plugin that manages Subresource Integrity.

= ‘Select Public CDN Service’ option =

Set to ‘jQuery.com CDN’ by default.

This option only has meaning when the ‘Use Content Distribution Network’ option
is enabled.

This option lets you select which public CDN you wish to use from a list of
five Public CDN services that are listed at
https://jquery.com/download/#using-jquery-with-a-cdn

For many websites, the default ‘jQuery.com CDN’ is *probably* the best choice
but it may not be the best choice for all websites, depending upon where the
majority of your users are located geographically and which public CDN has a
better response time in that geographical region.


== External Third Party Services ==

When you enable the CDN option (recommended but disabled by default) a third
party service will be used. You should make sure your website Privacy Policy
makes users are aware of this. In the current version of WordPress (WP 5.4.1),
the default Privacy Policy does make users aware of this, but you have to
actually publish that policy and you should check it yourself in case it has
been modified or in case the policy you have predates that policy and does
not include a Third Party Resource notice.

This plugin *always* sets the
`crossorigin="anonymous"`
attribute in association with the Third Party Service, which instructs the
browser not to send cookies or any other authentication information to the third
party when retrieving the resource.

It is possible another plugin or your theme runs its own filter on the
`jquery-core` or `jquery-migrate` script handles that results in that attribute
no longer being set. Make sure to check by viewing the HTML output of a non-
administrative frontend page.

By default, the plugin will set the
`integrity="[[expected base64 encoded hash]]"`
attribute in association with the Third Party Service, which instruct the
browser not to use the downloaded resource if the hatch does not match,
protecting your users from trojans.

The previously mentioned note about other plugins or themes running a filter
on the `jquery-core` or `jquery-migrate` script handles applies here as well.

This plugin uses the hashes associated with the files as provided by
https://jquery.com/download/

These are the potential third party services:

= jQuery.com CDN =

The default CDN used by this plugin when a CDN is enabled.

Link to service: https://code.jquery.com/

jQuery.com CDN is actually powered by StackPath.

Link to service: https://www.stackpath.com/
Privacy Policy: https://www.stackpath.com/legal/privacy-statement/

= CloudFlare CDNJS =

When you have a CDN enabled and have selected the CloudFlare CDNJS option then
the CloudFlare CDNJS service will be used.

Link to service: https://cdnjs.com/libraries/jquery/

For jQuery, CDNJS is powered by CloudFlare.

Link to service: https://www.cloudflare.com/
Terms of Use: https://www.cloudflare.com/website-terms/
Privacy Policy: https://www.cloudflare.com/privacypolicy/

= jsDelivr CDN =

When you have a CDN enabled and have selected the jsDelivr option then the
jsDelivr CDN will be used.

Link to service: https://www.jsdelivr.com/
Privacy Policy: https://www.jsdelivr.com/privacy-policy-jsdelivr-net

= Microsoft CDN =

When you have a CDN enabled and have selected the Microsoft CDN option then the
Microsoft CDN will be used.

Link to service: https://docs.microsoft.com/en-us/aspnet/ajax/cdn/overview
Terms of Use: https://docs.microsoft.com/en-us/legal/termsofuse
Privacy Policy: https://privacy.microsoft.com/en-us/privacystatement

= Google CDN =

When you have a CDN enabled and have selected the Google CDN option then the
Google CDN will be used.

Link to service: https://developers.google.com/speed/libraries
Terms of Use: https://developers.google.com/terms/site-terms
Privacy Policy: https://policies.google.com/privacy

The Google CDN does not host the jQuery Migrate plugin. When you have a CDN
enabled and the Migrate option enabled and have selected the Google CDN
option then the jQuery.com CDN will be used for the jQuery Migrate plugin.
The Privacy Policy for that CDN is listed earlier in this `readme.txt` file.


== Included Public Keys (SRI Hashes) ==

This plugin includes the Subresource Identity public keys (hashes actually,
they) for the minified versions of jQuery 3.5.1 and jQuery Migrate Plugin 3.3.0
which are published on the https://code.jquery.com/ website.

The file `versions.php` hard-codes the public keys. The SRI values that are
hard-coded in that file can be checked against the provided values at
https://code.jquery.com/

= CloudFlare CDNJS =

The minified jQuery Migrate plugin hosted at CloudFlare CDNJS has the following
addition at the end of the file:

    //# sourceMappingURL=jquery-migrate.min.map

For this reason, an SRI specific to the CDN is used when CloudFlare CDNJS is
the selected Public CDN.

= Security Reason for Hard Coding SRI Hashes =

For security reasons, it is not safe for these SRI hash values to be stored in
the database where an SQLi, XSS, or CSRF attack against an administrator could
result in their values being altered. These values really do need to be
hard-coded and defined as a PHP constant.


== Public CDN Notes ==

As of May 20th, 2020 the following notes apply.

The Microsoft CDN does not yet have current versions of either the core jQuery
library or the Migrate plugin. If you select that CDN, the files will be served
from your host until the Microsoft CDN has them.

The Google CDN does not host any version of the jQuery Migrate plugin. If you
select that option and have the Migrate plugin enabled, that plugin will be
served from the jQuery.com CDN.

The jsDelivr CDN and CloudFlare CDNJS have the current core jQuery library and
they pass SRI however the Migrate plugin they host, even though the version
number appears to be identical, has a different checksum. It is my *suspicion*
they are minifying the Migrate plugin themselves with a slightly different
algorithm rather than using the minified version of that plugin as provided by
jQuery.com. I will attempt to communicate with them and see if I can get them
to start using the minified version as supplied by jQuery.com but at this time
if you select either of those options and have the Migrate plugin enabled, the
version they host will fail the SRI test causing the client browser to fetch it
from your server instead.


== Plugin / Theme Compatibility ==

This plugin does not set the version for jQuery. Doing so greatly reduces the
odds that a browser will recognize it already has the scripts cached. Some
plugins and themes may query for the version of jQuery being used. They should
not do that, but some do. They will not get an answer if this plugin has run
before they run and they will get the wrong answer if they run before this
script has run.


== Update Policy ==

I will try to update this plugin when new versions of jQuery are released but
it may not be as fast as some may like. You can bug me by leaving sending an
e-mail to pipfroshpress[AT]gmail[DOT]com.

Please note updates to this plugin with new versions of jQuery will not be
pushed until that majority of the supported CDNs have the file.

For the included jQuery Migrate plugin, I am less likely to notice when new
versions are available but I do check whenever a new version of jQuery itself
is released. Again you can bug me if needed.

Development takes place on github. The `master` branch will usually be exactly
the same as what is distributed through WordPress except it will have a small
`README.md` file. The branch `pipjq` is where I develop and may not always be
stable. When a new release ready *and tested* from the `pipjq` branch, it will
be merged with `master` and then repacked for distribution through WordPress.

Please use the distribution from WordPress rather than from github. The version
from WordPress is audited by more eyes than my github.


== Versioning Scheme ==
Versions use an `Major.Minor.Tweak` scheme` using integers for each. Code in
github may have a `pre` appended at the end to indicate is not a released
version and should not be used on production systems.

= Tweak bump =
`Tweak` is incremented by one when a minor change is made, such as adding a new
language to the translation support. Generally you can ignore upgrading this
plugin when there is just a `Tweak` bump.

= Minor bump =
`Minor` is incremented by one when a functional bug is fixed or when an update
to jQuery or the jQuery Migrate plugin is made that is not a substantial jQuery
change. When `Minor` is bumped, `Tweak` will reset to `0`. Generally you should
upgrade when `Minor` is bumped.

= Major bump =

`Major` will be incremented when there is an upgrade to jQuery that is
significant in nature. Both `Minor` and `Tweak` are reset to `0` when `Major`
is bumped.

Generally you should test an update to `Major` before updating on a production
system just in case some of your jQuery code needs tweaks before deployment.


== Translations ==

This plugin is ready for translations but so far does not actually have any.
Note that the only strings where translations are beneficial require
administrative privileges to see (the Settings). Hopefully translations will
soon be made for the benefit of WordPress administrators who have a preferred
written language other than English.


== Frequently Asked Questions ==

These are *potential* Frequently Asked Questions. No one has yet asked them.
Actual frequently asked questions will be added if they come.

= Why should I use this plugin over another plugin that does the same thing? =

Only you can answer that question. The reason why I chose to write this plugin
is that the other options I looked at either did not allow use of a CDN and/or
did not provide Subresource Integrity when they did use a CDN and/or did not
provide a fallback to serve jQuery and jQuery Migrate locally when a CDN failed
to deliver the file or delivered a file that did not pass the SRI check.

If those issues do not concern you then maybe a different plugin addresses the
issues that do concern you better than this one does.

For example, this plugin only provides for one version of jQuery and does not
provide other jQuery plugins such as jQuery UI. If those are important to you
then use a different plugin (or fork this one). It is *possible* I may add some
of the more common jQuery plugins in the future as options but that has to be
carefully done to make sure the `$handle` argument to `wp_enqueue_script()`
matches what is expected by themes and plugins that want the jQuery plugin.

= Does the bundled version on jQuery impact blog administration? =

No. It is *possible* that some versions of WordPress and *probable* that some
plugins use jQuery for site administration that would break with the newer
versions of jQuery. I recommend always using the version of jQuery that ships
with WordPress for admin pages, so this plugin does not upgrade the WP Core
jQuery for admin pages.

= Why does this plugin require PHP 7 or newer? =

I have neither the financial means nor the will-power to set up a testing
environment suitable for testing with versions of PHP that no one should still
be using anyway.

= Why do I still sometimes see requests for jQuery in my server access log? =

If you enabled a CDN but still see requests for jQuery in your server access
logs, it means the client was not able to download jQuery from the CDN or that
the file it downloaded did not pass security checks. When this happens, it
triggers a fallback loading of jQuery from within the plugin directory.

= Can I hire you to write me a plugin? =

It does not hurt to ask but probably not, Pipfrosch Press is brand new and
right now, coding for it is taking the majority of my time. When I code
something for Pipfrosch Press I do not mind sharing I will share it, but
projects outside of Pipfrosch Press are difficult for me to commit to.


== Screenshots ==

1. The settings menu.
2. Generated WordPress HTML source code showing the failed CDN fallback code.


== Changelog ==

= 1.0.1 (Thursday May 21, 2020) =
* Updated `readme.txt` to include links to Public CDN services and their privacy policies.
* Other cleanup of documentation.
* No code changes from the 1.0.0 version.

= 1.0.0 (Wednesday May 20, 2020) =
* Initial release (jQuery 3.5.1 library with migrate 3.3.0).


== Upgrade Notice ==

The version of jQuery in WordPress Core is very archaic and limits what you can
do as a result. Upgrading the jQuery available for your front-end content will
open up options.
