//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// A subscription represents something that a wallet has subscribed to.
	/// For example, a monthly bill is a subscription. All payments are 'push' payments - the only way a company can 
	/// collect regular fees is if the payment is pushed to them by a wallet service.
	/// This also means wallets can gain a rich transaction history without it being exposed to the network.
	/// This way, a user knows exactly when all their payments are due - they're all listed in the one place.
	/// </summary>
	
	public class Subscription{
		
		
		public Subscription(){}
		
		public Subscription(PropertyValueSet set){
			
		}
		
	}
	
}