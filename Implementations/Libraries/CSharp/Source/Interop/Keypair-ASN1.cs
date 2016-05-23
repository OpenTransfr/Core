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
	/// Extra functionality which helps the OpenTransfr keypairs interchange with e.g. OpenSSL.
	/// </summary>

	public partial class Keypair{
		
		/// <summary>Loads a public/private key pair from the given ASN.1 formatted file.</summary>
		public static Keypair Load(string file){
			
			// Load the ASN.1 formatted data (errors if the file doesn't exist):
			byte[] asn1=File.ReadAllBytes(file);
			
			// Get the private key from it:
			BigInteger priv=Keypair.ExtractPrivateKeyFromAsn1(asn1);
			
			// Create the keypair:
			Keypair pair=new Keypair(priv);
			
			// Done:
			return pair;
			
		}
		
		/// <summary>Saves a keypair to the given file in ASN.1 format.</summary>
		public void Save(string file){
			
			// Get the directory:
			string dir=Path.GetDirectoryName(file);
			
			// Does it exist?
			if(dir!="" && !Directory.Exists(dir)){
				
				// Nope - create it now:
				Directory.CreateDirectory(dir);
				
			}
			
			// Write it out in ASN.1 format:
			byte[] asn1=Keypair.ToAsn1(_Private,_Public);
			
			// Delete if exists:
			if(File.Exists(file)){
				
				// Delete:
				File.Delete(file);
				
			}
			
			// Write it out:
			File.WriteAllBytes(file,asn1);
			
		}
		
		/// <summary>
		/// Outputs a keypair as an ASN.1 encoded private key, as understood by OpenSSL or used by the BitCoin reference
		/// implementation in its wallet storage format.
		/// </summary>
		public static byte[] ToAsn1(byte[] priv,byte[] pub)
		{
			using (MemoryStream baos = new MemoryStream(400))
			{
				using (Asn1OutputStream encoder = new Asn1OutputStream(baos))
				{
					// ASN1_SEQUENCE(EC_PRIVATEKEY) = {
					//   ASN1_SIMPLE(EC_PRIVATEKEY, version, LONG),
					//   ASN1_SIMPLE(EC_PRIVATEKEY, privateKey, ASN1_OCTET_STRING),
					//   ASN1_EXP_OPT(EC_PRIVATEKEY, parameters, ECPKPARAMETERS, 0),
					//   ASN1_EXP_OPT(EC_PRIVATEKEY, publicKey, ASN1_BIT_STRING, 1)
					// } ASN1_SEQUENCE_END(EC_PRIVATEKEY)
					DerSequenceGenerator seq = new DerSequenceGenerator(encoder);
					seq.AddObject(new DerInteger(1)); // version
					seq.AddObject(new DerOctetString(priv));
					seq.AddObject(new DerTaggedObject(0, SecNamedCurves.GetByName("secp256k1").ToAsn1Object()));
					seq.AddObject(new DerTaggedObject(1, new DerBitString(pub)));
					seq.Close();
				}
				
				return baos.ToArray();
			}
		}
		
		/// <summary>Gets the private key from an ASN1 encoded block of data.</summary>
		public static BigInteger ExtractPrivateKeyFromAsn1(byte[] asn1PrivKey){
			// To understand this code, see the definition of the ASN.1 format for EC private keys in the OpenSSL source
			// code in ec_asn1.c:
			//
			// ASN1_SEQUENCE(EC_PRIVATEKEY) = {
			//   ASN1_SIMPLE(EC_PRIVATEKEY, version, LONG),
			//   ASN1_SIMPLE(EC_PRIVATEKEY, privateKey, ASN1_OCTET_STRING),
			//   ASN1_EXP_OPT(EC_PRIVATEKEY, parameters, ECPKPARAMETERS, 0),
			//   ASN1_EXP_OPT(EC_PRIVATEKEY, publicKey, ASN1_BIT_STRING, 1)
			// } ASN1_SEQUENCE_END(EC_PRIVATEKEY)
			//
			DerOctetString key;
			using (Asn1InputStream decoder = new Asn1InputStream(asn1PrivKey))
			{
				DerSequence seq = (DerSequence) decoder.ReadObject();
				
				if(seq.Count != 4){
					
					throw new Exception("Input does not appear to be an ASN.1 OpenSSL EC private key");
					
				}
				
				if(!((DerInteger) seq[0]).Value.Equals(BigInteger.One)){
					
					throw new Exception("Input is of wrong version");
					
				}
				
				key = (DerOctetString) seq[1];
			}
			
			return new BigInteger(1, key.GetOctets());
		}

	}

}