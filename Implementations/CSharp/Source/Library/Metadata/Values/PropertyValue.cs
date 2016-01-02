//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A single value for a particular property.
	/// Properties are on e.g. commodities.
	/// </summary>
	
	public class PropertyValue{
		
		public int UnresolvedID=-1;
		
		
		public virtual int GetID(){
			return -1;
		}
		
		public bool ResolveRequired{
			get{
				return (UnresolvedID!=-1);
			}
		}
		
		public virtual PropertyValue Copy(){
			return Create();
		}
		
		public virtual PropertyValue Create(){
			return null;
		}
		
		public virtual void Read(Reader reader){}
		
		public virtual void Write(Writer writer){}
		
	}
	
}