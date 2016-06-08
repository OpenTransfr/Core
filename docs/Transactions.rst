.. _transactions:

Transactions
============

Transactions are of course the primary function of the network. All transactions start the same way, and can potentially take one of two paths depending on if the transaction remains within a root (internal) or goes between two roots (external).

1. All users of the network have a globally unique username. When a username is the target of a transaction, the sender looks up the owning bank of a username and obtains a public address from that bank.
2. This public address is brand new and currently in an 'unclaimed' state.
3. The transaction is 'dropped' on a root node. Let's call this root node the ReceiverRoot. The transaction is signed with the sender's private key and optionally with the sending bank key, depending on if the sending address has been claimed.
4. If the ReceiverRoot validates the signatures and there are enough funds, it holds the funds and forwards the transaction to its other neighbouring root nodes.
5. They also validate the transaction. If it's valid, they sign the transaction, hold the funds, and send the signature back to ReceiverRoot.
6. ReceiverRoot now has a collection of signatures. If it gets a majority of root signatures, it forwards the transaction and the set of signatures to all other root nodes.


Internal Root Transaction
-------------------------

This is the flow of a transaction within a single root.

7. Provided each node agrees that a majority was achieved through verifying the signature set, the receiving balance is updated and the funds are removed from holding.
8. The transaction is broadcast out of the root as a completed transaction.
9. The receiving bank sees a transaction has occurred on a public address they had previously given out in step 1.
10. That bank may now publically claim the address, using its private key as proof of ownership.

External Root Transaction
-------------------------

This is the flow of a transaction from one root to another.

7. ReceiverRoot checks the entity table to see which of its neighbour root nodes currently holds a connection to the target root. It either randomly tags one of the connected root nodes to handle the outbound part of the transaction (the 'tag' is sent in the previous forwarded transaction), or establishes a connection itself.
8. As with the internal root transaction, the funds are removed from holding on all nodes. The transaction is also temporarily added to an outbound set, for use in the event that there is a link or system failure during the long haul transaction.
9. The tagged root node, which may be ReceiverRoot if it had to establish a new connection, forwards the successful signed transaction to the remote root.
10. The remote root verifies all the signatures and confirms that a majority was achieved. The receiving balance is updated.
11. The transaction is broadcast out of the remote root as a completed transaction. The remote root informs the sending root that the transaction was successful and the pending transaction is removed from the outbound set.

In the event of a link failure, the sending root tries again, with a flag stating that this transaction is being repeated. If this flag is set, the remote root checks to see if the same transaction had been processed before by looking it up in its transaction history. If it had been processed before, the remote root simply informs that the transaction has been completed.

.. _ecdsaRandom:

Random Numbers
--------------

.. image:: http://imgs.xkcd.com/comics/random_number.png

Although it's a fairly moot point, you **must** ensure that your random number generator actually works rather than simply returning the same value repeatedly. ECDSA requires a random number during the signing process. It is possible to recover the private key from two signatures (the data signed doesn't matter) if they shared the same random number. Even relatively weak random number generators are incredibly unlikely to produce the same number twice due to the sheer size of the numbers involved, so long as it actually returns a new number. For more information on this hack, see this great article http://www.nilsschneider.net/2013/01/28/recovering-bitcoin-private-keys.html and how the hack was applied to the PS3 https://events.ccc.de/congress/2010/Fahrplan/attachments/1780_27c3_console_hacking_2010.pdf.

.. _addrClaim:

Address Claims
--------------

Addresses can be optionally claimed by the owning bank. This exchanges some of their privacy in favour of more security, as a claimed address has a known owning bank yet simultaneously only that same bank will then be allowed to perform a transaction with that address. In effect, if the bank has some of its address private keys leaked or stolen, the private keys can still only be used by that one bank.

Claims must always happen after the first successful transaction into a new address. This essentially forces the root to publish its anonymous transaction history; up until this point, the root does not know who needs to be notified when a particular transaction occurs. The transaction history must be published in order for the international community to verify the overall transaction system, in effect, to ensure that value has not been created or destroyed by anyone other than a particular commodities issuer.

