function MakeRequest(id,url) {
	var myRequest = new Request({
		url: url,
	    method: 'get',
		onFailure: function(xhr) {
			alert('Error occured for url:'+url);
		},
		onComplete: function(response) {
	    	HandleResponse(id,response);	    		
		}
	}).send();
}

function HandleResponse(id,response) {
	$(id).innerHTML = response;
}

////////////////////////
// show pop up for mouse over
//
function ShowAjaxPopup(hoverid, popupid, url) {
	if (!jte) var jte = window.event;
	var browser=navigator.appName;
	
	hEl = document.getElementById(hoverid);
	oEl = document.getElementById(popupid);
	
	// Set popup to visible
	if(isClassInElement(oEl, 'jt-ajax')) {
		swapClassInElement(oEl,'jt-ajax','jt-show');
		MakeRequest(popupid,url);
	}
	if(isClassInElement(oEl, 'jt-hide')) {
		swapClassInElement(oEl,'jt-hide','jt-show');
	}

	basetop  = 0;
	baseleft = 0;
	jttop    = hEl.offsetTop;
	jtleft   = hEl.offsetLeft;
	
	// Find out the absolute values for the position
	nextEl   = hEl.offsetParent;
	while (nextEl) {
		jttop  += nextEl.offsetTop;
		jtleft += nextEl.offsetLeft;
		nextEl  = nextEl.offsetParent;
	}

	// Find out the basic values for the element
	nextEl   = oEl.offsetParent;
	while (nextEl) {
		basetop  += nextEl.offsetTop;
		baseleft += nextEl.offsetLeft;
		nextEl    = nextEl.offsetParent;
	}
	
	oEl.style.top  = jttop  - basetop  + 30 + 'px';
	oEl.style.left = jtleft - baseleft - 30 + 'px';
	
}

////////////////////////
//toggle visibility of sources
//
function toggleAjaxSources(bCitElid, lCitElid, url) {
	bCitEl = document.getElementById(bCitElid); 
	lCitEl = document.getElementById(lCitElid); 
	
	if(isClassInElement(bCitEl, 'jt-button-closed')) 
		swapClassInElement(bCitEl,'jt-button-down-open','jt-button-closed');
	if(isClassInElement(lCitEl, 'jt-ajax')) {
		swapClassInElement(lCitEl,'jt-show','jt-ajax');
		MakeRequest(lCitElid,url);
	}
	if(isClassInElement(lCitEl, 'jt-hide')) 
		swapClassInElement(lCitEl,'jt-show','jt-hide');

	// nvd
	// resetFooter();
	// end: nvd
	
	return false;
}

////////////////////////
// toggle visibility of notes and sources
//
function toggleAjaxNotesSources(action, bNotElid, lNotElid, bCitElid, lCitElid, url) {
	bNotEl = document.getElementById(bNotElid); 
	lNotEl = document.getElementById(lNotElid);  
	bCitEl = document.getElementById(bCitElid); 
	lCitEl = document.getElementById(lCitElid); 
	
	if (action == 1) {
		if(isClassInElement(bNotEl, 'jt-button-closed')) 
			swapClassInElement(bNotEl,'jt-button-down-open','jt-button-closed');
		if(isClassInElement(lNotEl, 'jt-ajax')) { 
			swapClassInElement(lNotEl,'jt-show','jt-ajax');
			MakeRequest(lNotElid,url);
		}
		if(isClassInElement(lNotEl, 'jt-hide')) 
			swapClassInElement(lNotEl,'jt-show','jt-hide');
		if(isClassInElement(bCitEl, 'jt-button-down-open')) 
			swapClassInElement(bCitEl,'jt-button-down-open','jt-button-closed');
		if(isClassInElement(lCitEl, 'jt-show')) 
			swapClassInElement(lCitEl,'jt-show','jt-hide');
	} else if (action == 2) {
		if(isClassInElement(bNotEl, 'jt-button-down-open')) 
			swapClassInElement(bNotEl,'jt-button-down-open','jt-button-closed');
		if(isClassInElement(lNotEl, 'jt-show')) 
			swapClassInElement(lNotEl,'jt-show','jt-hide');
		if(isClassInElement(bCitEl, 'jt-button-closed')) 
			swapClassInElement(bCitEl,'jt-button-down-open','jt-button-closed');
		if(isClassInElement(lCitEl, 'jt-ajax')) {
			swapClassInElement(lCitEl,'jt-show','jt-ajax');
			MakeRequest(lCitElid,url);
		}
		if(isClassInElement(lCitEl, 'jt-hide')) 
			swapClassInElement(lCitEl,'jt-show','jt-hide');
	}
	
	// nvd
	// resetFooter();
	// end: nvd
	return false;
}

////////////////////////
// toggle visibility of notes and sources
//
function drilldownAjax(respid, url) {
	oEl = document.getElementById(respid); 
	
	if(isClassInElement(oEl, 'jt-ajax')) {
		oEl.style.left = oEl.style.left + 10 + 'px';
		swapClassInElement(oEl,'jt-show','jt-ajax');
		MakeRequest(respid,url);
	}
	if(isClassInElement(oEl, 'jt-hide')) 
		swapClassInElement(oEl,'jt-show','jt-hide');

	// nvd
	// resetFooter();
	// end: nvd
}


////////////////////////
// toggle visibility of parents
//
function drilldownAjaxParent(butid, respid, url) {
	oEl = document.getElementById(respid); 
	bEl = document.getElementById(butid); 
	
	if(isClassInElement(oEl, 'jt-show') | isClassInElement(oEl, 'jt-hide')) 
		swapClassInElement(oEl,'jt-show','jt-hide');
	if(isClassInElement(oEl, 'jt-ajax')) {
		oEl.style.left = oEl.style.left + 10 + 'px';
		swapClassInElement(oEl,'jt-show','jt-ajax');
		MakeRequest(respid,url);
	}
	if(isClassInElement(bEl, 'jt-button-up-open') | isClassInElement(bEl, 'jt-button-closed')) 
		swapClassInElement(bEl,'jt-button-up-open','jt-button-closed');

	// nvd
	// resetFooter();
	// end: nvd
}


////////////////////////
// toggle visibility of details
//
function drilldownAjaxDetail(butid, respid, url) {
	oEl = document.getElementById(respid); 
	bEl = document.getElementById(butid); 
	
	if(isClassInElement(oEl, 'jt-show') | isClassInElement(oEl, 'jt-hide')) 
		swapClassInElement(oEl,'jt-show','jt-hide');
	if(isClassInElement(oEl, 'jt-ajax')) {
		oEl.style.left = oEl.style.left + 10 + 'px';
		swapClassInElement(oEl,'jt-show','jt-ajax');
		MakeRequest(respid,url);
	}
	if(isClassInElement(bEl, 'jt-button-down-open') | isClassInElement(bEl, 'jt-button-closed')) 
		swapClassInElement(bEl,'jt-button-down-open','jt-button-closed');

	// nvd
	// resetFooter();
	// end: nvd
}

////////////////////////
//retrieve article
//
function retrieveAjaxArticle(url, respid, busysignal) {
	document.getElementById(respid).innerHTML = '<div class="jt-ajax-loader">'+busysignal+'</div>';
	MakeRequest(respid,url);
	
	// nvd
	// resetFooter();
	// end: nvd
	return false;
}

////////////////////////
//retrieve many-years-ago
//
function getManyYearsAgo(dayid, monthid, respid, url) {
	if ((dayid == 0) && (monthid == 0)) {
		url = url + "&day=0&month=0";
		
	} else {
		dEl = document.getElementById(dayid);
		mEl = document.getElementById(monthid);
		
		try {
			//Firefox, Opera 8.0+, Safari, chrome
			jtday = dEl.getValue();
		}
		catch(e) {
			//Internet Explorer
			jtday = dEl.value;
		}
	
		try {
			//Firefox, Opera 8.0+, Safari, chrome
			jtmonth = mEl.getValue();
		}
		catch(e) {
			//Internet Explorer
			jtmonth= mEl.value;
		}
			
		url = url + "&day=" + jtday + "&month=" + jtmonth;
	}
		
	MakeRequest(respid,url);
	
	return false;			
}

