Pipfrosch jQuery
================

Plugin for modern jQuery in WordPress.

__DO NOT USE MIGHT BE BORKED AT ANY GIVEN TIME__

Current Status
--------------

Options page is created and working. Microsoft CDN still does not current
versions of either jQuery or the migrate plugin. All the other CDNs have
current version of jQuery and it passes SRI.

Google does not have the migrate plugin but when Google is selected, this
WordPress plugin will use code.jquery.com for the migrate plugin.

CloudFlare and jsDelivr both have the migrate plugin but their copies do
not pass SRI.

Fortunately this plugin uses it's own copies when a CDN does not have the
file or it does not pass SRI.

Everything seems to be working but some code will be cleaned up.

General
-------

This github is for developing the plugin. When it is ready, right way to
install will be through the WordPress plugin installer where only tested
releases will exist.

Stable releases will not include a `README.md` file, only a `readme.txt`.

Originally my `piptheme` WordPress theme that is __not__ distributed through
WordPress contained an updated jQuery. I decided that was the wrong way to go
about it.

jQuery sometimes has important security updates. Therefore it is important that
updates to jQuery be made available when new releases are available and as such
it would be better for jQuery to be updated in a separate plugin from the theme,
preferably a plugin that can be updated through WordPress itself.

This is an attempt to create such a plugin with needed bells and whistles to do
that. It is not yet ready for submission to their repository.

See the `readme.txt` file.
