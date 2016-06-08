:: PosTerminal_:

Point of Sale Terminal
======================

These are for physical stores. Their main role is to collect what the user wants to purchase and provide the summary on a device the user trusts.

How it works
------------

::image ../images/PointOfSale-Sticker.png

- A merchant displays a sign like the one above. Note that this is just one way of expressing the checkout ID; for example, NFC could also be used.
- The consumer shows up at the till and either scans the QR code, or enters the code. Depending on their bank, they may be prompted for e.g. a finger print or a pin.
- The user then sees the total price and the pay now button.
- The user presses the pay now button to complete the transaction

Behind the scenes
-----------------

Each till has it's own globally unique code, assigned by merchant services. Usually they are static but they can be dynamically assigned too for extra privacy.
A small sign with a QR version of the code is displayed at the till (either printed, or on a screen). Internally, they direct to, for example, https://p.opentrans.fr/#HA7F2. When products are added to the order, e.g. by a merchant scanning its barcode, the information is sent to pay.opentrans.fr associated with this checkout code.
