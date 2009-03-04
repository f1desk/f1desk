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

/* Templates->HOME */

	/* auxFunctions */
	function _doLoading( formName, action ){
		setStyle(	gID(formName+'Loading'),	{
			'visibility': ( action=='hide' )?'hidden':'visible'
		});
	}
	function _updateTableRow( formName, IDReturn, content ){
		var editTable = gID(formName + 'Table').getElementsByTagName('tbody')[0];
		var tableTDs=[];
		if( formName == 'canned' ){
			tableTDs[0] = createElement( 'TD', {'class':'TicketNumber'},[content.StAlias,createElement('input', {'type':'hidden', 'id':'StCannedAlias'+IDReturn,'value':content.StAlias })] );
			tableTDs[1] = createElement( 'TD', {}, [content.StTitle,createElement('input', {'type':'hidden', 'id':'StCannedTitle'+IDReturn,'value':content.StTitle })] );
			tableTDs[2] = createElement( 'TD', {}, [createElement('input', {'type':'hidden', 'id':'TxCannedResponse'+IDReturn,'value':content.TxMessage }),createElement('img', {'src':'templates/default/images/button_edit.png', 'alt':'Editar', 'class':'cannedAction', 'onclick':"startEditElement ('canned','"+IDReturn+"');"} ),createElement('img', {'src':'templates/default/images/button_cancel.png', 'alt':'Remover', 'class':'cannedAction', 'style':'margin-left:6px;','onclick':"removeCannedResponse('"+IDReturn+"');"} ),createElement('img', {'src':'templates/default/images/visualizar.png', 'alt':'Visualizar', 'class':'cannedAction','style':'margin-left:6px'} )] );
		} else if( formName == 'note' ){
			tableTDs[0] = createElement( 'TD', {}, [content.StTitle,createElement('input', {'type':'hidden', 'id':'StNoteTitle'+IDReturn,'value':content.StTitle })] );
			tableTDs[1] = createElement( 'TD', {}, [createElement('input', {'type':'hidden', 'id':'TxNote'+IDReturn,'value':content.TxNote }),createElement('img', {'src':'templates/default/images/button_edit.png', 'alt':'Editar', 'class':'cannedAction', 'onclick':"startEditElement('note','"+IDReturn+"');"} ),createElement('img', {'src':'templates/default/images/button_cancel.png', 'alt':'Remover', 'class':'cannedAction', 'style':'margin-left:6px;','onclick':"removeNote('"+IDReturn+"');"} ),createElement('img', {'src':'templates/default/images/visualizar.png', 'alt':'Visualizar', 'class':'cannedAction','style':'margin-left:6px'} )] );
		}
		editTable.appendChild ( createElement('TR',{'id':formName+'TR'+IDReturn},tableTDs) );
	}
	/* auxFunctions end*/

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
		alert('Sem ações para executar'); return false;
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
    'okCallBack': function( IDInserted ){
    	_updateTableRow('note', IDInserted, content);
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
		'StAlias': editForm.elements['StAlias'].value,
		'StTitle': editForm.elements['StTitle'].value,
		'TxMessage': editForm.elements['TxCannedResponse'].value
  };
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':content,
    'okCallBack': function( IDInserted ){
    	_updateTableRow( 'canned', IDInserted, content);
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
		editForm.elements['StAlias'].value = gID('StCannedAlias'+IDMessage).value;	/*StAlias*/
		editForm.elements['StTitle'].value = gID('StCannedTitle'+IDMessage).value;	/*StTitle*/
		editForm.elements['TxCannedResponse'].value = gID('TxCannedResponse'+IDMessage).value;	/*TxMessage*/
	} else if( formName == 'note' ){
		editForm.elements['IDNote'].value = IDMessage;	/*ID*/
		editForm.elements['StTitle'].value = gID('StNoteTitle'+IDMessage).value;	/*StTitle*/
		editForm.elements['TxNote'].value = gID('TxNote'+IDMessage).value;	/*TxMessage*/
	}
	gID(formName + 'FormButton').value = "Editar";
}


function editCannedResponse () {
	var editForm = gID("cannedForm");
	if(editForm.elements['IDCanned'].value == ""){
		alert("Selecione uma resposta para editar."); return false;
	}
	_doLoading( 'canned','show' );
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
    		'type':'hidden', 'id':'StCannedAlias'+content.IDCannedResponse, 'value':content.StAlias
    	}) );
    	tableTDs[1].textContent = content.StTitle;
    	tableTDs[1].appendChild( createElement('input',{
    		'type':'hidden', 'id':'StCannedTitle'+content.IDCannedResponse, 'value':content.StTitle
    	}) );
    	gID('TxCannedResponse'+content.IDCannedResponse).value = content.TxMessage;
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
		alert("Selecione uma anotação para editar."); return false;
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
    'okCallBack': function(returnedValue){
    	var tableTDs = gID('noteTR'+content.IDNote).getElementsByTagName('TD');
    	tableTDs[0].textContent = content.StTitle;
    	tableTDs[0].appendChild( createElement('input',{
    		'type':'hidden', 'id':'StNoteTitle'+content.IDNote, 'value':content.StTitle
    	}) );
    	gID('TxNote'+content.IDNote).value = content.TxNote;
    	_doLoading( 'note','hide' );
    	toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide');
    }
  };
  var tUrl = 'notesAction.php';
  xhr.makeRequest('editNote',tUrl,tParams);
}


function removeCannedResponse (IDCannedResponse) {
	if(!IDCannedResponse){
		alert('Erro: ID da resposta não informado');
	}
	if (confirm("Excluir resposta definitivamente?")) {
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
	    		_doLoading( 'canned','hide' );
	    		toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide');
	    	}
	    }
	  };
	  var tUrl = 'cannedResponsesAction.php';
	  xhr.makeRequest('removeCannedResponse',tUrl,tParams);
	}
}


function removeNote (IDNote) {
	if(!IDNote){
		alert('Erro: ID da anotação não informado');
	}
	if (confirm("Excluir anotação definitivamente?")) {
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
	    		alert('Erro: Nenhuma anotação removida com o ID '+IDNote);
	    	} else {
	    		removeElements( gID('noteTR'+IDNote) );
	    		if( gID('noteTable').getElementsByTagName('TR').length == 0){
	    			gID('noteTable').appendChild( createElement('TR',{'id':'noNote'},
	    				createElement('TD',{
	    					'colspan':'3',	'align':'center'
	    				},'Não há anotações cadastradas.')
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
}


function startDataEdit(){
	toogleArrow('dataArrow', 'dataBoxEditAreaContent');
	var dataForm = gID('dataForm');
	var TxHeader = gID('TxDataHeader').getElementsByTagName('pre')[0].textContent;
	var TxSign = gID('TxDataSign').getElementsByTagName('pre')[0].textContent;
	dataForm.elements['StDataName'].value = gID('StDataName').textContent;
	dataForm.elements['StDataEmail'].value = gID('StDataEmail').textContent;
	dataForm.elements['TxDataHeader'].value = (TxHeader == "--")?'':TxHeader;
	dataForm.elements['TxDataSign'].value = (TxSign == "--")?'':TxSign;
}


function updateInformations(){
	var dataForm = gID('dataForm');
	var content = {
		'StName': dataForm.elements['StDataName'].value,
		'StEmail': dataForm.elements['StDataEmail'].value,
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
				gID('StDataName').textContent = dataForm.elements['StDataName'].value;
				gID('StDataEmail').textContent = dataForm.elements['StDataEmail'].value;
				gID('TxDataHeader').getElementsByTagName('pre')[0].textContent = (!TxHeader)?'--':TxHeader;
				gID('TxDataSign').getElementsByTagName('pre')[0].textContent = (!TxSign)?'--':TxSign;
				toogleArrow('dataArrow', 'dataBoxEditAreaContent','hide');
    	} else {
   			alert('Ocorreu um erro na atualização de seus dados. Por favor, recarregue a página');
    	}
    }
  };
	var tUrl = 'userData.submit.php';
	xhr.makeRequest('newCannedResponse',tUrl,tParams);
}


function removeBookmark (IDTicket) {
	if(!IDTicket){
		alert('Erro: ID do chamado não informado');
	}
	if (confirm("Excluir chamado dos favoritos definitivamente?")) {
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
	    		alert('Erro: Nenhum chamado removido com o ID '+IDTicket);
	    	} else {
	    		removeElements( gID('bookmarkTR'+IDTicket) );
	    		if( gID('bookmarkTable').getElementsByTagName('TR').length == 0){
	    			gID('bookmarkTable').appendChild( createElement('TR',{'id':'noBookmark'},
	    				createElement('TD',{
	    					'colspan':'3',	'align':'center'
	    				},'Não há chamados favoritados.')
	    			) );
	    		}
	    		_doLoading( 'bookmark','hide' );
	    	}
	    }
	  };
	  var tUrl = 'bookmarkAction.php';
	  xhr.makeRequest('removeBookmark',tUrl,tParams);
	}
}
/* Templates->HOME END*/

/* Ticket */
function submitTicketForm(IDTicket) {
  gID('StMessageType').selectedIndex = 0;
  gID('TxMessage').value = '';
  gID('Attachment').value = '';
  refreshCall(IDTicket);
}

function addCannedResponse(IDDepartment,IDSupporter) {
  var Responses = gID('cannedAnswers');
  var IDResponse = Responses[Responses.selectedIndex].value;
  tParams = {
    'method':'post',
    'content': {
      'IDResponse':IDResponse,
      'IDDepartment':IDDepartment,
      'IDSupporter':IDSupporter
    },
    'okCallBack':function(response) {
      gID('TxMessage').value += response + "\n";
      gID('TxMessage').focus();
    }
  };
  xhr.makeRequest('getCannedResponse','getResponse.php',tParams);
  return false;
}