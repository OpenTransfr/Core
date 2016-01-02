//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A single floating point value.
	/// </summary>
	
	public class FloatValue:NumericValue{
		
		public float Value;
		
		
		public FloatValue(){}
		
		public FloatValue(float value){
			Value=value;
		}
		
		public override int GetID(){
			return 202;
		}
		
		public override PropertyValue Create(){
			return new FloatValue();
		}
		
		public override PropertyValue Copy(){
			
			FloatValue value=new FloatValue();
			value.Value=Value;
			return value;
			
		}
		
		public override void Read(Reader reader){
			
			Value=reader.ReadSingle();
			
		}
		
		public override void Write(Writer writer){
			
			writer.Write(Value);
			
		}
		
		public override double ToDouble(){
			return Value;
		}
		
		public override float ZeroOneRange(){
			return Value;
		}
		
		public override long ToLong(){
			
			return (long)Value;
			
		}
		
		public override ulong ToULong(){
			
			return (ulong)Value;
			
		}
		
	}
	
}