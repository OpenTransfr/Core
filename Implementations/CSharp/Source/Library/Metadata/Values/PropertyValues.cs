//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Reflection;
using System.Collections;
using System.Collections.Generic;


namespace OpenTransfr{
	
	/// <summary>
	/// Manager for selecting anything such as a name of a commodity.
	/// Note that this is self starting on the first Get() request.
	/// </summary>
	
	public static class PropertyValues{
		
		/// <summary>All property value handlers.</summary>
		public static Dictionary<int,PropertyValue> All;
		
		
		/// <summary>Loads a property value from the given reader.</summary>
		public static PropertyValue ReadPropertyValue(Reader reader){
			
			// What value type is it?
			int type=(int)reader.ReadCompressed();
			
			// Create it:
			PropertyValue value=PropertyValues.Get(type);
			
			if(value==null){
				return null;
			}
			
			// Read it:
			value.Read(reader);
			
			return value;
		}
		
		/// <summary>Get all property value handlers.</summary>
		private static void Start(){
			
			All=new Dictionary<int,PropertyValue>();
			
			#if NETFX_CORE
			Assembly asm=typeof(PropertyValues).GetTypeInfo().Assembly;
			#else
			Assembly asm=Assembly.GetExecutingAssembly();
			#endif
			
			Find(asm.GetTypes());
		
		}
		
		public static void Find(Type[] allTypes){
			
			// For each type..
			for(int i=allTypes.Length-1;i>=0;i--){
				Type type=allTypes[i];
				
				if(type.IsGenericType){
					continue;
				}
				
				if( type.IsSubclassOf(typeof(PropertyValue)) ){
					PropertyValue value=(PropertyValue)Activator.CreateInstance(type);
					All[value.GetID()]=value;
				}
				
			}
			
		}
		
		public static PropertyValue Get(int id){
			
			if(All==null){
				Start();
			}
			
			PropertyValue result;
			
			if(All.TryGetValue(id,out result)){
				
				return result.Create();
				
			}
			
			return null;
			
		}
		
	}
	
}