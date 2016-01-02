//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// A single value for a particular property.
	/// </summary>
	
	public class TextValue:PropertyValue{
		
		public string Value;
		
		
		public TextValue(){}
		
		public TextValue(string value){
			Value=value;
		}
		
		public override int GetID(){
			return 200;
		}
		
		public override PropertyValue Create(){
			return new TextValue();
		}
		
		public override PropertyValue Copy(){
			
			TextValue value=new TextValue();
			value.Value=Value;
			return value;
			
		}
		
		public override void Read(Reader reader){
			
			Value=reader.ReadString();
			
		}
		
		public override void Write(Writer writer){
			
			writer.WriteString(Value);
			
		}
		
	}
	
}