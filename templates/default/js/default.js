var templateDir = 'templates/default/';

/**
 *  OBJECT CONTAIN GLOBALS OBJECTS AND METHODS FROM THE DEFAULT TEMPLATE
 */
var baseActions = {

  'animateReload': function(ID, action) {
    var reload = gID('reload' + ID);
    if (reload !== null) {
      if (action == 'start') {
        reload.src= 'templates/default/images/animated-reload.gif';
      } else {
        reload.src = 'templates/default/images/btn_reload.png';
      }
    }
  },

  'toogleArrow': function(StArrow, StDivContent, ForceAction) {
    var action;
    var arrow = gID(StArrow);
    var element = gID(StDivContent);

    if (ForceAction === undefined) {
      action = Visibility.toogleView(element);
    } else if (ForceAction == 'hide') {
      action = Visibility.hide(element);
    } else {
      action = Visibility.show(element);
    }

    if (action == 'hide') {
      arrow.src= 'templates/default/images/arrow_show.gif';
      arrow.alt = 'Show';
    } else {
      arrow.src = 'templates/default/images/arrow_hide.gif';
      arrow.alt = 'Hide';
    }
  },

  'validateQuickSearch': function(form) {
    var i = 0;
    var Inputs = gTN('input',form);
    var expreg = /^\#?[0-9]*$/;
    while (Inputs[i]) {
      if (Inputs[i].type == 'text') {
        if (! expreg.test(Inputs[i].value)) {
          Inputs[i].value = '';
          return false;
        } else if (Inputs[i].value.indexOf('#') !== false) {
          Inputs[i].value = Inputs[i].value.replace(/#/g,"");
        }
      }
      i++;
    }

    form.submit();
    return true;
  },

  'selectFromSearch': function(id) {
    IDDepartment = Ticket.findTicket(id);
    if (IDDepartment) {
      Ticket.showDepartmentTickets(IDDepartment);
      Clicked = gID(id);
      if (Clicked) {
        Ticket.selectTicket(Clicked.parentNode);
      }
    }
  }
};

/**
*
* OBJECT CONTAINS FUNCTIONS THAT HANDLES WITH THE FLOW WINDOW
*
*/
var flowWindow = {

  'alert': function(StArg) {
    var flowParams = new this.flowParams();
    flowParams.width = 350;
    flowParams.height = 175;
    flowParams.TBStyle.Caption = i18n.flowAlertTitle;
    flowParams.WindowStyle.Caption = '<br>';
    flowParams.innerHTML = StArg + '<br><br>';
    flowParams.Window = 'alert';
    var ID = Flow.open(flowParams); return ID;
  },

  'confirm': function(StArg,tFunction) {
    var option = ''; var flowParams = new this.flowParams();
    flowParams.width = 350;
    flowParams.height = 175;
    flowParams.TBStyle.Caption = i18n.flowConfirmTitle;
    flowParams.WindowStyle.Caption = '<br>';
    flowParams.innerHTML = StArg + '<br><br>';
    flowParams.Window = 'confirm';
    if(typeof(tFunction) == 'function') {
      flowParams.EventFuncs.Confirm = tFunction;
    }
    var ID = Flow.open(flowParams); return ID;
  },

  'flowParams': function(){
    this.y = Positions.getScrollOffSet(gTN('body')[0]).y + 50;
    this.x = Positions.getScrollOffSet(gTN('body')[0]).x + 200;
    this.width=350; this.height=250;
    this.definition='response';  this.innerHTML='TEXTO DA PAGINA';
    this.TB = true; this.Window = 'default';
    this.TBStyle = {
      'BackgroundColor': '#4F6C9C',
      'Color':'#fff',
      'Font':'12px verdana, sans-serif',
      'Image': '',
      'Caption': 'TEXTO CAPTION BARRA DE TITULO'
    };
    this.WindowStyle = {
      'BackgroundColor':'#ECEDEF',
      'BackgroundImage':'',
      'Caption':'TEXTO TITULO DA JANELA'
    };
    this.EventFuncs = {
      'Confirm':function(){ },
      'Prompt':function(){ },
      'Close':'',
      'Max':'',
      'Min':'',
      'Rest':''
    };
  },

  'previewAnswer': function(TxMessage, IDTicket, IDDepartment, StMessageType) {
    if(isEmpty(TxMessage)){ flowWindow.alert(i18n.answerPreviewNoAnswer); return false; }
    var  tParams = {
      'method':'post',
      'content':{
        'StAction':'previewAnswer',
        'TxMessage': TxMessage,
        'IDTicket': IDTicket,
        'IDDepartment': IDDepartment,
        'StMessageType': StMessageType
      },
      'okCallBack':function(response) {
        var flowParams = new flowWindow.flowParams();
        flowParams.width = 480;
        flowParams.height = 350;
        flowParams.innerHTML = response;
        flowParams.TBStyle.Caption = i18n.answerPreviewTitle;
        Flow.open(flowParams);
      }
    };
    xhr.makeRequest('preview Ticket', templateDir + 'ticketPreviewAnswer.php', tParams);
  },

  'previewCannedResponse': function(StTitle, TxMessage) {
    StTitle = unescape( StTitle );  TxMessage = unescape( TxMessage );
    var flowParams = new this.flowParams();
    flowParams.innerHTML = ''+
      '<table class="tableTickets">'+
        '<thead>'+
          '<th>'+i18n.cannedTableTitle+'</th>'+
        '</thead>'+
        '<tbody>'+
          '<td class="TicketNumber">'+ StTitle +'</td>'+
        '</tbody>'+
      '</table>'+
      '<br />'+
      '<table class="tableTickets">'+
        '<thead>'+
          '<th>'+i18n.cannedTableMessage+'</th>'+
        '</thead>'+
        '<tbody>'+
          '<td>'+ TxMessage +'</td>'+
        '</tbody>'+
      '</table>';
    flowParams.TBStyle.Caption = StTitle;
    flowParams.width = 550;
    flowParams.height = 380;
    var ID = Flow.open(flowParams);
  },

  'previewNote': function(StTitle, TxNote) {
    StTitle = unescape( StTitle );  TxNote = unescape( TxNote );
    var flowParams = new this.flowParams();
    flowParams.innerHTML = ''+
      '<table class="tableTickets">'+
        '<thead>'+
          '<th>'+i18n.noteTableTitle+'</th>'+
        '</thead>'+
        '<tbody>'+
          '<td class="TicketNumber">'+ StTitle +'</td>'+
        '</tbody>'+
      '</table>'+
      '<br />'+
      '<table class="tableTickets">'+
        '<thead>'+
          '<th>'+i18n.noteTableNote+'</th>'+
        '</thead>'+
        '<tbody>'+
          '<td>'+ TxNote +'</td>'+
        '</tbody>'+
      '</table>';
    flowParams.TBStyle.Caption = StTitle;
    flowParams.width = 550;
    flowParams.height = 380;
    var ID = Flow.open(flowParams);
  },

  'previewTicket': function(IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'preview':'true'
      },
      'okCallBack':function( ticketHTML ) {
        var flowParams = new flowWindow.flowParams();
        flowParams.innerHTML = '<span style="padding:10px">'+ticketHTML+'</span>';
        flowParams.TBStyle.Caption = i18n.ticketPreviewTitle + IDTicket;
        flowParams.width = 600;
        flowParams.height = 450;
        var ID = Flow.open(flowParams);
      }
    };
    xhr.makeRequest('Bookmark Ticket', templateDir + 'ticket.php',tParams);
  },

  'prompt': function(StArg, tFunction) {
    var flowParams = new this.flowParams();
    flowParams.width = 350;
    flowParams.height = 175;
    flowParams.innerHTML = '';
    flowParams.Window = 'prompt';
    if(typeof(tFunction) == 'function') {
      flowParams.EventFuncs.Prompt = tFunction;
    }
    var ID = Flow.open(flowParams); return ID;
  }

};

/**
 *  OBJECT CONTAIN METHODS FROM HOME TEMPLATE
 */
var Home = {

  '_doLoading': function( formName, action ){
    var loadingBox = gID(formName + 'Loading');
    if(!loadingBox) {
      return false;
    }
    if (action == 'hide') {
      loadingBox.className = 'loading hidden';
    } else {
      loadingBox.className = 'loading';
    }
  },

  'editData': function() {
    if (gID('dataEditAction').value == "start"){
      var dataTableTR = gID('dataTable').getElementsByTagName('TR');
      /*Deixando em modo de edição*/
      for (var aux=1; aux < dataTableTR.length; aux = aux+2) {
        var TD = dataTableTR.item(aux).getElementsByTagName('TD')[0];
        var PRE = TD.getElementsByTagName('PRE')[0];
        if(PRE.innerHTML == '<i>vazio</i>') {
          PRE.innerHTML = "";
        }
        if (aux == 7 || aux == 9) {
          PRE.innerHTML = '<textarea id="'+TD.id+'Input" class="answerArea">'+PRE.innerHTML+'</textarea>';
        } else if (aux == 5) {
          PRE.innerHTML = '<input type="radio" id="'+TD.id+'Input" name="'+TD.id+'Input" value="'+i18n.yes+'" checked>'+i18n.yes;
          PRE.innerHTML += '<input type="radio" id="'+TD.id+'Input" name="'+TD.id+'Input" value="'+i18n.no+'" >'+i18n.no;
        }
        else {
          PRE.innerHTML = '<input id="'+TD.id+'Input" type="text" class="inputCombo" value="'+PRE.innerHTML+'" >';
        }
      }
      /*Modificando o botão*/
      gID('dataButton').getElementsByTagName('IMG')[0].src = templateDir + 'images/unignore.png';
      gID('dataButton').getElementsByTagName('SPAN')[0].innerHTML = i18n.apply;
      gID('dataEditAction').value = "finish";
    } else {
      var tParams = {
        'method':'post',
        'content': {
          'StAction':'updateUserData',
          'StName': gID('StDataNameTDInput').value,
          'StEmail': gID('StDataEmailTDInput').value,
          'BoNotify': (gID('StDataNotifyTDInput').checked)?1:0,
          'TxHeader': gID('TxDataHeaderTDInput').value,
          'TxSign': gID('TxDataSignTDInput').value
        },
        'startCallBack': function(){
          gID('dataEditAction').value = "start";
          Home._doLoading('data', 'show');
        },
        'okCallBack':function(response) {
          appendHTML(response, gID('dataBox'), true);
        }
      };
      xhr.makeRequest('Edit User Data',templateDir + 'userInfo.php',tParams);
    }
  },

  'elementCreateSubmit': function(elementName) {
    var tURL, StAction, element;

    if (elementName == 'canned'){
      tURL = 'cannedResponses.php';
      StAction = 'createCannedResponse';
    }
    else if(elementName == 'notes'){
      tURL = 'notes.php';
      StAction = 'createNote';
    }
    var tParams = {
      'method':'post',
      'content': {
        'StAction': StAction,
        'StTitle': gID(elementName + 'InsertTitle').value,
        'TxMessage': gID(elementName + 'InsertAnswer').value,
        'BoPersonal': 1
      },
      'startCallBack': function(){
        if(elementName == 'canned') {
          Home._doLoading('cannedResponse', 'show');
        } else if(elementName == 'notes') {
          Home._doLoading('notes', 'show');
        }
      },
      'okCallBack':function(response) {
        if(elementName == 'canned') {
          element = gID('cannedResponsesBox');
        } else if(elementName == 'notes') {
          element = gID('notesBox');
        }
        appendHTML(response, element, true);
      }
    };
    xhr.makeRequest('Create Element',templateDir + tURL,tParams);
  },

  'elementEditSubmit': function(elementName, elementID) {
    var StAction, tURL, element;
    if (elementName == 'canned'){
      StAction = 'editCannedResponse';
      tURL = 'cannedResponses.php';
    }
    else if(elementName == 'notes'){
      StAction = 'editNote';
      tURL = 'notes.php';
    }
    var tParams = {
      'method':'post',
      'content': {
        'StAction': StAction,
        'IDEdit': elementID,
        'StTitle': gID(elementName + 'EditTitle'+elementID).value,
        'TxMessage': gID(elementName + 'Answer'+elementID).value
      },
      'startCallBack': function(){
        if (elementName == 'canned') {
          Home._doLoading('cannedResponse', 'show');
        } else if(elementName == 'notes') {
          Home._doLoading('notes', 'show');
        }
      },
      'okCallBack':function(response) {
        if(elementName == 'canned') {
          element = gID('cannedResponsesBox');
        } else if(elementName == 'notes') {
          element = gID('notesBox');
        }
        appendHTML(response, element, true);
      }
    };
    xhr.makeRequest('Edit Element',templateDir + tURL,tParams);
  },

  'removeBookmark': function(IDTicket) {
    if(!IDTicket){
      flowWindow.alert(i18n.noBookmarkID);
    }
    var tFunction = function(opt) {
      if (opt == 1) {
        Home._doLoading('bookmark','show');
        var tParams = {
          'enqueue':1,
          'method':'post',
          'content':{
            'StAction':'removeBookmark',
            'IDTicket': IDTicket
          },
          'okCallBack': function(response){
            appendHTML(response,gID('bookmarkBox'),true);
          }
        };
        var tUrl = templateDir + 'bookmark.php';
        xhr.makeRequest('removeBookmark',tUrl,tParams);
      }
    };
    flowWindow.confirm(i18n.deleteBookmark,tFunction);
  },

  'removeCannedResponse': function(IDCannedResponse) {
    if(!IDCannedResponse){  flowWindow.alert(i18n.noCannedID);  }
    var tFunction = function(opt) {
      if (opt == 1) {
        Home._doLoading( 'cannedResponse','show' );
        var tParams = {
          'enqueue':1,
          'method':'post',
          'content':{
            'StAction':'removeCannedResponse',
            'IDCannedResponse': IDCannedResponse
          },
          'okCallBack': function(response){
            appendHTML(response, gID('cannedResponsesBox'), true);
          }
        };
        xhr.makeRequest('removeCannedResponse',templateDir + 'cannedResponses.php',tParams);
      }
    };
    flowWindow.confirm(i18n.deleteCanned,tFunction);
  },

  'removeNote': function(IDNote) {
    if(!IDNote){
      flowWindow.alert(i18n.noNoteID);
    }
    var tFunction = function(opt) {
      if (opt == 1) {
        Home._doLoading( 'notes','show' );
        var tParams = {
          'enqueue':1,
          'method':'post',
          'content':{
            'StAction':'removeNote',
            'IDNote': IDNote
          },
          'okCallBack': function( response ){
            appendHTML(response, gID('notesBox'), true);
          }
        };
        var tUrl = templateDir + 'notes.php';
        xhr.makeRequest('removeNote',tUrl,tParams);
      }
    };
    flowWindow.confirm(i18n.deleteNote,tFunction);
  },

  'startCreateElement': function(elementName) {
    /*Visualizando TR de insert*/
    gID(elementName + 'InsertTitleTR').className = "";
    gID(elementName + 'InsertAnswerTR').className = "";
    /*deixa de ser burro!*/
    setStyle ( gID(elementName + 'InsertButton'), {'display': 'none'} );
  },

  'startEditElement': function(elementName, elementID) {
    /*Modificando o td de ações*/
    gID(elementName+'ActionEdit'+elementID).className = "hiddenTR";
    gID(elementName+'ActionApply'+elementID).className = "";
    /*Exibindo o textarea*/
    gID(elementName+'AnswerTR'+elementID).className = "";
    /*Titulo em modo de edição*/
    var TD = gID(elementName+'TitleTR'+elementID).getElementsByTagName('TD')[0];
    TD.innerHTML = '<input type="text" id="'+elementName+'EditTitle'+elementID+'" class="inputCombo" value="'+trim(TD.innerHTML)+'">';
  },

  'stopCreateElement': function(elementName) {
    /*Visualizando TR de insert*/
    gID(elementName + 'InsertTitleTR').className = "hiddenTR";
    gID(elementName + 'InsertAnswerTR').className = "hiddenTR";
    /* ¬¬ */
    setStyle ( gID(elementName + 'InsertButton'), {'display': ''} );
  },

  'stopEditElement': function(elementName, elementID) {
    /*Modificando o td de ações*/
    gID(elementName+'ActionEdit'+elementID).className = "";
    gID(elementName+'ActionApply'+elementID).className = "hiddenTR";
    /*Exibindo o textarea*/
    gID(elementName+'AnswerTR'+elementID).className = "hiddenTR";
    /*Titulo sem modo de edição*/
    var TD = gID(elementName+'TitleTR'+elementID).getElementsByTagName('TD')[0];
    TD.innerHTML = gID(elementName+'EditTitle'+elementID).value;
  }

};

/**
 *  OBJECT CONTAIN METHODS FROM TICKET LIST TEMPLATE
 */
var Ticket = {

  'addCannedResponse': function(IDDepartment,IDSupporter) {
    var Responses = gID('cannedAnswers');
    var TxMessage = Responses[Responses.selectedIndex].value;
    if (TxMessage && TxMessage != 'null') {
      gID('TxMessage').value += br2nl(unescape(TxMessage)) + '\n';
    }
    return false;
  },

  'attachTicket': function(IDTicket, IDDepartment) {
    var flowParams = new flowWindow.flowParams();
    flowParams.width = 350;
    flowParams.height = 175;
    flowParams.innerHTML = '#';
    flowParams.Window = 'prompt';
    flowParams.TBStyle.Caption = "Anexando chamado";
    flowParams.WindowStyle.Caption = "Informe o número do chamado a ser anexado";
    flowParams.EventFuncs = {
      'Prompt': function(IDAttached) {
        IDAttached = IDAttached.replace(/[#]|[^0-9]/g,'');
        if(IDAttached === '') { return false; }
        if(typeof(IDTicket) == 'undefined') {
          if(!gID('ArAttached')) {
            gID('formCreate').appendChild( createElement('input',{
              'type':'hidden',
              'id':'ArAttached',
              'name':'ArAttached',
              'value':IDAttached
            }) );
          } else {
            gID('ArAttached').value += ',' + IDAttached;
          }
          gID('AttachedTickets').appendChild( createElement('p',{
            'id':'attachedTickets',
            'style':'margin:0;padding:0;padding-bottom:5px;'
          },[ createElement('span',{'id':'ticket','class':'supporterName'}, [createTextNode('#'+IDAttached)] ) ]) );
          gID('AttachedTickets').className = '';
        } else {
          var tParams = {
            'method':'post',
            'content': {
              'IDTicket':IDTicket,
              'IDAttached':IDAttached,
              'IDDepartment':IDDepartment,
              'StAction':'attach'
            },
            'okCallBack':function(htmlReturn) {
              var contentDisplay = gID('contentDisplay');
              appendHTML(htmlReturn, contentDisplay, true);
            }
          };
          xhr.makeRequest('Attach Ticket', templateDir + 'ticket.php',tParams);
        }
      }
    };
    var ID = Flow.open(flowParams);
  },

  'bookmarkTicket': function(IDSupporter, IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDSupporter':IDSupporter,
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'StAction':'bookmark'
      },
      'okCallBack':function(htmlReturn) {
        var contentDisplay = gID('contentDisplay');
        Ticket.reloadTicketList('bookmark', true,'show');
        Ticket.reloadTicketList(IDDepartment, true, 'show');
        appendHTML(htmlReturn, contentDisplay, true);
      }
    };
    xhr.makeRequest('Bookmark Ticket', templateDir + 'ticket.php',tParams);
  },

  'removeBookmark': function(IDSupporter, IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDSupporter':IDSupporter,
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'StAction':'unbookmark'
      },
      'okCallBack':function(htmlReturn) {
        var contentDisplay = gID('contentDisplay');
        Ticket.reloadTicketList('bookmark', true,'show');
        Ticket.reloadTicketList(IDDepartment, true, 'show');
        appendHTML(htmlReturn, contentDisplay, true);
      }
    };
    xhr.makeRequest('Unbookmark Ticket', templateDir + 'ticket.php',tParams);
  },

  'changeDepartment': function(IDTicket, IDDepartmentTo, IDDepartmentFrom) {
    var tParams = {
      'method':'post',
      'content': {
        'IDDepartment':IDDepartmentTo,
        'IDTicket':IDTicket,
        'StAction':'changeDepartment'
      },
      'okCallBack':function(htmlReturn) {
        var contentDisplay = gID('contentDisplay');
        appendHTML(htmlReturn, contentDisplay, true);
        Ticket.reloadTicketList(IDDepartmentFrom, true, 'show');
        Ticket.reloadTicketList(IDDepartmentTo, true, 'show');
      }
    };
    xhr.makeRequest('Change Department', templateDir + 'ticket.php',tParams);
  },

  'findTicket': function(IDTicket) {
    var TicketNode = gID(IDTicket);
    if (TicketNode) {
      var TicketTable = TicketNode.parentNode.parentNode.parentNode.id;
      var ID = TicketTable.split('ticketTable');
      return ID[1];
    } else {
      return false;
    }
  },

  'ignoreTicket': function(IDSupporter,IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDSupporter':IDSupporter,
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'StAction':'ignore'
      },
      'okCallBack':function(htmlReturn) {
        var contentDisplay = gID('contentDisplay');
        Ticket.reloadTicketList('ignored', true,'show');
        Ticket.reloadTicketList(IDDepartment, true, 'show');
        appendHTML(htmlReturn, contentDisplay, true);
      }
    };
    var tFunction = function(opt) {
      if(opt == 1) {
        xhr.makeRequest('Ignore Ticket', templateDir + 'ticket.php',tParams);
      }
    };
    flowWindow.confirm(i18n.ignoreCall,tFunction);
  },

  'insertTickets': function(IDDepartment, HTMLTickets) {
    var departmentContent = gID( 'departmentContent' + IDDepartment );
    removeChilds(departmentContent);
    appendHTML(HTMLTickets, departmentContent);
  },

  'orderTicketList': function(ItTD, tableID) {
    var tBody = gID(tableID).getElementsByTagName('TBODY')[0];
    var toIterate = tBody.getElementsByTagName('TR');
    var tdsValues = [];
    for (var aux = 0; aux < toIterate.length; aux++){
      tdsValues[aux] = [];
      tdsValues[aux][0] = toIterate[aux].getElementsByTagName('TD')[ItTD].innerHTML.toLowerCase();
      tdsValues[aux][1] = toIterate[aux];
    }
    while (toIterate.length !== 0){
      removeElements(toIterate[0]);
    }
    tdsValues = tdsValues.sort();
    for (aux =0; aux < tdsValues.length; aux++){
      tBody.appendChild(tdsValues[aux][1]);
    }
  },

  'refreshNotReadCount': function(IDDepartment) {
    if (IDDepartment == 'closed' || IDDepartment == 'ignored') {
      return false;
    }
    var count = 0;  var table = gID('ticketTable' + IDDepartment);
    var tbody = gTN('tbody',table)[0];   var trs = gTN('tr',tbody);
    for (var i=0; i < trs.length; i++) {
      if (trs[i].className.indexOf('notRead') !== -1) {
        count += 1;
      }
    }
    var element = gID('notReadCount' + IDDepartment);
    removeChilds(element);
    element.appendChild( createTextNode( count + ' ') );
  },

  'refreshTicket': function(IDTicket, IDDepartment) {
    var tParams = {
      'enqueue':1,
      'method':'post',
      'content':{
        'IDTicket':IDTicket,
        'IDDepartment': IDDepartment
      },
      'startCallBack':function() {
        baseActions.animateReload( 'Header', 'start' );
      },
      'okCallBack':function(returnedValue) {
        baseActions.animateReload( 'Header', 'stop' );
        var contentDisplay = gID('contentDisplay'); removeChilds(contentDisplay);
        appendHTML(returnedValue, contentDisplay);
      }
    };
    var tUrl = templateDir + 'ticket.php';
    xhr.makeRequest('refreshTicket',tUrl,tParams);
    return true;
  },

  'reloadTicketList': function(IDDepartment, First, Force) {
    if (gID('departmentContent' + IDDepartment)) {
      var tParams = {
        'enqueue':1,
        'returnType':'txt',
        'method':'post',
        'content':{'IDDepartment':IDDepartment},
        'startCallBack' : function() {
          baseActions.animateReload( IDDepartment, 'start' );
        },
        'okCallBack':function(HTMLTickets) {
          baseActions.animateReload( IDDepartment, 'stop' );
          Ticket.insertTickets(IDDepartment, HTMLTickets);
          Ticket.refreshNotReadCount( IDDepartment );
          Ticket.showDepartmentTickets(IDDepartment, 'show');
        },
        'errCallBack':function(Return) {
          baseActions.toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment, 'hide');
          baseActions.animateReload( IDDepartment, 'stop' );
        }
      };
      var tUrl = templateDir + 'ticketList.php';
      xhr.makeRequest('reloadTicketList',tUrl,tParams);
    }
  },

  'selectTicket': function(Clicked) {
    var div = gID('contentDepartments');  var table = gTN('table',div);
    for (var i=0; i < table.length; i++) {
      var tbody = gTN('tbody',table[i])[0];
      var trs = gTN('tr',tbody);  var className = '';
      for (var j=0; j < trs.length; j++) {
        if (trs[j].className.indexOf('notRead') !== -1) {
          className = 'notRead';
        } else {
          className = '';
        }

        if (j % 2 === 0) {
          trs[j].className = className;
        } else {
          trs[j].className = className + ' Alt';
        }
      }
    }
    Clicked.className = 'Selected';
  },

  'setTicketOwner': function(IDTicket, IDSupporter, IDDepartment) {
    var tParams = {
      'enqueue':1,
      'method':'post',
      'content':{
        'IDSupporter':IDSupporter,
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'StAction': 'setOwner'
      },
      'okCallBack': function(htmlReturn){
        Ticket.reloadTicketList(IDDepartment,true,'show');
        var contentDisplay = gID('contentDisplay');
        appendHTML(htmlReturn, contentDisplay, true);
      }
    };
    var tUrl = templateDir + 'ticket.php';
    xhr.makeRequest('setTicketOwner',tUrl,tParams);
    return true;
  },

  'showDepartmentTickets': function(IDDepartment, Force) {
      baseActions.toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment, Force);
  },

  'showTicket': function(IDTicket, IDDepartment, Clicked) {
    var tParams = {
      'enqueue':1,
      'method':'post',
      'content':{
        'IDTicket':IDTicket,
        'IDDepartment': IDDepartment
      },
      'startCallBack':function() {
        baseActions.animateReload( IDDepartment, 'start' );
      },
      'okCallBack':function(returnedValue) {
        baseActions.animateReload( IDDepartment, 'stop' );
        var contentDisplay = gID('contentDisplay');
        removeChilds(contentDisplay);
        appendHTML(returnedValue, contentDisplay);
        Ticket.selectTicket(Clicked);
        Ticket.refreshNotReadCount( IDDepartment );
        if (IDDepartment == 'bookmark') {
          baseAction.selectFromSearch(IDTicket);
        }
      }
    };
    var tUrl = templateDir + 'ticket.php';
    xhr.makeRequest('showTicket', tUrl, tParams);
    return true;
  },

  'submitTicketForm': function() {
    if(gID('didSubmit').value == 'true') {
      gID('contentDisplay').innerHTML = gID('ajaxSubmit').contentWindow.document.getElementsByTagName('body')[0].innerHTML ;
    }
  },

  'unignoreTicket': function(IDSupporter,IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDSupporter':IDSupporter,
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'StAction':'unignore'
      },
      'okCallBack':function(htmlReturn) {
        var contentDisplay = gID('contentDisplay');
        Ticket.reloadTicketList('ignored',true,'show');
        Ticket.reloadTicketList(IDDepartment,true,'show');
        appendHTML(htmlReturn, contentDisplay, true);
      }
    };
    xhr.makeRequest('Unignore Ticket', templateDir + 'ticket.php',tParams);
  }
};

/**
 *  OBJECT CONTAIN METHODS FROM WRITING TEMPLATE
 */
var Writing = {

  'addReader': function(IDSupporter,StName) {
    for(var i in IDSupporter) {
      if(!gID('pR'+IDSupporter[i])) { // Se ainda nao existe
        /*Adicionando o ID no campo hidden*/
        if(! gID('ArReaders')) {
          gID('formCreate').appendChild( createElement('input',{
            'type':'hidden',
            'name':'ArReaders',
            'id':'ArReaders',
            'value':IDSupporter[i]
          }) );
        } else {
          gID('ArReaders').value += ',' + IDSupporter[i];
        }
        /*Agora, adicionando o nome à lista de exibição*/
        gID('addedReaders').appendChild( createElement('p',{
          'id':'pR'+IDSupporter[i],
          'style':'margin:0;padding:0;padding-bottom:5px;'
        }) );
        var aLink = createElement('a',{'href':'','onclick':"Writing.removeSupporter('Readers',"+"'pR"+IDSupporter[i]+"');"});
            aLink.appendChild( createElement('img',{
              'src':'templates/default/images/button_cancel.png',
              'style':'vertical-align:middle;padding-right:5px;'
            }) );
        var span = createElement('span',{'id':'respondto'+IDSupporter[i],'class':'supporterName'});
            span.appendChild( createTextNode(StName[i]) );
        gID('pR'+IDSupporter[i]).appendChild(aLink);  gID('pR'+IDSupporter[i]).appendChild(span);
        gID('addedReaders').className = '';
      }
    }
  },

  'addRecipient': function(IDSupporter,StName) {
    for(var i in IDSupporter) {
      if(!gID('p'+IDSupporter[i])) { // Se ainda nao existe
        /*Adicionando o ID no campo hidden*/
        if(! gID('ArRecipients')) {
          gID('formCreate').appendChild( createElement('input',{
            'type':'hidden',
            'name':'ArRecipients',
            'id':'ArRecipients',
            'value':IDSupporter[i]
          }) );
        } else {
          gID('ArRecipients').value += ',' + IDSupporter[i];
        }
        /*Agora, adicionando o nome à lista de exibição*/
        gID('addedRecipients').appendChild( createElement('p',{
          'id':'p'+IDSupporter[i],
          'style':'margin:0;padding:0;padding-bottom:5px;'
        }) );
        var aLink = createElement('a',{'href':'','onclick':"Writing.removeSupporter('Recipients',"+"'p"+IDSupporter[i]+"');"});
            aLink.appendChild( createElement('img',{
              'src':'templates/default/images/button_cancel.png',
              'style':'vertical-align:middle;padding-right:5px;'
            }) );
        var span = createElement('span',{'id':'respondto'+IDSupporter[i],'class':'supporterName'});
            span.appendChild( createTextNode(StName[i]) );
        gID('p'+IDSupporter[i]).appendChild(aLink);  gID('p'+IDSupporter[i]).appendChild(span);
        gID('addedRecipients').className = '';
      }
    }
  },

  'attachTicket': function(IDTicket) {
    Ticket.attachTicket(IDTicket);
  },

  'checkAdd': function(Type) {
    var combo = gID("supporters"), IDSupporter=[], StName=[], max=combo.options.length;
    for(var i=0;i<max;i++) {
      if(combo[i].selected === true) {
        IDSupporter.push(combo[i].value);
        StName.push(combo[i].innerHTML);
      }
    }
    if(combo.selectedIndex > -1) {
      if(Type == 'Recipients') {
        Writing.addRecipient(IDSupporter,StName);
      } else {
        Writing.addReader(IDSupporter,StName);
      }
    } else {
      flowWindow.alert(i18n.noSupporter);
    }
  },

  'createTicketSubmit': function() {
    if(gID('IDRecipient')[gID('IDRecipient').selectedIndex].value == 'null' && (! gID('ArRecipients') || gID('ArRecipients').value === '')) {
      gID('sendTo').appendChild( createElement('p',{'id':'Error','style':'color: red'}, [createTextNode(i18n.noRecipient)] ) );
      return false;
    }
  },

  'listSupporters': function(Type) {
    var tParams = {
      'method':'post',
      'content':{ 'StAction':'addSupporters' },
      'okCallBack':function(response) {
        var flowParams = new flowWindow.flowParams();
        flowParams.width = 410;
        flowParams.height = 260;
        flowParams.innerHTML = response;
        flowParams.TBStyle.Caption = i18n.addSupporter;
        Flow.open(flowParams);
      }
    };
    xhr.makeRequest('Add Supporters',templateDir + 'addSupporters.php',tParams);
    top.Type = Type;
  },

  'removeSupporter': function(Type,ID) {
    var currentIDs = gID('Ar'+Type).value.split(','); var newIDs=[];
    var deleteID = (Type == 'Recipients') ? ID.replace(/p/,'') : ID.replace(/pR/,'');
    for(var aux =0; aux<currentIDs.length; aux++) {
      if(currentIDs[aux] != deleteID) {
        newIDs[newIDs.length] = currentIDs[aux];
      }
    }
    gID('Ar'+Type).value = newIDs.join(','); removeElements(gID(ID));
    if(Type == 'Recipients') {
      if(gID('addedRecipients').getElementsByTagName('p').length === 0){
        gID('addedRecipients').className = 'Invisible';
        removeElements(gID('ArRecipients'));
      }
    } else {
      if(gID('addedReaders').getElementsByTagName('p').length === 0) {
        gID('addedReaders').className = 'Invisible';
        removeElements(gID('ArReaders'));
      }
    }
  }

};

var Search = {

};

var Admin = {

  'adminDir': templateDir + 'admin/' ,

  'addDepartment':function(){

    var combo = gID('IDDepartment');
    var IDDepartment = combo[combo.selectedIndex].value;

    if(combo[combo.selectedIndex].value == 'null') {
      flowWindow.alert(i18n.noDepartment);
      return false;
    }

    if(gID('p'+IDDepartment)) {
      return false;
    }

    if(gID('null')) {
      gID('null').className = 'Invisible';
    }

    if(gID('ArDepartments')) {
      gID('ArDepartments').value += ','+IDDepartment;
    } else {
      var hidden = createElement('input',{'type':'hidden','id':'ArDepartments','value':IDDepartment});
      document.body.appendChild(hidden);
    }

    var p = createElement('p',{'id':'p'+IDDepartment});
    var span = createElement('span');
    var link = createElement('a',{'href':'','onclick':'Admin.removeDepartment('+IDDepartment+');'});
    var img = createElement('img',{'src':templateDir+'images/button_cancel.png','style':'margin-right:5px;'});
    var text = createTextNode(combo[combo.selectedIndex].textContent);
    link.appendChild(img);
    span.appendChild(text);
    p.appendChild(link);
    p.appendChild(span);
    gID('Departments').appendChild(p);
  },

  'changeOption':function(StPage) {
    var tParams = {
      'method':'get',
      'okCallBack':function(response) {
        appendHTML(response,gID('contentAdminMenu'),true);
      }
    };
    xhr.makeRequest('Change Menu', Admin.adminDir + StPage,tParams);
  },

  'clearUserForm':function(){
    gID('StName').value = '';
    gID('StEmail').value = '';
    gID('StPassword').value = '';
    gID('IDDepartment').selectedIndex = 0;
    var fieldSet = gID('Departments');
    while( fieldSet.getElementsByTagName('p').length !== 0) {
      fieldSet.getElementsByTagName('p')[0].parentNode.removeChild( fieldSet.getElementsByTagName('p')[0] );
    }
    gID('null').className = '';
  },

  'editMenu':function() {
    var StName = gID('StNameEdit').value;
    var StAddress = gID('StAddressEdit').value;
    var StOldAddress = gID('StOldAddressEdit').value;
    StName = StName.replace(/\.php/,'');
    StAddress = StAddress.replace(/(\.php)|(\.html)|(\.htm)/,'');

    var content = { 'StAction':'editMenu', 'StName':StName, 'StAddress':StAddress, 'StOldAddress':StOldAddress };
    var tParams = {
      'method':'post',
      'content':content,
      'okCallBack':function(response) {
        Admin.editFromMenuList(StOldAddress, StName, StAddress);
        appendHTML(response,gID('contentAdminMenu'),true);
      }
    };
    xhr.makeRequest('Edit Menu', Admin.adminDir + 'manageMenus.php', tParams);
  },

  'hideEditMenu':function() {
    gID('editMenu').className += ' Invisible';
    gID('manageMenu').style.width = '100%';
    var table = gTN('table');
    table[0].style.width = '30%';
  },

  'editFromMenuList' : function (StOldAddress, StName, StAddress) {
    var Li = gID(StOldAddress);

    var A = gTN('a', Li).item(0);
    Li.setAttribute('id',StAddress);
    A.setAttribute('href','?page=' + StAddress);
    appendHTML(StName,A,true);
  },

  'insertInMenuList' : function(StName, StAddress) {
    var menuList = gID('menuList');
    var Html = '' +
      '<li id="' + StAddress+ '">' +
        '<span>' +
					'<a href="?page=' + StAddress + '">' +
						StName +
					'</a>' +
				'</span>' +
      '</li>';
    appendHTML(Html,menuList);
  },

  'insertMenu':function() {
    var StName = gID('StName').value;
    var StAddress = gID('StAddress').value;
    StName = StName.replace(/\.php/,'');
    StAddress = StAddress.replace(/(\.php)|(\.html)|(\.htm)/,'');
    var content = { 'StAction':'insertMenu', 'StName':StName, 'StAddress':StAddress };
    var tParams = {
      'method':'post',
      'content':content,
      'okCallBack':function(response) {
        Admin.insertInMenuList(StName,StAddress);
        appendHTML(response,gID('contentAdminMenu'),true);
      }
    };
    xhr.makeRequest('Insert Menu', Admin.adminDir + 'manageMenus.php', tParams);
  },

  'removeMenu':function(IDMenu) {
    var tFunction = function(ok) {
      if(ok) {
        var content = { 'StAction':'removeMenu', 'IDMenu':IDMenu };
        var tParams = {
          'method':'post',
          'content':content,
          'okCallBack':function(response) {
            removeElements(gID(IDMenu));
            appendHTML(response,gID('contentAdminMenu'),true);
          }
        };
        xhr.makeRequest('Remove Menu', Admin.adminDir + 'manageMenus.php',tParams);
      }
    };
    flowWindow.confirm(i18n.deleteMenu,tFunction);
  },

  'removeDepartment':function(IDDepartment) {
    if(!gID('p'+IDDepartment)) {
      return false;
    }
    var pattern = new RegExp(',?'+IDDepartment);
    gID('ArDepartments').value = gID('ArDepartments').value.replace(pattern,'');
    removeElements(gID('p'+IDDepartment));
    if(gID('Departments').getElementsByTagName('p').length === 0) {
      gID('null').className = '';
    }
  },

  'showEditMenu':function(IDMenu) {
    gID('StNameEdit').value = trim(gID(IDMenu).textContent);
    gID('StAddressEdit').value = IDMenu;
    gID('StOldAddressEdit').value = IDMenu;
    gID('manageMenu').style.width = '30%';
    var table = gTN('table');
    table[0].style.width = '100%';
    gID('editMenu').className = gID('editMenu').className.replace(/ ?Invisible ?/,'');
  },

  'startEditingDepartment': function(IDDepartment){
    gID('manageEditDepartment').className = 'Left';
    gID('StDepartmentEdit').value = gID('StDepartment'+IDDepartment).value;
    gID('StDescriptionEdit').value = gID('StDescription'+IDDepartment).value;
    gID('DepartmentID').value = IDDepartment;
  },
  
  'startEditingUnit': function(IDUnit){
    gID('manageEditUnit').className = 'Left';
    gID('StUnitEdit').value = gID('StUnit' + IDUnit).value;
    gID('BoAnswerEdit').checked = (gID('BoAnswer' + IDUnit).value=='1')?true:false;
    gID('BoAttachEdit').checked = (gID('BoAttachTicket' + IDUnit).value=='1')?true:false;
    gID('BoCreateEdit').checked = (gID('BoCreateTicket' + IDUnit).value=='1')?true:false;
    gID('BoDeleteEdit').checked = (gID('BoDeleteTicket' + IDUnit).value=='1')?true:false;
    gID('BoViewEdit').checked = (gID('BoViewTicket' + IDUnit).value=='1')?true:false;
    gID('BoReleaseEdit').checked = (gID('BoReleaseAnswer' + IDUnit).value=='1')?true:false;
    gID('BoMailErrorEdit').checked = (gID('BoMailError' + IDUnit).value=='1')?true:false;
    gID('BoCannedResponseEdit').checked = (gID('BoCannedResponse' + IDUnit).value=='1')?true:false;
    gID('UnitID').value = IDUnit;
  },

  'submitManageDepartment': function(StAction, IDDepartment){
    if(StAction == 'edit'){
      var tParams = {
        'method':'post',
        'content':{
          'StAction': 'editDepartment',
          'StDepartment': gID('StDepartmentEdit').value,
          'StDescription': gID('StDescriptionEdit').value,
          'IDDepartment': gID('DepartmentID').value
        },
        'okCallBack':function(response) {
          appendHTML(response, gID('adminWrapper'), true);
        }
      };
      xhr.makeRequest('Edit Department', this.adminDir + 'manageDepartments.php',tParams);
    } else if(StAction == 'create'){
      var tForm = gID('insertDepartmentForm');
      var tParams = {
        'method':'post',
        'content':{
          'StAction': 'createDepartment',
          'StDepartment': tForm.name.value,
          'StDescription': tForm.description.value,
          'IDSubDepartment': tForm.subOf.value
        },
        'okCallBack':function(response) {
          appendHTML(response, gID('adminWrapper'), true);
        }
      };
      xhr.makeRequest('Create Department', this.adminDir + 'manageDepartments.php',tParams);
    } else if(StAction == 'remove'){
      var tFunction = function(ok) {
        if(ok) {
          var tParams = {
            'method':'post',
            'content':{
              'StAction': 'removeDepartment',
              'IDDepartment': IDDepartment
            },
            'okCallBack':function(response) {
              appendHTML(response, gID('adminWrapper'), true);
            }
          };
          xhr.makeRequest('Remove Department', Admin.adminDir + 'manageDepartments.php',tParams);
        }
      }
      flowWindow.confirm(i18n.deleteDepartment,tFunction);
    } else {
      return false;
    }
  },
  
  'submitManageUnit': function(StAction, IDUnit){
    if (StAction == 'create'){
      var tForm = gID('insertUnitForm');
      var tParams = {
        'method':'post',
        'content':{
          'StAction': 'createUnit',
          'StUnit': tForm.name.value,
          'BoAnswer': (tForm.BoAnswer.checked)?'1':'0',
          'BoAttachTicket': (tForm.BoAttach.checked)?'1':'0',
          'BoCreateTicket': (tForm.BoCreate.checked)?'1':'0',
          'BoDeleteTicket': (tForm.BoDelete.checked)?'1':'0',
          'BoViewTicket': (tForm.BoView.checked)?'1':'0',
          'BoReleaseAnswer': (tForm.BoRelease.checked)?'1':'0',
          'BoMailError': (tForm.BoMailError.checked)?'1':'0',
          'BoCannedResponse': (tForm.BoCannedResponse.checked)?'1':'0'
        },
        'okCallBack':function(response) {
          appendHTML(response, gID('adminWrapper'), true);
        }
      };
      xhr.makeRequest('Create Unit', this.adminDir + 'manageUnits.php',tParams);
    } else if(StAction == 'edit'){
      var tParams = {
        'method':'post',
        'content':{
          'StAction': 'editUnit',
          'IDUnir': gID('UnitID'),
          'StUnit': gID('StUnitEdit').value,
          'BoAnswer': (gID('BoAnswerEdit').checked)?'1':'0',
          'BoAttachTicket': (gID('BoAttachEdit').checked)?'1':'0',
        },
        'okCallBack':function(response) {
          appendHTML(response, gID('adminWrapper'), true);
        }
      };
      xhr.makeRequest('Edit Unit', this.adminDir + 'manageUnits.php',tParams);
    } else {
      return false;
    }
  }

};

var flowWindow = {

  'alert': function(StArg) {
    var flowParams = new this.flowParams();
    with(flowParams) {
      width = 350;
      height = 175;
      TBStyle.Caption = i18n.flowAlertTitle;
      WindowStyle.Caption = '<br>';
      innerHTML = StArg + '<br><br>';
      Window = 'alert';
    }
    var ID = Flow.open(flowParams); return ID;
  },

  'confirm': function(StArg,tFunction) {
    var option = ''; var flowParams = new this.flowParams();
    with(flowParams) {
      width = 350;  height = 175;
      TBStyle.Caption = i18n.flowConfirmTitle;
      WindowStyle.Caption = '<br>';
      innerHTML = StArg + '<br><br>';
      Window = 'confirm';
      if(typeof(tFunction) == 'function') {
        EventFuncs.Confirm = tFunction;
      }
    }
    var ID = Flow.open(flowParams); return ID;
  },

  'flowParams': function(){
    this.y = Positions.getScrollOffSet(gTN('body')[0]).y + 50;
    this.x = Positions.getScrollOffSet(gTN('body')[0]).x + 200;
    this.width=350; this.height=250;
    this.definition='response';  this.innerHTML='TEXTO DA PAGINA';
    this.TB = true; this.Window = 'default';
    this.TBStyle = {
      'BackgroundColor': '#4F6C9C',
      'Color':'#fff',
      'Font':'12px verdana, sans-serif',
      'Image': '',
      'Caption': 'TEXTO CAPTION BARRA DE TITULO'
    };
    this.WindowStyle = {
      'BackgroundColor':'#ECEDEF',
      'BackgroundImage':'',
      'Caption':'TEXTO TITULO DA JANELA'
    };
    this.EventFuncs = {
      'Confirm':function(){ },
      'Prompt':function(){ },
      'Close':'',
      'Max':'',
      'Min':'',
      'Rest':''
    }
  },

  'previewAnswer': function(TxMessage, IDTicket, IDDepartment, StMessageType) {
    if(isEmpty(TxMessage)){ flowWindow.alert(i18n.answerPreviewNoAnswer); return false; }
    var  tParams = {
      'method':'post',
      'content':{
        'StAction':'previewAnswer',
        'TxMessage': TxMessage,
        'IDTicket': IDTicket,
        'IDDepartment': IDDepartment,
        'StMessageType': StMessageType
      },
      'okCallBack':function(response) {
        var flowParams = new flowWindow.flowParams();
        with(flowParams) {
          width = 480;  height = 350; innerHTML = response;
          TBStyle.Caption = i18n.answerPreviewTitle;
        }
        Flow.open(flowParams);
      }
    };
    xhr.makeRequest('preview Ticket', templateDir + 'ticketPreviewAnswer.php', tParams);
  },

  'previewCannedResponse': function(StTitle, TxMessage) {
    StTitle = unescape( StTitle );  TxMessage = unescape( TxMessage );
    var flowParams = new this.flowParams();
    with(flowParams){
      innerHTML = ''+
        '<table class="tableTickets">'+
          '<thead>'+
            '<th>'+i18n.cannedTableTitle+'</th>'+
          '</thead>'+
          '<tbody>'+
            '<td class="TicketNumber">'+ StTitle +'</td>'+
          '</tbody>'+
        '</table>'+
        '<br />'+
        '<table class="tableTickets">'+
          '<thead>'+
            '<th>'+i18n.cannedTableMessage+'</th>'+
          '</thead>'+
          '<tbody>'+
            '<td>'+ TxMessage +'</td>'+
          '</tbody>'+
        '</table>';
      TBStyle.Caption = StTitle; width = 550; height = 380;
    }
    var ID = Flow.open(flowParams);
  },

  'previewNote': function(StTitle, TxNote) {
    StTitle = unescape( StTitle );  TxNote = unescape( TxNote );
    var flowParams = new this.flowParams();
    with(flowParams){
      innerHTML = ''+
        '<table class="tableTickets">'+
          '<thead>'+
            '<th>'+i18n.noteTableTitle+'</th>'+
          '</thead>'+
          '<tbody>'+
            '<td class="TicketNumber">'+ StTitle +'</td>'+
          '</tbody>'+
        '</table>'+
        '<br />'+
        '<table class="tableTickets">'+
          '<thead>'+
            '<th>'+i18n.noteTableNote+'</th>'+
          '</thead>'+
          '<tbody>'+
            '<td>'+ TxNote +'</td>'+
          '</tbody>'+
        '</table>';
      TBStyle.Caption = StTitle; width = 550; height = 380;
    }
    var ID = Flow.open(flowParams);
  },

  'previewTicket': function(IDTicket, IDDepartment) {
    var tParams = {
      'method':'post',
      'content': {
        'IDTicket':IDTicket,
        'IDDepartment':IDDepartment,
        'preview':'true'
      },
      'okCallBack':function( ticketHTML ) {
        var flowParams = new flowWindow.flowParams();
        with(flowParams){
          innerHTML = '<span style="padding:10px">'+ticketHTML+'</span>';
          TBStyle.Caption = i18n.ticketPreviewTitle + IDTicket;
          width = 600; height = 450;
        }
        var ID = Flow.open(flowParams);
      }
    };
    xhr.makeRequest('Bookmark Ticket', templateDir + 'ticket.php',tParams);
  },

  'prompt': function(StArg, tFunction) {
    var flowParams = new this.flowParams();
    with(flowParams) {
      width = 350;  height = 175; innerHTML = ''; Window = 'prompt';
      if(typeof(tFunction) == 'function') {
        EventFuncs.Prompt = tFunction;
      }
    }
    var ID = Flow.open(windowParams); return ID;
  }

};