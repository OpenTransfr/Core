//--------------------------------------//               PowerUI////        For documentation or //    if you have any issues, visit//        powerUI.kulestar.com////    Copyright � 2013 Kulestar Ltd//          www.kulestar.com//--------------------------------------using System;using Wrench;using Nitro;namespace Wrench{	/// <summary>	/// Represents a literal JSON value.	/// </summary>	public class JSLiteral:JSObject{				/// <summary>The raw string value.</summary>		public string Value;						/// <summary>Creates an empty JSON value.</summary>		public JSLiteral(){}				/// <summary>Creates a new JSON value for the given literal string.</summary>		public JSLiteral(string value){			Value=value;		}				public override string ToJSONString(){			return Value;		}				public override string ToString(){			return Value;		}			}	}