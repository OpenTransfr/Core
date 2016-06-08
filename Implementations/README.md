# Implementations
Reference implementations of the various API's. The primary reference languages are:

- C#
- PHP*
- Javascript

# PHP? Why PHP?
The API's are built around web technologies. PHP was chosen for the reference implementation of those API's as it's a language many web developers can recognise and easily understand; A less popular language may otherwise result in aspects of the design being 'lost in translation'. The end result is code which is easy reading and most universally understood.

# Libraries and Services
In here are two directories - Libraries and Services. Libraries are used to interact with the Services. So if you're a Bank, you'll probably want to head into Services, otherwise take a look at Libraries instead. For example, the Bank API and Root API are Services and the C# Library hooks up to both.