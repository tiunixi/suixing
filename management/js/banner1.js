   var bannerAD=new Array();
   var bannerADlink=new Array();
   var adNum=0;

   bannerAD[0]="../img/01.png";
   bannerADlink[0]="";
   bannerAD[1]="../img/01.png";
   bannerADlink[1]="";
   bannerAD[2]="../img/01.png";
   bannerADlink[2]="";
   bannerAD[3]="../img/2.png";
   bannerADlink[3]="";
   bannerAD[4]="../img/3.png";
   bannerADlink[4]="";
   bannerAD[5]="../img/4.png";
   bannerADlink[5]="";
   
   var preloadedimages=new Array();
   for (i=1;i<bannerAD.length;i++){
      preloadedimages[i]=new Image();
      preloadedimages[i].src=bannerAD[i];
   }

function setTransition(){
	var m = 12;
   if (document.all){
      bannerADrotator.filters.revealTrans.Transition=m;
      bannerADrotator.filters.revealTrans.apply();
   }
   
}

function playTransition(){
   if (document.all)
      bannerADrotator.filters.revealTrans.play()
}

function nextAd(){
   if(adNum<bannerAD.length-1)adNum++ ;
      else adNum=0;
   setTransition();
   document.images.bannerADrotator.src=bannerAD[adNum];
   playTransition();
   theTimer=setTimeout("nextAd()", 5000);
}

function jump2url(){
   jumpUrl=bannerADlink[adNum];
   jumpTarget='';
   if (jumpUrl != ''){
      if (jumpTarget != '')window.open(jumpUrl,jumpTarget);
      else location.href=jumpUrl;
   }
}
function displayStatusMsg() { 
   status=bannerADlink[adNum];
   document.returnValue = true;
}