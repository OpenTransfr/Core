# OpenTransfr
OpenTransfr is a free, fast and open payment network. There's no transaction fees, the network is distributed and it can perform transactions using any commodity such as existing currencies. Say hello to the future of payments.

# Brief overview
The OpenTransfr network is a little like DNS. In order to allow the network to scale and to ensure network safety, a hierarchy of distributed nodes is used. The root nodes are the most secure and well trusted systems on the network and they must agree in order for transactions to complete. Transactions themselves are all based on a cryptocurrency system, ensuring top levels of security and trust. For more details on the networks structure, see the documentation at https://opentransfr.readthedocs.io/en/latest/

# Appreciations
OpenTransfr would like to thank the Bitcoin and Stellar communities, as well as the Legion of the Bouncy Castle for their excellent cryptography APIs. This network adopts concepts from both whilst attempting to improve on scalability and some of the fundamental trust issues. The notable differences are:
- History is not stored by every node. Only the latest balances and 5 days of transactions are held by the majority of nodes in the network.
- Network spam is largely blocked by using a hierarchy, meaning transaction fees are not necessary.
- More scalable as each additional node joining the network doesn't inherently slow it down.
- Considerably more trustworthy as no single person or organisation can gain majority voting control by simply having more computing power or more network links than anyone else.
- Any commodity can be traded, or you can invent and issue your own.
- Funds can be put on hold, allowing reversible payments if the goods aren't received.
- All transactions are 'push payments'. In short, this protects consumers as bank account details are never shared to merchants. Instead, a merchant tells a users bank about the transaction, and then the user authorises and performs the transaction. From the users point of view, this is typically no different from their existing experiences, except for payments e.g. over the phone where a user would normally give the merchant their card details. Instead, a merchant gives a user a very short code which they can enter to see all the transaction information and authorise it. In short, there's a lot more transparency and no risk of card details being stolen or charged repeatedly.
