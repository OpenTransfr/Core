//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// Metadata for a particular commodity (such as currency.gbp).
	/// </summary>
	
	public class CommodityInfo{
		
		/// <summary>The commodity ID.</summary>
		public ulong ID;
		/// <summary>Additional information such as the issuer and tag.</summary>
		public Metadata Meta;
		
		
		public CommodityInfo(ulong id,Metadata meta){
			
			ID=id;
			Meta=meta;
			
		}
		
		/// <summary>The message that gets signed by a root node to say this commodity is ok for issuing.
		/// Note that group x commodities do not need to be signed. (e.g. x.currency.gp).</summary>
		public Writer SignableMessage{
			get{
				
				// This is:
				// - A version number (1)
				// - Issuer public ID
				// - Commodity tag
				// Concatted together,
				
				// Get issuer and tag:
				byte[] issuer=Issuer;
				string tag=Tag;
				
				// Either not set?
				if(issuer==null || tag==null){
					
					// Must have all 3.
					return null;
					
				}
				
				Writer writer=new Writer();
				
				// A version number (1):
				writer.Write((byte)1);
				
				// Issuer:
				writer.Write(issuer);
				
				// Tag:
				writer.WriteString(tag);
				
				// Done:
				return writer;
				
			}
		}
		
		/// <summary>True if this commodity is in the unendorsed ('x') group.
		/// This just means it requires no endorsements.</summary>
		public bool UnendorsedGroup{
			get{
				
				return Tag.StartsWith("x.");
				
			}
		}
		
		/// <summary>A commodity gains trust by being endorsed by one or more root nodes.
		/// That's where a root node signs the 'SignableMessage' - 
		/// in doing so, that root node has endorsed this commodity and it's issuer.
		/// Commodities require at least one signature unless it's either virt.* or x.*</summary>
		public int Endorsements{
			get{
				
				return Meta.GetEndorsements(SignableMessage);
				
			}
		}
		
		/// <summary>Checks if this commodity is in the given group, which could be e.g 'currency'.</summary>
		public bool IsInGroup(string name){
			
			// Get the hierarchy from the tag:
			string[] hierarchy=Hierarchy;
			
			// For each except the last one, check if we've got a match:
			int max=name.Length-1;
			
			for(int i=0;i<max;i++){
				
				// Match?
				if(name==hierarchy[i]){
					
					// Yep!
					return true;
					
				}
				
			}
			
			// Nope - no matches.
			return false;
			
		}
		
		/// <summary>The commodities hierarchy.</summary>
		public string[] Hierarchy{
			get{
				
				// Get the tag and split it:
				return Tag.Split('.');
				
			}
		}
		
		/// <summary>The commodities tag, e.g. 'currency.gbp'. Always lowercase.</summary>
		public string Tag{
			get{
				return Meta.GetString("tag");
			}
		}
		
		/// <summary>The issuers public ID.</summary>
		public byte[] Issuer{
			get{
				return Meta.GetBytes("issuer");
			}
		}
		
		/// <summary>The English name of this commodity.</summary>
		public string Name{
			get{
				
				return GetName("en");
				
			}
		}
		
		/// <summary>Gets the name of this commodity in the given lowercase 2 character language code, e.g. 'en'.</summary>
		/// <returns>The name if it was found. Null otherwise.</summary>
		public string GetName(string lang){
			
			// Get the name from e.g. "name.en":
			return Meta.GetString("name."+lang);
			
		}
		
		/// <summary>The English description of this commodity.</summary>
		public string Description{
			get{
				
				return GetDescription("en");
				
			}
		}
		
		/// <summary>Gets the description of this commodity in the given lowercase 2 character language code, e.g. 'en'.</summary>
		/// <returns>The description if it was found. Null otherwise.</summary>
		public string GetDescription(string lang){
			
			// Get the description from e.g. "desc.en":
			return Meta.GetString("desc."+lang);
			
		}
		
	}

}