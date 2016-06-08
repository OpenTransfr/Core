.. _issuer:

Issuer
======

Issuers define a commodity_ and issue it onto the network. There is only ever *one issuer per commodity* and they are the ultimate source of all value.

Creating a commodity
--------------------

Without going into too much detail about commodities themselves, each commodity has it's own tag; for example, 'currency.eur' or 'currency.gbp'. An issuer registers a tag by sending a create request to the current holder of the *parent* tag (in the previous examples, that's the holder of the 'currency' tag). At this point an important note is required: When the parent tag grants a sub tag, it has no authority over the sub tag. Or in other words, 'currency' is not a point of failure. Depending on the parent tags *policy*, sub tag requests may be granted instantly or after a review process. For example, 'currency.something' requires a review; 'virt.something' does not, as 'virt' is open for all. An implementation of this can be found at https://txroot.opentrans.fr/#screen=commodity.create - it essentially finds the holder of a parent tag, then submits the request there.

Sub tag reviews
---------------

Major parent tags such as 'currency' will always perform reviews on sub tag requests. A review essentially involves checking if a particular issuer has the right to issue a particular commodity. For example, a random user may request to issue a major currency that is currently not being issued. It is of course in the best interests of everyone that the Central Bank of that currency is its issuer, or at the very least chooses who will issue it. If an issuer wishes to allow sub tags, it is that issuer that performs the review, and they may optionally charge a fee. Major top level tags such as 'currency' itself are intended to be owned and reviewed by, for example, the Bank for International Settlements.

Direct Benefits
---------------

The most notable advantage to issuers is the ability to see all circulation in action, international included, and respond accordingly. No form of mining is required so creating a currency is both easier and more secure than it is today. Central Banks are still required to play an ongoing and important role in the global financial system.

Issuing Vouchers
----------------

Vouchers are considered a form of commodity too which allows them to sit in a user's bank account. This avoids the need for merchants to implement an account balance system; a merchant simply declares through merchant services that they accept those vouchers.

Issuing Votes
-------------

The network has many properties which are favourable to a political voting system. For example, transparency, anonymity within the root and the ability for anyone to view transactions live. If we view votes as a kind of currency, and the action of voting for something is a transfer, an interesting usage of the network begins to emerge. Any form of Electoral Commission (or anyone who wishes to run a vote) could be an issuer of votes. However, there are a few key components that would be required in order for such a system to be widely possible:

- A user must not be able to prove who they voted for.
- No intermediate node must be able to prove who someone voted for.
- As with postal votes, it is not possible to achieve complete voter anonymity without compromising on eligibility.

However, this particular use case is currently entirely academic; although it is considered, it does not currently play an active role in the design of the system.