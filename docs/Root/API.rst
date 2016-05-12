Root API
========

The root API (a JSON web/HTTPS API) is used to submit transactions and receive network information, such as transaction history. Ideally a tree of nodes supporting the API is formed to help spread the load from the root. Note that it is generally recommended to submit transactions directly to the target root and request network information via 3rd party nodes.

Root node discovery
-------------------

Firstly, you must find a root node to use. Note that you **must not** use a hosted list of root nodes as there are a variety of attack vectors surrounding this. An attacker could take over that single point of failure and, from your point of view, entirely replace the root with a fake one. Instead, implementations come preloaded with a known safe list of current root nodes. On first usage, a selection of the roots are contacted and are requested to perform visibility and verify tests to essentially check that it is a real root that you are communicating with. Once it has been safely established, the full latest list of root nodes can then be downloaded from the root itself.

General technique
-----------------

The network has multiple root nodes forming multiple roots, and each root largely operates independently of one another. The process works like this:

- Select a random root node from the root you'd like to talk to
- Use the API at it's declared endpoint

Root endpoint
-------------

Root endpoints are the same as bank endpoints; they're simply a domain name and are typically of the form:

txroot.example.com

The API for the above URL is located at:

https://txroot.example.com/v1/

Note that root nodes *must not* provide the API over HTTP. If it receives a request over HTTP it *must not* redirect it; instead the root node should respond with a HTTP 400 Bad Request. 

API Functionality
-----------------

The API is designed to be self documenting, meaning the API directly exposes the functions available and provides examples on how they are used, as well as defining the types that it can generate or use. An example implementation of the API can be found at:

https://txroot.opentrans.fr/v1/
