//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// A wallet stores a set of public/private keypairs. The public key is more commonly known as an 'address'.
	/// Owning the private key allows you to spend the value held in an address.
	/// A wallet also stores a set of subscriptions. These are any automatic transactions such as monthly bills.
	/// Wallets can be either hosted (such as by OpenTransfr itself) or private, e.g. stored offline on your phone.
	/// Note that offline wallets may still need to be able to go online to fulfil any subscriptions.
	/// All wallet services must follow the "wallet service guidelines".
	/// </summary>
	
	public class Wallet{
		
		/// <summary>The content in this wallet.</summary>
		public KeyValueTable Content;
		
		
		/// <summary>The raw set of private/public keys in this wallet.</summary>
		public PropertyValueSet RawAddressEntries{
			get{
				return Content["addresses"] as PropertyValueSet;
			}
		}
		
		/// <summary>A loaded set of addresses.</summary>
		public List<WalletKeypair> AddressEntries{
			get{
				
				// Get the set:
				PropertyValueSet set=Content["addresses"] as PropertyValueSet;
				
				if(set==null){
					
					// None:
					return null;
					
				}
				
				// Results set:
				List<WalletKeypair> result=new List<WalletKeypair>();
				
				// For each one, load it up:
				for(int i=0;i<set.Length;i++){
					
					// Get the entry:
					PropertyValueSet adr=set[i] as PropertyValueSet;
					
					if(adr==null){
						
						// Invalid entry.
						return null;
						
					}
					
					// Add it:
					result.Add(new WalletKeypair(adr));
					
				}
				
				return result;
				
			}
		}
		
		/// <summary>Adds a keypair to this wallet.</summary>
		public bool AddKeypair(WalletKeypair pair){
			
			if(pair.PublicKey==null){
				return false;
			}
			
			// Get it as a value set:
			PropertyValueSet set=pair.ValueSet;
			
			// Add to addresses:
			Add("addresses",set);
			
			return true;
		}
		
		/// <summary>Adds a subscription to this wallet.</summary>
		public bool AddSubscription(Subscription subr){
			
			/*
			// Get it as a value set:
			PropertyValueSet set=subr.ValueSet;
			
			// Add to addresses:
			Add("subscriptions",set);
			*/
			
			return true;
		}
		
		/// <summary>Adds an entry to the named set, e.g. adds a new subscription to 'subscriptions' or
		/// a new address to 'addresses'.</summary>
		private void Add(string setName,PropertyValue entry){
			
			// Get current set:
			PropertyValueSet hostSet=Content[setName] as PropertyValueSet;
			
			if(hostSet==null){
				
				// Create it:
				hostSet=new PropertyValueSet(1);
				
				// Push to content:
				Content[setName]=hostSet;
				
				// Apply at 0:
				hostSet[0]=entry;
				
			}else{
				
				// Add:
				hostSet.Add(entry);
				
			}
			
		}
		
		/// <summary>Things this wallet is subscribed to. For example, monthly bills.
		/// Note that a wallet holder must initiate these subscriptions.</summary>
		public List<Subscription> Subscriptions{
			get{
				
				// Get the set:
				PropertyValueSet set=Content["subscriptions"] as PropertyValueSet;
				
				if(set==null){
					
					// None:
					return null;
					
				}
				
				// Results set:
				List<Subscription> result=new List<Subscription>();
				
				// For each one, load it up:
				for(int i=0;i<set.Length;i++){
					
					// Get the entry:
					PropertyValueSet subr=set[i] as PropertyValueSet;
					
					if(subr==null){
						
						// Invalid subscription.
						return null;
						
					}
					
					// Add it:
					result.Add(new Subscription(subr));
					
				}
				
				return result;
				
			}
		}
		
		/// <summary>Optional username. These can make sending value to this wallet a lot simpler.</summary>
		public string Username{
			get{
				return Content.GetString("username");
			}
		}
		
		/// <summary>The keypair for changing details for the username.</summary>
		public WalletKeypair UsernameKey{
			get{
				
				PropertyValueSet key=Content["username.key"] as PropertyValueSet;
				
				if(key==null){
					return null;
				}
				
				return new WalletKeypair(key);
				
			}
		}
		
		/// <summary>Delivery addresses in order of preference.</summary>
		public List<string> DeliveryAddresses{
			get{
				
				// Get the set (if there is one):
				PropertyValueSet set=Content["deliverto"] as PropertyValueSet;
				
				if(set==null){
					
					// None:
					return null;
					
				}
				
				// The results:
				List<string> result=new List<string>();
				
				// For each one..
				for(int i=0;i<set.Length;i++){
					
					// Read the address:
					string addr=set.GetString(i);
					
					if(!string.IsNullOrEmpty(addr)){
						
						// Got one - add it:
						result.Add(addr);
						
					}
					
				}
				
				return result;
			}
		}
		
	}

}