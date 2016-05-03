
.. rootNode:

Root Node
---------

A root nodes job to store a copy of all network information and keep it in sync, most importantly the balanceTable_. When a transaction gets requested, they check if it has been correctly authorised, then forward it on to all the other root nodes. If they agree that there's enough funds and it has been requested by the real owner, the transaction completes. The balance table is then updated and a transaction is logged.

Root Node Operators
-------------------

Operating a root node will be restricted to trusted financial organisations only to ensure that the root remains entirely trustworthy at all times. Typically this would be large banks and central banks. Each organisation can only run one root node (but it can involve multiple machines) so no single organisation is a bigger target than another and so every organisation has the same voting power.

Root Node Safety
----------------

Root nodes are extremely resiliant. A root node can be entirely hacked, taken offline or be situated in a natural disaster and the network will continue to function correctly. This is because:
- Transaction requests must have majority
- There are multiple precautions to prevent any single organisation accidentally providing a hacker with an instant majority. This includes DNS registrars as well as DNS registries like .com itself.
- There are multiple root nodes all syncing data, so there is never any data loss when one goes down.

Root Voting
-----------

The network uses a petition system which is used when network values need to be changed. This can include introducing a new root node or, for example, if a bank had an unlikely catastrophic data event and lost all their private keys. Root can then authorise a transaction into a new set of addresses for the bank, provided the petition passes because a majority of root nodes agree. These votes are also a type of commodity_ which helps reduce the amount of specialised code (and the potential security holes that entails) in the root itself.
