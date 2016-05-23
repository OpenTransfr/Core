C:\Windows\Microsoft.Net\Framework\v2.0.50727\csc.exe /nologo /out:Build\transfr.exe /recurse:Source\* /reference:..\..\Build\BouncyCastle.Crypto.dll /reference:..\..\Build\OpenTransfr.dll
@pause
Build\Standalone\transfr node:root
@pause