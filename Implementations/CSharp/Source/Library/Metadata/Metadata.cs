//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>Used when something interesting happens to some metadata.</summary>
	public delegate void MetaEvent(Metadata data,string tag);
	
	/// <summary>
	/// Represents the meta block found in e.g. transfers and root node overviews.
	/// Meta holds a generic set of tags e.g. issuer or name etc, allowing for an easy extension point.
	/// </summary>

	public class Metadata{
		
		/// <summary>Writes an empty metadata block.</summary>
		public static void WriteEmpty(Writer writer){
			
			// Just a zero:
			writer.WriteCompressed(0);
			
		}
		
		/// <summary>Loads a serialised metadata block.</summary>
		public static Metadata LoadFrom(Reader sr){
			
			// Create set:
			Metadata result=new Metadata();
			
			// # of tags:
			int tagCount=(int)sr.ReadCompressed();
			
			for(int tag=0;tag<tagCount;tag++){
				
				// Firstly the key:
				string key=sr.ReadString();
				
				// Then the value (which can be a set of values):
				PropertyValue value=PropertyValues.ReadPropertyValue(sr);
				
				// Apply it:
				result[key]=value;
				
			}
			
			return result;
		}
		
		/// <summary>The number of tags within this metadata.</summary>
		public int Count{
			get{
				return Tags.Count;
			}
		}
		
		/// <summary>An event which is triggered when a meta tag is changed.</summary>
		public event MetaEvent OnChanged;
		/// <summary>The raw set of tags within this metadata.</summary>
		public Dictionary<string,PropertyValue> Tags;
		
		
		/// <summary>Creates some empty metadata.</summary>
		public Metadata(){
			Tags=new Dictionary<string,PropertyValue>();
		}
		
		/// <summary>Gets the number of 'endorsements' of whatever this meta represents.
		/// E.g. a root node or a commodity. An endorsement is where a root node
		/// signs the given message. This verifies the signatures.</summary>
		public int GetEndorsements(Writer message){
			
			if(message==null){
				
				return 0;
				
			}
			
			return GetEndorsements(message.GetResult(),(int)message.Length());
			
		}
		
		/// <summary>Gets the number of 'endorsements' of whatever this meta represents.
		/// E.g. a root node or a commodity. An endorsement is where a root node
		/// signs the given message. This verifies the signatures.</summary>
		public int GetEndorsements(byte[] message,int length){
			
			// Get the signatures:
			Dictionary<byte[],byte[]> sigs=Signatures;
			
			if(sigs==null || sigs.Count==0){
				
				// None yet.
				return 0;
				
			}
			
			// Compute the hash of the message:
			byte[] hash=Verifier.DoubleDigest(message,0,length);
			
			// The resulting count:
			int count=0;
			
			// Verify each one:
			foreach(KeyValuePair<byte[],byte[]> kvp in sigs){
				
				// Verify:
				if( Verifier.VerifyHash(hash,kvp.Key,kvp.Value) ){
					
					// Ok!
					count++;
					
				}
				
			}
			
			// Done - return the verified count:
			return count;
			
		}
		
		/// <summary>Commodity signatures. These come from root nodes signing the issuer ID combined with the commodity tag.
		/// Can be null. Maps root node public ID to signature.</summary>
		public Dictionary<byte[],byte[]> Signatures{
			get{
				
				// Get the signatures:
				PropertyValueSet sigs=this["sign"] as PropertyValueSet;
				
				// Got any?
				if(sigs==null || sigs.Length==0){
					
					// None.
					return null;
					
				}
				
				// Create the signatures set:
				Dictionary<byte[],byte[]> result=new Dictionary<byte[],byte[]>();
				
				// For each one..
				for(int i=0;i<sigs.Length;i++){
					
					// Get the signature pair:
					PropertyValueSet sigPair=sigs[i] as PropertyValueSet;
					
					if( sigPair==null || sigPair.Length<2 ){
						
						// Ignore!
						continue;
						
					}
					
					// Get the public ID:
					byte[] publicID=GetBytes(sigPair,0);
					
					// Got one?
					if(publicID==null || publicID.Length==0){
						
						// Ignore!
						continue;
						
					}
					
					// Get the signature:
					byte[] signature=GetBytes(sigPair,1);
					
					// Got one?
					if(signature==null || signature.Length==0){
						
						// Ignore!
						continue;
						
					}
					
					// Great! Add to sig set:
					result[publicID]=signature;
					
				}
				
				if(result.Count==0){
					
					// None!
					return null;
					
				}
				
				// Got something!
				return result;
				
			}
		}
		
		/// <summary>Gets the value at the given index in a value set as a byte array.</summary>
		private byte[] GetBytes(PropertyValueSet set,int index){
			
			// Make sure index is in range:
			if(index<0 || index>=set.Length){
				
				// Out of range.
				return null;
				
			}
			
			// Get it:
			ByteArrayValue ar=set[index] as ByteArrayValue;
			
			if(ar==null){
				
				// Not a byte array.
				return null;
				
			}
			
			return ar.Value;
			
		}
		
		/// <summary>Gets the given tag. Case sensitive.</summary>
		public PropertyValue Get(string tag){
			PropertyValue values;
			Tags.TryGetValue(tag,out values);
			return values;
		}
		
		/// <summary>Sets the given tag. Case sensitive.</summary>
		public void Set(string tag,PropertyValue value){
			
			if(value==null){
				Tags.Remove(tag);
				return;
			}
			
			Tags[tag]=value;
			
			if(OnChanged!=null){
				OnChanged(this,tag);
			}
			
		}
		
		/// <summary>Gets or sets the given tag. Case insensitive.</summary>
		public PropertyValue this[string tag]{
			get{
				PropertyValue values;
				Tags.TryGetValue(tag.ToLower(),out values);
				return values;
			}
			set{
				
				tag=tag.ToLower();
				
				if(value==null){
					Tags.Remove(tag);
					return;
				}
				
				Tags[tag]=value;
				
				if(OnChanged!=null){
					OnChanged(this,tag);
				}
				
			}
		}
		
		/// <summary>Sets the given tags ulong value.</summary>
		public void Set(string tag,ulong value){
			
			// Apply the value:
			Set(tag.ToLower(),new UnsignedNumberValue(value));
			
		}
		
		/// <summary>Gets the given tags ulong value.</summary>
		public ulong GetUInt64(string tag){
			
			// Get the value:
			NumericValue value=Get(tag.ToLower()) as NumericValue;
			
			if(value==null){
				return 0;
			}
			
			// Get:
			return value.ToULong();
			
		}
		
		/// <summary>Sets the given tags string value.</summary>
		public void Set(string tag,string value){
			
			// Apply the value:
			Set(tag.ToLower(),new TextValue(value));
			
		}
		
		/// <summary>Sets the given tags string value.</summary>
		public void SetString(string tag,string value){
			
			// Apply the value:
			Set(tag.ToLower(),new TextValue(value));
			
		}
		
		/// <summary>Gets the given tags string value.</summary>
		public string GetString(string tag){
			
			// LC the tag:
			tag=tag.ToLower();
			
			// Get the value:
			TextValue value=Get(tag) as TextValue;
			
			if(value==null){
				return null;
			}
			
			// Get:
			return value.Value;
			
		}
		
		/// <summary>Gets the given tag as a byte[] value.</summary>
		public byte[] GetBytes(string tag){
			
			// LC the tag:
			tag=tag.ToLower();
			
			// Get the value:
			ByteArrayValue value=Get(tag) as ByteArrayValue;
			
			if(value==null){
				return null;
			}
			
			// Get:
			return value.Value;
			
		}
		
		/// <summary>Writes out the metadata block to the given writer.</summary>
		public void Write(Writer writer){
			
			// # of tags:
			writer.WriteCompressed((uint)Count);
			
			foreach(KeyValuePair<string,PropertyValue> kvp in Tags){
				
				// Firstly the key:
				writer.Write(kvp.Key);
				
				// Write the value:
				kvp.Value.Write(writer);
				
			}
			
		}
		
	}
	
}