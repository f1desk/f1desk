var DragNdrop = function (Element,Options) {
  var defaults = {
    'onStart':false,
    'onDrag':false,
    'onFinish':false,
    'handle':false,
    'zindex': 1000,
    'vMove':1,
    'hMove':1,
    'type':'normal', // normal, outline, ghost
    'limits': false,  // false, ou {'xMin':x1, 'xMax':x2, 'yMin':y1, 'yMax':y1} ou function(x,y){ return {'x':x,'y':y}; }
    'revert':false,
    'opacity':0.5 // false ou valor de 0 a 1
  };

  if (typeof(Element) !== 'object') { this.element = gID(Element); } else { this.element = Element; }
  this.options = merge(defaults,Options);
  if ( ! this.options.handle) { this.options.handle = this.element; }
  else {
    if (typeof(this.options.handle) !== 'object') {
      this.options.handle = gID(this.options.handle);
    }
  }
  this.mouseDown = Events.listen( this.options.handle, 'mousedown', bindPlus(this.eventMouseDown,this) );
};

var DragNdropUtils = {
  '_dragging':[]
};

DragNdrop.prototype = {
  'element':null,
  'options':null,
  'mouseDown':null,
  'mouseUp':null,
  'mouseMove':null,
  'dragging':false,
  'offset':null,
  'drawed':null,
  'lastPointer':{},
  'zIndexOld':0,
  'positions':{},

  'notify':function(eventName,objeto) {
    if(this.options[eventName]) { this.options[eventName](objeto); }
    return 1;
  },

  'getDelta': function(element) {
    if (element === undefined) { element = this.element; }
    return(
    {
      'x':parseInt(getStyle(element,'left') || '0',10),
      'y':parseInt(getStyle(element,'top') || '0',10)
    });
  },

  'getPosition':function(event,element) {
    var pointer = Events.getMouse(event);
    var newPosition = {};
    var pos = Positions.getCumulativeOffset(element);
    var delta = this.getDelta(element);
    pos.x -= delta.x;
    pos.y -= delta.y;
    newPosition.x = pointer.x - pos.x - this.offset.x;
    newPosition.y = pointer.y - pos.y - this.offset.y;
    newPosition = this.getLimits(newPosition);

    return newPosition;
  },

  'getDrawed':function() {
    return this.drawed;
  },

  'reverteffect': function(element, left, top) {
      setStyle(element, { 'top': top, 'left': left});
      //Futuramente implementar uma interpolacao de movimento
      return 1;
    },

  'eventMouseDown':function(event) {
    if ( DragNdropUtils._dragging[this.element] || this.dragging || Events.getButton(event) != 'left' )  { return false; }
    this.dragging = true;
    DragNdropUtils._dragging[this.element] = true;
    this.configDrag(event);
    var Position = this.getPosition(event, this.element);
    this.notify('onStart',{'element':this.element,'x':Position.x,'y':Position.y});

    return Events.cancel(event);
  },

  'eventMouseMove':function(event) {
    if ( ! DragNdropUtils._dragging[this.element] || ! this.dragging )  { return false; }
    var pointer = Events.getMouse(event);
    if( this.lastPointer && (this.lastPointer.x == pointer.x && this.lastPointer.y == pointer.y ) ) { return false; }
    this.lastPointer = pointer;
    this.updateDrag(event, pointer);

    return Events.cancel(event);
  },

  'eventMouseUp':function(event) {
    DragNdropUtils._dragging[this.element] = false;
    this.dragging = false;

    Events.stopListening(this.mouseMove);
    Events.stopListening(this.mouseUp);
    if (this.options.type != 'normal') {
      var top = getStyle(this.drawed,'top'); var left = getStyle(this.drawed,'left');
      setStyle(this.element,{'position':'absolute','top':top,'left':left});
      this.drawed.parentNode.removeChild(this.drawed);
    }

    setStyle(this.element,{'zIndex':this.zIndexOld,'opacity':'1'});
    this.zIndexOld = 0;

    if (this.options.revert == 1) {
      this.reverteffect(this.element,this.delta.x, this.delta.y);
    }

    var Position = this.getPosition(event, this.element);
    this.notify('onFinish',{'element':this.element,'x':Position.x,'y':Position.y});

    return Events.cancel(event);
  },

  'configDrag':function(event) {
    var Styles = {};
    var pointer = Events.getMouse(event);
    var pos     = Positions.getCumulativeOffset(this.element);
    var offsetX = pointer.x - pos.x;
    var offsetY = pointer.y - pos.y;
    this.offset = {'x':offsetX,'y':offsetY};
    this.delta = this.getDelta();

    this.mouseMove = Events.listen( document, 'mousemove', bindPlus(this.eventMouseMove,this) );
    this.mouseUp = Events.listen( document, 'mouseup', bindPlus(this.eventMouseUp,this) );
    this.zIndexOld = getStyle(this.element,'zIndex') || 0;

    if (this.options.type == 'normal') { this.drawed = this.element; }
    else if(this.options.type == 'outline') { this.drawed = this.drawOutline();}
    else if(this.options.type == 'ghost') { this.drawed = this.drawGhost(); }

    if (this.options.zIndex) { Styles.zIndex = this.options.zindex; }
    if (this.options.opacity) { Styles.opacity = this.options.opacity; }
    setStyle(this.drawed,Styles);

    return 1;
  },

  'drawOutline':function(){
    var Styles = {}, outline;

    outline = createElement('div', {},
                      createElement('div',{},
                        createElement('div',{},'')));

    Styles.position = 'absolute';
    Styles.left = getStyle(this.element,'left');
    Styles.top = getStyle(this.element,'top');
    Styles.width = getStyle(this.element,'width');
    Styles.height = getStyle(this.element,'height');
    if(getStyle(outline,'visibility') == "hidden") { Styles.visibility = ''; }

    setStyle(outline,Styles);
    setStyle(outline.firstChild,{'border':'1px solid blue'});
    setStyle(outline.firstChild.firstChild,{'height':parseInt(getStyle(this.element,'height'),10) - 2 +'px'});

    insertBefore(this.element, outline);

    return outline;
  },

  'drawGhost':function(){
    var Styles = {}, clone, tBody;
    clone = this.element.cloneNode(true);
    tBody = document.body || document.getElementsByTagName('body')[0];

    Styles.position = getStyle(this.element,'position');
    Styles.left = getStyle(this.element,'left');
    Styles.top = getStyle(this.element,'top');
    Styles.width = getStyle(this.element,'width');
    Styles.height = getStyle(this.element,'height');
    if(getStyle(clone,'visibility') == "hidden") { Styles.visibility = ''; }

    setStyle(clone,Styles);

    //insertBefore(this.element, clone);
    this.element.parentNode.appendChild(clone);
    //tBody.appendChild(clone);

    return clone;
  },

  'updateDrag':function(event,pointer) {
    var Styles = {};
    var newPosition = this.getPosition(event, this.drawed);

    if (this.options.hMove == 1) { Styles.left = newPosition.x +'px'; }
    if (this.options.vMove == 1) { Styles.top = newPosition.y +'px'; }
    Styles.position = 'absolute';
    if(getStyle(this.drawed,'visibility') == "hidden") { Styles.visibility = ''; }
    setStyle(this.drawed,Styles);

    var Position = this.getPosition(event,this.element);
    this.notify('onDrag',{'element':this.element,'x':Position.x,'y':Position.y});

    return 1;
  },

  'getLimits':function(newPosition) {
    var Limits;
    if(this.options.limits) {
      if(typeof(this.options.limits) == 'function') {
        Limits = this.options.limits(newPosition.x,newPosition.y,this.element);
      } else if(typeof(this.options.limits) == 'object') {
        Limits = this.options.limits;
      }

      if (Limits) {
         if (newPosition.x > Limits.xMax)       { newPosition.x = Limits.xMax; }
           else if(newPosition.x < Limits.xMin) { newPosition.x = Limits.xMin; }
           if (newPosition.y > Limits.yMax)     { newPosition.y = Limits.yMax; }
           else if(newPosition.y < Limits.yMin) { newPosition.y = Limits.yMin; }
      }

    }

    return newPosition;
  }
};