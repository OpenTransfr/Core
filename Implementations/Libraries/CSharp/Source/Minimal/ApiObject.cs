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
	/// Each API provides two things: Functions and Types.
	/// Both are described/ documented in JSON which is found at the root of the API.
	/// This represents the base class of those functions/ types.
	/// An example of a type is error/field/invalid - an error object description.
	/// An example of a function is username/send (from the Bank API) - something that you can post JSON to ('run').
	/// </summary>
	
	public class ApiObject{
	
		/// <summary>The raw JSON data.</summary>
		public JSObject Data;
		/// <summary>The unique path of this object.
		/// Note that no type/function uses the same path as any other type/function.
		/// Also note that these contain the version. They also don't start with a forward slash.</summary>
		public string RelativePath;
		/// <summary>The API this belongs to.</summary>
		public Api Api;
		
		
		/// <summary>Creates a new API object.</summary>
		internal ApiObject(JSObject data,Api api,string path){
			Data=data;
			RelativePath=path;
			Api=api;
		}
		
		/// <summary>Gets the full path of this object relative to the given location.</summary>
		public string FullPath(Location at){
			
			// Simply take the URL and add the relative path to it
			// (Relative path doesn't start with a forward slash but Url always does).
			return at.Url+RelativePath;
			
		}
		
		/// <summary>Runs this API with the given JSON payload and a signee.</summary>
		public virtual JSObject Run(JSObject payload,Signee signer,Location at){
			
			// This occurs when the user, for example, attempted to 'run' an API type.
			// An example of a type is error/field/invalid - an error object description.
			throw new Exception("This only works on Api functions.");
			
		}
		
	}
	
}