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
using Org.BouncyCastle.Asn1;
using Org.BouncyCastle.Asn1.X9;
using Org.BouncyCastle.Asn1.Sec;
using Org.BouncyCastle.Crypto.Generators;
using Org.BouncyCastle.Crypto.Parameters;
using Org.BouncyCastle.Crypto.Signers;
using Org.BouncyCastle.Math;
using Org.BouncyCastle.Security;
using System.Security.Cryptography;
using Org.BouncyCastle.Crypto.Digests;


namespace OpenTransfr{
	
	/// <summary>
	/// Verifies a signature for a given message with a given public key.
	/// </summary>

	public static class Verifier{
		
		private static readonly ECDomainParameters _ecParams;

		static Verifier(){
			
			// Get secp256k1 params.
			X9ECParameters curParams = SecNamedCurves.GetByName("secp256k1");
			_ecParams = new ECDomainParameters(curParams.Curve, curParams.G, curParams.N, curParams.H);
			
		}
		
		/// <summary>Verifies the signature for the given message. 
		/// Note that public key is hex and the signature is base64. The message is the complete message. This uses the
		/// bitcoin double sha256 hash on the message.</summary>
		public static bool Verify(string msg,string publicKeyHex,string signature64){
			
			// Get the bytes from the strings:
			byte[] signatureBytes=Base64.Decode(signature64);
			byte[] publicKeyBytes=Hex.Decode(publicKeyHex);
			
			// Compute the hash of the message:
			byte[] hash=DoubleDigest(System.Text.Encoding.UTF8.GetBytes(msg));
			
			// Verify!
			return VerifyHash(hash,signatureBytes,publicKeyBytes);
			
		}
		
		/// <summary>Verifies the signature for the given message. 
		/// Note that public key and signature are their raw bytes, and msg is the complete message. This uses the
		/// bitcoin double sha256 hash on the message.</summary>
		public static bool Verify(string msg,byte[] publicKeyBytes,byte[] signatureBytes){
			
			// Compute the hash of the message:
			byte[] hash=DoubleDigest(System.Text.Encoding.UTF8.GetBytes(msg));
			
			// Verify!
			return VerifyHash(hash,signatureBytes,publicKeyBytes);
			
		}
		
		/// <summary>Verifies the signature for the given message. 
		/// Note that public key and signature are their raw bytes, and msg is the complete message. This uses the
		/// bitcoin double sha256 hash on the message.</summary>
		public static bool VerifyFull(byte[] msg,byte[] publicKeyBytes,byte[] signatureBytes){
			
			// Compute the hash of the message:
			byte[] hash=DoubleDigest(msg);
			
			// Verify!
			return VerifyHash(hash,signatureBytes,publicKeyBytes);
			
		}
		
		/// <summary>
		/// Calculates the SHA-256 hash of the given bytes and then hashes the resulting hash again.
		/// </summary>
		public static byte[] DoubleDigest(byte[] input){
			SHA256Managed algorithm = new SHA256Managed();
			byte[] first = algorithm.ComputeHash(input, 0, input.Length);
			return algorithm.ComputeHash(first);
		}

		/// <summary>
		/// Calculates the SHA-256 hash of the given bytes and then hashes the resulting hash again.
		/// </summary>
		public static byte[] DoubleDigest(byte[] input,int offset,int length){
			SHA256Managed algorithm = new SHA256Managed();
			byte[] first = algorithm.ComputeHash(input, offset, length);
			return algorithm.ComputeHash(first);
		}
		
		/// <summary>
		/// Verifies the given DER encoded ECDSA signature against a hash using the public key.
		/// </summary>
		/// <param name="data">Hash of the data to verify.</param>
		/// <param name="signature">DER encoded signature.</param>
		/// <param name="pub">The public key bytes to use.</param>
		public static bool VerifyHash(byte[] data, byte[] signature, byte[] pub)
		{
			ECDsaSigner signer = new ECDsaSigner();
			ECPublicKeyParameters curParams = new ECPublicKeyParameters(_ecParams.Curve.DecodePoint(pub), _ecParams);
			signer.Init(false, curParams);
			DerInteger r;
			DerInteger s;
			using (Asn1InputStream decoder = new Asn1InputStream(signature))
			{
				DerSequence seq = (DerSequence) decoder.ReadObject();
				r = (DerInteger) seq[0];
				s = (DerInteger) seq[1];
			}
			return signer.VerifySignature(data, r.Value, s.Value);
		}
		
	}
	
}