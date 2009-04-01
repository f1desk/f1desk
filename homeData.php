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
$ArCannedResponses = F1DeskUtils::listCannedResponses(getSessionProp('IDSupporter'));

#
# Note's Data
#
$ArNotes = F1DeskUtils::listNotes(getSessionProp('IDSupporter'));

#
# Bookmarked Ticket's data
#
$ArBookMark = F1DeskUtils::listSupporterBookmark(getSessionProp('IDSupporter'));
?>