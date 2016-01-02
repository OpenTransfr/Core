//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A single value for a particular property.
	/// Range is +-(a very big number!)
	/// </summary>
	
	public class NumberValue:NumericValue{
		
		public long Value;
		
		
		public NumberValue(){}
		
		public NumberValue(long value){
			Value=value;
		}
		
		public override int GetID(){
			return 201;
		}
		
		public override PropertyValue Create(){
			return new NumberValue();
		}
		
		public override PropertyValue Copy(){
			
			NumberValue value=new NumberValue();
			value.Value=Value;
			return value;
			
		}
		
		public override void Read(Reader reader){
			
			Value=reader.ReadCompressedSigned();
			
		}
		
		public override void Write(Writer writer){
			
			writer.WriteCompressedSigned(Value);
			
		}
		
		public override double ToDouble(){
			return (double)Value;
		}
		
		public override float ZeroOneRange(){
			
			// Single byte caps out at 125:
			// (Generally better to use U-Numbers here)
			return (float)Value / 125f;
			
		}
		
		public override long ToLong(){
			
			return Value;
			
		}
		
		public override ulong ToULong(){
			
			return (ulong)Value;
			
		}
		
	}
	
}