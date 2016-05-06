.. _issuer:

Issuer
======

Issuers define a commodity and issue them onto the network. There is only ever *one issuer per commodity*. This is so it can guarentee a withdrawal from the network, but more on that later. Usually, anyone can be an issuer but there are some special exceptions. For example, traditional currencies are restricted - the central bank that normally issues the currency decides who issues it on the network. In most cases, the central bank itself is the best issuer. However, you can create your own virtual currency or commodity and issue it however you want.

Issuing Vouchers
----------------

Vouchers are considered a form of commidity too which allows them to sit in a users bank account. This avoids the need for merchants to implement an account balance system. Vouchers use the same token system as other forms of issuing, and they essentially work as follows:

- Voucher purchaser performs a transfer to the voucher issuer
- The voucher issuer responds with a token (Essentially the voucher itself). This token would most likely be printed.
- The claimer calls the redeem API with a public key to claim into and their token
- The issue then occurs into the given public key

Anonymous issuing
-----------------

The network has many properties which are favourable to a political voting system. For example, transparency, anonymity within the root and the ability for anyone to view transactions live. If we view votes as a kind of currency, and the action of voting for something is a transfer, an interesting usage of the network begins to emerge. However, there are a few key components that would be required in order for such a system to be widely possible. A Government (or anyone who wishes to run a vote) would be the issuer of votes, and would need to only issue them to users who are eligible to vote. Issuers always issue to a public address, however, as the flow of transactions from that public address are clearly visible, this would expose exactly who a particular user voted for to the Government. In order to avoid exposing this, anonymous issuing is required.
