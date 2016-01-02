//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// Metadata for a particular issuer.
	/// </summary>
	
	public class IssuerInfo{
		
		/// <summary>Information such as the nice name and public key.</summary>
		public Metadata Meta;
		
		
		public IssuerInfo(Metadata meta){
			
			Meta=meta;
			
		}
		
		/// <summary>The tags (including groups, e.g. just 'currency') that this issuer can issue.</summary>
		public List<string> Tags{
			get{
				
				// Get the set:
				PropertyValueSet tags=Meta["tags"] as PropertyValueSet;
				
				if(tags==null){
					
					// None found!
					return null;
					
				}
				
				// Create the result set:
				List<string> result=new List<string>();
				
				// For each one, get it as a string:
				for(int i=0;i<tags.Length;i++){
					
					// Get tag as a string:
					string tag=tags.GetString(i);
					
					if(tag==null){
						// Wasn't a string.
						continue;
					}
					
					// Add to results:
					result.Add(tag);
					
				}
				
				return result;
				
			}
		}
		
		/// <summary>The message that gets signed by a root node to say this issuer is endorsed.</summary>
		public Writer SignableMessage{
			get{
				return null;
			}
		}
		
		/// <summary>An issuer gains trust by being endorsed by one or more root nodes.
		/// That's where a root node signs the 'SignableMessage' - 
		/// in doing so, that root node has endorsed this root and its provider.</summary>
		public int Endorsements{
			get{
				
				return Meta.GetEndorsements(SignableMessage);
				
			}
		}
		
		/// <summary>The English name of this issuer.</summary>
		public string Name{
			get{
				
				return GetName("en");
				
			}
		}
		
		/// <summary>Gets the name of this issuer in the given lowercase 2 character language code, e.g. 'en'.</summary>
		/// <returns>The name if it was found. Null otherwise.</summary>
		public string GetName(string lang){
			
			// Get the name from e.g. "name.en":
			return Meta.GetString("name."+lang);
			
		}
		
	}

}