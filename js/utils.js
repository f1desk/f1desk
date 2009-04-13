//////////////////////////////////////
//
//    FrameWork Base - Utils.js
//
//  * framework procedural com funções
//     básicas aprimoradas.
//  * autor: Dimitri Lameri
//  * modificado por: Mario Vítor
//
//         USEM COM SAÚDE!!!!
//
//////////////////////////////////////

var Positions, Events, Visibility;

/**
 *  gID (getElementById)
 *  @param elementID < string >
 *  @param otherDocument [ document obj ]
 */
function gID(elementID, otherDocument) {
  var newObject = (otherDocument)?otherDocument:document;
  return newObject.getElementById ( elementID );
}

/**
 *  gTN (getElementsByTagName)
 *  @param elementID < string >
 *  @param otherDocument [ document obj ]
 */
function gTN(elementID, otherDocument) {
  var newObject = (otherDocument)?otherDocument:document;
  return newObject.getElementsByTagName ( elementID );
}

/**
 *  createTextNode
 *  @param newText < string >
 */
function createTextNode(newText) {
  return (document.createTextNode && document.createTextNode(newText));
}

/**
 *  toArray
 *  @param iterable < mixed >
 */
function toArray(iterable) {
  if (!iterable){ return []; }
  if (iterable instanceof Array) { return iterable; }
  /*if (iterable.toArray){ return iterable.toArray(); }*/
  var result=[iterable];
  return result;
}

/**
 *  empty
 *  @param mixed_var < mixed >
 */
function empty( mixed_var ) {
  var key;
  if ( mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || mixed_var === undefined ) { return true; }
  if (typeof mixed_var == 'object') {
    for (key in mixed_var) { if (typeof mixed_var[key] !== 'function' ) { return false; } }
    return true;
  }
  return false;
}

/**
 *  createElement
 *  @param newElement < string >
 *  @param elementProperties [ obj ]
 *  @param elementContent [ string || array ]
 */
function createElement(newElement, elementProperties, elementContent){
  var aux;
  if(!newElement || !document.createElement){ return false; }
  newElement = document.createElement(newElement);
  for (aux in elementProperties) { // 'class' and 'className' are especials
    if (aux == 'class' || aux == 'className') { newElement.className = elementProperties[aux]; }
    else { newElement.setAttribute(aux, elementProperties[aux]); }
  }
  if (elementContent) {
    elementContent = toArray(elementContent);
    for (aux = 0; aux < elementContent.length; aux++) {
      newElement.appendChild( (typeof(elementContent[aux])=='string') ? createTextNode(elementContent[aux]) : elementContent[aux] );
    }
  }
  return newElement;
}

/**
 *  insertBefore
 *  @param currentElement < obj >
 *  @param newElement < obj >
 */
function insertBefore(currentElement, newElement) {
  return currentElement.parentNode.insertBefore(newElement, currentElement);
}

/**
 *  insertAfter
 *  @param targetElement < obj >
 *  @param newElement < obj >
 */
function insertAfter(targetElement, newElement) {
  // if target is the last node element, just inser with appendChild
  // else insert it before the next target son
  if(targetElement.parentNode.lastchild == targetElement){
    targetElement.parentNode.appendChild(newElement);
  } else {
    targetElement.parentNode.insertBefore(newElement, targetElement.nextSibling);
  }
}

/**
 *  removeElements
 *  @param toRemove < obj >
 */
function removeElements(toRemove) {
  if (!toRemove) { return false; }
  toRemove.parentNode.removeChild(toRemove);
  return true;
}

/**
 *  removeChilds
 *  @param Parent < obj >
 */
function removeChilds(Parent) {
  if (! Parent) { return false; }
  if (Parent.hasChildNodes()) {
    while (Parent.hasChildNodes()) {
      removeElements(Parent.firstChild);
    }
  }
  return true;
}

/**
 *  getStyle
 *  @param element < obj >
 *  @param style < string >
 */
function getStyle(element, style) {
  style = (style == 'float') ? 'cssFloat' : style;
  var value = element.style[style];
  if (!value) {
    var css =  element.currentStyle || document.defaultView.getComputedStyle(element, null);
    value = css ? css[style] : null;
  }
  if (style == 'opacity'){ return value ? parseFloat(value) : 1.0; }
  if (style == 'left' && element.style[style] === 0) { value = Positions.getOffset(element).x; }
  if (style == 'top' && element.style[style] === 0) { value = Positions.getOffset(element).y; }
  return value == 'auto' ? null : value;
}

/**
 *  _setOpacity - AUX
 *  @param element < obj >
 *  @param value < string >
 */
function _setOpacity(element, value) {
  element.style.opacity = (value == 1 || value === '') ? '' : (value < 0.00001) ? 0 : value;
  var newValue = (value * 100);
  element.style.filter = 'alpha(opacity=' + newValue + ')';
  return element;
}

/**
 *  setStyle
 *  @param element < obj >
 *  @param style < string >
 */
function setStyle(element, styles) {
	if( element instanceof Array ){
		for(var aux=0; aux < element.length; aux++){
			setStyle( element[aux], styles );
		}
	} else {
	  var elementStyle = element.style, match;
	  if ( typeof(styles) == 'string' ) {
	    elementStyle.cssText += ';' + styles;
	    return element;
	  }
	  for (var property in styles) {
	    if (property == 'opacity') {
	      _setOpacity(element,styles[property]);
	    } else {
	      elementStyle[(property == 'float' || property == 'cssFloat') ?
	        ((elementStyle.styleFloat === undefined) ? 'cssFloat' : 'styleFloat') :
	          property] = styles[property];
	    }
	  }
	  return element;
	}
}

/**
 *  _recursive
 *  @param toDebug < mixed >
 */
function _recursive(toDebug){
  var recursivePRE = createElement('P',{}); var texto = "";
  setStyle(recursivePRE, {'paddingLeft':'15px'});
  if (empty(toDebug)){
    recursivePRE.appendChild( createElement ('pre',{},'NULL') );
  }
  for (var property in toDebug) {
    if(typeof(toDebug[property])=='object' || toDebug[property] instanceof Array){
      recursivePRE.appendChild(createElement('b', {}, property + ' : Object / Array'  ) );
      texto = _recursive(toDebug[property]);
    } else {
      var value = toDebug[property] + '';
      if (value !== '0' && !value) { value = 'NULL'; }
      texto = createElement('p',{'id':'Pdebug'},[createElement('b',{'id':'Bdebug'},property + ' : '), createElement('pre',{'style':'height:auto; padding-left: 15px;'},value)]);
    }
    recursivePRE.appendChild( texto );
  }
  return recursivePRE;
}

/**
 *  debug
 *  @param todebug < mixed >
 *  @param recursive < bool >
 *  @desc useful to look all properties and methods of an object, an array or even the value of a string
 */
function debug(toDebug, recursive){
  var style={'border':'black ridge medium','padding':'15px','backgroundColor':'#F8FAAF','width':'auto','height':'auto','position':'absolute','zindex':'1000','top':'10px','left':'10px'};
  var aux = 0;  var debugArea = createElement('div',{'id':'debugTest'});
  setStyle(debugArea, style);
  var texto,value;
  if(typeof(toDebug)=='object' || toDebug instanceof Array){
    for (var property in toDebug) {
      if( recursive === true && (typeof(toDebug[property])=='object' || toDebug[property] instanceof Array)){
        debugArea.appendChild(createElement('b', {}, property + ' : Object / Array' ) );
        texto = _recursive(toDebug[property]);
      } else {
        value = toDebug[property] + '';
        if (value !== '0' && !value) { value = 'NULL'; }
        texto = createElement('p',{'id':'Pdebug'+aux},[createElement('b',{'id':'Bdebug'+aux},property + ' : '), createElement('pre',{'style':'height:auto; padding-left: 15px;'},value)]);
      }
      debugArea.appendChild(texto); aux++;
    }
  } else {
    value = toDebug + '';
    if (value !== '0' && !value) { value = 'NULL'; }
    texto = createElement('P',{'id':'Pdebug'+aux},[createElement('b',{'id':'Bdebug'+aux},'Var' + ' : '), createElement('pre',{'style':'height:auto'},value)]);
    debugArea.appendChild(texto); aux++;
  }
  document.body.appendChild(debugArea);
}

/**
 *  isEmpty
 *  @param Text < text >
 */
function isEmpty(Text){
  var TxEmpty; TxEmpty = Text.replace(/\s+|\s+/g,"");
  if(TxEmpty === ""){
    return true;
  } else {
    return false;
  }
}

/**
 *  br2nl
 *  @param Text < text >
 */
function br2nl(Text){
  Text = Text.split('<br />').join('\n');
  Text = Text.split('<br/>').join('\n');
  return Text.split('<br>').join('\n');
}

/**
 * trim
 * @param Text < text >
 */
function trim(Text) {
  return Text.replace(/^\s*/, "").replace(/\s*$/, "");
}

/**
 *  merge
 *  @param destination < obj >
 *  @param source < obj >
 */
function merge(destination, source) {
  for (var property in source) { destination[property] = source[property]; }
  return destination;
}

/**
 *  disableSelection
 *  @param element < obj >
 */
function disableSelection(element) {
  element.onselectstart = function() {
      return false;
  };

  element.unselectable = "on";
  element.style.MozUserSelect = "none";

  return true;
}

/**
 *  bind
 *  @param method < string >
 */
function bind(thisMethod, thisHandler, args) {
  if(! args instanceof Array) { args = [args]; }
  return (function() { return thisMethod.apply(thisHandler,args); });
}

/**
 *  bindPlus
 *  @param method < string >
 *  @param handler < obj >
 */
function bindPlus(method,handler) {
  var thisMethod = method, thisHandler = handler, args = [],qtd = arguments.length;
  for(var i=0;i<qtd;i++) {  args[i] = arguments[i]; }
  args = toArray(args);
  return function(event) { return thisMethod.apply(thisHandler, [event || window.event].concat(args)); };
}

/**
* appends an HTML text to an HTML object
*
*/
function appendHTML(HTML, Target, Refresh) {
    var tmpDiv = createElement('div');
    tmpDiv.innerHTML = HTML;
    var node = null;
    var i = 0;
    var nodes = tmpDiv.childNodes, element;

    if(Refresh){ Target.innerHTML = ""; }
    while (nodes[i]) {
        node = nodes[i];
        element = node.cloneNode(true);
        Target.appendChild(element);
        i++;
    }

    return (nodes.length > 0);
}


/**
 *  Events - OBJECT
 *  @desc specific object to control all events
 */
Events = {
  'uniqID':0,
  'storedEvents':[],

  'getTarget': function(event) {
    var element;
    switch(event.type) {
      case 'mouseover': element = (event.currentTarget || event.fromElement); break;
      case 'mouseout':  element = (event.currentTarget || event.toElement);   break;
      case 'click': element = (event.srcElement || event.target); break;
      default: return null;
    }
    return element;
  },

  'getMouse': function(event) {
    return {
      x: event.pageX || (event.clientX +
        (document.documentElement.scrollLeft || document.body.scrollLeft)),
      y: event.pageY || (event.clientY +
        (document.documentElement.scrollTop || document.body.scrollTop))
    };
  },

  'getMouseX': function(event) { return Events.getMouse(event).x; },
  'getMouseY': function(event) { return Events.getMouse(event).y; },

  'cancel': function(event) {
    if (event.preventDefault) {event.preventDefault();}
    if (event.stopPropagation) {event.stopPropagation();}
    if (event.cancelBubble) {event.cancelBubble=true;}
    if (event.returnValue) {event.returnValue=false;}
    if (event.stopped) {event.stopped=true;}
    return false;
  },

  'listen': function(element, eventName, handler) {
    eventName = toArray(eventName);
    var ID = Events.uniqID++;
    Events.storedEvents[ID] = {'element':element,'events':eventName,'handler':handler};

    for (var i=0;i<eventName.length;i++) {
      var eToListen = eventName[i];
      if (element.addEventListener) { element.addEventListener(eToListen, handler, false); }
      else if (element.attachEvent) { element.attachEvent('on'+eToListen, handler); }
      else { element['on'+ eToListen]=handler; return false; }
    }
    return ID;
  },

  'stopListening': function(ID) {
    var Objeto = Events.storedEvents[ID];
    var element = Objeto.element;
    var eventName = Objeto.events;
    var handler = Objeto.handler;

    for (var i=0;i<eventName.length;i++) {
      var eToStopListen = eventName[i];
      if (element.removeEventListener) { element.removeEventListener(eToStopListen, handler, false); }
      else if (document.detachEvent) { element.detachEvent('on'+ eToStopListen, handler); }
      else { element["on"+ eToStopListen] = null; }
    }
    return true;
  },
  'getEv': function(e) { return e || (window.event ? window.event : false); },

  'getButton':function(event) {
    var button;
    event = (event || window.event);

    if (event.button) {
       button = (event.button < 2) ? "left" :
                 ((event.button == 4) ? "middle" : "right");
    } else if (event.which) {
       button = (event.which < 2) ? "left" :
                 ((event.which == 2) ? "middle" : "right");
    } else {
      return false;
    }

    return button;
  }

};

/**
 *  Visibility - OBJECT
 *  @desc specific object to control visibility
 */
Visibility = {
  'toogleView': function(element) {
    var action;
    if (typeof(element) !== 'object') { element = gID(element); }
    var display = getStyle(element,'display');
    if (display != 'none' && display !== null) {
      action = Visibility.hide(element);
    } else {
      action = Visibility.show(element);
    }

    return action;
  },

  'hide': function(element) {
    setStyle(element,{'display':'none'});
    return 'hide';
  },

  'show': function(element) {
    setStyle(element,{'display':''});
    return 'show';
  }
};

/**
 *  Positions - Object
 *  @desc especific object to control all kind of posiitons
 */
Positions = {
  'getDimensions': function(element) {
    if (typeof(element) !== 'object') { element = gID(element); }
    var display = getStyle(element,'display');
    if (display != 'none' && display !== null){
      return { 'width': element.offsetWidth, 'height': element.offsetHeight };
    }

    var els = element.style;
    var originalVisibility = els.visibility;
    var originalPosition = els.position;
    var originalDisplay = els.display;
    els.visibility = 'hidden';
    els.position = 'absolute';
    els.display = 'block';
    var originalWidth = element.clientWidth;
    var originalHeight = element.clientHeight;
    els.display = originalDisplay;
    els.position = originalPosition;
    els.visibility = originalVisibility;
    return { 'width': originalWidth, 'height': originalHeight };
  },

  'getCumulativeOffset': function(element) {
    var Top = 0, Left = 0;
    do {
      Top += element.offsetTop  || 0;
      Left += element.offsetLeft || 0;
      element = element.offsetParent;
    } while (element);
    return {'x':Left, 'y':Top};
  },

 'getOffset': function(element) {
    var Top = 0, Left = 0;
    do {
      Top += element.offsetTop  || 0;
      Left += element.offsetLeft || 0;
      element = element.offsetParent;
      if (element) {
        if (element.tagName == 'BODY'){ break; }
        var p = getStyle(element, 'position');
        if (p == 'relative' || p == 'absolute'){ break; }
      }
    } while (element);
    return {'x':Left, 'y':Top};
  },

  'getScrollOffSet': function(element) {
    var Top = 0, Left = 0;
    do {
      Top += element.scrollTop  || 0;
      Left += element.scrollLeft || 0;
      element = element.parentNode;
    } while (element);
    return {'x':Left, 'y':Top};
  }
};