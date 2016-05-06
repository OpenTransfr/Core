Standard Issuer API
===================

Note: This is included in the Standard Bank API but is only active if the endpoint issues one or more commodities. Otherwise it simply responds with NOT_ISSUED to everything. There are two main flows here, depending on how a user wishes to use the API. If a user wishes to entirely automate issuing, they can obtain a token and use it as a reference elsewhere. For most normal cases though, a consumer is present for issuing, in which case the visual API is used instead.

issue.token
-----------

The user POST's the following:

{
    "address":"1a2b..",
    "amount":1000,
    "commodity":"currency.gbp"
}

:address
    The address to issue to. This address must either not currently exist or it must be already holding the requested commodity (in the URL). If the address holds an existing balance which is of a different commodity, the page must display a clear error to the user.

:amount
    The exact amount that the user would like to have issued. Note that this is always an integer and represents e.g. pennies or cents. Fees, e.g. card payment fees, must not be taken out of this; they are added on top.

:commodity
    The commodity that the user wants to have issued to them.

The response is:

{
    "token":"M8X9S"
}

:token
    A short token which can be used by the issuer to successfully issue.

All error messages must have a 400 HTTP status. The possible errors are:

:ADDRESS_WRONG_COMMODITY

{
    "error":"ADDRESS_WRONG_COMMODITY"
}

This error occurs when the given address is using a different commodity. E.g. you asked for currency.gbp to be issued, but the address exists and contains currency.usd.

:NOT_ISSUED

{
    "error":"NOT_ISSUED"
}

This error occurs when a user asked an issuer to issue a commodity that it does not control. E.g. you asked the GBP issuer to issue USD.

:INVALID_FIELD

{
    "error":"INVALID_FIELD('amount')"
}

This error occurs if a field is either malformed (for example, receiving a string when a number is expected etc) or a required field is missing.

Once the user has obtained a token, they can then either use the `issue.visual API`_ or trigger a transaction some other way, for example, manually creating a PayPal transaction using the token as the payment reference. This depends on what options are provided by the issuer.

issue.options/{commodity tag}
-----------------------------

This API lists all of the options for a given commodity. For example, currency.gbp could be issued after receiving through PayPal, GoCardless or Stripe.

Response:

[
    {
        "name":"GoCardless",
        "fee":"1%"
    },
    {
        "name":"Stripe",
        "fee":"2.5%+30"
    }
]

issue.visual?token={token}
--------------------------

This API displays all of the available issuing options (for example, different card gateways etc) and must be opened in a popup window. The response is a complete webpage displaying one or more options that the user has in order to be issued to. The webpage *should* follow the `standard bank page format`_.

For example, a user wants to have GBP issued to them. https://{GBP issuer endpoint}/v1/issue.visual?token=M8X9S is loaded in a popup menu. The GBP issuer may choose to support PayPal, GoCardless and Stripe in order to accept a traditional payment and issue the amount onto the network. The user selects the option they want to use, fills out any additional information (such as their card details), and completes the transaction. The issuer then looks for a successful transaction and issues the amount into the address as a result using the `root API`_.

issue.visual
------------

The user POSTs the same request as for the issue.token API. It then displays all of the available issuing options and must be opened in a popup window.
