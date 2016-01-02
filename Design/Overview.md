# Address pairs
Like with bitcoin, a private/public keypair is created. The public key is the 'address' which can send or recieve payments and the private key is kept secret, typically in an online or offline 'wallet'.

# Address contents
Each address can have a balance, a hold balance and the type of commodity that the balance is of.

# Commodity tags
Commodities are each given a unique tag, such as 'currency.usd' or 'metals.gold'.

# Issuer
An issuer registers the tag for their commodity and can then issue it onto the network. There can only be one issuer per commodity and they are the equivelant to (or may actually be) a central bank or federal reserve. Because of this, certain commodity tags are reserved in order to guarentee that the issuer will fulfil withdrawal requests.

# Withdrawals
This is where a user requests an issuer to, for example, send them US dollars from their US dollar balance on the network.

# Transactions
A transaction consists of two addresses and the amount being transferred. Both must be for the same commodity. They are signed by the senders private key and submitted through any node onto the network.

# Conflicted transactions
Two or more transactions could occur simultaneously which result in a conflicted balance; that's where both transactions if accepted would result in a negative balance. Typically one transaction is favoured over the other after a resolution process.