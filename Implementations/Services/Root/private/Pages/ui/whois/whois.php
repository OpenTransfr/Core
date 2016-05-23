<?php

class Whois
{
	
	// Contains all available TLD's.
	var $tlds=array();
	
	/*
	 * Constructor function
	 */
	function Whois()
	{
		// Load tld's:
		$tldList=explode(',','aaa,aarp,abb,abbott,abbvie,abogado,abudhabi,ac,academy,accenture,accountant,accountants,aco,active,actor,ad,adac,ads,adult,ae,aeg,aero,af,afl,ag,agakhan,agency,ai,aig,airforce,airtel,akdn,al,alibaba,alipay,allfinanz,ally,alsace,am,amica,amsterdam,analytics,android,anquan,ao,apartments,app,apple,aq,aquarelle,ar,aramco,archi,army,arpa,arte,as,asia,associates,at,attorney,au,auction,audi,audio,author,auto,autos,avianca,aw,aws,ax,axa,az,azure,ba,baby,baidu,band,bank,bar,barcelona,barclaycard,barclays,barefoot,bargains,bauhaus,bayern,bb,bbc,bbva,bcg,bcn,bd,be,beats,beer,bentley,berlin,best,bet,bf,bg,bh,bharti,bi,bible,bid,bike,bing,bingo,bio,biz,bj,black,blackfriday,bloomberg,blue,bm,bms,bmw,bn,bnl,bnpparibas,bo,boats,boehringer,bom,bond,boo,book,boots,bosch,bostik,bot,boutique,br,bradesco,bridgestone,broadway,broker,brother,brussels,bs,bt,budapest,bugatti,build,builders,business,buy,buzz,bv,bw,by,bz,bzh,ca,cab,cafe,cal,call,camera,camp,cancerresearch,canon,capetown,capital,car,caravan,cards,care,career,careers,cars,cartier,casa,cash,casino,cat,catering,cba,cbn,cc,cd,ceb,center,ceo,cern,cf,cfa,cfd,cg,ch,chanel,channel,chase,chat,cheap,chloe,christmas,chrome,church,ci,cipriani,circle,cisco,citic,city,cityeats,ck,cl,claims,cleaning,click,clinic,clinique,clothing,cloud,club,clubmed,cm,cn,co,coach,codes,coffee,college,cologne,com,commbank,community,company,compare,computer,comsec,condos,construction,consulting,contact,contractors,cooking,cool,coop,corsica,country,coupon,coupons,courses,cr,credit,creditcard,creditunion,cricket,crown,crs,cruises,csc,cu,cuisinella,cv,cw,cx,cy,cymru,cyou,cz,dabur,dad,dance,date,dating,datsun,day,dclk,dds,de,dealer,deals,degree,delivery,dell,deloitte,delta,democrat,dental,dentist,desi,design,dev,diamonds,diet,digital,direct,directory,discount,dj,dk,dm,dnp,do,docs,dog,doha,domains,download,drive,dubai,durban,dvag,dz,earth,eat,ec,edeka,edu,education,ee,eg,email,emerck,energy,engineer,engineering,enterprises,epson,equipment,er,erni,es,esq,estate,et,eu,eurovision,eus,events,everbank,exchange,expert,exposed,express,extraspace,fage,fail,fairwinds,faith,family,fan,fans,farm,fashion,fast,feedback,ferrero,fi,film,final,finance,financial,firestone,firmdale,fish,fishing,fit,fitness,fj,fk,flickr,flights,flir,florist,flowers,flsmidth,fly,fm,fo,foo,football,ford,forex,forsale,forum,foundation,fox,fr,fresenius,frl,frogans,frontier,ftr,fund,furniture,futbol,fyi,ga,gal,gallery,gallo,gallup,game,garden,gb,gbiz,gd,gdn,ge,gea,gent,genting,gf,gg,ggee,gh,gi,gift,gifts,gives,giving,gl,glass,gle,global,globo,gm,gmail,gmbh,gmo,gmx,gn,gold,goldpoint,golf,goo,goog,google,gop,got,gov,gp,gq,gr,grainger,graphics,gratis,green,gripe,group,gs,gt,gu,guardian,gucci,guge,guide,guitars,guru,gw,gy,hamburg,hangout,haus,hdfcbank,health,healthcare,help,helsinki,here,hermes,hiphop,hitachi,hiv,hk,hkt,hm,hn,hockey,holdings,holiday,homedepot,homes,honda,horse,host,hosting,hoteles,hotmail,house,how,hr,hsbc,ht,htc,hu,hyundai,ibm,icbc,ice,icu,id,ie,ifm,iinet,il,im,imamat,immo,immobilien,in,industries,infiniti,info,ing,ink,institute,insurance,insure,int,international,investments,io,ipiranga,iq,ir,irish,is,iselect,ismaili,ist,istanbul,it,itau,iwc,jaguar,java,jcb,jcp,je,jetzt,jewelry,jlc,jll,jm,jmp,jnj,jo,jobs,joburg,jot,joy,jp,jpmorgan,jprs,juegos,kaufen,kddi,ke,kerryhotels,kerrylogistics,kerryproperties,kfh,kg,kh,ki,kia,kim,kinder,kitchen,kiwi,km,kn,koeln,komatsu,kp,kpmg,kpn,kr,krd,kred,kuokgroup,kw,ky,kyoto,kz,la,lacaixa,lamborghini,lamer,lancaster,land,landrover,lanxess,lasalle,lat,latrobe,law,lawyer,lb,lc,lds,lease,leclerc,legal,lexus,lgbt,li,liaison,lidl,life,lifeinsurance,lifestyle,lighting,like,limited,limo,lincoln,linde,link,lipsy,live,living,lixil,lk,loan,loans,locus,lol,london,lotte,lotto,love,lr,ls,lt,ltd,ltda,lu,lupin,luxe,luxury,lv,ly,ma,madrid,maif,maison,makeup,man,management,mango,market,marketing,markets,marriott,mba,mc,md,me,med,media,meet,melbourne,meme,memorial,men,menu,meo,metlife,mg,mh,miami,microsoft,mil,mini,mk,ml,mls,mm,mma,mn,mo,mobi,mobily,moda,moe,moi,mom,monash,money,montblanc,mormon,mortgage,moscow,motorcycles,mov,movie,movistar,mp,mq,mr,ms,mt,mtn,mtpc,mtr,mu,museum,mutual,mutuelle,mv,mw,mx,my,mz,na,nadex,nagoya,name,natura,navy,nc,ne,nec,net,netbank,network,neustar,new,news,next,nextdirect,nexus,nf,ng,ngo,nhk,ni,nico,nikon,ninja,nissan,nissay,nl,no,nokia,northwesternmutual,norton,nowruz,nowtv,np,nr,nra,nrw,ntt,nu,nyc,nz,obi,office,okinawa,olayan,olayangroup,om,omega,one,ong,onl,online,ooo,oracle,orange,org,organic,origins,osaka,otsuka,ovh,pa,page,pamperedchef,panerai,paris,pars,partners,parts,party,passagens,pccw,pe,pet,pf,pg,ph,pharmacy,philips,photo,photography,photos,physio,piaget,pics,pictet,pictures,pid,pin,ping,pink,pizza,pk,pl,place,play,playstation,plumbing,plus,pm,pn,pohl,poker,porn,post,pr,praxi,press,pro,prod,productions,prof,progressive,promo,properties,property,protection,ps,pt,pub,pw,pwc,py,qa,qpon,quebec,quest,racing,re,read,realtor,realty,recipes,red,redstone,redumbrella,rehab,reise,reisen,reit,ren,rent,rentals,repair,report,republican,rest,restaurant,review,reviews,rexroth,rich,richardli,ricoh,rio,rip,ro,rocher,rocks,rodeo,room,rs,rsvp,ru,ruhr,run,rw,rwe,ryukyu,sa,saarland,safe,safety,sakura,sale,salon,samsung,sandvik,sandvikcoromant,sanofi,sap,sapo,sarl,sas,saxo,sb,sbi,sbs,sc,sca,scb,schaeffler,schmidt,scholarships,school,schule,schwarz,science,scor,scot,sd,se,seat,security,seek,select,sener,services,seven,sew,sex,sexy,sfr,sg,sh,sharp,shaw,shell,shia,shiksha,shoes,shouji,show,shriram,si,sina,singles,site,sj,sk,ski,skin,sky,skype,sl,sm,smile,sn,sncf,so,soccer,social,softbank,software,sohu,solar,solutions,song,sony,soy,space,spiegel,spot,spreadbetting,sr,srl,st,stada,star,starhub,statebank,statefarm,statoil,stc,stcgroup,stockholm,storage,store,stream,studio,study,style,su,sucks,supplies,supply,support,surf,surgery,suzuki,sv,swatch,swiss,sx,sy,sydney,symantec,systems,sz,tab,taipei,talk,taobao,tatamotors,tatar,tattoo,tax,taxi,tc,tci,td,team,tech,technology,tel,telecity,telefonica,temasek,tennis,teva,tf,tg,th,thd,theater,theatre,tickets,tienda,tiffany,tips,tires,tirol,tj,tk,tl,tm,tmall,tn,to,today,tokyo,tools,top,toray,toshiba,total,tours,town,toyota,toys,tr,trade,trading,training,travel,travelers,travelersinsurance,trust,trv,tt,tube,tui,tunes,tushu,tv,tvs,tw,tz,ua,ubs,ug,uk,unicom,university,uno,uol,us,uy,uz,va,vacations,vana,vc,ve,vegas,ventures,verisign,versicherung,vet,vg,vi,viajes,video,vig,viking,villas,vin,vip,virgin,vision,vista,vistaprint,viva,vlaanderen,vn,vodka,volkswagen,vote,voting,voto,voyage,vu,vuelos,wales,walter,wang,wanggou,warman,watch,watches,weather,weatherchannel,webcam,weber,website,wed,wedding,weibo,weir,wf,whoswho,wien,wiki,williamhill,win,windows,wine,wme,wolterskluwer,work,works,world,ws,wtc,wtf,xbox,xerox,xihuan,xin,xn--11b4c3d,xn--1ck2e1b,xn--1qqw23a,xn--30rr7y,xn--3bst00m,xn--3ds443g,xn--3e0b707e,xn--3pxu8k,xn--42c2d9a,xn--45brj9c,xn--45q11c,xn--4gbrim,xn--55qw42g,xn--55qx5d,xn--5tzm5g,xn--6frz82g,xn--6qq986b3xl,xn--80adxhks,xn--80ao21a,xn--80asehdb,xn--80aswg,xn--8y0a063a,xn--90a3ac,xn--90ais,xn--9dbq2a,xn--9et52u,xn--9krt00a,xn--b4w605ferd,xn--bck1b9a5dre4c,xn--c1avg,xn--c2br7g,xn--cck2b3b,xn--cg4bki,xn--clchc0ea0b2g2a9gcd,xn--czr694b,xn--czrs0t,xn--czru2d,xn--d1acj3b,xn--d1alf,xn--e1a4c,xn--eckvdtc9d,xn--efvy88h,xn--estv75g,xn--fct429k,xn--fhbei,xn--fiq228c5hs,xn--fiq64b,xn--fiqs8s,xn--fiqz9s,xn--fjq720a,xn--flw351e,xn--fpcrj9c3d,xn--fzc2c9e2c,xn--fzys8d69uvgm,xn--g2xx48c,xn--gckr3f0f,xn--gecrj9c,xn--h2brj9c,xn--hxt814e,xn--i1b6b1a6a2e,xn--imr513n,xn--io0a7i,xn--j1aef,xn--j1amh,xn--j6w193g,xn--jlq61u9w7b,xn--jvr189m,xn--kcrx77d1x4a,xn--kprw13d,xn--kpry57d,xn--kpu716f,xn--kput3i,xn--l1acc,xn--lgbbat1ad8j,xn--mgb9awbf,xn--mgba3a3ejt,xn--mgba3a4f16a,xn--mgba7c0bbn0a,xn--mgbaam7a8h,xn--mgbab2bd,xn--mgbayh7gpa,xn--mgbb9fbpob,xn--mgbbh1a71e,xn--mgbc0a9azcg,xn--mgbca7dzdo,xn--mgberp4a5d4ar,xn--mgbpl2fh,xn--mgbt3dhd,xn--mgbtx2b,xn--mgbx4cd0ab,xn--mix891f,xn--mk1bu44c,xn--mxtq1m,xn--ngbc5azd,xn--ngbe9e0a,xn--node,xn--nqv7f,xn--nqv7fs00ema,xn--nyqy26a,xn--o3cw4h,xn--ogbpf8fl,xn--p1acf,xn--p1ai,xn--pbt977c,xn--pgbs0dh,xn--pssy2u,xn--q9jyb4c,xn--qcka1pmc,xn--qxam,xn--rhqv96g,xn--rovu88b,xn--s9brj9c,xn--ses554g,xn--t60b56a,xn--tckwe,xn--unup4y,xn--vermgensberater-ctb,xn--vermgensberatung-pwb,xn--vhquv,xn--vuq861b,xn--w4r85el8fhu5dnra,xn--w4rs40l,xn--wgbh1c,xn--wgbl6a,xn--xhq521b,xn--xkc2al3hye2a,xn--xkc2dl3a5ee0h,xn--y9a3aq,xn--yfro4i67o,xn--ygbi2ammx,xn--zfr164b,xperia,xxx,xyz,yachts,yahoo,yamaxun,yandex,ye,yodobashi,yoga,yokohama,you,youtube,yt,yun,za,zara,zero,zip,zm,zone,zuerich,zw');
		
		foreach($tldList as $entry){
			
			// Add to the tld set:
			$this->tlds[$entry]=true;
			
		}
		
	}
	
	function Formatted($domain,$hint=false){
		
		// Lookup our domain:
		$result = $this->Lookup($domain,$hint);
		
		// Got any results?
		if(!$result){
			return null;
		}

		// Format into something more useful:
		return $this->Format($result);
		
	}
	
	/*
	 *  Lookup query
	 */
	function Lookup($domain, $hint=false)
	{
		
		// Trim the query:
		$domain = trim($domain);
		
		// If domain to query was not set
		if (!isset($domain) || $domain == '')
		{
			return null;
		}
		
		// Lowercase it:
		$domain = strtolower($domain);
		
		// Get the TLD.
		$dp = explode('.', $domain);
		$np = count($dp);
		
		if($np==1){
			// Not valid.
			return null;
		}
		
		// Find it:
		$tld=$dp[$np-1];
		
		if(!isset($this->tlds[$tld])){
			
			// TLD not found.
			return null;
			
		}
		
		$tldSize=1;
		
		// Ok! We've got a valid TLD.
		// We now need to figure out if our domain includes a subdomain.
		// If it does, many whois lookups will fail to return a result.
		
		// So, if it's got 3+ parts then it's possible there's a subdomain.
		if($np>=3){
			
			// 3 or more parts.
			// Subdomain is unlikely if the 2nd part is short (e.g. example.co.uk - the 'co' is 2 chars).
			$secondLast=$dp[$np-2]; // e.g. that 'co'
			
			if( strlen($secondLast)<=3 && !$hint){
				
				// Short 2nd from last part. Very likely that it's part of the domain. Add it to TLD:
				$tldSize++;
				
			}
			
			$domainSize=($tldSize+1);
			
			if($np>$domainSize){
				
				// There's subdomains.
				// Now need to chop them off.
				$domain=implode('.',array_slice($dp,-$domainSize));
				
			}
			
		}
		
		// Next we'll use the following server DNS:
		$server=$tld.'.whois-servers.net';
		
		// We'll lookup $domain at $server:
		$results=$this->GetRawData($domain,$server);
		
		if($results==null){
			return null;
		}
		
		// The results might not actually contain the whois data.
		// It can contain a whois server address in it - find that:
		$whoisAddress=$this->GetWhoisServer($results);
		
		if($whoisAddress){
			
			// Get from it instead:
			$results=$this->GetRawData($domain,$whoisAddress);
			
		}
		
		return $results;
		
	}
	
	function GetWhoisServer($results){
		
		// Look out for a line starting with 'Whois Server:'.
		$lineCount=count($results);
		$address='';
		
		for($i=0;$i<$lineCount;$i++){
			
			// Get the line:
			$line=$results[$i];
			
			if(strstr($line,'Whois Server:')){
				
				// Check that server and return its data instead.
				// Address is either after the colon or on the following line.
				$pieces=explode(':',$line);
				
				if(count($pieces>1)){
					
					$address=trim($pieces[1]);
					
				}
				
				if($address==''){
					
					// Use the next line instead:
					$address=trim($results[$i+1]);
					
					if($address!=''){
						break;
					}
					
				}
				
			}
			
		}
		
		return $address;
		
	}
	
	function Format($results){
		
		$set=array();
		$currentIndex='';
		$indexCount=0;
		
		foreach($results as $result){
			
			// Split at the colon:
			$parts=explode(':',$result,2);
			
			// Get the lowercase index:
			$index=trim( $parts[0] );
			
			if($index=='' || substr($index,0,2)=='%%'){
				// Comment. Ignore it.
				continue;
			}
			
			if(count($parts)==2){
				
				// If it starts with >>>, break.
				if(substr($index,0,3)=='>>>'){
					break;
				}
				
				// Get the trimmed data:
				$data=trim($parts[1]);
				
				// Lowercase the index:
				$index=strtolower($index);
				
				if($data==''){
					// The data is actually on the following line(s).
					$currentIndex=$index;
					$indexCount=0;
					
				}else{
					
					// Add it to the set:
					$set[$index]=$data;
					
				}
				
			}else if($currentIndex!=''){
				
				// In this instance, $index is actually our data.
				// This happens when we have 'Some Field:' then the data on following lines.
				// 'Some Field' is currentIndex, indexCount is the current line
				// index is the current lines data.
				
				if($indexCount==0){
					// Assume single line:
					$set[$currentIndex]=$index;
				}else if($indexCount==1){
					// Convert to array:
					$set[$currentIndex]=array($set[$currentIndex],$index);
				}else{
					// Push into the array:
					array_push($set[$currentIndex],$index);
				}
				
				$indexCount++;
				
			}
			
		}
		
		return $set;
		
	}
	
	function GetRawData ($domain,$server) {
		
		$port=43;
		
		// Connect to whois server, or return if failed
		$errno;
		$errstr;
		$ptr = @fsockopen($server, $port, $errno, $errstr,3);
		
		if($ptr < 0){
			return null;
		}
		
		stream_set_timeout($ptr,3);
		stream_set_blocking($ptr,0);
			
		// Send query
		fputs($ptr,$domain."\r\n");
		
		// Prepare to receive result
		$raw = '';
		$start = time();
		$null = NULL;
		$r = array($ptr);

		while (!feof($ptr))
		{
			$raw.=fgets($ptr,512);
		}
		
		fclose($ptr);
		$output = explode("\n", $raw);
		
		return $output;
	}
	
}

?>
