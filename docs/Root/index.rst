.. _roots:

Roots
-----

A root is defined as a collection of organisations which all contain a copy of the core information that operates the network. The information is primarily the following:

- The balance table (a root specific set of all address balances)
- The commodities table (a universal table of all available commodities and their issuer)
- The username table (a universal table of unique usernames and their nice names)
- The entity table (a universal table of primarily root nodes and trusted banks, along with which root they belong to). Tracks which root nodes have a link to an external root node.
- The transaction table (the transactions from the last 5 days). Archive nodes store the full history.

A root node is a single organisation within their root. For value to successfully transfer from one balance to another, root nodes must reach consensus; essentially an agreement that there are enough funds and it is not a fraudulent transaction. The consensus mechanism is described in the transaction section.

.. _multipleRoots:

Multiple Roots
--------------

Reaching consensus requires a group of root nodes to communicate with each other. Each transaction triggers 3 messages between a receiving root node and all the other root nodes in the group. For example, if there are 10 root nodes, then (10 - 1) * 3, 27, messages are exchanged. All users must be equally represented, so latency caused by geopolitics would quickly become a major issue. To solve this, multiple roots are required.
Trusted financial organisations from large geopolitical bloc's collectively host the bloc's root. Roots communicate with each other when an external transaction occurs - essentially one which transfers value from one root to another. Such a system scales well into the future; for example, in the event that a Mars colony is created within the next decade, that colony would be able to operate it's own root which can, in turn, communicate with the roots back on Earth. In essence, the only transactions that would be heavily affected by latency are ones which move value from one planet to another.


.. rootNode:

Root Node
---------

A root nodes job to store a copy of all network information and keep it in sync, most importantly the balanceTable_. When a transaction gets requested, they check if it has been correctly authorised, then forward it on to all the other root nodes. If they agree that there's enough funds and it has been requested by the real owner, the transaction completes. The balance table is then updated and a transaction is logged.

Root Node Operators
-------------------

Operating a root node will be restricted to trusted financial organisations only to ensure that the root remains entirely trustworthy at all times. Typically this would be large banks and central banks. Each organisation can only run one root node (but it can involve multiple machines) so no single organisation is a bigger target than another and so every organisation has the same voting power.

Joining as a root node
----------------------

To become a root node, you'll need to:

1. Create an `entity`_ (note: registered banks have already done this).
2. Check that your domain registrar is not used by more than 20% of the root you wish to join. `More info<_rootNodeDns>`_.
3. Petition the root you wish to join. You must petition the root which is geographically closest.
4. Other root nodes will vote for you to join using the `root voting system<rootVoting>`_.
5. If you obtain a majority, congratulations! You are now an authorised root node. In general, roots should be no larger than 30 nodes and no smaller than 3.

Root Node Safety
----------------

Root nodes are extremely resiliant. A root node can be entirely hacked, taken offline or be situated in a natural disaster and the network will continue to function correctly. This is because:

- Transaction requests must have majority
- There are multiple precautions to prevent any single organisation accidentally providing a hacker with an instant majority. This includes `DNS registrars<rootNodeDns>`_ as well.
- There are multiple root nodes all syncing the same data, so there is never data loss when one goes down.

.. _rootNodeDns:

Root Nodes and Domain Registrars
--------------------------------

DNS is typically the weakpoint, so in order to maximise safety more than 20% of root nodes can't share the same registrar. Although a domain registrar hack does not inherently weaken the root (as a hacker would need the private keys in order to fully impersonate a root node), it allows a hacker to proxy a root node. However, a hacker proxying a root node is no different than the same hacker simply joining the network in Tier 1. A hacker would have to both proxy a majority of the root and obtain a majority of the private keys in order to successfully infiltrate a root. This therefore requires a hacker to infiltrate at least 3 registrars and steal private keys from the root nodes themselves. Assuming most banks and registrars have good security, obtaining even one of these keys should be prohibitively difficult.

.. _rootVoting:

Root Voting
-----------

The network uses a petition system which is used when network values need to be changed. This can include introducing a new root node or, for example, if a bank had an unlikely catastrophic data event and lost all their private keys. Root can then authorise a transaction into a new set of addresses for the bank, provided the petition passes because a majority of root nodes agree. These votes are also a type of commodity_ which helps reduce the amount of specialised code (and the potential security holes that entails) in the root itself.
