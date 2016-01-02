//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A transfer being created. You construct one of these, then Submit() it to the network.
	/// Generally a wallet application does this as it requires authorisation by the sender.
	/// </summary>
	
	public class Transfer{
		
		/// <summary>Any additional metadata.</summary>
		public KeyValueTable Meta;
		/// <summary>The amount being transferred.</summary>
		public ulong Balance;
		/// <summary>The commodity being transferred.</summary>
		public ulong CommodityID;
		
		
		public Transfer(ulong balance,ulong commodity){
			
			Balance=balance;
			CommodityID=commodity;
			
		}
		
		public Transfer(ulong balance,string commodityTag){
			
			Balance=balance;
			
			/*
			// Look up the commodity tag and get the ID:
			Commodities.Get(commodityTag,delegate(ulong id){
				
				CommodityID=id;
				
			});
			*/
			
		}
		
		/// <summary>Sends this transfer request to the network.</summary>
		public void Submit(){
			
			// Get the timestamp in ticks:
			ulong time=Time.UnixTimeTicks;
			
			
			
			// Compute the binary of the transfer (which must then be signed by the sending address):
			
			
		}
		
	}
	
}