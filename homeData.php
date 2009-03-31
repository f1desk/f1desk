<?php
#
# User's data
#
if ( getSessionProp('isSupporter') == "true" ){
	$ArUser = TemplateHandler::getUserData( getSessionProp('IDSupporter'), 0);
} else {
	$ArUser = TemplateHandler::getUserData( getSessionProp('IDClient'), 1);
}

#
# Canned response's data
#
$ArCannedResponses = TemplateHandler::getCannedResponses(getSessionProp('IDSupporter'));
?>