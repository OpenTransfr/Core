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
using System.Collections;
using System.Collections.Generic;
using Wrench;


namespace OpenTransfr{
	
	/// <summary>
	/// Represents a location that a particular API is available at.
	/// For example, 'txroot.opentrans.fr' is a location that the root API is available from.
	/// </summary>

	public class Location{
		
		/// <summary>The DNS address.</summary>
		public string Address;
		/// <summary>The full URL of the location. Doesn't include the version (as the actual functions do).
		/// Includes a trailing forward slash.</summary>
		public string Url;
		/// <summary>The API being used at this location.</summary>
		public Api Api;
		
		
		/// <summary>Don't use this directly; instead, see the Api.GetAt method.</summary>
		internal Location(string address,Api api){
			
			Address=address;
			
			Api=api;
			
			// Note we don't include version here as the function/type names include the version.
			Url="https://"+Address+"/";
			
			if(api.AvailableObjects==null){
				
				// Load the index and apply it to available objects now:
				api.AvailableObjects=GetIndex();
				
			}
			
		}
		
		/// <summary>Gets the index of the API which lists all types/functions available.</summary>
		public Dictionary<string,ApiObject> GetIndex(){
			
			// Create the set:
			Dictionary<string,ApiObject> set=new Dictionary<string,ApiObject>();
			
			// Now we add the version:
			string path=Url+Api.VersionString;
			
			// Go get it:
			HttpResponse req;
			JSObject json=Http.RequestJson(path,out req);
			
			// Load functions and types - we just put them all into a single buffer:
			LoadIndex(json["functions"],set,false);
			LoadIndex(json["types"],set,true);
			
			return set;
		}
		
		/// <summary>Loads e.g. a set of functions described with JSON into a dictionary.</summary>
		private void LoadIndex(JSObject data,Dictionary<string,ApiObject> set,bool isType){
			
			// Get as array:
			JSArray dataSet=data as JSArray;
			
			// For each one in the data set..
			foreach(KeyValuePair<string,JSObject> kvp in dataSet.Values){
				
				// Transfer to set, creating an API function or API type for it:
				JSObject value=kvp.Value;
				
				// Get the path (including the version, but never a forward slash at the beginning):
				string path=kvp.Key;
				
				if(isType){
					// Value is from the types set. Let's create an ApiType for it:
					set[path]=new ApiType(value,Api,path);
				}else{
					// Value is from the functions set. Let's create an ApiFunction for it:
					set[path]=new ApiFunction(value,Api,path);
				}
				
			}
			
		}
		
		/// <summary>Runs the given function at this location.</summary>
		public JSObject Run(string function,JSObject payload,Signee signer){
			
			// Get the function from the API:
			ApiObject func=Api[function];
			
			if(func==null){
				// Function not found.
				throw new Exception("The function '"+function+"' is not a valid function for this API.");
			}
			
			// Try running it:
			return func.Run(payload,signer,this);
			
		}
		
	}

}