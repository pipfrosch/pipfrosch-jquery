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

Resources served from a Public CDN should *always* have the attribute
`crossorigin="anonymous"` set. This instructs browsers not to send any
identifying information (such as cookies) to the Public CDN when retrieving the
file. Some (most?) browsers behave this way by default now with JavaScript from
a third party resource, but they will include the `HTTP_REFERER` header telling
the browser what website caused the request for the file. In theory anyway,
using the `crossorigin="anonymous"` attribute prevents even that from being
sent. I have not tested that theory.


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

If an attacker is able to fool the DNS server a client uses to resolve the
third party resource, the attacker can cause the client (web browser) to
download the file from a server the attacker controls, allowing them to serve a
malicious script to the client that the client then executes.

Obviously only using TLS (SSL) for third party resources helps mitigate this as
the attacker has to have a fraudulent certificate for the browser to accept the
file, but that actually happens with some frequency.

If an attacker can gain access to the CDN itself, the attacker could also put
a trojan on the CDN. This does not happen too often but it has happened in the
past.

In both scenarios, the mitigation for this attack is __Subresoure Integrity__.


SubResource Integrity
---------------------

This is a means by which your web page tells the client what the checksum of
the remote resource should be.

The `<script></script>` node that tells the client where to retrieve the script
from will have an `integrity` attribute that includes a hash algorithm (such as
`sha256` or `sha384`) followed by a `-` and then followed by a base64 encoded
hash of the file.

When the browser retrieves the remote file, it will hash the file using the
specified algorithm and if the result does not match the specified hash, then
the browser will reject the file as corrupt. This is very effective at
protecting the client from execuring trojan content.

SRI is supported by current versions of virtually every browser commonly in
use. See https://caniuse.com/#feat=subresource-integrity

As of May 22, 2020 the only commonly used browsers that do not support SRI are
Internet Explorer (a dead browser no longer being updated) and Opera Mini
(which executes JavaScript on a proxy rather than in the browser).

However SRI is not completely foolproof.

### The Dynamic Content Hole

Most wensite content, including WordPress content is dynamically generated. If
the attacker can trick the web server platform into using a SRI tag that
matches their trojan script then the browser will still execute it.

The hash for an SRI tag should *never* be stored in a database the wen server
has permission to modify. The best way to ensure the integrity of the hash is
to hard code it in your server code, such as defining it as a constant in PHP.

If an attacker is able to pull off an SQLi attack, they can potentially inject
a hash that matches their trojan script into the database causing your
dynamically generated page to assist in their attack.

This why this plugin defines them as PHP constants in the `versions.php` file.


Robust Fallback
---------------

What happens when the Public CDN goes down, is blocked by a political firewall,
or the file served fails the SRI check?

If the resource is a JavaScript library used by your website then without an
adequate fallback, your website is broken for any user who does not already
have the resource cached.

With JavaScript libraries there is a solution. After the `<script></script>`
node that tells the browser to fetch the library, put in a small piece of
inline JavaScript that tests to see if the library is loaded. If it is not
loaded then instruct the client to download the library from your webserver.

### Timeout Problem

The fallback will not solve the potential problem of a CDN having technical
issues that result in a timeout on the request slowing down the rendering of
your website, but even in that case the client will at least load the library
and the site will function.

However the timeout problem will persist on every page of yours the user visits
until the CDN fixes its technical problem.

There has been talk of adding a `timeout` attribute to help mitigate this issue
but at this time it does not appear exist in any of the standards, so as far as
I know, no browser implements it.

Fortunately this is a rare issue.
