.. _roots:

Roots
-----

A root is defined as a collection of organisations which all contain a copy of the core information that operates the network. The information is primarily the following:

- The balance table (a root specific set of all address balances)
- The commodities table (a universal table of all available commodities and their issuer)
- The username table (a universal table of unique usernames and their nice names)
- The entity table (a universal table of primarily root nodes and trusted banks, along with which root they belong to). Tracks which root nodes have a link to an external root node.

A root node is a single organisation within their root. For value to successfully transfer from one balance to another, root nodes must reach consensus; essentially an agreement that there are enough funds and it is not a fraudulent transaction. The consensus mechanism is described in the transaction section.

.. _multipleRoots:

Multiple Roots
--------------

Reaching consensus requires a group of root nodes to communicate with each other. Each transaction triggers 3 messages between a receiving root node and all the other root nodes in the group. For example, if there are 10 root nodes, then (10 - 1) * 3, 27, messages are exchanged. All users must be equally represented, so latency caused by geopolitics would quickly become a major issue. To solve this, multiple roots are required.
Trusted financial organisations from large geopolitical bloc's collectively host the bloc's root. Roots communicate with each other when an external transaction occurs - essentially one which transfers value from one root to another. Such a system scales well into the future; for example, in the event that a Mars colony is created within the next decade, that colony would be able to operate it's own root which can, in turn, communicate with the roots back on Earth. In essence, the only transactions that would be heavily affected by latency are ones which move value from one planet to another.

.._transactions:

Transactions
------------

Transactions are of course the primary function of the network. All transactions start the same way, and can potentially take one of two paths depending on if the transaction remains within a root (internal) or goes between two roots (external).

1. All users of the network have a globally unique username. When a username is the target of a transaction, the sender looks up the owning bank of a username and obtains a public address from that bank.
2. This public address is brand new and currently in an 'unclaimed' state.
3. The transaction is 'dropped' on a root node. Let's call this root node the ReceiverRoot. The transaction is signed with the senders private key and optionally with the sending bank key, depending on if the sending address has been claimed.
4. If the ReceiverRoot validates the signatures and there are enough funds, it holds the funds and forwards the transaction to its other neighbouring root nodes.
5. They also validate the transaction. If it's valid, they sign the transaction, hold the funds, and send the signature back to ReceiverRoot.
6. ReceiverRoot now has a collection of signatures. If it gets a majority of root signatures, it forwards the transaction and the set of signatures to all other root nodes.


Internal Root Transaction
-------------------------

This is the flow of a transaction within a single root.

7. Provided each node agrees that a majority was achieved through verifying the signature set, the receiving balance is updated and the funds are removed from holding.
8. The transaction is broadcast out of the root as a completed transaction.
9. The receiving bank sees a transaction has occured on a public address they had previously given out in step 1.
10. That bank may now publically claim the address, using its private key as proof of ownership.

External Root Transaction
-------------------------

This is the flow of a transaction from one root to another.

7. ReceiverRoot checks the entity table to see which of its neighbour root nodes currently holds a connection to the target root. It either randomly tags one of the connected root nodes to handle the outbound part of the transaction (the 'tag' is sent in the previous forwarded transaction), or establishes a connection itself.
8. As with the internal root transaction, the funds are removed from holding on all nodes. The transaction is also temporarily added to an outbound set, for use in the event that there is a link or system failure during the long haul transaction.
9. The tagged root node, which may be ReceiverRoot if it had to establish a new connection, forwards the successful signed transaction to the remote root.
10. The remote root verifies all the signatures and confirms that a majority was achieved. The receiving balance is updated.
11. The transaction is broadcast out of the remote root as a completed transaction. The remote root informs the sending root that the transaction was successful and the pending transaction is removed from the outbound set.

In the event of a link failure, the sending root tries again, with a flag stating that this transaction is being repeated. If this flag is set, the remote root checks to see if the same transaction had been processed before by looking it up in it's transaction history. If it had been processed before, the remote root simply informs that the transaction has been completed.


.. _addrClaim:

Address Claims
--------------

Addresses can be optionally claimed by the owning bank. This exchanges some of their privacy in favour of more security, as a claimed address has a known owning bank yet simultaneously only that same bank will then be allowed to perform a transaction with that address. In effect, if the bank has some of its address private keys leaked or stolen, the private keys can still only be used by that one bank.

Claims must always happen after the first successful transaction into a new address. This essentially forces the root to publish it's anonymous transaction history; up until this point, the root does not know who needs to be notified when a particular transaction occurs. The transaction history must be published in order for the international community to verify the overall transaction system, in effect, to ensure that value has not been created or destroyed by anyone other than a particular commodities issuer.

