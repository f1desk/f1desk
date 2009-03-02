var Flow=function (tName, tURL, fProps, tbProps, fCalls) {
  if (!fProps) { fProps={}; }
  if (!tbProps) { tbProps={}; }
  if (!fCalls) { fCalls={}; }
  return this.init(tName, tURL, fProps, tbProps, fCalls);
};

var FlowAux={

  'fZindexStart':123456,
  'fixedFlows':[],

  'getFlow': function (idprefix, tName) {
    var gotFlow = gID(idprefix + tName);
    if(gotFlow instanceof Array){
      return gotFlow[0];
    } else {
      return gotFlow;
    }
  },

  'minimize': function (idprefix, fName) {
    var slot = idprefix + fName;  var myFlow = this.getFlow(idprefix, fName);
    var flowContent = gID('CONTENT'+ slot);
    if ( this.fixedFlows[slot].state == "normal" ) {
      var newLeft = window.screen.width - 180 + 'px';
      var newStyle = {
        'left': newLeft, 'top':this.getMinTop() + 'px',
        'width': '180px', 'height':'30px'
      };
      this.saveInfos(idprefix, fName, '', "min");
      setStyle ( myFlow, newStyle );  setStyle ( flowContent, {'visibility':'hidden'} );
      if ( typeof(this.fixedFlows[slot].minimizeCallBack) == 'function' ){
        this.fixedFlows[slot].minimizeCallBack.apply();
      }
    } else {
      setStyle ( flowContent, {'visibility':'visible'} );
      this.maximize(idprefix, fName); // Cai no restaurar
    }
  },

  'getMinTop': function () {
    var finalTop = 0;
    for (var aux in this.fixedFlows) {
      if ( this.fixedFlows[aux].state == "min" ) {
        finalTop += 30;
      }
    }
    return finalTop;
  },

  'maximize': function (idprefix, fName) {
    var newStyle;
    var slot = idprefix + fName;
    var myFlow = this.getFlow(idprefix, fName);
    var flowContent = gID('CONTENT'+ slot);

    if (this.fixedFlows[slot].state == "normal") {  // Maximizar
      this.saveInfos(idprefix, fName, '', "max");
      newStyle = {'top':'0px', 'left':'0px', 'width':'100%', 'height':'100%'};
      setStyle(myFlow, newStyle);  setStyle(flowContent, {'height':myFlow.offsetHeight - 30 + 'px' , 'width':'100%'});
      gID('tbMAX'+slot).src = 'img/restaurar.gif';
      if ( typeof(this.fixedFlows[slot].maximizeCallBack) == 'function' ){
        this.fixedFlows[slot].maximizeCallBack.apply();
      }
    } else {  // Restaurar
      newStyle = {
        'top':  this.fixedFlows[slot].top, 'left': this.fixedFlows[slot].left,
        'width':  this.fixedFlows[slot].width,  'height':  this.fixedFlows[slot].height
      };
      setStyle (myFlow, newStyle);
      setStyle(flowContent, {'height': parseInt(newStyle.height,  10) - 30 + 'px' , 'width':'100%'});
      gID('tbMAX'+slot).src = 'img/maximizar.gif';
      this.fixedFlows[slot].state = "normal";
      if ( typeof(this.fixedFlows[slot].restoreCallBack) == 'function' ){
        this.fixedFlows[slot].restoreCallBack.apply();
      }
    }
  },

  'close': function (idprefix, fName) {
    var myFlow = this.getFlow(idprefix, fName);
    removeElements(myFlow);   removeElements(gID(idprefix+'ResBoth'));
		removeElements(gID(idprefix+'ResBottom'));  removeElements(gID(idprefix+'ResRight'));
    if ( typeof(this.fixedFlows[idprefix + fName].closeCallBack) == 'function' ){
      this.fixedFlows[idprefix + fName].closeCallBack.apply();
    }
    delete(this.fixedFlows[idprefix + fName]);
  },

  'setMaxZindex': function ( idprefix, fName ) {
    var slot = idprefix + fName;
    if ( this.fixedFlows[slot] ){
      setStyle(this.getFlow(idprefix, fName), {'zIndex': this.fZindexStart++});
    }
  },

  'saveInfos': function (idprefix, fName, fCalls, state) {
    var slot = idprefix + fName; var myFlow = this.getFlow(idprefix, fName);
    if (!this.fixedFlows[slot]){
      this.fixedFlows[slot] = [];
    }
    if (fCalls) {
      this.fixedFlows[slot].minimizeCallBack = ( fCalls.min )?( fCalls.min ):( '' );
      this.fixedFlows[slot].maximizeCallBack = ( fCalls.max )?( fCalls.max ):( '' );
      this.fixedFlows[slot].restoreCallBack = ( fCalls.res )?( fCalls.res ):( '' );
      this.fixedFlows[slot].closeCallBack = ( fCalls.close )?( fCalls.close ):( '' );
    }
    this.fixedFlows[slot].width = myFlow.style.width;
    this.fixedFlows[slot].height = myFlow.style.height;
    this.fixedFlows[slot].top = myFlow.style.top;
    this.fixedFlows[slot].left = myFlow.style.left;
    this.fixedFlows[slot].state = state;
  }

};

Flow.prototype={

  'fName': null,
  'fURL': null,
  'idprefix': null,
  'scroll': null,
  'fStyles':[],
  'tbStyles':[],

  'fContainer': null,
  'fTabBar': null,
  'fMainContent': null,
  'fResize': [],

  'init': function (tName, tURL, fProps, tbProps, fCalls) {
    this.fName=tName;  this.fURL = tURL;

    /*Configuraveis - fProps*/
    this.scroll = ( fProps.scroll )?( fProps.scroll ):( 'yes' );
    this.idprefix = ( fProps.idprefix )?( fProps.idprefix ):( 'fWindow' );
    this.fStyles.width = ( fProps.width )?( fProps.width ):( '500px' );
    this.fStyles.height = ( fProps.height )?( fProps.height ):( '500px' );
    this.fStyles.left = ( fProps.left )?( fProps.left ):( '0px' );
    this.fStyles.top = ( fProps.top )?( fProps.top ):( '0px' );

    /*Configuraveis - tbProps*/
    this.tbStyles.iconSRC = ( tbProps.iconSRC )?( tbProps.iconSRC ):( '' );
    this.tbStyles.title = ( tbProps.title )?( tbProps.title ):( 'Nova Janela' );

    this.fStyles.zIndex = FlowAux.fZindexStart;  FlowAux.fZindexStart++;
    this.drawContainer();   this.drawTabPane();   this.drawMainContent();
    this.drawBorders();

    /*Configuraveis - fCalls*/
    FlowAux.saveInfos(this.idprefix, this.fName, fCalls, "normal");
  },

  'drawContainer': function () {
    var cID = this.idprefix + this.fName;
    var tB = document.body || document.getElementsByTagName('body')[0];
    var cProperties = {'class':'flowContent', 'id':cID};
    this.fContainer = createElement('div', cProperties);
    setStyle(this.fContainer, this.fStyles);
    tB.appendChild(this.fContainer);
  },

  'drawTabPane': function () {
    var tbChilds = [];
    tbChilds[0] = createElement('span', {'class':'tbTitle'}, this.tbStyles.title );
                  disableSelection(tbChilds[0]);
    tbChilds[1] = createElement('img', {'src':'img/minimizar.gif', 'class':'tbMIN'});
                  Events.listen( tbChilds[1], 'click', bind(FlowAux.minimize,FlowAux,[this.idprefix, this.fName]) );
    tbChilds[2] = createElement('img', {'src':'img/maximizar.gif', 'class':'tbMAX', 'id':'tbMAX'+this.idprefix + this.fName});
                  Events.listen( tbChilds[2], 'click', bind(FlowAux.maximize,FlowAux,[this.idprefix, this.fName]) );
    tbChilds[3] = createElement('img', {'src':'img/fechar.gif', 'class':'tbCLS'});
                  Events.listen( tbChilds[3], 'click', bind(FlowAux.close,FlowAux,[this.idprefix, this.fName]) );
    if (this.tbStyles.iconSRC !== '') {
      tbChilds[4] = createElement('img', {'src':this.tbStyles.iconSRC,'class':'tbIcon'});
    }
    this.fTabBar = createElement('div', {'class': 'flowTabPane'}, tbChilds);
    Events.listen( this.fTabBar, 'click', bind(FlowAux.setMaxZindex,FlowAux,[this.idprefix, this.fName]) );
    var Drag = new DragNdrop(this.fContainer, {'handle': this.fTabBar,'type':'outline', 'onFinish': bindPlus (this.callBackMove, this)  });
    this.fContainer.appendChild( this.fTabBar );
  },

  'drawMainContent': function () {
    this.fMainContent = createElement('div', {'id':'CONTENT'+this.idprefix+this.fName,'class':'flowMainContent'},
                          createElement('iframe', {'src':this.fURL, 'frameborder':'0', 'scrolling':this.scroll, 'class':'flowIframse'})
                                     );
    var correctHeight = ( parseInt( this.fStyles.height,10 ) - 34 ) + 'px';
    setStyle(this.fMainContent, {'height':correctHeight});
    this.fContainer.appendChild( this.fMainContent );
  },

  'drawBorders': function (){
  	var tB = document.body || document.getElementsByTagName('body')[0];
  	var borders =[];
		borders.left = createElement('div', {'class':'flowLEFTBorder'});
		borders.bottom = createElement('div', {'id':this.idprefix + 'ResBottom', 'class':'flowBOTTOMBorder'});
		borders.right = createElement('div', {'id':this.idprefix + 'ResRight','class':'flowRIGHTBorder'});
		borders.both = createElement('div', {'id':this.idprefix + 'ResBoth', 'class':'flowBOTHBorder'});
    var borderRight = {
    	'left' : parseInt(this.fStyles.left,10) + parseInt(this.fStyles.width,10) + 'px',
    	'top': this.fStyles.top,
    	'height': this.fStyles.width
    };
    var borderBottom = {
    	'top': parseInt(this.fStyles.top,10) + parseInt(this.fStyles.height,10) + 'px',
    	'left': this.fStyles.left,
    	'width': this.fStyles.width,
    	'height': '0px'
    };
    var borderBoth = {
    	'top': parseInt(this.fStyles.top,10) + parseInt(this.fStyles.height,10) - 3 + 'px',
    	'left': parseInt(this.fStyles.left,10) + parseInt(this.fStyles.width,10) - 3 + 'px'
    };

    var drag1 = new DragNdrop(borders.right, {'handle': borders.right,'type':'ghost', 'vMove':0, 'onFinish': bindPlus(this.endResize,this,['right']) });
    var drag2 = new DragNdrop(borders.bottom, {'handle': borders.bottom,'type':'ghost', 'hMove':0, 'onFinish': bindPlus(this.endResize,this,['bottom']) });
    var drag3 = new DragNdrop(borders.both, {'handle': borders.both,'type':'ghost', 'onFinish': bindPlus(this.endResize,this,['both']) });

    this.fMainContent.appendChild( borders.left );
    tB.appendChild( borders.bottom );
    tB.appendChild( borders.both );
    tB.appendChild( borders.right );
    setStyle( borders.right, borderRight );
    setStyle( borders.bottom, borderBottom );
    setStyle( borders.both, borderBoth );

    this.fResize = borders;
  },

  'callBackMove': function ( Pos ){
  	var styleRight = {
  		'left': Pos.x + parseInt( this.fStyles.width,10 ) + 'px',
  		'top': Pos.y
  	};
  	var styleBottom = {
  		'left': Pos.x,
  		'top': Pos.y + parseInt( this.fStyles.height,10 ) + 'px'
  	};
  	setStyle( this.fResize.right, styleRight );
  	setStyle( this.fResize.bottom, styleBottom );
  },

  'endResize': function ( Pos, tFunc, tObj, Type ){
  	var newHeight = parseInt(this.fContainer.style.height,10) + (Pos.y - (parseInt(this.fContainer.style.height,10) + parseInt(this.fStyles.top,10)) ) + 'px' ;
  	var newWidth = parseInt(this.fContainer.style.width,10) + (Pos.x - (parseInt(this.fContainer.style.width,10) + parseInt(this.fContainer.style.left,10)) ) + 'px' ;
  	if(Type == 'right'){
  	  setStyle ( this.fContainer, {'width' : newWidth} );
  	  setStyle ( this.fResize.bottom, {'width' : newWidth} );
  	  setStyle ( this.fResize.both, {'left' : Pos.x - 3 + 'px'} );
  	} else if(Type == 'bottom'){
		  setStyle ( this.fMainContent, {'height' : parseInt(newHeight,10) - 34 + 'px'} );
		  setStyle ( this.fContainer, {'height' : newHeight} );
  	  setStyle ( this.fResize.right, {'height' : newHeight} );
  	  setStyle ( this.fResize.both, {'top' : Pos.y - 3 + 'px'} );
  	} else if(Type == 'both'){
			setStyle ( this.fContainer, {'width' : newWidth} );
  	  setStyle ( this.fResize.bottom, {'width' : newWidth, 'top': Pos.y} );
  	  setStyle ( this.fResize.both, {'left' : Pos.x - 3 + 'px', 'top': Pos.y - 3 + 'px' } );
  	  setStyle ( this.fMainContent, {'height' : parseInt(newHeight,10) - 34 + 'px'} );
		  setStyle ( this.fContainer, {'height' : newHeight} );
  	  setStyle ( this.fResize.right, {'height' : newHeight, 'left': Pos.x} );
  	}
  }

};