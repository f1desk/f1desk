<?php

/**
 * class to handle templates
 *
 */
abstract class TemplateHandler {

  /**
   * Print's all messages of the ticket given
   *
   * JOHN POR FAVOR, DE UMA OLHADA NESSA FUNCAO E FAZ QUE NEM AS OUTRAS QUE TU FEZ, PRA CUSTOMIZAR =D
   * EU JA TIREI O "PROCESSAMENTO DELA"
   *
   * @param array $ArMessages
   * @param array $ArAttachments
   */
  public static function showHistory($ArMessages, $ArAttachments) {
    $i = 0;
    $StHtml = "";
    #
    # for exibition, replaces "\n" for "<br>"
    #
    foreach ($ArMessages as &$ArMessage) {
      $ArMessage['TxMessage'] = nl2br( $ArMessage['TxMessage'] );

      switch ($ArMessage['EnMessageType']) {
        case 'SYSTEM':
          $StClass = 'messageSystem';
        break;
        case 'INTERNAL':
          $StClass = 'messageInternal';
        break;
        default:
          $StClass = 'message';
          if ($i++ % 2 == 0) { $StClass .= 'Alt'; }
        break;
      }

      $ArMessage['StClass'] = $StClass;
      if (!F1DeskUtils::IsSupporter() && $ArMessage['EnMessageType'] == 'INTERNAL')
        continue;
      $DtSended = F1DeskUtils::formatDate('datetime_format',$ArMessage['DtSended']);
      $StHtml .= "<div class='{$ArMessage['StClass']}'>";
      $StHtml .= '<b>'.DATE_MSG_SENT.$DtSended.BY.'<span class="TxAtendente">'.$ArMessage['SentBy'].'</span></b>';
      if (array_key_exists($ArMessage['IDMessage'],$ArAttachments)) {
        foreach ($ArAttachments[$ArMessage['IDMessage']] as $Attachment) {
          $StHtml .= "<p><b>".ATTACHMENT."</b>: <a class='Link' href='download.php?IDAttach={$Attachment['IDAttachment']}'>{$Attachment['StFile']}</a></p>";
        }
      }
      $StHtml .= '<p>'.$ArMessage['TxMessage'] . '</p></div>';
    }
    return $StHtml;
  }

	/**
	 * Create the departments combobox in the ticket creation page
	 *
	 * @param array $ArDepartments
	 */
	public static function createFormattedCombo($ArDepartments, $StID = 'IDRecipient', $StName = 'IDRecipient', $StClass = 'inputCombo') {
	  $StHtml = "<select id='$StID' name='$StName' class='$StClass'>";
    $StHtml .= "<option value='null'>".DEFAULT_OPTION."</option>";
	  foreach ($ArDepartments as $ArDepartment) {
	    if(isset($ArDepartment['SubDepartments'])) {
	      $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
	      $StHtml .= "<optgroup>";
	      foreach ($ArDepartment['SubDepartments'] as $SubDepartments) {
	        $StHtml .= "<option value='{$SubDepartments['IDSub']}'>{$SubDepartments['StSub']}</option>";
	      }
	      $StHtml .= "</optgroup>";
	    } else {
	      $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
	    }
	  }
	  $StHtml .= "</select>";
	  return $StHtml;
	}

	/**
	 * Create the comboboxes of categories and priorities in the create ticket page
	 *
	 * @param array $Array
	 * @param string $StID
	 * @param string $StName
	 * @param string $StClass
	 * @return string
	 */
	public static function createCategory_PriorityCombobox($Array, $StID, $StName, $StClass = 'inputCombo') {
	  $StHtml = "<select id='$StID' name='$StName' class='$StClass'>";
	  foreach ($Array as $Key => $Value) {
      $StHtml .= "<option value='$Key'>$Value</option>";
    }
    $StHtml .= "</select>";
    return $StHtml;
	}

	/**
	 * Create the supporters combobox in ticket headers
	 *
	 * @param int $IDTicket
	 * @param array $ArSupporters
	 * @param array $ArHeaders
	 * @param string $StID
	 * @param string $StClass
	 * @return string
	 */
	public static function createSupportersCombo($IDTicket,$IDDepartment, $ArSupporters, $ArHeaders, $StID, $StClass, $preview) {
	  $StHtml = (isset($ArHeaders['StName'])) ? $ArHeaders['StName'] : '';
	  if (F1DeskUtils::IsSupporter() && !$preview) {
	    $StHtml = "<select id='$StID' onchange='Ticket.setTicketOwner(\"$IDTicket\", this.value, \"$IDDepartment\")' class='$StClass'>";
	    foreach ( $ArSupporters as $IDSupporter => $StSupporter ) {
	      if ($ArHeaders['IDSupporter'] != $IDSupporter) {
	        $StHtml .= "<option value='$IDSupporter'>$StSupporter</option>";
	      } else {
	        $StHtml .= "<option selected='selected' value='$IDSupporter'>$StSupporter</option>";
	      }
	    }
	    $StHtml .= "</select>";
	  } else {
	    foreach ( $ArSupporters as $IDSupporter => $StSupporter ) {
	      if ($ArHeaders['IDSupporter'] == $IDSupporter) {
	        $StHtml = "<span id='$StID'>$StSupporter</span>";
	      }
	    }
	  }
	  return $StHtml;
	}

	/**
	 * create de Departments combobox in ticket header
	 *
	 * @param unknown_type $ArDepartments
	 * @param unknown_type $IDDepartment
	 * @param unknown_type $IDTicket
	 * @param unknown_type $StID
	 * @param unknown_type $StClass
	 * @return unknown
	 */
	public static function createHeaderDepartmentCombo($ArDepartments, $IDDepartment, $IDTicket, $StID, $StClass = 'inputCombo', $preview) {
	  $StHtml = SINGLE;
    if (F1DeskUtils::IsSupporter() && !$preview) {
      $StHtml = "<select id='$StID' class='$StClass' onchange='Ticket.changeDepartment(\"$IDTicket\",this.value, \"$IDDepartment\")'>";
      foreach ($ArDepartments as $ArDepartment) {
        if(isset($ArDepartment['SubDepartments'])) {
          if ($ArDepartment['IDDepartment'] == $IDDepartment) {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}' selected>{$ArDepartment['StDepartment']}</option>";
          } else {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
          }
          $StHtml .= "<optgroup>";
          foreach ($ArDepartment['SubDepartments'] as $SubDepartments) {
            if ($SubDepartments['IDSub'] == $IDDepartment) {
              $StHtml .= "<option value='{$SubDepartments['IDSub']}' selected>{$SubDepartments['StSub']}</option>";
            } else {
              $StHtml .= "<option value='{$SubDepartments['IDSub']}'>{$SubDepartments['StSub']}</option>";
            }
          }
          $StHtml .= "</optgroup>";
        } else {
          if ($ArDepartment['IDDepartment'] == $IDDepartment) {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}' selected>{$ArDepartment['StDepartment']}</option>";
          } else {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
          }
        }
      }
      $StHtml .= "</select>";
    } else {
      foreach ($ArDepartments as $ArDepartment) {
      	if ($ArDepartment['IDDepartment'] == $IDDepartment) {
          $StHtml = "<span id='{$StID}'>{$ArDepartment['StDepartment']}</span>";
      	}
      	if(isset($ArDepartment['SubDepartments'])) {
      		foreach ($ArDepartment['SubDepartments'] as $ArSubDepartment) {
      			if ($ArSubDepartment['IDSub'] == $IDDepartment) {
      				$StHtml = "<span id='{$StID}'>{$ArSubDepartment['StSub']}</span>";
      			}
      		}
      	}
      }
    }
    return $StHtml;
	}

	/**
	 * show all attached files
	 *
	 * @param array $ArAttachments
	 * @return unknown
	 */
	public static function showAttachments($ArAttachments) {
	  $StHtml = '';
	  if (count($ArAttachments)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_FILES .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArAttachments as $Attachment) {
        $Attachment = $Attachment[0];
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='download.php?IDAttach={$Attachment['IDAttachment']}'>{$Attachment['StFile']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
	  }
	  return $StHtml;
	}

	/**
	 * show all attached tickets
	 *
	 * @param unknown_type $ArAttachedTickets
	 * @return unknown
	 */
	public static function showAttachedTickets($ArAttachedTickets) {
	  $StHtml = '';
	  if (count($ArAttachedTickets)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_ATTACHED_TICKETS .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArAttachedTickets as $AttachedTicket) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='javascript:void(0);' onclick='flowWindow.previewTicket(\"{$AttachedTicket['IDAttachedTicket']}\")'>#{$AttachedTicket['IDAttachedTicket']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all tickets attached
	 *
	 * @param array $ArAttachedTickets
	 * @return string HTML
	 */
	public static function showTicketsAttached($ArTicketsAttached) {
	  $StHtml = '';
	  if (count($ArTicketsAttached)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_TICKETS_ATTACHED .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketsAttached as $TicketAttached) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='javascript:void(0);' onclick='flowWindow.previewTicket(\"{$TicketAttached['IDTicket']}\")'>#{$TicketAttached['IDTicket']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all recipient departments
	 *
	 * @param unknown_type $ArTicketDepartments
	 * @return unknown
	 */
	public static function showTicketDepartments($ArTicketDepartments) {
	  $StHtml = '';
	  if (count($ArTicketDepartments)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_DEPARTMENT_SENTTO .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDepartments as $TicketDepartments) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDepartments['StDepartment'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all recipient supporters
	 *
	 * @param unknown_type $ArTicketDestinations
	 * @return unknown
	 */
	public static function showTicketSupporters($ArTicketDestinations) {
	  $StHtml = '';
	  if (count($ArTicketDestinations)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_SUPPORTER_SENTTO .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDestinations as $TicketDestination) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDestination['StName'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all department readers
	 *
	 * @param unknown_type $ArTicketDepartmentsReader
	 * @return unknown
	 */
	public static function showDepartmentReaders($ArTicketDepartmentsReader) {
	  $StHtml = '';
	  if (count($ArTicketDepartmentsReader)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_DEPARTMENTS_READER .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDepartmentsReader as $TicketDepartmentsReader) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDepartmentsReader['StDepartment'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all supporter readers
	 *
	 * @param unknown_type $ArTicketReaders
	 * @return unknown
	 */
	public static function showSupporterReaders($ArTicketReaders) {
	  $StHtml = '';
	  if (count($ArTicketReaders)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_SUPPORTER_READER .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketReaders as $TicketReaders) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketReaders['StName'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
	  }
	  return $StHtml;
	}

	/**
	 * Create the combobox with the Canned Answers
	 *
	 * @param unknown_type $ArResponses
	 * @return unknown
	 */
	public static function createCannedCombo($ArResponses) {
	  $StHtml = '';
	  if (F1DeskUtils::IsSupporter()) {
	    $StHtml = "<select class='inputCombo' id='cannedAnswers'>";
	    if ($ArResponses[0]['IDCannedResponse'] != '') {
	      foreach ($ArResponses as $Response) {
	        $StHtml .= "<option value='".(f1desk_escape_string($Response['TxMessage'],true,true))."' >".$Response['StTitle']."</option>";
        }
	    } else {
	      $StHtml .= "<option value='null'>".NO_ANSWER."</option>";
      }
      $StHtml .= '</select>';
      $StHtml .= "<button class='button' onclick='Ticket.addCannedResponse(); return false;'>".ADD."</button>";
    }
    return $StHtml;
	}

	/**
	 * Create a combobox with all ticket types registered
	 *
	 * @param unknown_type $StClass
	 * @return unknown
	 */
	public static function showTicketTypes($ArTypes, $StClass = 'inputCombo') {
	  if (count($ArTypes)) {
	    $StHtml = '<span>' . reset($ArTypes) . '</span>';
	  } elseif (!empty($ArTypes)) {
	    $StHtml = "<select id='IDType' name='IDType' class='$StClass'>";
	    foreach ($ArTypes as $Key => $Type) {
	      $StHtml .= "<option value='$Key'>$Type</option>";
	    }
	    $StHtml .= '</select>';
	  } else {
	    $StHtml = '<span>'.NOTYPE.'</span>';
	  }
    return $StHtml;
	}

	/**
	 * Return the HTML Table with all Canned Responses
	 *
	 * @param array $ArCannedResponses
	 * @param string $StClass
	 * @return string
	 */
	public static function showCannedAnswers($ArCannedResponses, $StClass = 'inputCombo') {
	  $StHtml = '';
	  if (!empty($ArCannedResponses[0]['IDCannedResponse'])) {
      foreach ($ArCannedResponses as $ArCannedResponse) {
        $StHtml .= "<tr id='cannedTR{$ArCannedResponse['IDCannedResponse']}'>";
        $StHtml .= '<td class="TicketNumber">';
        $StHtml .= $ArCannedResponse['StTitle'];
        $StHtml .= "<input type='hidden' id='StCannedTitle{$ArCannedResponse['IDCannedResponse']}' value='".f1desk_escape_string($ArCannedResponse['StTitle'],false,true)."'>";
        $StHtml .= '</td><td>';
        $StHtml .= "<input type='hidden' id='TxCannedResponse{$ArCannedResponse['IDCannedResponse']}' value='".f1desk_escape_string($ArCannedResponse['TxMessage'],false,true)."'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_edit.png' alt='Editar' title='Editar' class='cannedAction' onclick='Home.startEditElement(\"canned\",\"{$ArCannedResponse['IDCannedResponse']}\");'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_cancel.png' alt='Remover' title='Remover' class='cannedAction' onclick='Home.removeCannedResponse(\"{$ArCannedResponse['IDCannedResponse']}\")'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/visualizar.png' title='Visualizar' id='previemCanned{$ArCannedResponse['IDCannedResponse']}' alt='Visualizar' class='cannedAction' onclick='flowWindow.previewCannedResponse(\"".f1desk_escape_string($ArCannedResponse['StTitle'],false,true).'","'.f1desk_escape_string($ArCannedResponse['TxMessage'], true,true).'");\'>';
        $StHtml .= '</td> </tr>';
      }
    } else {
      $StHtml = '<tr id="noCanned">';
      $StHtml .= '<td colspan="3" align="center">'.NO_CANNED.'</td></tr>';
    }
    return $StHtml;
	}

	/**
	 * Return the HTML Table with all Notes
	 *
	 * @param array $ArNotes
	 * @param string $StClass
	 * @return string
	 */
	public static function showNotes($ArNotes, $StClass = '') {
	  $StHtml = '';
	  if (empty($ArNotes[0])) {
      $StHtml = '<tr id="noNote">';
      $StHtml .= '<td colspan="3" align="center">'.NO_NOTES.'</td> </tr>';
	  } else {
      foreach ($ArNotes as $ArNote) {
        $StHtml .= "<tr id='noteTR{$ArNote['IDNote']}'>";
        $StHtml .= '<td class="TicketNumber">';
        $StHtml .= $ArNote['StTitle'];
        $StHtml .= "<input type='hidden' id='StNoteTitle{$ArNote['IDNote']}' value=".f1desk_escape_string($ArNote['StTitle'],false,true)."> </td> <td>";
        $StHtml .= "<input type='hidden' id='TxNote{$ArNote['IDNote']}' value='".f1desk_escape_string($ArNote['TxNote'],false,true)."'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_edit.png' alt='Editar' title='Editar' class='cannedAction' onclick=\"Home.startEditElement('note',{$ArNote['IDNote']});\">";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_cancel.png' alt='Remover' title='Remover' class='cannedAction' onclick=\"Home.removeNote({$ArNote['IDNote']})\">";
        $StHtml .= "<img src='".TEMPLATEDIR."images/visualizar.png' alt='Visualizar' title='Visualizar' class='cannedAction' onclick='flowWindow.previewNote(\"".f1desk_escape_string($ArNote['StTitle'],false,true)."\", \"".f1desk_escape_string($ArNote['TxNote'], true, true)."\");'>";
        $StHtml .= '</td> </tr>';
      }
    }
    return $StHtml;
	}

	/**
	 * Return the HTML Table with all Bookmarked Tickets
	 *
	 * @param array $ArBookmarks
	 * @param string $StClass
	 * @return string
	 */
	public static function showBookmarkedTickets($ArBookmarks) {
	  $StHtml = '';
	  if ( empty($ArBookmarks[0]) ) {
      $StHtml .= '<tr id="noBookmark">';
			$StHtml .= '<td colspan="3" align="center">'.NO_BOOKMARK.'</td> </tr>';
	  } else {
      foreach ($ArBookmarks as $ArBookmark) {
        $StHtml .= "<tr id='bookmarkTR{$ArBookmark['IDTicket']}'>";
				$StHtml .= '<td class="TicketNumber">';
				$StHtml .= $ArBookmark['IDTicket'];
				$StHtml .= "<input type='hidden' id='StBookmarkID{$ArBookmark['IDTicket']}' value='{$ArBookmark['IDTicket']}'>";
				$StHtml .= '</td> <td>';
				$StHtml .= $ArBookmark['StTitle'];
				$StHtml .= "<input type='hidden' id='StBookmarkTitle{$ArBookmark['IDTicket']}' value={$ArBookmark['StTitle']}'>";
				$StHtml .= '</td> <td>';
				$StHtml .= "<img src='".TEMPLATEDIR."images/button_cancel.png' alt='Remover' title='Remover' class='cannedAction' onclick=\"Home.removeBookmark({$ArBookmark['IDTicket']})\">";
				$StHtml .= "<img src='".TEMPLATEDIR."images/visualizar.png' alt='Visualizar' title='Visualizar' class='cannedAction' onclick=\"flowWindow.previewTicket({$ArBookmark['IDTicket']})\">";
				$StHtml .= '</td> </tr>';
      }
    }
    return $StHtml;
	}
}
?>