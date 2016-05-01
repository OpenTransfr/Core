Welcome to the OpenTransfr network documentation!
=================================================

This documentation describes the structure of all parts of the network in depth. If you're just looking for the various API's, see `External APIs`_.

.. contents:: Table of Contents
   :local:

.. _overview:

Overview
========

.. image:: images/1.png

At the heart of the network is a group of servers, each being a rootNode_, called the root - it's where the magic happens. A majority of those servers must all agree that a transaction is valid for it to go through. Issuers and tier 1 users connect up to the root to collect information and submit requests. Everyone else forms a tree of servers around the root, tier 1 onwards, to help spread load and to keep the root operating quickly. Anybody can be in that tree, however there are a few typical node types that will occur inside it.

.. broadcastNode:

Broadcast Node
==============

A broadcast node receives transaction information and broadcasts it on to anybody who wants it. There can be multiple broadcast nodes in a chain to dramatically increase the amount of users receiving the data.

.. rootNode:

Root Node
=========

A root nodes job to store a copy of all network information and keep it in sync, most importantly the balanceTable_. When a transaction gets requested, they check if it has been correctly authorised, then forward it on to all the other root nodes. If they agree that there's enough funds and it has been requested by the real owner, the transaction completes. The balance table is then updated and a transaction is logged.

Root Node Operators
===================

Operating a root node will be restricted to trusted financial organisations only to ensure that the root remains entirely trustworthy at all times. Typically this would be large banks and central banks. Each organisation can only run one root node (but it can involve multiple machines) so no single organisation is a bigger target than another and so every organisation has the same voting power.

Root Node Safety
================

Root nodes are extremely resiliant. A root node can be entirely hacked, taken offline or be situated in a natural disaster and the network will continue to function correctly. This is because:
- Transaction requests must have majority
- There are multiple precautions to prevent any single organisation accidentally providing a hacker with an instant majority. This includes DNS registrars as well as DNS registries like .com itself.
- There are multiple root nodes all syncing data, so there is never any data loss when one goes down.

Root Voting
===========

The network uses a petition system which is used when network values need to be changed. This can include introducing a new root node or, for example, if a bank had an unlikely catastrophic data event and lost all their private keys. Root can then authorise a transaction into a new set of addresses for the bank, provided the petition passes because a majority of root nodes agree. These votes are also a type of commodity_ which helps reduce the amount of specialised code (and the potential security holes that entails) in the root itself.

.. _commodity:

Commodity
=========

A commodity is something that represents value. Usually these are currencies - like Euros or US Dollars - but they can also be things like property deeds, votes or shares in a company. Every bank_ can hold any commodity. Commodities come into existance when someone decides to become an issuer_ and issues it onto the network. To use a particular commodity like Euros, you'd use its commodityTag_.

.. _commodityTag:

Commodity Tag
=============

Commodities are neatly organised into groups to help clearly see what it is. For example 'currency.usd' is the US Dollar. 'shares.nyse.goog' refers to shares of Google on the New York Stock Exchange. Each commodity also has a unique number which is used internally on the network. When needed, the edges of the network simply swap the tag with the number before submitting requests.

.. _bank:

Bank
====

A bank is the consumer facing organisation that holds your bank accounts. All banks must provide the `Standard Bank API`_ and declare the endpoint that their API is found at. These will usually be banks that you recognise and trust. Anyone is able to setup a bank but only financially regulated ones are endorsed by the network. Consumers are warned when a bank is not.

.. _issuer:

Issuer
======

Issuers define a commodity and issue them onto the network. There is only ever *one issuer per commodity*. This is so it can guarentee a withdrawal from the network, but more on that later. Usually, anyone can be an issuer but there are some special exceptions. For example, traditional currencies are restricted - the central bank that normally issues the currency decides who issues it on the network. In most cases, the central bank itself is the best issuer. However, you can create your own virtual currency or commodity and issue it however you want.

.. _exchange:

Exchange
========

The network has a built in exchange called OpenExchange or just OPEX for short. It's a matching exchange - to convert one commodity into another, an exchange must also occur in the opposite direction. For example, if you want to swap GBP for USD, someone else must be swapping USD for GBP. To prevent there being potentially millions of pairs of commodities, everything exchanges through a central commodity to keep things flowing fast. The matchings can still take a few minutes to be paired with something going the other way though, so exchanges are typically done 'offline' rather than during a transaction. Note that the exchange is not used when you happen to be buying another commodity - for example, buying shares with GBP.
