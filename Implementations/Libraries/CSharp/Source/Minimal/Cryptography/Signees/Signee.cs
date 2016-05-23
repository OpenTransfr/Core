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
using System.IO;


namespace OpenTransfr{
	
	/// <summary>
	/// An entity, device or straight keypair which signs a request.
	/// </summary>
	
	public partial class Signee{
		
		/// <suummary>Loads a signee (Entity, Device or Signee object) from the given file.</summary>
		public static Signee LoadFromFile(string path){
			
			// Load the file and the JSON:
			JSObject json=JSON.Parse( File.ReadAllText(path) );
			
			// Load the signee from the JSON:
			return Load(json);
			
		}
		
		/// <summary>
		/// Loads a signee from the given JS object. They are either a Device,
		/// Entity or basic Signee.</summary>
		public static Signee Load(JSObject obj){
			
			// Get the type if it has one:
			JSObject type=obj["type"];
			string typeString="";
			
			if(type!=null){
				// Get the name:
				typeString=type.ToString().ToLower().Trim();
			}
			
			// Type string is either 'device' or 'entity'.
			// Anything else and we just have a basic signee.
			switch(typeString){
				
				case "device":
					
					// Load a device:
					return new Device(obj);
				
				case "entity":
					
					// Load an entity:
					return new Entity(obj);
				
				default:
					
					// Basic signee:
					return new Signee(obj);
				
			}
			
		}
		
		/// <summary>
		/// This is primarily used by an authenticating device on the Bank API.
		/// It's the sequence code that must be sent with the next request.
		/// This blocks two users from 'sharing' the same private key (i.e. it helps detect a stolen key).
		/// </summary>
		public string Sequence;
		/// <summary>
		/// The keypair itself.
		/// </summary>
		public Keypair Key;
		
		
		/// <summary>
		/// Use Signee.Load if you don't know what type of signee a JSON file is.
		/// Creates a signee from the given JSON object.
		/// Used by the Device or Entity objects.</summary>
		internal Signee(JSObject obj){
			
			// Load the keypair:
			Key=new Keypair(obj["keypair"]);
			
		}
		
		/// <summary>
		/// Creates a new signee.
		/// Typically used by the Device and Entity objects.</summary>
		public Signee(){
			
			// Create a keypair:
			Key=new Keypair();
			
		}
		
		/// <summary>
		/// The JWS header defines what signed the JWS payload - an entity, a device etc.
		/// This creates the JWS header.</summary>
		public virtual void GetJwsHeader(JSObject header){
			
			// The default is to directly use the public key - aka the 'pk' header.
			// It's in hex, like so:
			header["pk"]=new JSValue( Hex.Encode( Key.Public ) );
			
		}
		
		/// <summary>
		/// Fills a JSON object for this signee.
		/// </summary>
		public virtual void ToJson(JSObject obj){
			
			// Just the keypair by default:
			obj["keypair"]=Key.ToJson();
			
		}
		
		/// <summary>
		/// Builds a JSON object for this signee.
		/// </summary>
		public JSObject ToJson(){
			
			// Create:
			JSObject obj=new JSArray();
			
			// Fill it:
			ToJson(obj);
			
			return obj;
			
		}
		
		/// <summary>
		/// Saves this signee as JSON to the given file.</summary>
		public void SaveJson(string path){
			
			// Build the JSON:
			string jsonData=JSON.Stringify(ToJson());
			
			// Write it out:
			File.WriteAllText(path,jsonData);
			
		}
		
		/// <summary>
		/// Signs the given message using this keypair. The result is a base64 string.
		/// </summary>
		public string Sign(string message){
			
			// Sign using the key:
			return Key.Sign(message);
			
		}
		
	}
	
}