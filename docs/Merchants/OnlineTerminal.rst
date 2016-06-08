:: onlineTerminal_:

Online Terminals
================

A merchant must simply embed a button on their website; the type of button varies depending on if they're creating a subscription or a one-off payment. When a user clicks or taps the button, it will either almost instantly complete the transaction (depending on the users own trust settings stored by their bank), or open a window hosted by the users bank which displays the total to pay as usual, optionally asking for a pin depending on the bank and the device. The button also comes with a series of API's which can be used to, for example, display prices in the users preferred currency.

Identifying the merchant
------------------------

When a button is embedded on a website, it doesn't know which merchant the payments should go to. There are a few methods which can be used to identify which merchant it is:

- The button's embed code provides the username of the merchant. This is the least secure, but it is what most merchants will be used to (i.e. it's what PayPal does).
- The domain name of the website that the button is on. This is considered more secure and is recommended for more technically capable merchants. In order for a merchant to 'claim' a domain name, they must prove that they own it. This is done by either adding a DNS record, or by uploading a small text file.

Shopping carts
--------------

The same API also provides functionality to build shopping carts, making the process as easy as possible for both the merchant and consumer. This uses the same backend functionality as all other forms of terminals.
