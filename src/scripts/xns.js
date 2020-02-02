var ua = navigator.userAgent.toLowerCase();
var divw=0;
var divh=0;

if (document.getElementById || document.all)
//	document.write('<div id="imgtrailer" style="position:absolute;visibility:hidden;z-index:100;"></div>')
	document.write('<div id="imgtrailer" style="position:absolute;visibility:hidden;z-index:110;border:0px solid #f00;height:100px;"></div>')

function gettrailobject()
	{
	if (document.getElementById)
		return document.getElementById("imgtrailer")
	else if (document.all)
		return document.all.trailimagid
	}

function gettrailobj()
	{
	if (document.getElementById)
		return document.getElementById("imgtrailer").style
	else if (document.all)
		return document.all.trailimagid.style
	}

function truebody()
	{
	return (!window.opera && document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
	}

function hidetrail()
	{
	gettrailobject().innerHTML=" ";
	document.onmousemove='';
	gettrailobj().visibility="hidden";
	}

function trailOn(thumbimg,imgtitle,imgscription,imgsize,filesize,credit,level,thw,thh,flvvid,samplepath,massstab,kartennr)
	{
	//if(ua.indexOf('opera') == -1 && ua.indexOf('safari') == -1)
		//{
		//gettrailobj().left="50px";
		//gettrailobj().top="0px";
		gettrailobj().left="-500px";
		divthw = parseInt(thw) + 2;
		gettrailobject().innerHTML = '<table style="position:relative;z-index:999;background-color:#FFF; layer-background-color:#000000; border:1pt solid black;width:'+divthw+'px;"><tr><td colspan="2" style="border-bottom:solid 1px green;"><img src="'+thumbimg+'" width="'+thw+'" height="'+thh+'" style="border:none;"></td></tr><tbody class="karten"><tr><tr><td style="width:30%;padding-left:10px;padding-top:6px;font-weight:bold;">Name:</td><td style="width:70%;padding-top:6px;">'+imgtitle+'</td></tr><tr><td style="padding-left:10px;font-weight:bold;">Jahr:</td><td>'+imgscription+'</td></tr><tr><td style="padding-left:10px;font-weight:bold;">Massstab:</td><td>'+massstab+'</td></tr><tr><td style="padding-left:10px;padding-bottom:10px;font-weight:bold;">Karten-Nr.:</td><td>'+kartennr+'</td></tr></tbody></table>';
		
		
		gettrailobj().visibility="visible";
		divw = parseInt(thw)+25;
		divh = parseInt(thh)+160;
		document.onmousemove=followmouse;
		//}
	}
function showImage(thumbimg)
	{
		gettrailobj().left="-500px";
		gettrailobject().innerHTML = '<img src="'+thumbimg+'" style="border:1pt solid black;">';
		gettrailobj().visibility="visible";
		document.onmousemove=followmouse;
	}

function followmouse(e)
	{
	var docwidth=document.all? truebody().scrollLeft+truebody().clientWidth : pageXOffset+window.innerWidth-15
	var docheight=document.all? Math.min(truebody().scrollHeight, truebody().clientHeight) : Math.min(document.body.offsetHeight, window.innerHeight)
if(typeof e != "undefined")
	{
	if(docwidth < 15+e.pageX+divw)
		xcoord = e.pageX-divw-5;
	else
		xcoord = 15+e.pageX;
	if(docheight < 15+e.pageY+divh)
		ycoord = 15+e.pageY-Math.max(0,(divh + e.pageY - docheight - truebody().scrollTop - 0));
	else
		ycoord = 15+e.pageY;
	}
else if (typeof window.event != "undefined")
	{
	if(docwidth < 15+truebody().scrollLeft+event.clientX+divw)
		xcoord = truebody().scrollLeft-5+event.clientX-divw;
	else
		xcoord = truebody().scrollLeft+15+event.clientX;

	if(docheight < 15+truebody().scrollTop+event.clientY+divh)
		ycoord = 15+truebody().scrollTop+event.clientY-Math.max(0,(divh + event.clientY - docheight - 30));
	else
		ycoord = truebody().scrollTop+15+event.clientY;
	}
	gettrailobj().left=xcoord+"px"
	gettrailobj().top=ycoord+"px"
	}

