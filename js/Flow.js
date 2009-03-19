/***********************************************************************/
/*         Nova Lib de Janelas  --> Window Digirati Versao 2           */
/*                                                                     */
/*                     Usar em conjunto com:                           */
/*  - dragNdrop.js (http://www.ultramail.com.br/lib/js/dragNdrop.js)   */
/*  - basico2.js (http://www.ultramail.com.br/lib/js/basico2.js)       */
/*  - xhrLib.js (http://www.ultramail.com.br/lib/js/xhrLib.js)         */
/*                                                                     */
/* Documentado em http://mario.prv.digirati.com.br/JS/Windig2/doc.txt  */
/*                                                                     */
/***********************************************************************/
/*             Mario Vitor -> mario[em]digirati.com.br                 */
/*           http://mario.prv.digirati.com.br/JS/Windig2               */
/*        http://mario.prv.digirati.com.br/JS/Windig2/doc.txt          */
/***********************************************************************/

var Flow={

'MaxFunc': new Array(),
'MinFunc': new Array(),
'ClsFunc': new Array(),
'RestFunc': new Array(),
'ConfirmFunc': new Array(),
'PromptFunc': new Array(),
'PathImages':'images/',

'Objects':{
	'WindigBase':new Array(),
	'TBWindig':new Array(),
	'Windig':new Array(),
	'ResV1':new Array(),
	'ResV2':new Array(),
	'ResH1':new Array(),
	'ResH2':new Array(),
	'ResHV':new Array()
},

'outline':new Array(),

'newID':-1,

'WindowBKP':new Array(),

'open':function(tParams){

	var Validate = this.ValidateParams(tParams) ;
	if( !Validate ){
		return false;
	} else {
		var WindowIDReturn = this.NewFlow(tParams);
	}

	return WindowIDReturn;

},

'ValidateParams':function(tParams){

	for (var i in tParams){
		if( i == "TB" && (tParams[i] != true && tParams[i] != false)  ) tParams[i] = true;

		else if( i == "definition" && ( tParams[i].toLowerCase() != "url" && tParams[i].toLowerCase() != "response") ){
			tParams[i] = 'response';
		}

		else if( (i == "width" || i == "height") && ( !tParams[i] || tParams[i] < 100 || typeof(tParams[i]) != 'number' ) ) {
			tParams[i] = 100;
		}

		else if( (i == "x" || i == "y") && ( tParams[i] === "" || typeof(tParams[i]) != 'number' ) ) {
			tParams[i] = 0;
		}

	}

	return true;

},

'NewFlow':function(tParams){

	var NewID = this.MakeID();
	var WindigBase = createElement('div',{'id':'WindigBase'+NewID, 'class':'WindigBase', 'onclick':'Flow.Focus('+NewID+');'},'');

	var body = gTN('body')[0];
	body.appendChild(WindigBase);

	setStyle( WindigBase, {
		'left':tParams['x'] + 'px',
		'top':tParams['y'] + 'px',
		'width':tParams['width'] + 'px',
		'height':tParams['height'] + 'px',
		'zIndex':1000+NewID
	});

	this.Objects.WindigBase[NewID] = WindigBase;
	this.CriateResizers(WindigBase, NewID, tParams);

	if(tParams['TB'] == true){
		this.CriateTitleBar(WindigBase, NewID, tParams);
	}

	this.CriateWindow(WindigBase, NewID, tParams);
	
	if( tParams.EventFuncs.length != 0 ) {
		this.loadEventFunctions( tParams.EventFuncs, NewID );
	}

	return NewID;

},

'loadEventFunctions': function( eventObj, NewID ){
	if( eventObj['Max'] != undefined && eventObj['Max'] != "" ) this.MaxFunc[NewID] = eventObj['Max'];
	if( eventObj['Min'] != undefined && eventObj['Min'] != "" ) this.MinFunc[NewID] = eventObj['Min'];
	if( eventObj['Rest'] != undefined && eventObj['Rest'] != "" ) this.RestFunc[NewID] = eventObj['Rest'];
	if( eventObj['Close'] != undefined && eventObj['Close'] != "" ) this.ClsFunc[NewID] = eventObj['Close'];
	if( eventObj['Confirm'] != undefined && eventObj['Confirm'] != "" ) this.ConfirmFunc[NewID] = eventObj['Confirm'];
	if( eventObj['Prompt'] != undefined && eventObj['Prompt'] != "" ) this.PromptFunc[NewID] = eventObj['Prompt'];
},

'CriateTitleBar':function(WindigBase, NewID, tParams){

	var TitleBar = createElement('div', {'id':'TBWindig' + NewID, 'class':'TitleBar'},'');
	WindigBase.appendChild(TitleBar);
	this.Objects.TBWindig[NewID] = TitleBar;

	var IMG = "";
	switch( tParams['Window'] ){
		case "default":
			if(tParams['TBStyle'].Image != "" && tParams['TBStyle'].Image != undefined)
				IMG = "<img src='"+ this.PathImages + tParams['TBStyle'].Image +"' border='0' class='img'>";
		break;
		
		default:
			IMG = "<img src='"+ this.PathImages + 'flow_' + tParams['Window']+".gif' border='0' class='img'>";
		break;
	}
	
	var inner =  "<span class='img'>"+IMG+"</span>";
	inner += "<span id='cap"+ NewID +"' class='cap' onclick=\"if(this.nextSibling.className=='minres'){Flow.Minimize("+NewID+")}\">" + tParams['TBStyle'].Caption + "</span>";
	inner += "<span id='min"+ NewID +"' class='min' onclick=Flow.Minimize(" + NewID + ")></span>";
	inner += "<span id='max"+ NewID +"' class='max' onclick=Flow.Maximize(" + NewID + ")></span>";
	inner += "<span id='fec"+ NewID +"' class='fec' onclick=Flow.Close(" + NewID + ")></span>";

	appendHTML(inner, TitleBar);

	if(tParams['TBStyle'].BackgroundColor != undefined && tParams['TBStyle'].BackgroundColor != "") {
		setStyle(TitleBar, {'backgroundColor':tParams['TBStyle'].BackgroundColor} );
		setStyle(WindigBase, {'borderColor': tParams['TBStyle'].BackgroundColor});
	} else {
		setStyle(TitleBar, {'background': "url('" + this.PathImages + "flow_top.png') repeat-x"});
		setStyle( this.Objects.TBWindig[NewID], {'backgroundColor':'gray'} );
	}

	if(tParams['TBStyle'].Color != undefined && tParams['TBStyle'].Color != "")
	setStyle(TitleBar, {'color':tParams['TBStyle'].Color});

	if(tParams['TBStyle'].Font != undefined && tParams['TBStyle'].Font != ""){
		setStyle(TitleBar, {'font':tParams['TBStyle'].Font});
		setStyle(TitleBar, {'lineHeight':'30px'});
	}

	this.outline['WindigBase'+NewID] = new DragNdrop(WindigBase, {'handle':Flow.Objects.TBWindig[NewID],'type':'outline', 'onStart':this.LimitFunc,'onFinish': this.CallBack  });

},

'CriateWindow':function(WindigBase, NewID, tParams) {

	var Window = createElement('div', {'id':'Windig' + NewID, 'class':'Janela'},'');

	WindigBase.appendChild(Window);

	setStyle(Window, {'height': parseInt(WindigBase.style.height) - 32 + 'px'});
	if( tParams['WindowStyle'] && tParams['WindowStyle'].BackgroundImage != "" && tParams['WindowStyle'].BackgroundImage != undefined)
		setStyle(Window, {'background': "url('"+tParams['WindowStyle'].BackgroundImage+"')"});
	else if( tParams['WindowStyle'] && tParams['WindowStyle'].BackgroundColor != "" && tParams['WindowStyle'].BackgroundImage != undefined)
		setStyle(Window, {'background': tParams['WindowStyle'].BackgroundColor});

	var OptWindow = tParams['Window'].toLowerCase();

	if(OptWindow == 'default' || OptWindow == "" || OptWindow == undefined) { //CRIANDO JANELA COMUM
		if( tParams['definition'].toLowerCase() == 'url') {//OU e URL
			this.doIframe(tParams['innerHTML'], Window, NewID);
		}
		else if( tParams['definition'].toLowerCase() == 'response'){ //Ou e conteudo ja tratado (ResponseText, ResponseXML)
			Window.innerHTML = tParams['innerHTML'];
			setStyle(Window, {'overflow': 'auto'});
		}
	}

	else if(OptWindow == 'error' || OptWindow == 'information' || OptWindow == 'alert') { //CRIANDO JANELA ESPECIAL DE ERRO ou INFO

		var inner =  "<p class='Msg'>";
				inner +=   tParams['WindowStyle'].Caption;
				inner += "</p>";
				inner += "<p class='Msg'> "+tParams['innerHTML']+" </p>";
				inner += "<p class='Msg'>";
				inner +=    "<input type='button' value='OK' onclick='Flow.Close("+NewID+");'>";
				inner += "</p>";

		Window.innerHTML = inner;

	}

	else {

		var inner =  "<p class='Msg'>";
				inner +=   tParams['WindowStyle'].Caption;
				inner += "</p>";
		if(OptWindow == "confirm"){
			inner += "<p class='Msg'> "+tParams['innerHTML']+" </p>";
			inner += "<p class='Msg'>";
			inner +=    "<input type='button' id='sim' value='Sim' onclick='Flow.ConfirmFunc["+NewID+"](1,"+NewID+");Flow.Close("+NewID+");'>";
			inner +=    "<input type='button' id='nao' value='N&atilde;o' onclick='Flow.ConfirmFunc["+NewID+"](0,"+NewID+");Flow.Close("+NewID+");'>";
			inner += "</p>";
			Window.innerHTML = inner;
			gID('nao').focus();
		} else {
			inner += "<form name='prompt"+NewID+"' id='prompt"+NewID+"' class='Prompt' onsubmit='prompt"+NewID+".ok.onclick();return false;'>";
			inner +=     "<input type='text' id='abcd' class='inputCombo' name='abcd' value='"+tParams['innerHTML']+"'>";
			inner +=     "<p class='Msg'>";
			inner +=       "<input type='button' name='ok' value='OK' onclick='Flow.PromptFunc["+NewID+"](prompt"+NewID+".abcd.value,"+NewID+");Flow.Close("+NewID+");'>";
			inner +=     "</p>";
			inner += "</form>";
			Window.innerHTML = inner;
			gID('abcd').focus();
		}

	}

	this.Objects.Windig[NewID] = Window;

},

'CriateResizers':function(WindigBase, NewID, tParams){

	var ResH1 = createElement('div', {'id':'ResH1' + NewID, 'class':'ResH1'},'');
	var ResH2 = createElement('div', {'id':'ResH2' + NewID, 'class':'ResH2'},'');

	WindigBase.appendChild(ResH1);WindigBase.appendChild(ResH2);

	with(this){ Objects.ResH1[NewID] = ResH1;Objects.ResH2[NewID] = ResH2; }
	this.outline['ResH'+NewID] = new DragNdrop(ResH2, {'handle': ResH2,'type':'ghost', 'vMove': true, 'hMove':false, 'onFinish': this.CallBack, 'onStart':this.LimitFunc, 'onDrag': this.Resizing  });

	var ResV1 = createElement('div', {'id':'ResV1' + NewID, 'class':'ResV1'},'');
	var ResV2 = createElement('div', {'id':'ResV2' + NewID, 'class':'ResV2'},'');

	WindigBase.appendChild(ResV1);WindigBase.appendChild(ResV2);
	with(this){
		Objects.ResV1[NewID] = ResV1;Objects.ResH2[NewID] = ResH2;
		Objects.ResV2[NewID] = ResV2;Objects.ResH1[NewID] = ResH1;
	}
	if(tParams['TBStyle'].BackgroundColor != undefined && tParams['TBStyle'].BackgroundColor != "") {
		setStyle( [ ResV1, ResV2, ResH1, ResH2 ], {'borderColor':tParams['TBStyle'].BackgroundColor} );
	}
	this.outline['ResV'+NewID] = new DragNdrop(ResV2, {'handle': ResV2,'type':'ghost', 'vMove': false, 'hMove':true, 'onFinish': this.CallBack, 'onStart':this.LimitFunc, 'onDrag': this.Resizing  });

	var ResHV = createElement('div', {'id':'ResHV' + NewID, 'class':'ResHV'},'');

	WindigBase.appendChild(ResHV);
	with(this){ Objects.ResHV[NewID] = ResHV;  }
	this.outline['ResHV'+NewID] = new DragNdrop(ResHV, {'handle': ResHV,'type':'ghost', 'vMove': true, 'hMove': true, 'onFinish': this.CallBack, 'onStart':this.LimitFunc, 'onDrag': this.Resizing  });

},

'Resizing':function(Obj){

	var x = Obj.x;	var y = Obj.y;
	Obj = Obj.element;

	var ID = Obj.id.substr(5);
	var TotalHeight = /*parseInt(Flow.Objects.WindigBase[ID].style.top) + */parseInt(Flow.Objects.WindigBase[ID].style.height) - 5;
	var TotalWidth = /*parseInt(Flow.Objects.WindigBase[ID].style.left) +*/ parseInt(Flow.Objects.WindigBase[ID].style.width);

	if(Obj.id.substr(0,5) == "ResH2"){
		setStyle(Flow.Objects.ResH2[ID],{'visibility':'hidden'});
		var NewHeight = y + 'px';
		setStyle([ Flow.Objects.ResV1[ID], Flow.Objects.ResV2[ID] ], {'height':NewHeight});
	}

	else if(Obj.id.substr(0,5) == "ResV2") {
		setStyle(Flow.Objects.ResV2[ID], {'visibility':'visibleo', 'height':'100%'});
		var NewWidth = x + 'px';
		setStyle([ Flow.Objects.ResH1[ID], Flow.Objects.ResH2[ID] ], {'width':NewWidth});
	}

	else if(Obj.id.substr(0,5) == "ResHV") {

		setStyle([Flow.Objects.ResH1[ID],Flow.Objects.ResH2[ID]],{ 'width': x + 'px'});
		setStyle([Flow.Objects.ResV1[ID],Flow.Objects.ResV2[ID]],{ 'height': y + 'px'});
		setStyle(Flow.Objects.ResH2[ID], {'bottom': parseInt( TotalHeight - y )  +'px'});
		setStyle(Flow.Objects.ResV2[ID], {'right': parseInt( TotalWidth - x ) +'px'});

	}

},

'LimitFunc':function(e){
	e = e.element;
	if(e.id.substr(0, 10) == 'WindigBase'){	return;	}
	var ID = e.id.substr(5); var Nome = e.id.substr(0, 5);

	if( Nome == "ResHV" ){
		setStyle( [Flow.Objects.ResH1[ID], Flow.Objects.ResH2[ID], Flow.Objects.ResV1[ID], Flow.Objects.ResV2[ID]], {
		'borderStyle':'dashed', 'borderWidth':'1px'
		} );
		setStyle( Flow.Objects.ResV2[ID], {'borderLeft':'none', 'borderTop': 'none', 'borderBottom':'none'} );
		setStyle( Flow.Objects.ResH2[ID], {'borderLeft':'none', 'borderRight': 'none', 'borderTop':'none'} );
		setStyle( Flow.Objects.ResV1[ID], {'borderRight':'none', 'borderTop':'none', 'borderBottom':'none'} );
		setStyle( Flow.Objects.ResH1[ID], {'borderBottom':'none', 'borderRight':'none', 'borderLeft':'none'} );
		if(Flow.Objects.TBWindig[ID]){
			setStyle( [Flow.Objects.ResH1[ID], Flow.Objects.ResH2[ID], Flow.Objects.ResV1[ID], Flow.Objects.ResV2[ID]], {
			'borderColor':Flow.Objects.TBWindig[ID].style.backgroundColor
			});
		}
		Flow.outline['ResHV'+ID].options.limits = {'yMin':100, 'xMin':100};
	}

	else if(Nome.substr(0,4) == "ResH"){

		setStyle([ Flow.Objects.ResV1[ID], Flow.Objects.ResV2[ID] ],{
		'borderStyle':'dashed',	'borderWidth':'1px', 'borderBottom': 'none',
		});
		if(Flow.Objects.TBWindig[ID])
		setStyle([ Flow.Objects.ResV1[ID], Flow.Objects.ResV2[ID] ],{
		'borderColor':Flow.Objects.TBWindig[ID].style.backgroundColor
		});

		setStyle(Flow.Objects.ResV1[ID],{'borderRight': 'none'});
		setStyle(Flow.Objects.ResV2[ID],{'borderLeft': 'none'});

		Flow.outline['ResH'+ID].options.limits = {'yMin':100};

	}

	else if(Nome.substr(0,4) == "ResV") {

		setStyle([ Flow.Objects.ResH1[ID], Flow.Objects.ResH2[ID] ],{
		'borderStyle':'dashed', 'borderWidth':'1px',
		'borderRight': 'none',	'borderLeft': 'none'
		});
		if(Flow.Objects.TBWindig[ID])
		setStyle([ Flow.Objects.ResH1[ID], Flow.Objects.ResH2[ID] ],{
		'borderColor':Flow.Objects.TBWindig[ID].style.backgroundColor
		});

		setStyle(Flow.Objects.ResH1[ID], {'borderBottom':'none'});
		setStyle(Flow.Objects.ResH2[ID], {'borderTop':'none'});

		Flow.outline['ResV'+ID].options.limits = {'xMin':100};

	}

},

'MakeID':function(){
  Flow.newID = Flow.newID + 2;
	return Flow.newID;
},

'doIframe':function(url, Window, NewID){
	Window.innerHTML = "<iframe width='105%' height='105%' frameborder='0' src='"+url+"'></iframe>";
},

'Focus':function(JanelaID){

	var Janelas = this.Objects.WindigBase;

	if (Janelas[JanelaID]) {
		for(var i = 0; i <= Janelas.length-1; i++) {
			if( Janelas[i] && (Janelas[i].style.zIndex >= Janelas[JanelaID].style.zIndex) ) {
				Janelas[JanelaID].style.zIndex = parseInt(Janelas[i].style.zIndex) + 1;
			}
		}
	}

},

'CallBack': function(a){

	if (a['element'].id.substr(0,10) == "WindigBase") {
		Flow.Focus( a['element'].id.substr(10) );
		return;
	} else {
		var ID = a['element'].id.substr(5);
		if ( a['element'].id.substr(0, 5) == "ResHV" ) {
			setStyle(Flow.Objects.ResHV[ID], {
			'visibility':'visible', 'top':'', 'left':'',
			'right':'0px', 'bottom':'0px'
			});
			setStyle( Flow.Objects.ResV1[ID], {
			'borderStyle':'ridge', 'borderWidth':'2px',
			'height':'100%',  'borderRight':'none',
			'borderTop':'none', 'borderBottom':'none'
			});
			setStyle( Flow.Objects.ResV2[ID], {
			'borderStyle':'ridge', 'borderWidth':'2px',
			'height':'100%', 'borderLeft':'none',
			'borderTop':'none', 'borderBottom':'none',
			'right': '0px'
			});
			setStyle( Flow.Objects.ResH1[ID], {
			'borderStyle':'ridge', 'borderWidth':'2px',
			'width':'100%', 'borderRight':'none',
			'borderBottom':'none', 'borderLeft':'none'
			});
			setStyle( Flow.Objects.ResH2[ID], {
			'borderStyle':'ridge', 'borderWidth':'2px',
			'width':'100%', 'borderLeft':'none',
			'borderRight':'none', 'borderTop':'none',
			'bottom':'0px'
			});
			setStyle( Flow.Objects.WindigBase[ID], {
			'height': a.y + 4 + 'px',
			'width': a.x + 4 + 'px'
			});
			setStyle( Flow.Objects.Windig[ID], {
				'height': a.y - 26 + 'px'
			});

		}
		else if( a['element'].id.substr(0, 4) == "ResH" ){

			setStyle(Flow.Objects.ResH2[ID], {'visibility':'visible', 'top':'', 'bottom':'0px'});
			setStyle(Flow.Objects.ResV1[ID], {
			'borderStyle': 'ridge', 'borderWidth': '2px',
			'height': '100%', 'borderRight': 'none',
			'borderTop': 'none', 'borderBottom': 'none'
			});
			setStyle(Flow.Objects.ResV2[ID], {
			'borderStyle': 'ridge', 'borderWidth': '2px',
			'height': '100%', 'borderLeft': 'none',
			'borderTop': 'none', 'borderBottom': 'none'
			});
			setStyle( Flow.Objects.WindigBase[ID], {'height': (a.y)+4+'px'});
			setStyle( Flow.Objects.Windig[ID], {'height': (a.y)-26+'px'});

		}

		else if ( a['element'].id.substr(0, 4) == "ResV" ){

			setStyle(Flow.Objects.ResV2[ID], {'visibility':'visible', 'left':'', 'right':'0px'});
			setStyle(Flow.Objects.ResH1[ID], {
			'borderStyle': 'ridge', 'borderWidth': '2px',
			'width': '100%', 'borderRight': 'none',
			'borderLeft': 'none', 'borderBottom': 'none'
			});
			setStyle(Flow.Objects.ResH2[ID], {
			'borderStyle': 'ridge', 'borderWidth': '2px',
			'width': '100%', 'borderRight': 'none',
			'borderLeft': 'none', 'borderTop': 'none'
			});
			setStyle( Flow.Objects.WindigBase[ID],{'width': a.x + 4 + 'px'});

		}

	}

},

'MergeMinPos':function(ID){

	var ArrObj = this.Objects.WindigBase;

	for(var i = 0; i <= ArrObj.length-1; i++) {
		if( ArrObj[i] && ArrObj[i].childNodes[5].childNodes[2].className == "minres" && (parseInt(ArrObj[i].style.bottom) > parseInt(ArrObj[ID].style.bottom) ) )
		setStyle(ArrObj[i], {'bottom':parseInt(ArrObj[i].style.bottom) - 30 + "px"});
	}

},

'Minimize':function(TBJanelaID){
	var WinHeight = (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight);
	var WinWidth = (typeof window.innerWidth != 'undefined' ? window.innerWidth : document.body.offsetWidth);
	var MIN = gID('min'+TBJanelaID);var MAX = gID('max'+TBJanelaID); var NewBottom = 0;
	var ArrObj = this.Objects.WindigBase; var Obj = ArrObj[TBJanelaID]; var Limit = ArrObj.length;

	if(MIN.className == "min"){

		if(MAX.className == "max") this.SavePos(Obj, TBJanelaID);

		if(Limit != 0){
			for(var i = 0; i <= ArrObj.length-1; i++)
			if( ArrObj[i] && ArrObj[i].childNodes[5].childNodes[2].className == "minres" )
			NewBottom += 30;
		}

		setStyle( [ this.Objects.ResH2[TBJanelaID], this.Objects.ResV2[TBJanelaID], this.Objects.ResHV[TBJanelaID] ], {'visibility':'hidden'});
		setStyle(this.Objects.Windig[TBJanelaID], {
		'height':'0px', 'visibility':'hidden'
		});
		setStyle(this.Objects.WindigBase[TBJanelaID],{
		'width':'200px', 'height':'30px',
		'bottom':NewBottom+'px', 'top':'auto',
		'left':'auto', 'right':'0pt',
		'position':'fixed'
		} );

		MIN.className = "minres"; MAX.className = "max";

		if( this.MinFunc[TBJanelaID] != null )  this.MinFunc[TBJanelaID](TBJanelaID);

	} else {
		setStyle(this.Objects.WindigBase[TBJanelaID], {
		'width':this.WindowBKP[TBJanelaID].width,
		'height': this.WindowBKP[TBJanelaID].height,
		'position': 'absolute',
		'left': this.WindowBKP[TBJanelaID].left,
		'top': this.WindowBKP[TBJanelaID].top
		});
		setStyle(this.Objects.Windig[TBJanelaID],{
		'height':parseInt(this.WindowBKP[TBJanelaID].height) - 32 + 'px',
		'visibility': 'visible'
		});
		setStyle( [ this.Objects.ResH2[TBJanelaID], this.Objects.ResV2[TBJanelaID], this.Objects.ResHV[TBJanelaID] ], {'visibility':'visible'});
		this.MergeMinPos(TBJanelaID);

		MIN.className = "min"; MAX.className = "max"; this.Focus(TBJanelaID);

		if( this.RestFunc[TBJanelaID] != null ) this.RestFunc[TBJanelaID](TBJanelaID);

	}

},

'Maximize':function(TBJanelaID){

	var WinHeight = (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight);
	var WinWidth = (typeof window.innerWidth != 'undefined' ? window.innerWidth : document.body.offsetWidth);
	var MAX = gID('max' + TBJanelaID);  var MIN = gID('min' + TBJanelaID);
	var WindigBase = this.Objects.WindigBase[TBJanelaID];

	if(MAX.className == "max"){

		if(MIN.className != "minres")  this.SavePos(WindigBase, TBJanelaID);

		MAX.className = "res"; MIN.className = "min";

		this.MergeMinPos(TBJanelaID);
		setStyle(this.Objects.WindigBase[TBJanelaID],{
		'width':'100%', 'height':'100%', 'position':'fixed',
		'top':'0px',	'left':'0px'
		});
		setStyle(this.Objects.Windig[TBJanelaID],{
		'height':WinHeight - 32 + 'px',	'width':'100%', 'visibility':'visible'
		});
		setStyle([this.Objects.ResH2[TBJanelaID],this.Objects.ResV2[TBJanelaID]],{'visibility':'visible'} );
		this.Focus(TBJanelaID);

		if( this.MaxFunc[TBJanelaID] != null )  this.MaxFunc[TBJanelaID](TBJanelaID, WinWidth, WinHeight);

	} else {

		MAX.className = "max";

		setStyle(this.Objects.WindigBase[TBJanelaID],{
		'width': this.WindowBKP[TBJanelaID].width,
		'height': this.WindowBKP[TBJanelaID].height,
		'position': 'absolute',
		'top': this.WindowBKP[TBJanelaID].top,
		'left': this.WindowBKP[TBJanelaID].left
		});
		setStyle(this.Objects.Windig[TBJanelaID],{
		'height': parseInt(WindigBase.style.height) - 32 + 'px',
		'width': '99%',
		});
		setStyle([ this.Objects.ResH2[TBJanelaID],this.Objects.ResV2[TBJanelaID] ], { 'visibility':'visible' } );

		if( this.RestFunc[TBJanelaID] != null )  this.RestFunc[TBJanelaID]( TBJanelaID );

	}

},

'Close':function(JanelaID){

	if( this.ClsFunc[JanelaID] != null )  this.ClsFunc[JanelaID](JanelaID);

	if( (this.Objects.TBWindig[JanelaID]) && this.Objects.TBWindig[JanelaID].childNodes[2].className == "minres" )
	this.MergeMinPos(JanelaID);

	var Obj = this.Objects.WindigBase[JanelaID];
	Obj.parentNode.removeChild(Obj);

	if (this.Objects.WindigBase[JanelaID]) {
		delete(this.Objects.WindigBase[JanelaID]);
		delete(this.Objects.TBWindig[JanelaID]);
		delete(this.Objects.Windig[JanelaID]);
		delete(this.Objects.ResH1[JanelaID]);
		delete(this.Objects.ResH2[JanelaID]);
		delete(this.Objects.ResV1[JanelaID]);
		delete(this.Objects.ResV2[JanelaID]);
		delete(this.Objects.ResHV[JanelaID]);
	}

},

'SavePos':function(Jan, ID){

	this.WindowBKP[ID] = { 'left':   Jan.style.left,
	'top':    Jan.style.top,
	'height': Jan.style.height,
	'width':  Jan.style.width
	};

}

};