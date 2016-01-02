//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A single true/false value.
	/// </summary>
	
	public class BoolValue:NumericValue{
		
		public bool Value;
		
		
		public BoolValue(){}
		
		public BoolValue(bool value){
			Value=value;
		}
		
		public override int GetID(){
			return 204;
		}
		
		public override PropertyValue Create(){
			return new BoolValue();
		}
		
		public override PropertyValue Copy(){
			
			BoolValue value=new BoolValue();
			value.Value=Value;
			return value;
			
		}
		
		public override void Read(Reader reader){
			
			Value=(reader.ReadByte()==1);
			
		}
		
		public override void Write(Writer writer){
			
			if(Value){
				writer.WriteByte((byte)1);
			}else{
				writer.WriteByte((byte)0);
			}
			
		}
		
		public override double ToDouble(){
			if(Value){
				return 1.0;
			}
			
			return 0.0;
		}
		
		public override float ZeroOneRange(){
			if(Value){
				return 1f;
			}
			
			return 0f;
		}
		
	}
	
}