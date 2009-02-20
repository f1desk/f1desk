var xhr = {
  'uID':0,
  'curName':'',
  'curUID':null,
  'currentRequest':null,
  'queue':[],
  'instance':{},
  'timer':{},
  'ef':function(){},

  'getUID':function() { return 'request'+(xhr.uID++); },
  'makeRequest':function(reqName,URI,params) {
    if(! reqName || ! URI) { return false; }

    xhr.checkParams(params);
    if(typeof(params.enqueue) !== undefined && params.enqueue == 1)  {
      xhr.addToQueue(reqName,URI,params);
      xhr.checkQueue();
      return true;
    }
    else if(params.enqueue === 0) {
      var uID = xhr.getUID();
      xhr.stopRequests(reqName,uID);
      xhr.execute({'uID':uID, 'name':reqName, 'uri':URI, 'params':params});
    }
  },

  'checkParams':function(params) {
    if(typeof(params.enqueue) != 'number' || params.enqueue !== 0) { params.enqueue = 1; }
    if(typeof(params.okStatus) != 'number') { params.okStatus=200; }
    if(typeof(params.content) != 'object' && !(params.content instanceof Array)) { params.content = []; }
    params.content = xhr.makeQueryString(params.content);
    if(typeof(params.content) != 'string') { params.content = ''; }
    if(typeof(params.timeout) != 'number') { params.timeout = 20 * 1000; }
    if(typeof(params.okCallBack) != 'function') { params.okCallBack = xhr.ef; }
    if(typeof(params.errCallBack) != 'function') { params.errCallBack = xhr.ef; }
    if(typeof(params.onStartCallBack) != 'function') { params.onStartCallBack = xhr.ef; }
    if(typeof(params.timeOutCallBack) != 'function') { params.timeOutCallBack = xhr.ef; }
    if(typeof(params.returnType) != 'string') { params.returnType = 'txt'; }
    if(typeof(params.method) != 'string' || params.method != 'post') { params.method = 'get'; }
    if(typeof(params.headers) != 'object' || !(params.headers instanceof Array)) { params.headers=[]; }
    if(typeof(params.extraParams) != 'object' || !(params.extraParams instanceof Array)) {
      params.extraParams=[];
    }
  },

  'addToQueue':function(reqName,URI,params) {
    if(!(xhr.queue instanceof Array)) { xhr.queue = []; }
    xhr.queue.push({ 'uID':xhr.getUID(), 'name':reqName, 'uri':URI, 'params':params });
    return true;
  },

  'checkQueue':function() {
    if(xhr.isWorking()) { return false; }
    var reqData = xhr.queue.shift();
    if((! (xhr.queue instanceof Array) || typeof(reqData) != 'object')) { return false; }
    if(typeof(reqData.params) === undefined) { alert('Sem parametros de configuração'); }
    xhr.execute(reqData);
  },

  'isWorking':function() {
    var working = (xhr.currentRequest !== null) ? true : false;
    return working;
  },

  'stopRequests':function(reqName, uID) {
    if(xhr.instance[reqName]) {
      var tXHR = xhr.instance[reqName];
      tXHR.onreadystatechange = xhr.ef; tXHR.abort();
    }
    if (xhr.timer[reqName] !== null) { window.clearTimeout(xhr.timer[reqName]); }
    try {
      delete(tXHR);
      delete(xhr.instance[reqName]);
      delete(xhr.timer[reqName]);
      xhr.currentRequest = null;
    } catch(e) { tXHR = xhr.instance[reqName] = xhr.timer[reqName] = xhr.currentRequest = null; }
  },

  'getUIDByName':function(name) {
    var max = xhr.queue.lenght;
    for(var i=0;i<max;i++) {
      if(xhr.queue[i].name == name) {
        return xhr.queue[i].uID;
      }
    }
  },

  'execute':function(objReq){
    xhr.currentRequest = objReq;
    xhr.curName = objReq.name;
    xhr.curUID = objReq.uID;

    var tXHR = xhr.newXHR();
    xhr.instance[xhr.curName] = tXHR;
    try { xhr.instance[xhr.curName].open(objReq.params.method,objReq.uri, true); }
    catch(e) {
      alert('Erro na conexão: ' +  e);
      return xhr.checkQueue();
    }
    if(objReq.params.method == 'post') { xhr.instance[xhr.curName].setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8"); }
    for(var i=0;i<objReq.params.headers.length;i++){ tXHR.setRequestHeader(objReq.params.headers[i][0],objReq.params.headers[i][1]); }
    tXHR.onreadystatechange = xhr.stateChanged;
    tXHR.send(objReq.params.content);
    if (objReq.param !== undefined && objReq.param.extraParams !== undefined) {
      var tPs = objReq.param.extraParams;
    }
    if(typeof(objReq.params.onStartCallBack) == 'function') { objReq.params.onStartCallBack(tPs); }
    if(objReq.params.timeout > 0) {
      xhr.timer[xhr.curName] = window.setTimeout(xhr.timedOutRequest, window, [xhr.curName, objReq.uID], objReq.params.timeout);
    } else { xhr.timer[xhr.curName] = null; }
    return xhr.checkQueue();
  },

  'newXHR':function(){
    if (window.XMLHttpRequest) { return new XMLHttpRequest(); }
    else {
      var ts=['Microsoft.XMLHTTP', 'MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
      for (var n=ts.length, i=0; i<n; i++) {
        try {   return new window.ActiveXObject(ts[i]);   } catch(e) { }
      }
      return undefined;
    }
  },

  'stateChanged':function(){
    var tXHR = xhr.instance[xhr.curName], msg, tParams, reqData;
    if(xhr.currentRequest === null || tXHR === undefined) { return false; }
    reqData = xhr.currentRequest;
    if(typeof(reqData) === undefined || xhr.currentRequest.uID != xhr.curUID) {
      xhr.stopRequests(xhr.curName, xhr.curUID);
    }
    try {
      if(tXHR.readyState !== 4 || typeof(tXHR) === undefined) { return false; }
      tParams = reqData.params;
      if(tXHR.status == tParams.okStatus) {
        var tPs = tParams.extraParams;
        var response = (tParams.returnType == 'xml') ? 'responseXML' : 'responseText';
        tPs.unshift(tXHR[response]);
        if (tParams.returnType == 'json') { tPs = JSON.parse(tPs[0]); }
        xhr.stopRequests(xhr.curName, xhr.uID);
        xhr.checkQueue();
         tParams.okCallBack(tPs);
      } else {
        switch(tXHR.status) {
          case 404:
            msg = 'Página não encontrada';
          break;
          case 403:
            msg = 'Acesso negado';
          break;
          case 500:
            msg = 'Erro Interno do servidor';
          break;
          default:
            msg = 'Status HTTP inesperado: ' + tXHR.status;
          break;
        }
        alert(msg);
      }
    } catch(e) {
      alert('Erro na requisição ' + xhr.curName + ':\n' + e.message); }
  },

  'timedOutRequest':function(reqName, uID) {
    var reqData = xhr.currentRequest;
    if(reqData.uID != uID) { return false; }
    var tF = reqData.timeOutCallBack || xhr.ef;
    tF.apply(window, [reqName, (reqData.timeout/1000)]);
    return xhr.stoprequests(reqName, uID);
  },

  'clearQueue':function() {
    if(xhr.isWorking()) { return false; }
    xhr.queue = []; xhr.objects = {}; xhr.timer = {}; xhr.instance = {}; xhr.currentRequest = null;
  },

  'makeQueryString':function(content) {
    var qS = '', json = '';
    for (var field in content) {
      if(typeof(content[field]) == 'object' || content[field] instanceof Array) {
        json = JSON.stringify(content[field]);
        qS += encodeURI(field) + "=" + json + "&";
      } else {
        qS += encodeURI(field) + "=" + encodeURI(content[field]) + "&";
      }
    }
    return qS;
  }
};