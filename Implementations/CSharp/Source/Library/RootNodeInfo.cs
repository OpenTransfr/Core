//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// Metadata for a particular root node.
	/// </summary>
	
	public class RootNodeInfo{
		
		/// <summary>The root node ID.</summary>
		public ulong ID;
		/// <summary>Additional information such as the nice name.</summary>
		public Metadata Meta;
		
		
		public RootNodeInfo(ulong id,Metadata meta){
			
			ID=id;
			Meta=meta;
			
		}
		
		/// <summary>The message that gets signed by another root node to say this root node is endorsed.</summary>
		public Writer SignableMessage{
			get{
				return null;
			}
		}
		
		/// <summary>A root node gains trust by being endorsed by one or more root nodes.
		/// That's where a root node signs the 'SignableMessage' - 
		/// in doing so, that root node has endorsed this root and its provider.</summary>
		public int Endorsements{
			get{
				
				return Meta.GetEndorsements(SignableMessage);
				
			}
		}
		
		/// <summary>The English name of this root provider.</summary>
		public string Name{
			get{
				
				return GetName("en");
				
			}
		}
		
		/// <summary>Gets the name of this root provider in the given lowercase 2 character language code, e.g. 'en'.</summary>
		/// <returns>The name if it was found. Null otherwise.</summary>
		public string GetName(string lang){
			
			// Get the name from e.g. "name.en":
			return Meta.GetString("name."+lang);
			
		}
		
	}

}