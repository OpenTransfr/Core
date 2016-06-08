.. _bank:

Bank
====

A bank is the consumer facing organisation that holds a users bank accounts. All banks must provide the Standard Bank API and declare the endpoint that their API is found at. Typically these will be banks that you recognise and trust; Anyone is able to setup a bank but only financially regulated ones are endorsed by the network itself. The ability for anyone to setup a bank is primarily for testing, entry and innovation uses.

Setting up a bank
-----------------

In order to become a bank on the network, at a minimum, you need a domain name that you can prove is yours. Contact information is obtained via WHOIS which can then be edited later. A simple UI for the bank creation process can be found at https://txroot.opentrans.fr/ui#screen=entity.create

Internally the join process works like this:

1. A company that wishes to join the network as a bank firstly provides their website domain name, for example “bank.opentrans.fr” and a public key through the entity/create API.
2. The receiving root node must now trust that the requesting user has access to the given domain. The response here is similar to the ACME protocol (as used by Let's Encrypt). Essentially, the user is challenged to prove ownership by either creating a DNS record or by serving a special piece of text over HTTPS.
3. The user then fulfils the challenge by creating the file or DNS entry. The user then informs Root that the challenge is complete. Root then validates it by e.g. performing a DNS lookup or loading the webpage.
4. Provided the validation is successful and it achieved a majority in the receiving root, the entity has been created.

Signing your DNS
----------------

It's very easy to forge DNS records. In order to prevent your DNS from being either hacked and changed or records being changed in-flight by untrusted proxies, it's highly recommended to provide a signature in your DNS. To do this, add a TXT record at your endpoint with the following:

opentx sig:{base64 encoded signature}

The data to sign is as follows:

1. Get all your IP and IPV6 address records
2. Treat the addresses as strings 'as-is' on the records
3. Sort alphabetically
4. JSON stringify the set with no spaces or tabs. The result looks like this (with complete IPs):

'["178.232..","178.233..","2a03:b0c0:.."]' // Sign this string with your private entity key.

Anyone wishing to verify the signature can perform the same steps as above and use your public entity key.

A Javascript implementation is available at https://txroot.opentrans.fr/v1/dev. Pop open the console, load your private key with loadKey("hex_key") or generate one for testing with generateKey(), then call signIPs("your.domain.com").

Ascending to root
-----------------

In order to become fully certified and act as a root node, an entity must prove that it is regulated by a financial regulator (for example, the FCA in the United Kingdom). They submit the reference number with the regulator to root, which in turn then check the entity details match those of the regulator. If a majority agree, then a minimum delay of 5 days begins, after which the entity can then act as a root node. 