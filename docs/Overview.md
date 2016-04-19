# The basics - Address pairs
Like with Bitcoin, a private/public keypair is created. The public key is the 'address' which can send or recieve payments and the private key is kept secret, typically in an online 'account' stored by a bank.
Only someone with access to the private key may perform a transaction with that balance.

# Address contents
Each address can have a balance and the type of commodity that the balance is for. The balance is always an integer value and is usually the smallest division of a commodity. For example, a US dollar balance is in cents. A British pound sterling balance is in pennies, and so on.

# Accounts
An account is mainly a collection of these addresses, and it's stored by a bank in order to keep them safe.

# Commodity tags
Commodities are each given a unique tag, such as 'currency.usd' or 'metals.gold'. These provide an easy to use textual representation for commodities, as well as a tree-like structure for grouping up commodities too.

# Issuer
An issuer registers a commodity tag and can then issue it onto the network. There can only be one issuer per commodity and they are the equivelant to (or may actually be) a central bank or federal reserve. Some commodity tags require an issuer to be certified first. For example, currency.* may only be issued by a certified bank; this is in order to guarentee that a withdrawal request will be fulfilled

# What's a withdrawal?
This is where a user requests an issuer to, for example, send them US dollars from their US dollar balance on the network. Entirely virtual commodities which only exist inside the network are supported too so not all commodities can be withdrawn.

# Transactions
A transaction primarily consists of two addresses and the amount being transferred. Both must be for the same commodity. They are signed by the senders private key and submitted through any node onto the network. The root of the network checks to see if the transaction is possible (i.e. if enough funds are available) and clears it.

# Conflicted transactions
Two or more transactions could occur simultaneously which result in a conflicted balance; that's where both transactions if accepted would result in a negative balance. Typically one transaction is favoured over the other after a resolution process.