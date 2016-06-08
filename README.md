# OpenTransfr
OpenTransfr is a group of specifications for a complete financial system. For more details on the designs see the documentation at https://opentransfr.readthedocs.io/en/latest/. It's an end-to-end design meaning that it covers everything from the parts consumers interact with everyday through to the payment network behind the scenes. By designing it end-to-end like this, it helps the system be as consumer friendly as possible as the internals are being constantly pushed forward by the consumer facing front ends. This helps with futureproofing too - for example, banks that support delivery addresses that are suitable for use by drones, payments that can be made from self-driving cars, and a financial system that works between Earth, Mars and beyond. Say hello to the future of finance.

# Brief overview
The core payment network of OpenTransfr is a little like DNS. In order to allow the network to scale and to ensure network safety, a hierarchy of distributed nodes is used. The root nodes are the most secure and well trusted systems on the network and they must agree in order for transactions to complete - a process called reaching consensus. Transactions themselves are all based on a cryptocurrency system ensuring top levels of security and trust.

# Reference Implementations
The major components have reference implementations at the following locations:
- https://bank.opentrans.fr/. OpenBank. An example of a bank with OpenTransfr capabilities.
- https://txroot.opentrans.fr/. OpenTransfr Root Node. An example of a root node on the network.
- https://pay.opentrans.fr/. OpenPay Merchant Services. An example of a merchant service which accepts payments on the network.
- https://issuer.opentrans.fr/. OpenIssuer. An example of a commodity issuer (typically a Central Bank role).
- https://regulator.opentrans.fr/. Regulator. An example of a regulator which essentially defines a new root.

# Appreciations
OpenTransfr would like to thank the Bitcoin and Stellar communities, as well as the Legion of the Bouncy Castle for their excellent cryptography APIs. This network adopts concepts from both whilst attempting to improve on scalability and some of the fundamental trust issues. The notable differences are:
- History is not stored by every node. Only the latest balances and at least 5 days of transactions are held by the majority of nodes in the network.
- Network spam is largely blocked by using a hierarchy, meaning transaction fees are not necessary.
- More scalable as each additional node joining the network doesn't inherently slow it down.
- Considerably more trustworthy as no single person or organisation can gain majority voting control by simply having more computing power or more network links than anyone else.
- Any commodity can be traded, including abstract ones like land, or you can invent and issue your own.
- All transactions are 'push payments'. In short, this protects consumers as bank account details are never shared to merchants. Instead, a merchant tells a users bank about the transaction, and then the user authorises and performs the transaction. From the users point of view, this is typically no different from their existing experiences, except for payments e.g. over the phone where a user would normally give the merchant their card details. Instead, a merchant gives a user a short code which they can enter to see all the transaction information and authorise it. In short, there's a lot more transparency and no risk of card details being stolen or charged repeatedly.
