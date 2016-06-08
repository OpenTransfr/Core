:: spokenTerminals_:

Spoken Terminals
================

Often there are cases where a person needs to pay for something over the phone and similar. Traditionally a users bank information is given to the merchant allowing them to complete the transaction, however this has many severe security issues:

- Calls are typically recorded, meaning those details can be listened to by anyone who has accessed to the recordings
- The operator could easily take a copy of the details
- Someone nearby could overhear you saying them

So, to fix these, we need to turn it around. Rather than the merchant authorising the transaction on behalf of the user, the user *always* authorises ('pushes') transactions.

How it works
============

:: image ../images/Over-The-Phone.png

- When a merchant wants a user to pay over the phone, they send off the summary to the network, such as the products being bought and the total price, and get allocated a short code.
- The user enters that code into their device
- They see the summary and, if it's really what they want, can accept it
- The merchant receives a notification to say the transaction was completed

Essentially the merchant simply provides a digital summary of what is to be paid to the user, and then the user can authorise it themselves.

Terminal Codes
==============

All terminals have a short code which can be entered into the users device to retrieve that summary. The shortest possible codes (6 characters and less) are reserved specifically for spoken use and are 'pooled' so they are continually being recycled.
