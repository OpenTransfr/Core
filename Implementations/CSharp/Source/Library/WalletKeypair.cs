//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// A public and private key pair stored within a wallet.
	/// These are almost always used to represent the actual addresses which send and receive value.
	/// A wallet stores a set of them.
	/// </summary>
	
	public class WalletKeypair{
		
		/// <summary>The public key.</summary>
		public byte[] PublicKey;
		/// <summary>The private key.</summary>
		private byte[] PrivateKey;
		
		
		/// <summary>Generates a new keypair.</summary>
		public WalletKeypair(){
			
			// Generate now:
			Keypair.Generate(out PrivateKey,out PublicKey);
			
		}
		
		/// <summary>Loads a keypair from a set of values.</summary>
		public WalletKeypair(PropertyValueSet set){
			
			if(set.Length!=2){
				return;
			}
			
			// Get the private and public keys:
			PrivateKey=set.GetBytes(0);
			PublicKey=set.GetBytes(1);
			
		}
		
		/// <summary>Gets this keypair as a value set.</summary>
		public PropertyValueSet ValueSet{
			get{
				
				// It's a set of 2 values:
				PropertyValueSet set=new PropertyValueSet(2);
				
				// First entry is private:
				set[0]=new ByteArrayValue(PrivateKey);
				
				// Second entry is public:
				set[1]=new ByteArrayValue(PublicKey);
				
				return set;
				
			}
		}
		
	}
	
}