Standard Issuer API
===================

Note: This is included in the Standard Bank API but is only active if the endpoint issues one or more commodities.

issue.options/{commodity tag}
-----------------------------

The user POST's the following and opens the response in a popup window:

{
    "address":"1a2b..",
    "amount":1000
}

:address
    The address to issue to. This address must either not currently exist or it must be already holding the requested commodity (in the URL). If the address holds an existing balance which is of a different commodity, the page must display a clear error to the user.

:amount
    The exact amount that the user would like to have issued. Note that this is always an integer and represents e.g. pennies or cents. Fees, e.g. card payment fees, must not be taken out of this; they are added on top.

The response is a complete webpage displaying one or more options that the user has in order to be issued to. The webpage *should* follow the `standard bank page format`_.

For example, a user wants to have GBP issued to them. https://{GBP issuer endpoint}/v1/issue.options/currency.gbp is loaded in a popup menu. The GBP issuer may choose to support PayPal, GoCardless and Stripe in order to accept a traditional payment and issue the amount onto the network. The user selects the option they want to use, fills out any additional information (such as their card details), and completes the transaction. The issuer then looks for a successful transaction and issues the amount into the address as a result using the `root API`_.
