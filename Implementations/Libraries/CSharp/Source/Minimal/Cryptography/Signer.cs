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
	/// Signs a message with a given private key.
	/// </summary>

	public static class Signer{
		
		private static readonly ECDomainParameters _ecParams;

		static Signer(){
			
			// Get secp256k1 params.
			X9ECParameters curParams = SecNamedCurves.GetByName("secp256k1");
			_ecParams = new ECDomainParameters(curParams.Curve, curParams.G, curParams.N, curParams.H);
			
		}
		
		/// <summary>Signs the given message. 
		/// Note that this uses the bitcoin double sha256 hash on the message.</summary>
		public static byte[] Sign(string msg,byte[] privateKeyBytes){
			
			// Compute the hash of the message:
			byte[] hash=Verifier.DoubleDigest(System.Text.Encoding.UTF8.GetBytes(msg));
			
			// Sign!
			return SignHash(hash,privateKeyBytes);
			
		}
		
		/// <summary>Signs the given message bytes. 
		/// Note that this uses the bitcoin double sha256 hash on the message.</summary>
		public static byte[] SignFull(byte[] msg,byte[] privateKeyBytes){
			
			// Compute the hash of the message:
			byte[] hash=Verifier.DoubleDigest(msg);
			
			// Sign!
			return SignHash(hash,privateKeyBytes);
			
		}
		
		/// <summary>Signs a hash using the given private key.</summary>
		public static byte[] SignHash(byte[] data,byte[] priv){
			
			ECDsaSigner signer = new ECDsaSigner();
			ECPrivateKeyParameters curParams = new ECPrivateKeyParameters(new BigInteger(priv), _ecParams);
			signer.Init(true, curParams);
			
			// Generate the signature (produces the r and s values):
			BigInteger[] rs=signer.GenerateSignature(data);
			
			// Output stream:
			System.IO.MemoryStream stream=new System.IO.MemoryStream();
			
			// Output writer:
			System.IO.BinaryWriter sig=new System.IO.BinaryWriter(stream);
			
			// Get r and s as bytes (signed arrays):
			byte[] rBa = rs[0].ToByteArray();
			byte[] sBa = rs[1].ToByteArray();
			
			// Sequence:
			sig.Write((byte)0x30);
			
			// Length (0 for now):
			sig.Write((byte)0);
			
			// Int:
			sig.Write((byte)(0x02));
			sig.Write((byte)rBa.Length);
			sig.Write(rBa);
			
			// Int:
			sig.Write((byte)(0x02));
			sig.Write((byte)sBa.Length);
			sig.Write(sBa);
			
			// Get the length:
			int length=(int)stream.Length;
			
			// Seek back and write the length - Goto 1:
			sig.Seek(1,System.IO.SeekOrigin.Begin);
			sig.Write((byte)length);
			
			// Get the data as a block of bytes:
			byte[] rawData=stream.ToArray();
			stream.Close();
			stream.Dispose();
			
			return rawData;
			
		}
		
	}
	
}