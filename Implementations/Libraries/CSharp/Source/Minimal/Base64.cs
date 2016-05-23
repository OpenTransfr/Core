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

/// <summary>
/// Encodes and decodes base64 messages.
/// Primarily used by JSON Web Signature (JWS) objects.
/// </summary>

public static class Base64{
	
	/// <summary>Encodes the given message as base64.</summary>
	public static string Encode(string msg){
		
		// Get the message bytes:
		byte[] msgBytes=System.Text.Encoding.UTF8.GetBytes(msg);
		
		// Encode it:
		return Encode(msgBytes);
		
	}
	
	/// <summary>Encodes the given message as base64.</summary>
	public static string Encode(byte[] msgBytes){
		
		// Convert to base 64:
		return System.Convert.ToBase64String(msgBytes);
		
	}
	
	/// <summary>Decodes the given message from base64.</summary>
	public static byte[] Decode(string msg64){
		
		// Convert from base 64:
		return System.Convert.FromBase64String(msg64);
		
	}
	
}