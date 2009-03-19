var initialized = [];
var windowParams;
_resetFlow();
function _resetFlow() {
  windowParams = {
    'y':Positions.getScrollOffSet(gTN('body')[0]).y + 200,
    'x':Positions.getScrollOffSet(gTN('body')[0]).x + 350,
    'width':350,
    'height':250,
    'definition': 'response',
    'innerHTML': 'TEXTO DA PAGINA',
    'TB': true,
    'Window': 'default',
    'TBStyle':{'BackgroundColor': '#4F6C9C','Color':'#fff','Font':'12px verdana, sans-serif', 'Image': '', 'Caption': 'TEXTO CAPTION BARRA DE TITULO'},
    'WindowStyle':{'BackgroundColor':'#ECEDEF','BackgroundImage':'','Caption':'TEXTO TITULO DA JANELA'},
    'EventFuncs':{
    'Confirm':function(){ },
    'Prompt':function(){ },
    'Close':'',
    'Max':'',
    'Min':'',
    'Rest':''
    }
  };
}

/* auxFunctions */
function _doLoading( formName, action ){
	setStyle(	gID(formName+'Loading'),	{
		'visibility': ( action=='hide' )?'hidden':'visible'
	});
}

function _isEmpty(Text){
  var TxEmpty; TxEmpty = Text.replace(/\s+|\s+/g,"");
  if(TxEmpty == ""){
    return true;
  } else {
    return false;
  }
}

function _br2nl(Text){
  Text = Text.split('<br />').join('\n');
  Text = Text.split('<br/>').join('\n');
  return Text.split('<br>').join('\n');
}
/* auxFunctions end*/

function flowAlert(StArg) {
  with(windowParams) {
    width = 350;
    height = 175;
    TBStyle.Caption = default_ptBR.flowAlertTitle;
    WindowStyle.Caption = '<br>';
    innerHTML = StArg + '<br><br>';
    Window = 'alert';
  }
  var ID = Flow.open(windowParams);
  _resetFlow();
  return ID;
}

function flowConfirm(StArg,tFunction) {
  var option = '';
  with(windowParams) {
    width = 350;
    height = 175;
    TBStyle.Caption = default_ptBR.flowConfirmTitle;
    WindowStyle.Caption = '<br>';
    innerHTML = StArg + '<br><br>';
    Window = 'confirm';
    if(typeof(tFunction) == 'function') {
      EventFuncs.Confirm = tFunction;
    }
  }
  var ID = Flow.open(windowParams);
  _resetFlow();
  return ID;
}

function flowPrompt(StArg, tFunction) {
  with(windowParams) {
    width = 350;
    height = 175;
    innerHTML = '';
    Window = 'prompt';
    if(typeof(tFunction) == 'function') {
      EventFuncs.Prompt = tFunction;
    }
  }
  var ID = Flow.open(windowParams);
  _resetFlow();
  return ID;
}


/**
 * change the visibility and the arrow image
 *
 * @param string StArrow
 * @param string StDivContent
 * @param string ForceAction
 */
function toogleArrow( StArrow, StDivContent, ForceAction ) {
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
}


/**
* makes the arrow alive
*
* @param int IDDepartment
* @param string action
*/
function animateReload( ID, action ) {
  var reload = gID('reload' + ID);
  if (reload !== null) {
    if (action == 'start') {
      reload.src= 'templates/default/images/animated-reload.gif';
    } else {
      reload.src = 'templates/default/images/btn_reload.png';
    }
  }
}


/**
* counts the number of not read tickets
*
* @param int IDDepartment
*/
function refreshNotReadCount( IDDepartment ){
  if (IDDepartment == 'closed' || IDDepartment == 'ignored') {
    return false;
  }
  var count = 0;
	var table = gID('ticketTable' + IDDepartment);
	var tbody = gTN('tbody',table)[0];
	var trs = gTN('tr',tbody);

	for (var i=0; i < trs.length; i++) {
	  if (trs[i].className.indexOf('notRead') !== -1) {
	    count += 1;
	  }
	}

	var element = gID('notReadCount' + IDDepartment);
	removeChilds(element);
	element.appendChild( createTextNode( count ) );
}


/**
* gets the html of the table and includes at the right department content
*
* @param string HTMLTickets
*/
function insertTickets(IDDepartment, HTMLTickets) {
  var departmentContent = gID( 'departmentContent' + IDDepartment );
  removeChilds(departmentContent);
  appendHTML(HTMLTickets, departmentContent);
}


/**
* gets the ticket of an specif department
*
* @param int IDDepartment
* @param string StUser
* @param bool First
*/
function reloadTicketList( IDDepartment, First, Force ){
	var tParams = {
    'enqueue':1,
    'returnType':'txt',
    'method':'post',
    'content':{'IDDepartment':IDDepartment},
    'startCallBack' : function() {
      animateReload( IDDepartment, 'start' );
    },
    'okCallBack':function(HTMLTickets) {
      animateReload( IDDepartment, 'stop' );
      insertTickets(IDDepartment, HTMLTickets);
      refreshNotReadCount( IDDepartment );
      if (First === true) { toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment, Force); }
    },
    'errCallBack':function(Return) {
      toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment, 'hide');
      animateReload( IDDepartment, 'stop' );
    }
  };
  var tUrl = 'listTickets.php';
  xhr.makeRequest('showTickets',tUrl,tParams);
}

function showDepartmentTickets( IDDepartment, StUser ) {
  if (!initialized[IDDepartment] || initialized[IDDepartment] === undefined) {
    initialized[IDDepartment] = true;
    reloadTicketList( IDDepartment, true );
  } else {
    toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment);
  }
}

function selectTicket(Clicked) {

  var div = gID('contentDepartments');
	var table = gTN('table',div);

	for (var i=0; i < table.length; i++) {
  	var tbody = gTN('tbody',table[i])[0];
  	var trs = gTN('tr',tbody);
  	var className = '';

  	for (var j=0; j < trs.length; j++) {
  	  if (trs[j].className.indexOf('notRead') !== -1) {
        className = 'notRead';
  	  } else {
  	    className = '';
  	  }

  	  if (j % 2 == 0) {
         trs[j].className = className + ' Alt';
  	  } else {
  	    trs[j].className = className;
  	  }
  	}
	}

	Clicked.className = 'Selected';
}

function findTicket(IDTicket) {
  var Ticket = gID('ticket1');
  if (Ticket && Ticket.parentNode.className.indexOf == 'notRead') {
    var TicketTable = Ticket.parentNode.parentNode.parentNode.id;
    var ID = TicketTable.split('ticketTable');
    reloadTicketList(ID[1]);
  }
}

function showCall( IDTicket, IDDepartment, Clicked ) {
   var tParams = {
    'enqueue':1,
    'method':'post',
    'content':{'IDTicket':IDTicket},
    'startCallBack':function() {
      animateReload( IDDepartment, 'start' );
    },
    'okCallBack':function(returnedValue) {
      animateReload( IDDepartment, 'stop' );
      var contentDisplay = gID('contentDisplay');
      removeChilds(contentDisplay);
      appendHTML(returnedValue, contentDisplay);
      selectTicket(Clicked);
      refreshNotReadCount( IDDepartment );
      if (IDDepartment == 'bookmark') {
        findTicket(IDTicket);
      }
    }
  };
  var tUrl = 'ticketDetails.php';
  xhr.makeRequest('refreshCall',tUrl,tParams);
  return true;
}

function refreshCall( IDTicket ) {
  var tParams = {
    'enqueue':1,
    'method':'post',
    'content':{'IDTicket':IDTicket},
    'startCallBack':function() {
      animateReload( 'Header', 'start' );
    },
    'okCallBack':function(returnedValue) {
      animateReload( 'Header', 'stop' );
      var contentDisplay = gID('contentDisplay');
      removeChilds(contentDisplay);
      appendHTML(returnedValue, contentDisplay);
    }
  };
  var tUrl = 'ticketDetails.php';
  xhr.makeRequest('refreshCall',tUrl,tParams);
  return true;
}

function setTicketOwner(IDTicket, IDSupporter) {
  var IDDepartment;
  var tParams = {
    'enqueue':1,
    'method':'post',
    'content':{'IDSupporter':IDSupporter, 'IDTicket':IDTicket},
    'okCallBack': function(returnedValue){
      var IDDepartment = gID('IDDepartment').value;
      refreshCall( IDTicket );
    }
  };
  var tUrl = 'setTicketOwner.php';
  xhr.makeRequest('setTicketOwner',tUrl,tParams);

  return true;
}



/**
 * Templates->HOME
 */
function startCreatingElement ( StElement ) {
	var editForm = gID(StElement + "Form");
	for (var aux = 0; aux < editForm.elements.length; aux++) {
		editForm.elements[aux].value = "";
	}
	gID(StElement+'FormButton').value = "Criar";
	toogleArrow( StElement + 'Arrow', StElement+'BoxEditAreaContent', 'show');
}


function submitForm (formName, action) {
	if(!action || !formName){
		flowAlert(default_ptBR.noAction); return false;
	} else {
		if( formName == 'canned' ){	// if form is cannedForm
			switch (action){
				case "Editar":	// if action is edit
					editCannedResponse();
				break;

				case "Criar": // if action is create
					newCannedResponse();
				break;
			}
		} else if( formName == 'note' ){ // if form is noteForm
			switch (action){
				case "Editar": // if action is edit
					editNote();
				break;

				case "Criar": // if action is create
					newNote();
				break;
			}
		}
	}
}


function newNote(){
	_doLoading( 'note', 'show' );
	var editForm = gID("noteForm");
	var content = {
		'action':'insert',
		'IDNote': 'autoincrement',
		'StTitle': editForm.elements['StTitle'].value,
		'TxNote': editForm.elements['TxNote'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( htmlCallBack ){
    	gID('noteTable').getElementsByTagName('tbody')[0].innerHTML += htmlCallBack;
    	/*Testando se exista a coluna "nao ha respostas"*/
    	var noNote = gID( 'noNote' );
    	if( noNote ){	removeElements(noNote);	}
    	toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide');
    	_doLoading( 'note','hide' );
    }
  };
	var tUrl = 'notesAction.php';
	xhr.makeRequest('newNote',tUrl,tParams);
}


function newCannedResponse(){
	_doLoading( 'canned','show' );
	var editForm = gID("cannedForm");
	var content = {
		'action':'insert',
		'IDCannedResponse': 'autoincrement',
		'StTitle': editForm.elements['StTitle'].value,
		'TxMessage': editForm.elements['TxCannedResponse'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( htmlCallBack ){
    	gID('cannedTable').getElementsByTagName('tbody')[0].innerHTML += htmlCallBack;
    	/*Testando se exista a coluna "nao ha respostas"*/
    	var noCanned = gID('noCanned');
    	if( noCanned ){	removeElements(noCanned);	}
    	toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
    	_doLoading( 'canned','hide' );
    }
  };
	var tUrl = 'cannedResponsesAction.php';
	xhr.makeRequest('newCannedResponse',tUrl,tParams);
}


function startEditElement ( formName, IDMessage ){
	toogleArrow( formName+'Arrow', formName+'BoxEditAreaContent', 'show');
	var editForm = gID(formName + 'Form');
	if( formName == 'canned' ){
		editForm.elements['IDCanned'].value = IDMessage;	/*ID*/
		editForm.elements['StTitle'].value = unescape(gID('StCannedTitle'+IDMessage).value);	/*StTitle*/
		editForm.elements['TxCannedResponse'].value = unescape(gID('TxCannedResponse'+IDMessage).value);	/*TxMessage*/
	} else if( formName == 'note' ){
		editForm.elements['IDNote'].value = IDMessage;	/*ID*/
		editForm.elements['StTitle'].value = unescape(gID('StNoteTitle'+IDMessage).value);	/*StTitle*/
		editForm.elements['TxNote'].value = unescape(gID('TxNote'+IDMessage).value);	/*TxMessage*/
	}
	gID(formName + 'FormButton').value = "Editar";
}


function editCannedResponse () {
	var editForm = gID("cannedForm");
	if(editForm.elements['IDCanned'].value == ""){
		flowAlert(default_ptBR.noCannedSelected); return false;
	}
	_doLoading( 'canned','show' );
	var content = {
  	'action':'edit',
  	'IDCannedResponse': editForm.elements['IDCanned'].value,
  	'StTitle': editForm.elements['StTitle'].value,
  	'TxMessage': editForm.elements['TxCannedResponse'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function(htmlEdited){
    	gID('cannedTR'+content.IDCannedResponse).innerHTML = htmlEdited;
    	_doLoading( 'canned','hide' );
    	toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
    }
  };
  var tUrl = 'cannedResponsesAction.php';
  xhr.makeRequest('editCannedResponse',tUrl,tParams);
}


function editNote () {
	var editForm = gID("noteForm");
	if(editForm.elements['IDNote'].value == ""){
		flowAlert(default_ptBR.noNoteSelected); return false;
	}
	_doLoading( 'note','show' );
	var content = {
  	'action':'edit',
  	'IDNote': editForm.elements['IDNote'].value,
  	'StTitle': editForm.elements['StTitle'].value,
  	'TxNote': editForm.elements['TxNote'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( htmlEdited ){
    	gID('noteTR'+content.IDNote).innerHTML = htmlEdited;
    	_doLoading( 'note','hide' );
    	toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide');
    }
  };
  var tUrl = 'notesAction.php';
  xhr.makeRequest('editNote',tUrl,tParams);
}


function removeCannedResponse (IDCannedResponse) {
	if(!IDCannedResponse){
		flowAlert(default_ptBR.noCannedID);
	}
	var tFunction = function(opt) {
  	if (opt == 1) {
  		_doLoading( 'canned','show' );
  		var tParams = {
  	    'enqueue':1,
  	    'method':'post',
  	    'content':{
  	    	'action':'remove',
  	    	'IDCannedResponse': IDCannedResponse,
  	    },
  	    'okCallBack': function(returnedValue){
  	    	if(returnedValue == 'error'){
  	    		flowAlert(default_ptBR.wrongCannedID+IDCannedResponse);
  	    	} else {
  	    		removeElements( gID('cannedTR'+IDCannedResponse) );
  	    		if( gID('cannedTable').getElementsByTagName('TR').length == 0){
  	    			gID('cannedTable').appendChild( createElement('TR',{'id':'noCanned'},
  	    				createElement('TD',{
  	    					'colspan':'3',	'align':'center'
  	    				},default_ptBR.noCanned)
  	    			) );
  	    		}
  	    		_doLoading( 'canned','hide' );
  	    		toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
  	    	}
  	    }
  	  };
  	  var tUrl = 'cannedResponsesAction.php';
  	  xhr.makeRequest('removeCannedResponse',tUrl,tParams);
  	}
  }
  flowConfirm(default_ptBR.deleteCanned,tFunction);
}


function removeNote (IDNote) {
	if(!IDNote){
		flowAlert(default_ptBR.noNoteID);
	}
	tFunction = function(opt) {
  	if (opt == 1) {
  		_doLoading( 'note','show' );
  		var tParams = {
  	    'enqueue':1,
  	    'method':'post',
  	    'content':{
  	    	'action':'remove',
  	    	'IDNote': IDNote,
  	    },
  	    'okCallBack': function(returnedValue){
  	    	if(returnedValue == 'error'){
  	    		flowAlert(default_ptBR.wrongNoteID+IDNote);
  	    	} else {
  	    		removeElements( gID('noteTR'+IDNote) );
  	    		if( gID('noteTable').getElementsByTagName('TR').length == 0){
  	    			gID('noteTable').appendChild( createElement('TR',{'id':'noNote'},
  	    				createElement('TD',{
  	    					'colspan':'3',	'align':'center'
  	    				},default_ptBR.noNote)
  	    			) );
  	    		}
  	    		_doLoading( 'note','hide' );
  	    		toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide');
  	    	}
  	    }
  	  };
  	  var tUrl = 'notesAction.php';
  	  xhr.makeRequest('removeNote',tUrl,tParams);
  	}
  };
  flowConfirm(default_ptBR.deleteNote,tFunction);
}


function startDataEdit(){
	toogleArrow('dataArrow', 'dataBoxEditAreaContent');
	var dataForm = gID('dataForm');
	dataForm.elements['StDataName'].value = unescape(gID('StDataName').value);
	dataForm.elements['StDataEmail'].value = unescape(gID('StDataEmail').value);
	dataForm.elements['StDataNotify'][gID('StDataNotify').value].checked = 'true';
	dataForm.elements['TxDataHeader'].value = unescape(gID('TxDataHeader').value);
	dataForm.elements['TxDataSign'].value = unescape(gID('TxDataSign').value);
}


function updateInformations(){
  _doLoading('data','show');  toogleArrow('dataArrow', 'dataBoxEditAreaContent','hide');
	var dataForm = gID('dataForm');
	var content = {
		'StName': dataForm.elements['StDataName'].value,
		'StEmail': dataForm.elements['StDataEmail'].value,
		'BoNotify': (dataForm.elements['StDataNotify'][0].checked)?0:1,
		'TxHeader': dataForm.elements['TxDataHeader'].value,
		'TxSign': dataForm.elements['TxDataSign'].value
  };

	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( requestResponse ){
    	if(requestResponse == 'sucess'){
				var dataForm = gID('dataForm');
				var TxHeader = dataForm.elements['TxDataHeader'].value;
				var TxSign = dataForm.elements['TxDataSign'].value;
				/*Update TD's*/
				gID('StDataNameTD').getElementsByTagName('pre')[0].textContent = dataForm.elements['StDataName'].value;
				gID('StDataEmailTD').getElementsByTagName('pre')[0].textContent = dataForm.elements['StDataEmail'].value;
				gID('StDataNotifyTD').getElementsByTagName('pre')[0].textContent = (dataForm.elements['StDataNotify'][0].checked)?'Não':'Sim';
				gID('TxDataHeaderTD').getElementsByTagName('pre')[0].innerHTML = (!TxHeader)?'<i>'+default_ptBR.empty+'</i>':TxHeader;
				gID('TxDataSignTD').getElementsByTagName('pre')[0].innerHTML = (!TxSign)?'<i>'+default_ptBR.empty+'</i>':TxSign;
				/*Update Hiddens*/
				gID('StDataName').value = escape(dataForm.elements['StDataName'].value);
				gID('StDataEmail').value = escape(dataForm.elements['StDataEmail'].value);
				gID('StDataNotify').value = (dataForm.elements['StDataNotify'][0].checked)?'0':'1';
				gID('TxDataHeader').value = escape(TxHeader);
				gID('TxDataSign').value = escape(TxSign);
				_doLoading('data','hide');
    	} else {
   			flowAlert(default_ptBR.updateError);
    	}
    }
  };
	var tUrl = 'userData.submit.php';
	xhr.makeRequest('newCannedResponse',tUrl,tParams);
}


function removeBookmark (IDTicket) {
	if(!IDTicket){
		flowAlert(default_ptBR.noBookmarkID);
	}
	tFunction = function(opt) {
  	if (opt == 1) {
  		_doLoading( 'bookmark','show' );
  		var tParams = {
  	    'enqueue':1,
  	    'method':'post',
  	    'content':{
  	    	'action':'remove',
  	    	'IDTicket': IDTicket,
  	    },
  	    'okCallBack': function(returnedValue){
  	    	if(returnedValue == 'error'){
  	    		flowAlert(default_ptBR.wrongBookmarkID+IDTicket);
  	    	} else {
  	    		removeElements( gID('bookmarkTR'+IDTicket) );
  	    		if( gID('bookmarkTable').getElementsByTagName('TR').length == 0){
  	    			gID('bookmarkTable').appendChild( createElement('TR',{'id':'noBookmark'},
  	    				createElement('TD',{
  	    					'colspan':'3',	'align':'center'
  	    				},default_ptBR.noBookmark)
  	    			) );
  	    		}
  	    		_doLoading( 'bookmark','hide' );
  	    	}
  	    }
  	  };
  	  var tUrl = 'bookmarkAction.php';
  	  xhr.makeRequest('removeBookmark',tUrl,tParams);
  	}
  };
	flowConfirm(default_ptBR.deleteBookmark,tFunction);
}
/**
 * Templates->HOME END
 */



/**
 * Templates->Ticket
 */
function submitTicketForm(IDTicket) {
  gID('StMessageType').selectedIndex = 0;
  gID('TxMessage').value = '';
  gID('Attachment').value = '';
  refreshCall(IDTicket);
}


function addCannedResponse(IDDepartment,IDSupporter) {
  var Responses = gID('cannedAnswers');
  var TxMessage = Responses[Responses.selectedIndex].value
  gID('TxMessage').value += _br2nl(unescape(TxMessage)) + '\n';
  return false;
}


function ignoreTicket(IDSupporter,IDTicket) {
  var tParams = {
    'method':'post',
    'content': {
      'IDSupporter':IDSupporter,
      'IDTicket':IDTicket,
      'StAction':'ignore',
    },
    'okCallBack':function(response) {
      if(response == 'ok') {
        var department = gID('IDDepartment').value;
        reloadTicketList('ignored',true,'show');
        reloadTicketList(department,false);
        refreshCall(IDTicket);
      } else {
        flowAlert(response);
      }
    }
  };
  tFunction = function(opt) {
    if(opt == 1) {
      xhr.makeRequest('Ignore Ticket','ticketActions.php',tParams);
    }
  }
  flowConfirm(default_ptBR.ignoreCall,tFunction);
}


function unignoreTicket(IDSupporter,IDTicket) {
  var tParams = {
    'method':'post',
    'content': {
      'IDSupporter':IDSupporter,
      'IDTicket':IDTicket,
      'StAction':'unignore',
    },
    'okCallBack':function(response) {
      if(response == 'ok') {
        var department = gID('IDDepartment').value;
        reloadTicketList('ignored',false);
        reloadTicketList(department,false);
        refreshCall(IDTicket);
      } else {
        flowAlert(response);
      }
    }
  };
  xhr.makeRequest('Unignore Ticket','ticketActions.php',tParams);
}


function bookmarkTicket(IDSupporter, IDTicket) {
  var tParams = {
    'method':'post',
    'content': {
      'IDSupporter':IDSupporter,
      'IDTicket':IDTicket,
      'StAction':'bookmark',
    },
    'okCallBack':function(response) {
      if(response == 'ok') {
        reloadTicketList('bookmark',true, 'show');
      } else {
        flowAlert(response);
      }
    }
  };
  xhr.makeRequest('Bookmark Ticket','ticketActions.php',tParams);
}


function attachTicket(IDTicket) {
  with(windowParams) {
    width = 350;
    height = 175;
    innerHTML = '#';
    Window = 'prompt';
    EventFuncs = {
      'Prompt': function(IDAttached) {
        IDAttached = IDAttached.replace(/[#]|[^0-9]/g,'');
        if(IDAttached == '') { return false; }
        if(typeof(IDTicket) == 'undefined') {
          if(!gID('ArAttached')) {
            var hidden = createElement('input',{'type':'hidden','id':'ArAttached','name':'ArAttached','value':IDAttached});
            gID('formCreate').appendChild(hidden);
          } else {
            var hidden = gID('ArAttached');
            hidden.value += ',' + IDAttached;
          }
          var p = createElement('p',{'id':'attachedTickets','style':'margin:0;padding:0;padding-bottom:5px;'});
          gID('AttachedTickets').appendChild(p);
          var span = createElement('span',{'id':'ticket','class':'supporterName'});
          var textNode = createTextNode('#'+IDAttached);
          span.appendChild(textNode);
          p.appendChild(span);
          gID('AttachedTickets').className = '';
        } else {
          var tParams = {
            'method':'post',
            'content': {
              'IDTicket':IDTicket,
              'IDAttached':IDAttached,
              'StAction':'attach',
            },
            'okCallBack':function(response) {
              if(response == 'ok') {
                refreshCall(IDTicket);
              } else {
                flowAlert(response);
              }
            }
          };
          xhr.makeRequest('Attach Ticket','ticketActions.php',tParams);
        }
      }
    };
  }
  var ID = Flow.open(windowParams);
  _resetFlow();
}

function changeDepartment(IDTicket, IDDepartment) {
  tParams = {
    'method':'post',
    'content': {
      'IDDepartment':IDDepartment,
      'IDTicket':IDTicket,
      'StAction':'change'
    },
    'okCallBack':function(response) {
      if(response == 'ok') {
        refreshCall(IDTicket);
        reloadTicketList(IDDepartment,true);
      } else {
        flowAlert(response);
      }
    }
  };
  xhr.makeRequest('Change Department','ticketActions.php',tParams);
}
/**
 * Templates->Ticket END
 */


/**
 * PREVIEWS => All functions that open previews in Flow's Library
 */
var previewInFlow = {

  'CannedResponse': function(StTitle, TxMessage) {
  	StTitle = unescape( StTitle );	TxMessage = unescape( TxMessage );
  	windowParams.innerHTML = ''+
  		'<table class="tableTickets">'+
  			'<thead>'+
  				'<th>'+default_ptBR.cannedTableTitle+'</th>'+
  			'</thead>'+
  			'<tbody>'+
  				'<td class="TicketNumber">'+ StTitle +'</td>'+
  			'</tbody>'+
  		'</table>'+
  		'<br />'+
  		'<table class="tableTickets">'+
  			'<thead>'+
  				'<th>'+default_ptBR.cannedTableMessage+'</th>'+
  			'</thead>'+
  			'<tbody>'+
  				'<td>'+ TxMessage +'</td>'+
  			'</tbody>'+
  		'</table>';
  	windowParams.TBStyle.Caption = StTitle;
  	windowParams.width = 550; windowParams.height = 380;
  	windowParams.y = Positions.getScrollOffSet(gTN('body')[0]).y + 50;
    windowParams.x = Positions.getScrollOffSet(gTN('body')[0]).x + 200;
    var ID = Flow.open(windowParams);
  },

  'Note': function(StTitle, TxNote) {
  	StTitle = unescape( StTitle );	TxNote = unescape( TxNote );
  	windowParams.innerHTML = ''+
  		'<table class="tableTickets">'+
  			'<thead>'+
  				'<th>'+default_ptBR.noteTableTitle+'</th>'+
  			'</thead>'+
  			'<tbody>'+
  				'<td class="TicketNumber">'+ StTitle +'</td>'+
  			'</tbody>'+
  		'</table>'+
  		'<br />'+
  		'<table class="tableTickets">'+
  			'<thead>'+
  				'<th>'+default_ptBR.noteTableNote+'</th>'+
  			'</thead>'+
  			'<tbody>'+
  				'<td>'+ TxNote +'</td>'+
  			'</tbody>'+
  		'</table>';
  	windowParams.TBStyle.Caption = StTitle;
  	windowParams.width = 550; windowParams.height = 380;
  	windowParams.y = Positions.getScrollOffSet(gTN('body')[0]).y + 50;
    windowParams.x = Positions.getScrollOffSet(gTN('body')[0]).x + 200;
    var ID = Flow.open(windowParams);
  },

  'Ticket': function(IDTicket) {
  	var tParams = {
      'method':'post',
      'content': {
        'IDTicket':IDTicket,
        'preview':'true'
      },
      'okCallBack':function( ticketHTML ) {
        windowParams.innerHTML = '<span style="padding:10px">'+ticketHTML+'</span>';
        windowParams.TBStyle.Caption = default_ptBR.ticketPreviewTitle + IDTicket;
        windowParams.y = Positions.getScrollOffSet(gTN('body')[0]).y + 50;
        windowParams.x = Positions.getScrollOffSet(gTN('body')[0]).x + 200;
        windowParams.width = 600; windowParams.height = 450;
        var ID = Flow.open(windowParams);
      }
    };
    xhr.makeRequest('Bookmark Ticket','ticketDetails.php',tParams);
  },

  'Answer': function(TxMessage) {
    if(_isEmpty(TxMessage)){ flowAlert(default_ptBR.answerPreviewNoAnswer); return false; }
    var  tParams = {
      'method':'post',
      'content':{
        'action':'preview',
        'TxMessage': TxMessage
      },
      'okCallBack':function(response) {
        with(windowParams) {
          y = Positions.getScrollOffSet(gTN('body')[0]).y + 100;
          x = Positions.getScrollOffSet(gTN('body')[0]).x + 250;
          width = 480;
          height = 350;
          innerHTML = response;
          TBStyle.Caption = default_ptBR.answerPreviewTitle;
        }
        Flow.open(windowParams);
      }
    };
    xhr.makeRequest('preview Ticket','answerTicket.php',tParams);
  }

};
/**
 * END PREVIEWS
 */

function checkAdd(Type) {
  var combo = gID("supporters"); IDSupporter = []; StName = []; max = combo.options.length; exit = false;
  for(var i=0;i<max;i++) {
    if(combo[i].selected == true) {
      if(!gID('p'+combo[i].value) && Type == 'Recipients') {
          IDSupporter.push(combo[i].value);
          StName.push(combo[i].textContent);
      } else if(!gID('pR'+combo[i].value) && Type == 'Readers') {
          IDSupporter.push(combo[i].value);
          StName.push(combo[i].textContent);
      } else {
        exit = true;
        break;
      }
    }
  }
  if(exit == false) {
    if(combo.selectedIndex > -1) {
      if(Type == 'Recipients') {
        addRecipient(IDSupporter,StName);
      } else {
        addReader(IDSupporter,StName);
      }
    } else {
      flowAlert(default_ptBR.noSupporter);
    }
  }
}

function addReader() {
  var hidden;

  if(! gID('ArReaders')) {
    hidden = createElement('input',{'type':'hidden','name':'ArReaders','id':'ArReaders','value':IDSupporter});
    gID('formCreate').appendChild(hidden);
  } else {
    hidden = gID('ArReaders');
    hidden.value = hidden.value + ',' + IDSupporter;
  }
  for(i in IDSupporter) {
    p = createElement('p',{'id':'pR'+IDSupporter[i],'style':'margin:0;padding:0;padding-bottom:5px;'});
    gID('addedReaders').appendChild(p);
    img = createElement('img',{'src':'templates/default/images/button_cancel.png','style':'vertical-align:middle;padding-right:5px;'});
    a = createElement('a',{'href':'javascript:void(0);','onclick':"removeSupporter('Readers',"+"'pR"+IDSupporter[i]+"');"})
    a.appendChild(img);
    span = createElement('span',{'id':'respondto'+IDSupporter[i],'class':'supporterName'})
    textNode = createTextNode(StName[i]);
    span.appendChild(textNode);
    gID('pR'+IDSupporter[i]).appendChild(a);
    gID('pR'+IDSupporter[i]).appendChild(span);
    gID('addedReaders').className = '';
  }
}

function addRecipient(IDSupporter,StName) {
  var hidden;

  if(! gID('ArRecipients')) {
    hidden = createElement('input',{'type':'hidden','name':'ArRecipients','id':'ArRecipients','value':IDSupporter});
    gID('formCreate').appendChild(hidden);
  } else {
    hidden = gID('ArRecipients');
    hidden.value = hidden.value + ',' + IDSupporter;
  }
  for(i in IDSupporter) {
    p = createElement('p',{'id':'p'+IDSupporter[i],'style':'margin:0;padding:0;padding-bottom:5px;'});
    gID('addedRecipients').appendChild(p);
    img = createElement('img',{'src':'templates/default/images/button_cancel.png','style':'vertical-align:middle;padding-right:5px;'});
    a = createElement('a',{'href':'javascript:void(0);','onclick':"removeSupporter('Recipients',"+"'p"+IDSupporter[i]+"');"})
    a.appendChild(img);
    span = createElement('span',{'id':'respondto'+IDSupporter[i],'class':'supporterName'})
    textNode = createTextNode(StName[i]);
    span.appendChild(textNode);
    gID('p'+IDSupporter[i]).appendChild(a);
    gID('p'+IDSupporter[i]).appendChild(span);
    gID('addedRecipients').className = '';
  }
}

function listSupporters(Type) {
  var tParams = {
    'method':'get',
    'okCallBack':function(response) {
      with(windowParams) {
        width = 410;
        height = 260;
        innerHTML = response;
        TBStyle.Caption = default_ptBR.addSupporter;
      }
      Flow.open(windowParams);
    }
  };
  xhr.makeRequest('Add Supporters','listSupporters.php',tParams);
  top.Type = Type;
}

function removeSupporter(Type,ID) {
  var ID2 ='',p = false, value = '', pattern = '';
  var hidden = gID('Ar'+Type);
  ID2 = (Type == 'Recipients') ? ID.replace(/p/,'') : ID.replace(/pR/,'');
  value = gID('Ar'+Type).value
  pattern = new RegExp('('+ID2+',?)|(,?'+ID2+')');
  hidden.value = value.replace(pattern,'');
  removeElements(gID(ID));
  if(Type == 'Recipients') {
    for(i in gID('addedRecipients').childNodes) {
      if(gID('addedRecipients').childNodes[i] == '[object HTMLParagraphElement]') {
        var p = true;
      }
    }
    if(p == false) {
      gID('addedRecipients').className = 'Invisible';
      removeElements(hidden);
    }
  } else {
    for(i in gID('addedReaders').childNodes) {
      if(gID('addedReaders').childNodes[i] == '[object HTMLParagraphElement]') {
        var p = true;
      }
    }
    if(p == false) {
      gID('addedReaders').className = 'Invisible';
      removeElements(hidden);
    }
  }
}

function createTicketSubmit() {

  if(gID('IDRecipient')[gID('IDRecipient').selectedIndex].value == 'null' && (! gID('ArRecipients') || gID('ArRecipients').value == '')) {
    var p = createElement('p',{'id':'Error','style':'color: red'});
    var textError = createTextNode(default_ptBR.noRecipient);
    p.appendChild(textError);
    gID('sendTo').appendChild(p);
    return false;
  }
}