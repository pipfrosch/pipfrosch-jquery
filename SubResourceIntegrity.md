SubResourceIntegrity.md
=======================

This file is meant to be a quick primer on Subresource Integrity (often
abbreviated as SRI) and why it is important.


Third Party Resource
--------------------

When a web browser loads a webpage from your server but has to make requests to
other servers as part of rendering the page (for example fonts, scripts, and
images), those resources are called ‘Third Party Resources’ because they are
served from a server other than the server in the browser address bar.


Content Distribution Network
----------------------------

A Content Distribution Network (often abbreviated as CDN) is usually a server
that is tuned to serve static content (content that does not change) really
fast. With the proper hardware and server software, a CDN can serve static
content to your clients much faster the server you use for dynanic generated
content (such as WordPress). Using a CDN for your static content can really
speed up how fast your website loads and renders in a browser.

This performance enhancement does not come for free, you typically have to pay
a commercial service to host your static content for you.


Public CDN
----------

A Public CDN is a CDN that hosts static files commonly used by many websites
rather than static files specific to your website. Use of a Public CDN is free,
but you do not get to pick what files are available from the Public CDN.

The most common files you will find hosted on a Public CDN are open source
JavaScript libraries (such as jQuery) and open source webfonts.

If a user visits twelve websites that all use the same version of jQuery served
from the same CDN, the browser may have to download it once if it does not have
it cached already but then for the other eleven websites, the browser can just
use the copy it has cached. This is a huge benefit, especially for JavaScript
libraries.

### Render Blocking Content

Render blocking content is content the web browser must completely download
before it can start rendering the web page to the user. Any external resource
referenced in the `<head/>` node of a web page is render blocking content, and
that is usually where JavaScript libraries are loaded so that they are
available when needed by JavaScript within the content itself.

By using a Public CDN for JavaScript libraries, you increase the odds that the
user already has the file cached in their browser and instantly available so
that the browser does not have to wait for it to download before it can start
rendering the page to the client.

For performance, using a Public CDN for libraries like jQuery is definitely the
right choice, but there are both some security and high availability
considerations that need to be addressed when using a Public (or any) CDN.


Trojan Scripts
--------------

















