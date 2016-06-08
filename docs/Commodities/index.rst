
.. _commodity:

Commodity
=========

A commodity is something that represents value. Usually these are currencies - like Euros or US Dollars - but they can also be things like property deeds, votes or shares in a company. Every bank_ can hold any commodity. Commodities come into existance when someone decides to become an issuer_ and issues it onto the network. To use a particular commodity like Euros, you'd use its commodityTag_.

.. _commodityTag:

Commodity Tag
-------------

Commodities are neatly organised into groups to help clearly see what it is. They must always be lowercase - For example, 'currency.usd' is the US Dollar. 'shares.nyse.goog' refers to shares of Google on the New York Stock Exchange.

Claiming Tags
-------------

Tags form a 'tree' of possible options. Note that tags are intended to be free. Claiming a sub-tag depends on the upper level tags properties; some such as 'currency' are restricted. Claiming a new 'top level' tag is restricted by default. Consider the following examples:

- 'currency' is a top-level tag. It's claimed by the network and is restricted; this is because the network wants the central bank of their currency to claim their tag.
- 'currency.gbp' is a sub-tag of 'currency'. Whoever wishes to claim this has to send the claim to the owner of 'currency', unless it is unrestricted.
- 'shares' is also a top-level tag. This one is restricted to known stock markets and Governments.
- The London Stock Exchange may claim, for example, 'shares.lse'. Sub-tags such as 'shares.lse.ba' representing British Airways are automatically assumed to be owned by the parent LSE issuer, unless there is a more specific tag.

Standard Top-Level Tags
-----------------------

The proposed main top-level tags are listed here. An up-to-date version can be obtained from the network using the Root API.

:currency
    Fiat currencies such as GBP or USD. Example: 'currency.gbp'

:substance
    Physical substances such as gold, silver, oil or plastics. Example: 'substance.plastic.pete'

:voucher
    Unrestricted. Represents any form of redeemable voucher/ gift card. Used when the receiving username is unknown or a physical gift is wanted. Example: 'voucher.itunes.currency.gbp' represents a pound sterling iTunes voucher. If the user requests for, for example, 'voucher.itunes' their preferred currency is appended onto the end (provided the voucher issuer has a sub-tag which matches). The merchant then simply accepts 'voucher.itunes' as well as 'currency'.

:stock
    Shares in a public or private company. Example: 'stock.lse.ba'

:shares
    Forwarder to 'stock'. Example: 'shares.lse.ba'

:land
    Physial land. For some futuristic optimism, this is actually organised by planet. Example: 'land.e.warwks.81920' representing a particular plot in Earth/Warwickshire (UK). More information on the plot itself is obtained through the Issuer API. If authoritive districts change, forwarders are simply added.

:ip
    Intellectual property. Example: 'ip.tm.ukipo.11827' representing a trademark filed at the UK IPO.

:vote
    Any form of user vote, political, corporate etc. Example: 'vote.election.us.2016' representing a vote in a US Election. (A vote is placed by transferring it to the selected option, each of which has it's own address). Note: This inherently leaks unwanted information; a Government could figure out who voted for what. More design work, namely 'anonymous issuing', is required for this tag to work as intended.

:virt
    Virtual versions of all tags. Unrestricted. All top-level tags are also registered as sub-tags here. Example: 'virt.land.gt.91082' represents virtual land in an online game.

:x
    Unrestricted open-to-all tag designated for testing purposes. All top-level tags are also registered as sub-tags here, for example, 'x.currency'.

