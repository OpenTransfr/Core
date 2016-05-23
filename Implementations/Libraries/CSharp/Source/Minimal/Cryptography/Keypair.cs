//--------------------------------------
//             OpenTransfr
//
//        For documentation or 
//    if you have any issues, visit
//             opentrans.fr
//
//          Licensed under MIT
//--------------------------------------


// Some of the methods here are with many thanks to bitcoinsharp. It's license follows:

/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

using System;
using System.IO;
using Wrench;
using Org.BouncyCastle.Asn1;
using Org.BouncyCastle.Asn1.X9;
using Org.BouncyCastle.Asn1.Sec;
using Org.BouncyCastle.Crypto;
using Org.BouncyCastle.Crypto.Generators;
using Org.BouncyCastle.Crypto.Parameters;
using Org.BouncyCastle.Crypto.Signers;
using Org.BouncyCastle.Math;
using Org.BouncyCastle.Security;
using System.Security.Cryptography;
using Org.BouncyCastle.Crypto.Digests;


namespace OpenTransfr{
	
	/// <summary>
	/// Generates a keypair for use with the signer and verifiers.
	/// Typically these keypairs are used as addresses.
	/// </summary>

	public partial class Keypair{
		
		/// <summary>The parameters for the secp256k1 curve.</summary>
		private static ECDomainParameters _ecParams;
		/// <summary>A keypair generator.</summary>
		private static ECKeyPairGenerator Generator;
		
		
		/// <summary>Sets up shared parameters. Occurs when a keypair is first generated.</summary>
		private static void Setup(){
			
			// Get the secp256k1 curve parameters:
			X9ECParameters curParams = SecNamedCurves.GetByName("secp256k1");
			_ecParams = new ECDomainParameters(curParams.Curve, curParams.G, curParams.N, curParams.H);
			
			// Create a generator:
			Generator = new ECKeyPairGenerator();
			
			// With the params:
			ECKeyGenerationParameters keygenParams = new ECKeyGenerationParameters(_ecParams, new SecureRandom());
			
			// Start the generator:
			Generator.Init(keygenParams);
			
		}
		
		/// <summary>The raw private key.</summary>
		private byte[] _Private;
		/// <summary>The raw public key.</summary>
		private byte[] _Public;
		
		
		/// <summary>The raw private key.</summary>
		public byte[] Private{
			get{
				return _Private;
			}
		}
		
		/// <summary>The raw public key.</summary>
		public byte[] Public{
			get{
				return _Public;
			}
		}
		
		/// <summary>Generates a new keypair.</summary>
		public Keypair(){
			
			if(Generator==null){
				
				// It's not been setup yet. Setup now:
				Setup();
				
			}
			
			// Generate a new keypair:
			AsymmetricCipherKeyPair keypair = Generator.GenerateKeyPair();
			
			// Get the parameters:
			ECPrivateKeyParameters privParams = (ECPrivateKeyParameters) keypair.Private;
			ECPublicKeyParameters pubParams = (ECPublicKeyParameters) keypair.Public;
			
			// Grab the private key (A BigInteger):
			BigInteger privBI = privParams.D;
			
			// And the public key too (an encoded point on the elliptic curve):
			_Public = pubParams.Q.GetEncoded();
			
			// Get the private keys bytes:
			_Private = privBI.ToByteArray();
			
		}
		
		/// <summary>Loads a keypair from the given object.</summary>
		public Keypair(JSObject obj){
			
			// Decode the private and public keys:
			_Private=Hex.Decode(obj["private"].ToString());
			_Public=Hex.Decode(obj["public"].ToString());
			
		}
		
		/// <summary>Loads a keypair from the given raw private key.</summary>
		public Keypair(BigInteger privateKey){
			
			// Get the priv key:
			byte[] privateBytes=privateKey.ToByteArray();
			
			// Apply the private key:
			_Private=privateBytes;
			
			// Get the public key:
			_Public=Keypair.PublicKeyFromPrivate(privateKey);
			
		}
		
		/// <summary>Signs the given message, returning the base64 encoded signature 
		/// (as required by JWS for the OpenTransfr API).</summary>
		public string Sign(string msg){
			
			// Sign using the signer:
			byte[] signature=Signer.Sign(msg,_Private);
			
			// Encode as base 64:
			return Base64.Encode(signature);
			
		}
		
		/// <summary>Converts this keypair to a JSON object. Both parts are hex encoded.</summary>
		public JSObject ToJson(){
			
			// Create:
			JSObject obj=new JSArray();
			
			// Add priv/pub:
			obj["private"]=new JSValue(Hex.Encode(_Private));
			obj["public"]=new JSValue(Hex.Encode(_Public));
			
			// Done:
			return obj;
			
		}
		
		/// <summary>
		/// Derive the public key by doing a point multiply of G * priv.
		/// </summary>
		public static byte[] PublicKeyFromPrivate(BigInteger privKey){
			return _ecParams.G.Multiply(privKey).GetEncoded();
		}
		
	}
	
}