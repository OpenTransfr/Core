Regulators
==========

A regulator is a financial authority which has the goal of ensuring safe and fair conduct in the financial system. The Financial Conduct Authority in the UK is one example. As with every other node on the network, these are also automated. However, unlike other nodes, they must be reviewed at least once every 5 days.

Primary function
----------------
Typically, a regulator defines a new root and then at least two major banks (under the same jurisdiction as the regulator) join it as root nodes to create a minimum root of 3 nodes. A regulator ensures that their root consists of only trustworthy nodes and that no single organisation has obtained a majority through creating multiple entities. They also act as global verifiers, verifying that all other roots are trustworthy by following the global transaction chains.

How it works
------------
When an entity wishes to join as a root node, the existing root nodes each ask the regulator if the new incoming entity is regulated. If a majority agree that it is, the new root node begins a typically 5 day waiting period, after which it functions as any ordinary root.

Why the wait?
-------------
If there is no delay, then a regulator becomes a single point of failure. For example, imagine this attack scenario. An attacker obtains access to the regulator and is able to impersonate it. They create tens of entities, and their fake regulator marks them all as being regulated, resulting in them all joining root and the end result is the attacker would have obtained a majority. The delay acts as a simple safety barrier; the attacked regulator can see that something has gone wrong and act accordingly (i.e. by simply withdrawing the pending root join requests). The duration of the wait is set by the regulator but must be a minimum of 5 days.
