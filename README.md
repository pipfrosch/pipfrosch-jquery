Pipfrosch jQuery
================

Plugin for modern jQuery in WordPress.

this branch is for cleaning up the code in preparation for WordPress.org
submission.

Installation
------------
Assuming this plugin is approved by WordPress, the easiest way to install it
is through the WordPress plugin installer. Search for `Pipfrosch jQuery` and
you will find it. It is not yet approved.

### Manual Install

To install from github you can make a zip archive by executing the bash script
`mkzip.sh` on a UNIX-ish system that has both `mktemp` and `zip` available. If
someone wants to make a Windows compatible batch file (or whatever they call
it, I do not do Windows) I would appreciate it.

The bash script:

https://raw.githubusercontent.com/pipfrosch/pipfrosch-jquery/master/mkzip.sh

Execute that script and it will make a versioned zip archive from the master
branch on github which always has the most recent stable version of this
plugin.

The resulting zip archive can be unpacked in the `wp-content/plugins/`
directory of your WordPress install. If you want a `.htaccess` file created
upon plugin activation that (on Apache servers) tells clients to cache the
jQuery files for a year, then make sure the web server has write permission
to the directory resulting from unpacking the zip archive.

Other than creation of that `.htaccess` file, the web server does not need to
have write permission to the plugin directory for the plugin to function.


Plugin Details
--------------

See the `readme.txt` file. This `README.md` is for the github project.
