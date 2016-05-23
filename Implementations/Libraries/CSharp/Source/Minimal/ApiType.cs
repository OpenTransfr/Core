//--------------------------------------
//             OpenTransfr
//
//        For documentation or 
//    if you have any issues, visit
//             opentrans.fr
//
//          Licensed under MIT
//--------------------------------------

using System;
using Wrench;


namespace OpenTransfr{
	
	/// <summary>
	/// Represents a type description. For example, these define all the properties
	/// of error objects in a nice localisable way.
	/// An example of a type is error/field/invalid - an error object description.
	/// </summary>
	
	public class ApiType:ApiObject{
		
		public ApiType(JSObject data,Api api,string path):base(data,api,path){}
		
	}
	
}