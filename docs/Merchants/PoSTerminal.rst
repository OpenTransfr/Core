:: PosTerminal_:

Point of Sale Terminal
======================

These are for physical stores. Their main role is to collect what the user wants to purchase and provide the summary on a device the user trusts.

How it works
------------

::image ../images/PointOfSale-Sticker.png

- A merchant displays a sign like the one above
- The consumer shows up at the till and either scans the QR code, or enters the code. Depending on their bank, they may be prompted for e.g. a finger print or PIN. The consumer can do this at any point, ideally whilst the merchant is busy scanning the products, saving a little time
- The user then sees the current summary of what's being bought, the total price, and the pay now button.
- When all products have been scanned, the merchant enables the pay now button (preventing the consumer from accidentally pressing it earlier)
- The user presses the pay now button to complete the transaction

Behind the scenes
-----------------

Each till has it's own globally unique code. Usually they are static but they can be dynamically assigned too for extra privacy.
A small sign with a QR version of the code is displayed at the till (either printed, or on a screen). Internally, they direct to, for example, https://pay.opentrans.fr/HA7F2. When products are added to the order, e.g. by a merchant scanning its barcode, the information is sent to pay.opentrans.fr, live updating the summary.
