//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// Base value of numeric properties.
	/// </summary>
	
	public class NumericValue:PropertyValue{
		
		/// <summary>Gets this value in the zero - 1 range.</summary>
		public virtual float ZeroOneRange(){
			
			return 0f;
			
		}
		
		/// <summary>Gets the value as a double.</summary>
		public virtual double ToDouble(){
			return 0.0;
		}
		
		/// <summary>Gets this value in long range.</summary>
		public virtual long ToLong(){
			
			return 0;
			
		}
		
		/// <summary>Gets this value in ulong range.</summary>
		public virtual ulong ToULong(){
			
			return 0;
			
		}
		
	}
	
}