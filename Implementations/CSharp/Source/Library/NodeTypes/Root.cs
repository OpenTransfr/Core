//--------------------------------------//              OpenTransfr//    For copyright info etc see the License//--------------------------------------using System;using System.IO;using Org.BouncyCastle.Math;namespace OpenTransfr{		/// <summary>	/// This is a root node. Root nodes are the core of the system; they all agree that a given transfer	/// has occured. Root nodes are expected to be operated by the most trusted and most secure servers on	/// the network in order to guarentee network safety. If you're not sure which to run, run a broadcast node.	/// </summary>		public class RootNode : Node{				/// <summary>The file which contains the public and private keys.</summary>		public const string KeyPairFile="FilesToBackup/root-keypair.bin";				/// <summary>The public key for this root node.</summary>		public byte[] PublicKey;		/// <summary>The private key for this root node.</summary>		private byte[] PrivateKey;		/// <summary>This root nodes info. A reference to the same object in the root node info set.</summary>		public RootNodeInfo Info;						/// <summary>The name of this type of node. Always lowercase, e.g. 'root' or 'broadcast'.</summary>		public override string Name{			get{				return "root";			}		}				public RootNode(){						// Load the private and public keys:			if(!LoadKeyPair()){								// Generate a new private/public keypair right now.				// Then, store it in this file.				GenerateKeyPair();							}					}				public RootNode(byte[] priv,byte[] pub){						PrivateKey=priv;			PublicKey=pub;					}				/// <summary>Generates a new key pair.</summary>		public void GenerateKeyPair(){						// Generate the pair:			Keypair.Generate(out PrivateKey,out PublicKey);						// Save it now:			SaveKeyPair();					}				/// <summary>Saves the key pair to 'KeyPairFile'.</summary>		public void SaveKeyPair(){						// Save:			Keypair.Save(KeyPairFile,PrivateKey,PublicKey);					}				public bool LoadKeyPair(){						// Load the pair:			return Keypair.Load(KeyPairFile,out PrivateKey,out PublicKey);					}				/// <summary>Signs the given message.</summary>		/// <returns>The signature.</summary>		public byte[] Sign(byte[] message){						return Signer.SignFull(message,PrivateKey);					}			}	}