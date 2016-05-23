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


namespace OpenTransfr{

	/// <summary>
	/// Represents a particular API.
	/// For example, the Bank API or the Issuer API.
	/// Internally they are all the same structure.
	/// The API is implemented at a 'Location'. To call an API's functionality
	/// at a given location, see GetAt.
	/// </summary>

	public class Api{
		
		/// <summary>The version of the API that this implementation is designed to use.</summary>
		public int Version;
		/// <summary>The value 'v1/'.</summary> 
		public string VersionString;
		/// <summary>Currently available locations that implement this API.</summary>
		public Dictionary<string,Location> Locations;
		/// <summary>The available functions and types that the API provides.
		/// This is set when the first location implementing this API is loaded (which occurs in the GetAt method).</summary>
		public Dictionary<string,ApiObject> AvailableObjects;
		
		
		/// <summary>Creates an API implementation for version 1.</summary>
		public Api():this(1){
		}
		
		/// <summary>Creates an API implementation for the given version.</summary>
		public Api(int version){
			
			Version=version;
			
			// Apply the version string:
			VersionString="v"+Version+"/";
			
		}
		
		/// <summary>Gets the implementor at the given DNS address. 
		/// With one of these, you can call the API methods using Location.Run.</summary>
		/// <param name='dns'>The hostname of the implementor, e.g. 'bank.opentrans.fr'</param>
		public Location GetAt(string dns){
			
			if(Locations==null){
				// Setup locations:
				Locations=new Dictionary<string,Location>();
			}
			
			Location result;
			if(!Locations.TryGetValue(dns,out result)){
				
				// Create and add to the set:
				result=new Location(dns,this);
				Locations[dns]=result;
				
			}
			
			return result;
			
		}
		
		/// <summary>Gets the object (a type or a function) available at the given path.
		/// Note that the path does not include version; for example, 'commodity/list' is a valid function
		/// in the Root API.</summary>
		/// <param name='objectPath'>The path to the function/type you'd like
		///	the information for. e.g. 'commodity/list'</param>
		public ApiObject this[string objectPath]{
			get{
				
				// Does it start with a forward slash or the version string?
				// If so, remove it.
				if(objectPath[0]=='/'){
					
					// Chop it off:
					objectPath=objectPath.Substring(1);
					
				}
				
				// Does it already start with e.g. 'v1/'?
				if(!objectPath.StartsWith(VersionString)){
					
					// Add the version string:
					objectPath=VersionString+objectPath;
					
				}
				
				ApiObject result;
				// Lookup the object:
				AvailableObjects.TryGetValue(objectPath,out result);
				return result;
			}
		}
		
	}
	
}