.. _bank:

Bank
====

A bank is the consumer facing organisation that holds a users bank accounts. All banks must provide the `Standard Bank API`_ and declare the endpoint that their API is found at. Typically these will be banks that you recognise and trust; Anyone is able to setup a bank but only financially regulated ones are endorsed by the network itself. The ability for anyone to setup a bank is primarily for testing and innovation uses.

Setting up a bank
-----------------

In order to become a bank on the network, at a minimum, you need a domain name that you can prove is yours. Contact information is obtained via WHOIS which can then be edited later. An implementation of the bank creation process can be found at https://en.opentrans.fr/bank.create

Internally the join process works like this:

1. A company that wishes to join the network as a bank firstly provides their website domain name, for example “bank.opentrans.fr” and a public key through the `entity.create API`.
2. The receiving root node must now trust that the requesting user has access to the given domain. The response here is compatible with the `ACME protocol`_; essentially, the user is challenged to prove ownership by either creating a DNS record or by serving a special piece of text over HTTP/S.
3. The user then fulfils the challenge by creating the file or DNS entry. Also compatible with the ACME protocol, the user then informs Root that the challenge is complete. Root then validates it by e.g. performing a DNS lookup or loading the webpage.
4. Provided the validation is successful, the receiving root node looks up the domain name to obtain the registrant information such as the company name and country, then creates the entity.

At this point the entity is **not certified**. These banks may join the network for testing purposes, however consumers will be clearly notified that the bank they are using is not regulated.

Editing Contact Details
-----------------------

Contact details are obtained from WHOIS as they are assumed to be correct for most trustworthy banks. However, if a banks details change or are otherwise not correct, the information can be updated through the `entity.update API`.
Banks **must** make sure this information matches the information held by the financial regulator if they wish to become certified.

Becoming certified
------------------

In order to become fully certified, a bank must pass basic security testing and prove that it is regulated by a financial regulator (for example, the FCA in the United Kingdom).

Internally the certification process works like this:

1. A registered entity (see setting up above) provides the name of their regulator from a known list and an ID. This is sent to the `entity.certify API` and signed with the entities private key.
2. The receiving root checks the security properties of the website itself, essentially performing a small security audit:

- Does the website use HTTPS? If not, it's rejected.
- Does the website handle connections on HTTP at all? If it does, warn about a security hole.
- If it does handle HTTP connections, is it a redirect? If it's not a redirect, i.e. the site works as normal over HTTP, reject.
- Does it use HSTS? If not, strongly recommend HSTS registration.

3. Provided it was not rejected, the root node looks up the website on the financial regulator to see if it really is a regulated entity. Note that the matching compares as much information as possible, so a bank must ensure that it's contact details are correct (see above).
4. If a valid match is found, the entity is successfully marked as certified.
