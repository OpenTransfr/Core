Welcome to the OpenTransfr network documentation!
=================================================

This documentation describes the structure of all parts of the network in depth. Please note that it is very much a work in progress and the design will regularly change during this process until it is marked as stable.

.. toctree::
   :maxdepth: 2
   :local:
   Components

.. _overview:

What is OpenTransfr?
--------------------

OpenTransfr is a payment network designed to handle currencies and commodities that you see and use everyday such as pound sterling or the US dollar. It's primary goals are to be free, fast and open to everyone to allow as much innovation as possible in the financial sector. This network takes concepts from cryptocurrency networks and combines them with traditional systems to create a hybrid that works for everyone. This brings up two questions - why do we need new transaction networks, and what's wrong with cryptocurrencies?

Why do we need a new network?
-----------------------------

- Bank fees, transaction fees and exchange fees are excessively expensive
- Existing payment networks are too centralised, putting too much power in one place
- It's difficult to change from one bank to another
- Extremely limited information in the existing banking system on e.g. what has been purchased and where from
- As a consumer, I have no idea who currently holds my card details. This creates security problems, plus..
- It’s difficult to know what payments will actually be leaving your bank account and when
- Too much trust is placed with merchants in dealing with card details, e.g. providing them over the phone is a major security risk. This creates hassle for merchants as they have to deal with extra security measures.

Why not use cryptocurrencies out of the box?
--------------------------------------------

First and foremost, cryptocurrencies have changed everything. Their existing setup can just be considered version 1 in an ever evolving financial system. In case you haven't been following, Bitcoin is having some extremely widespread problems. Here's just a few of them:

- It was built under the assumption that no-one will gain a 'majority', however, a Chinese-lead group has managed to do exactly that.
- Anyone who holds a majority (or creates a powerful computer to instantly get a majority) can overtake the blockchain and entirely replace it with fake information.
- The currency itself is extremely volatile
- Shrouded in legal problems because of a total lack of regulation. As a result, banks naturally hate it.
- Blockchain systems waste far too much energy
- Addresses look too complicated for consumers
- People keep loosing their private keys and as a result, permanently loosing access to their value.
- Blockchain has fundamentally slow transaction speeds. A Bitcoin transaction can take anywhere from 10 minutes to a whole day as the network throughput is seemingly maxing out at 1 transaction per second.
- Current cryptocurrency systems essentially completely fail to scale, particularly where high latency links are introduced as the entire network slows down to suit them.

For more information, there's an excellent blog by a previous Bitcoin developer here: https://medium.com/@octskyward/the-resolution-of-the-bitcoin-experiment-dabb30201f7#.7tweula3q

How can we fix it?
------------------

Here's the process that got the design of OpenTransfr to where it is today:

- Cryptocurrencies have a lot of excellent properties which must be kept. The security, the transparency and the anonymity are all ideal. So, we start from a cryptocurrency design and build up from there.
- Blockchain has failed, so let's remove that.
- The consensus system has failed too, so we'll replace that as well.
- The network needs to work for everyone, so we want some regulation, but not too much (we don't want Governments to see everything you bought, for example, but we do want them to have something against things like criminal activity), and it has to be trustworthy. The network must be allowed to grow and evolve as new and exciting ideas appear.
- Abstract off addresses with a username.
- It needs to be a singular network that can transfer anything so it can work with all existing commodities.
- If there's no blockchain then we'll need a new way of introducing value into the network.
- Merchants must never get bank details.

Now we've got some of the baseline requirements, the network design is summarised as follows:

.. image:: images/Network-Overview.png

- A tiered system. The very top tier, called the root, is the most trusted part of the network. There can be more than one root, grouped together based primarily on latency between the nodes. It's their job to authorise transactions. Everyone connects up to the root to collect information and submit requests, optionally forming new tiers of servers around the root to help spread load. Everything root does is publically visible and verifiable by anyone.
- Individual organisations in the root, each called a root node, hold a single 'vote' each. It doesn't matter how much computing power they have.
- A majority of root nodes must all agree that a transaction is valid for it to go through. This is done by them each using their private key and signing the transaction to say that they believe it is valid (because the 'from' account has enough funds).
- The tiering plus majority voting is what makes it a hybrid of centralised and distributed. This is considered most favourable because distributed systems have a habit of becoming centralised (think GitHub and Bitcoin itself), so it might as well be a trusted 'best of both'.
- An organisation joins root by petitioning the root they wish to join. Root nodes may then vote to accept them in or not and, as usual, a majority is required. There must be no fees to become an organisation or to join root, however, typically a root will consist of trusted financial organisations to make the network trustworthy.
- Root tracks current balances in all the known addresses. Addresses are anonymous and are the public keys of public-private key pairs.
- Banks, typically known and trusted ones, store the set of private keys.
- A username is related to a particular bank. A sender can ask a bank for a new address for a given username, then perform a transaction.
- A transaction states which address value is coming from and which it is going to. It's signed using the senders private key.
- Value enters the network when an issuer 'issues' it onto the network. For currencies, this would be a central bank role. For example, a central bank could receive its currency over a traditional transaction, and then issue the same amount onto the network.
- There's only ever one issuer per commodity. This prevents any form of 'one pound/euro/dollar etc being worth more than another' and a withdrawal from the network guarentee (intended to be rare).
- As the network handles multiple types of commodity, it has an exchange to swap one for another.
- Everything will always be what we'll call a 'push' transaction; that's where the consumer is always sending value out of their account (either manually or as a result of a 'subscription'), rather than it being 'pulled' out by a merchant. This way the consumer can always see and know exactly what is going out of their bank and when, and cut something off without having to go through the existing major hassle of cancelling a card. Plus, merchants don't need to worry about security problems in order to take payments.
- All payments will have much more metadata, known only by the users bank, allowing analysis and categorisation to be trivial. This would make things like tax payments entirely automatable.
- Banks will share a common API allowing API users to setup subscriptions, perform payments, transfer an account to another bank etc.

The end results of the above are a network which is trustworthy, open, more secure than existing systems, distributed yet able to scale (because root is intended to be small groups of organisations and scaling problems would otherwise happen with the 'majority consensus' aspect) and built on a cryptographic guarentee.

For more detail on individual components, such as roots, issuers or commodities etc, see their related documentation.
