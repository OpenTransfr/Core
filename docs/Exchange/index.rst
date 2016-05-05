.. _exchange:

Exchange
========

The network has a built in exchange called OpenExchange or just OPEX for short. It's a matching exchange - to convert one commodity into another, an exchange must also occur in the opposite direction. For example, if you want to swap GBP for USD, someone else must be swapping USD for GBP.

How it works
------------

To prevent there being potentially millions of pairs of commodities, everything exchanges through a central commodity (at the moment, USD) to keep things flowing fast. This means exchanges occur in two 'hops' (e.g. for a GBP <-> EUR exchange the hops are GBP<->USD, USD<->EUR) and the matchings may still take a few minutes to be paired with something going the other way though, so exchanges are intended to be done 'offline' rather than during a transaction. Note that the exchange is not used when you happen to be *buying* another commodity - for example, buying shares with GBP.

Rates
-----

Exchange rates are the same for all exchange requests and are simply the current mid-market rate for the commodity exchanging to/from the central commodity. These rates are currently updated once an hour. As there are no fees on exchanges, this currently means commodity traders can potentially trade through OPEX on the hour boundaries.

Floating rates
--------------

As the network can handle all forms of commodities such as stock and currencies, it may in the future be required to define market rates as existing exchange systems migrate to OPEX. 

Patience slider (proposal)
--------------------------

Exchange requests provide a 0-1 value which notes how long they're willing to wait for good deal; A value of 0 is like selling cheap. The more patient user gets the better rate, essentially converting the bid-ask spread into a 0-1 range. The parameters of the spread itself move depending on trading volumes throughout the spread.

Operation
---------

Currently OpenExchange is the only thing on the network operated by a single entity. This will change in the future as it moves to a distributed setup, being instead hosted by existing exchanges around the world.
