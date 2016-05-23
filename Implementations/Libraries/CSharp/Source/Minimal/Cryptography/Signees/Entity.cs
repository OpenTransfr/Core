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
using OpenTransfr;


namespace OpenTransfr{
	
	/// <summary>
	/// An entity typically authenticates with the Root API.
	/// Entities are things like banks and issuers.
	/// </summary>
	
	public class Entity:Signee{
		
		/// <summary>The endpoint of this entity. E.g. 'bank.opentrans.fr'</summary>
		public string Endpoint;
		
		/// <summary>
		/// Creates a new entity for the given endpoint.
		/// Note that this does not register it.
		/// </summary>
		public Entity(string endpoint):base(){
			
			Endpoint=endpoint.Trim().ToLower();
			
		}
		
		/// <summary>
		/// Loads an entity from the given JSON object.
		/// </summary>
		public Entity(JSObject json):base(json){
			
			// Load the endpoint:
			Endpoint=json["endpoint"].ToString();
			
		}
		
		/// <summary>
		/// The JWS header defines what signed the JWS payload - an entity, a device etc.
		/// This creates the JWS header.</summary>
		public override void GetJwsHeader(JSObject header){
			
			// Entity's use the 'entity' header along with their endpoint.
			
			// Create the device header now:
			header["entity"]=new JSValue(Endpoint);
			
		}
		
		/// <summary>
		/// Builds a JSON string representation of this signee.
		/// </summary>
		public override void ToJson(JSObject obj){
			
			// Must store the endpoint (as well as the keypair, done by base)
			base.ToJson(obj);
			
			// Apply a type:
			obj["type"]=new JSValue("entity");
			
			obj["endpoint"]=new JSValue(Endpoint);
			
		}
		
	}
	
}