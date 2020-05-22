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















