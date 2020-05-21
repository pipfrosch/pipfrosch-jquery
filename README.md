Pipfrosch jQuery
================

Plugin for modern jQuery in WordPress.

this branch is for cleaning up the code in preparation for WordPress.org
submission.

You can make a zip archive by executing the bash script `mkzip.sh` on a
UNIX-ish system that has both `mktemp` and `zip` available.

Current Status
--------------

Pre-submission cleanup work is finished, merging into master.

General
-------

This github is for developing the plugin. When it is ready, right way to
install will be through the WordPress plugin installer where only tested
releases will exist.

Stable releases will not include this `README.md` file, only a `readme.txt`.

Originally my `piptheme` WordPress theme that is __not__ distributed through
WordPress contained an updated jQuery. I decided that was the wrong way to go
about it.

jQuery sometimes has important security updates. Therefore it is important that
updates to jQuery be made available when new releases are available and as such
it would be better for jQuery to be updated in a separate plugin from the theme,
preferably a plugin that can be updated through WordPress itself.

This is an attempt to create such a plugin with needed bells and whistles to do
that.

See the `readme.txt` file.
