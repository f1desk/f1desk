var initialized = [];


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
function reloadTicketList( IDDepartment, First ){
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
      if (IDDepartment !== 'closed' && IDDepartment !== 'ignored') { refreshNotReadCount( IDDepartment ); }
      if (First === true) { toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment); }
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
	var table = gTN('table');

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
      refreshNotReadCount( IDDepartment )
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
      reloadTicketList(IDDepartment);
    }
  };
  var tUrl = 'setTicketOwner.php';
  xhr.makeRequest('setTicketOwner',tUrl,tParams);

  return true;
}

/* CannedResponses - Templates->HOME */
function startEditResponse ( IDResponse ){
	var editForm = gID("cannedForm");
	editForm.elements['IDCanned'].value = IDResponse;	/*ID*/
	editForm.elements['StAlias'].value = gID('StAlias'+IDResponse).value;	/*StAlias*/
	editForm.elements['StTitle'].value = gID('StTitle'+IDResponse).value;	/*StTitle*/
	editForm.elements['TxCannedResponse'].value = gID('TxCannedResponse'+IDResponse).value;	/*TxMessage*/
	gID('cannedFormButton').value = "Editar";
}

function submitCannedResponse (action) {
	if(!action){
		alert('Sem ações para executar'); return false;
	} else {
		switch (action){
			case "Editar":
				editCannedResponse();
			break;
	
			case "Criar":
				newCannedResponse();
			break;
		}
	}
}

function editCannedResponse () {
	var editForm = gID("cannedForm");
	if(editForm.elements['IDCanned'].value == ""){
		alert("Selecione uma resposta para editar."); return false;
	}
	setStyle(gID('loadingRequest'),{'visibility':'visible'});
	var content = {
  	'action':'edit',
  	'IDCannedResponse': editForm.elements['IDCanned'].value,
  	'StAlias': editForm.elements['StAlias'].value,
  	'StTitle': editForm.elements['StTitle'].value,
  	'TxMessage': editForm.elements['TxCannedResponse'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function(returnedValue){
    	var tableTDs = gID('cannedTR'+content.IDCannedResponse).getElementsByTagName('TD');
    	tableTDs[0].textContent = content.StAlias;
    	tableTDs[0].appendChild( createElement('input',{
    		'type':'hidden', 'id':'StAlias'+content.IDCannedResponse, 'value':content.StAlias
    	}) );
    	tableTDs[1].textContent = content.StTitle;
    	tableTDs[1].appendChild( createElement('input',{
    		'type':'hidden', 'id':'StTitle'+content.IDCannedResponse, 'value':content.StTitle
    	}) );
    	gID('TxCannedResponse'+content.IDCannedResponse).value = content.TxMessage;
    	setStyle(gID('loadingRequest'),{'visibility':'hidden'});
    	toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
    }
  };
  var tUrl = 'cannedResponsesAction.php';
  xhr.makeRequest('editCannedResponse',tUrl,tParams);
}

function removeCannedResponse (IDCannedResponse) {
	if(!IDCannedResponse){
		alert('Erro: ID da resposta não informado');
	}
	if (confirm("Excluir resposta definitivamente?")) {
		setStyle(gID('loadingRequest'),{'visibility':'visible'});
		var tParams = {
	    'enqueue':1,
	    'method':'post',
	    'content':{
	    	'action':'remove',
	    	'IDCannedResponse': IDCannedResponse,
	    },
	    'okCallBack': function(returnedValue){
	    	if(returnedValue == 'error'){
	    		alert('Erro: Nenhuma resposta removida com o ID '+IDCannedResponse);
	    	} else {
	    		removeElements( gID('cannedTR'+IDCannedResponse) );
	    		if( gID('cannedTable').getElementsByTagName('TR').length == 0){
	    			gID('cannedTable').appendChild( createElement('TR',{'id':'noCanned'},
	    				createElement('TD',{
	    					'colspan':'3',	'align':'center'
	    				},'Não há respostas cadastradas.')
	    			) );
	    		}
	    		setStyle(gID('loadingRequest'),{'visibility':'hidden'});
	    		toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
	    	}
	    }
	  };
	  var tUrl = 'cannedResponsesAction.php';
	  xhr.makeRequest('removeCannedResponse',tUrl,tParams);
	}
}

function startCreateCannedResponse () {
	var editForm = gID("cannedForm");
	for (var aux = 0; aux < editForm.elements.length; aux++) {
		editForm.elements[aux].value = "";
	}
	gID('cannedFormButton').value = "Criar";
	toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'show');
}

function newCannedResponse(){
	setStyle(gID('loadingRequest'),{'visibility':'visible'});
	var editForm = gID("cannedForm");
	var content = {
		'action':'insert',
		'IDCannedResponse': 'autoincrement',
		'StAlias': editForm.elements['StAlias'].value,
		'StTitle': editForm.elements['StTitle'].value,
		'TxMessage': editForm.elements['TxCannedResponse'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( IDCannedResponseInserted ){
    	_updateTableRow(IDCannedResponseInserted, content);
    	/*Testando se exista a coluna "nao ha respostas"*/
    	var noCanned = gID('noCanned');
    	if( noCanned ){	removeElements(noCanned);	}
    	toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
    	setStyle(gID('loadingRequest'),{'visibility':'hidden'});
    }
  };
	var tUrl = 'cannedResponsesAction.php';
	xhr.makeRequest('newCannedResponse',tUrl,tParams);
}

function _updateTableRow( IDCannedResponse, content ){
	var cannedTable = gID('cannedTable').getElementsByTagName('tbody')[0];
	var tableTDs=[];
	tableTDs[0] = createElement( 'TD', {'class':'TicketNumber'},[content.StAlias,createElement('input', {'type':'hidden', 'id':'StAlias'+IDCannedResponse,'value':content.StAlias })] );
	tableTDs[1] = createElement( 'TD', {}, [content.StTitle,createElement('input', {'type':'hidden', 'id':'StTitle'+IDCannedResponse,'value':content.StTitle })] );
	tableTDs[2] = createElement( 'TD', {}, [createElement('input', {'type':'hidden', 'id':'TxCannedResponse'+IDCannedResponse,'value':content.TxMessage }),createElement('img', {'src':'templates/default/images/button_edit.png', 'alt':'Editar', 'class':'cannedAction', 'onclick':"toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'show'); startEditResponse ('"+IDCannedResponse+"');"} ),createElement('img', {'src':'templates/default/images/button_cancel.png', 'alt':'Remover', 'class':'cannedAction', 'style':'margin-left:6px;','onclick':"removeCannedResponse('"+IDCannedResponse+"');"} ),createElement('img', {'src':'templates/default/images/visualizar.png', 'alt':'Visualizar', 'class':'cannedAction','style':'margin-left:6px'} )] );
	cannedTable.appendChild ( createElement('TR',{'id':'cannedTR'+IDCannedResponse},tableTDs) );
}

function startDataEdit(){
	var dataForm = gID('dataForm');
	dataForm.elements['StDataName'].value = gID('StDataName').textContent;
	dataForm.elements['StDataEmail'].value = gID('StDataEmail').textContent;
	dataForm.elements['TxDataHeader'].innerHTML = gID('TxDataHeader').getElementsByTagName('pre')[0].textContent
	dataForm.elements['TxDataSign'].innerHTML = gID('TxDataSign').getElementsByTagName('pre')[0].textContent
}

function updateInformations(){
	var dataForm = gID('dataForm');
	var content = {
		'StName': dataForm.elements['StDataName'].value,
		'StEmail': dataForm.elements['StDataEmail'].value,
		'TxHeader': dataForm.elements['TxDataHeader'].innerHTML,
		'TxSign': dataForm.elements['TxDataSign'].innerHTML
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( requestResponse ){
    	alert( requestResponse );
    }
  };
	var tUrl = 'userData.submit.php';
	xhr.makeRequest('newCannedResponse',tUrl,tParams);
}
/* CannedResponses  END*/