var initialized = [];


/**
 *  toogleArrow
 *  @desc change the visibility and the arrow image
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


function animateReload( IDDepartment, action ) {
  var reload = gID('reload' + IDDepartment);
  if (action == 'start') {
    reload.src= 'templates/default/images/animated-reload.gif';
  } else {
    reload.src = 'templates/default/images/btn_reload.png';
  }
}

function refreshNotReadCount( IDDepartment, ItNotReadCount ){
	var element = gID('notReadCount' + IDDepartment);
	var count = ItNotReadCount;

	removeChilds(element);
	element.appendChild( createTextNode( count ) );
}

function insertTickets(IDDepartment, ArTickets) {
	animateReload( IDDepartment, 'stop' );
  var i, TR;
  var Table = gID( 'ticketTable' + IDDepartment );
  var Tbody = gTN('tbody',Table)[0];
  removeChilds(Tbody);

  if (ArTickets.TicketList[0].emptyMessage !== undefined) {
    TR = createElement("tr",{},[
           createElement("td",{'colspan':'3','style':'text-align:center;'},ArTickets.TicketList[0].emptyMessage)
         ]
  		);
		Tbody.appendChild(TR);
  } else {
  	for(i = 0; i < ArTickets.TicketList.length; i++) {
  		var Class = "";
  	  if (ArTickets.TicketList[i].Status == 'NOT_READ') { Class += 'notRead'; }
  	  Class += (i % 2 === 0) ? '' : ' Alt';

  		TR = createElement("tr",{'class': Class},[
				     createElement("td",{"class":"TicketNumber"},'#' + ArTickets.TicketList[i].Number),
  				   createElement("td",{},ArTickets.TicketList[i].Title),
  				   createElement("td",{},ArTickets.TicketList[i].Supporter)
  			   ]
  		);
  		Tbody.appendChild(TR);
  	}
  	refreshNotReadCount( IDDepartment, ArTickets.notReadCount );
  }
}

function reloadTicketList( IDDepartment, StUser, First ){
	animateReload( IDDepartment, 'start' );
	var tParams = {
    'enqueue':1,
    'returnType':'json',
    'method':'post',
    'content':{'StUser':StUser, 'IDDepartment':IDDepartment},
    'okCallBack':function(ArTickets) {
      insertTickets(IDDepartment, ArTickets);
      if (First === true) {
        toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment);
      }
      return true;
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
    reloadTicketList( IDDepartment, StUser, true );
  } else {
    toogleArrow("arrow"+IDDepartment, 'departmentContent' + IDDepartment);
  }
}

function refreshCall( IDTicket ) {
  var tParams = {
    'enqueue':1,
    'method':'post',
    'content':{'IDTicket':IDTicket},
    'okCallBack':function(returnedValue) {
      removeElements(gID('contentDisplay'));
      gID('contentWrapper').innerHTML += returnedValue;
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
      /*refreshCall( IDTicket );*/
/*
* FAZER ATUALIZAR COM gID AO INVES DE FAZER UMA REQUISICAO
*/
      IDDepartment = gID('IDDepartment').value;
      reloadTicketList( IDDepartment, 'supporter' );
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
}

function editCannedResponse () {
	var editForm = gID("cannedForm");
	if(editForm.elements['IDCanned'].value == ""){
		alert("Selecione uma resposta para editar."); return false;
	}
	var tParams = {
    'enqueue':1,
    'method':'post',
    'content':{
    	'action':'edit',
    	'IDCannedResponse': editForm.elements['IDCanned'].value,
    	'StAlias': editForm.elements['StAlias'].value,
    	'StTitle': editForm.elements['StTitle'].value,
    	'TxMessage': editForm.elements['TxCannedResponse'].value
    },
    'okCallBack': function(returnedValue){
    	alert(returnedValue);
      /*IDDepartment = gID('IDDepartment').value;
      reloadTicketList( IDDepartment, 'supporter' );*/
    }
  };
  var tUrl = 'cannedResponsesAction.php';
  xhr.makeRequest('setTicketOwner',tUrl,tParams);
}