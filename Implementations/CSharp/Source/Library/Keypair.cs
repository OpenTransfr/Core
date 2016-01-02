//--------------------------------------
//			  OpenTransfr
//	For copyright info etc see the License
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
	/// Generates a keypair for use with the signer and verifiers.
	/// Typically these keypairs are used as addresses.
	/// </summary>

	public static class Keypair{
		
		private static readonly ECDomainParameters _ecParams;
		private static readonly ECKeyPairGenerator Generator;
		
		static Keypair(){
			
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
		
		/// <summary>Generates a new keypair.</summary>
		public static void Generate(out byte[] priv,out byte[] pub){
			
			// Generate a new keypair:
			AsymmetricCipherKeyPair keypair = Generator.GenerateKeyPair();
			
			// Get the parameters:
			ECPrivateKeyParameters privParams = (ECPrivateKeyParameters) keypair.Private;
			ECPublicKeyParameters pubParams = (ECPublicKeyParameters) keypair.Public;
			
			// Grab the private key (A BigInteger):
			BigInteger privBI = privParams.D;
			
			// And the public key too (an encoded point on the elliptic curve):
			pub = pubParams.Q.GetEncoded();
			
			// Get the private keys bytes:
			priv = PrivateKeyBytes(privBI);
			
		}
		
		/// <summary>Gets the given private key bigInt as a byte array.</summary>
		public static byte[] PrivateKeyBytes(BigInteger privBI){
			
			return privBI.ToByteArray();
			
		}
		
		/// <summary>Loads a public/private key pair from the given ASN.1 formatted file.</summary>
		public static bool Load(string file,out byte[] privKey,out byte[] pubKey){
			
			// Does the file exist?
			if(!File.Exists(file)){
				
				privKey=null;
				pubKey=null;
				return false;
				
			}
			
			// Load the ASN.1 formatted data:
			byte[] asn1=File.ReadAllBytes(file);
			
			// Get the private key from it:
			BigInteger priv=Keypair.ExtractPrivateKeyFromAsn1(asn1);
			
			// Get the public key:
			pubKey=Keypair.PublicKeyFromPrivate(priv);
			
			// Get the private key bytes:
			privKey=Keypair.PrivateKeyBytes(priv);
			
			return true;
			
		}
		
		/// <summary>Saves a keypair to the given file in ASN.1 format.</summary>
		public static void Save(string file,byte[] priv,byte[] pub){
			
			// Get the directory:
			string dir=Path.GetDirectoryName(file);
			
			// Does it exist?
			if(dir!="" && !Directory.Exists(dir)){
				
				// Nope - create it now:
				Directory.CreateDirectory(dir);
				
			}
			
			// Write it out in ASN.1 format:
			byte[] asn1=Keypair.ToAsn1(priv,pub);
			
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
		
		/// <summary>
		/// Derive the public key by doing a point multiply of G * priv.
		/// </summary>
		public static byte[] PublicKeyFromPrivate(BigInteger privKey){
			return _ecParams.G.Multiply(privKey).GetEncoded();
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