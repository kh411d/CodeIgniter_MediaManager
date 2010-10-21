<?php
	function html_message($data = '')
	{
		if($data)
		return '<div id="status_message" style="font-family:verdana;font-size:15px;padding:15px;background:#FBE6F2;border:1px solid #D893A1;color:#333333;">'
		 		.$data.
			   '</div>';
		 else
		 return '';		
	}
	
	function html_makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim( $text ) ? $text : $value;
		return $obj;
	}

  function html_writableCell( $folder, $relative=1, $text='', $visible=1 ) {
	$writeable 		= '<b><font color="green">Writeable</font></b>';
	$unwriteable 	= '<b><font color="red">Unwriteable</font></b>';

  	echo '<tr>';
  	echo '<td class="item">';
	echo $text;
	if ( $visible ) {
		echo $folder . '/';
	}
	echo '</td>';
  	echo '<td align="left">';
	if ( $relative ) {
		echo is_writable( "../$folder" ) 	? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" ) 		? $writeable : $unwriteable;
	}
	echo '</td>';
  	echo '</tr>';
  }

	/**
	* Generates an HTML select list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	function html_selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {
		// check if array
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		$html 	= "\n<select name=\"$tag_name\" $tag_attribs>";
		$count 	= count( $arr );

		for ($i=0, $n=$count; $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}

	/**
	* Writes a select list of integers
	* @param int The start integer
	* @param int The end integer
	* @param int The increment
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The printf format to be applied to the number
	* @returns string HTML for the select list
	*/
	function html_integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format="" ) {
		$start 	= intval( $start );
		$end 	= intval( $end );
		$inc 	= intval( $inc );
		$arr 	= array();

		for ($i=$start; $i <= $end; $i+=$inc) {
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = html_makeOption( $fi, $fi );
		}

		return html_selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Writes a select list of month names based on Language settings
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	function html_monthSelectList( $tag_name, $tag_attribs, $selected ) {
		$arr = array(
			html_makeOption( '01', _JAN ),
			html_makeOption( '02', _FEB ),
			html_makeOption( '03', _MAR ),
			html_makeOption( '04', _APR ),
			html_makeOption( '05', _MAY ),
			html_makeOption( '06', _JUN ),
			html_makeOption( '07', _JUL ),
			html_makeOption( '08', _AUG ),
			html_makeOption( '09', _SEP ),
			html_makeOption( '10', _OCT ),
			html_makeOption( '11', _NOV ),
			html_makeOption( '12', _DEC )
		);

		return html_selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML select list from a tree based query list
	* @param array Source array with id and parent fields
	* @param array The id of the current list item
	* @param array Target array.  May be an empty array.
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	function html_treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected ) {

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($src_list as $v ) {
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$ilist = mosTreeRecurse( 0, '', array(), $children );

		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) {
			if ($this_treename) {
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
					$tgt_list[] = html_makeOption( $item->id, $item->treename );
				}
			} else {
				if ($item->id != $src_id) {
					$tgt_list[] = html_makeOption( $item->id, $item->treename );
				} else {
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return html_selectList( $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}

	/**
	* Writes a yes/no select list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	function html_yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
		html_makeOption( '0', $no ),
		html_makeOption( '1', $yes ),
		);

		return html_selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML radio list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @returns string HTML for the select list
	*/
	function html_radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
			$html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
		}
		$html .= "\n";

		return $html;
	}

	/**
	* Writes a yes/no radio list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	function html_yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
			html_makeOption( '0', $no ),
			html_makeOption( '1', $yes )
		);

		return html_radioList( $arr, $tag_name, $tag_attribs, $selected );
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	function html_idBox( $rowNum, $recId, $checkedOut=false, $name='cid' ) {
		if ( $checkedOut ) {
			return '';
		} else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
		}
	}

	function html_sortIcon( $base_href, $field, $state='none' ) {
		global $mosConfig_live_site;

		$alts = array(
			'none' 	=> _CMN_SORT_NONE,
			'asc' 	=> _CMN_SORT_ASC,
			'desc' 	=> _CMN_SORT_DESC,
		);
		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. "<img src=\"$mosConfig_live_site/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
		. "</a>";
		return $html;
	}

	/**
	* Writes Close Button
	*/
	function html_CloseButton ( &$params, $hide_js=NULL ) {
		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<script language="javascript" type="text/javascript">
			<!--
			document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
			document.write('<a href="#" onclick="javascript:window.close();"><span class="small"><?php echo _PROMPT_CLOSE;?></span></a>');
			document.write('</div>');
			//-->
			</script>
			<?php
		}
	}

	/**
	* Writes Back Button
	*/
	function html_BackButton ( &$params, $hide_js=NULL ) {
		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
				<a href='javascript:history.go(-1)'>
					<?php echo _BACK; ?></a>
			</div>
			<?php
		}
	}

	/**
	* Cleans text of all formating and scripting code
	*/
	function html_cleanText ( &$text ) {
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		$text = htmlspecialchars( $text );

		return $text;
	}

	/**
	* Writes Print icon
	*/
	function html_PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL ) {
		if ( $params->get( 'print' )  && !$hide_js ) {
			// use default settings if none declared
			if ( !$status ) {
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT, _CMN_PRINT );
			} else {
				$image = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
			}

			if ( $params->get( 'popup' ) && !$hide_js ) {
				// Print Preview button - used when viewing page
				?>
				<script language="javascript" type="text/javascript">
				<!--
				document.write('<td align="right" width="100%" class="buttonheading">');
				document.write('<a href="#" onclick="javascript:window.print(); return false;" title="<?php echo _CMN_PRINT;?>">');
				document.write('<?php echo $image;?>');
				document.write('</a>');
				document.write('</td>');
				//-->
				</script>
				<?php
			} else {
				// Print Button - used in pop-up window
				?>
				<td align="right" width="100%" class="buttonheading">
					<a href="<?php echo $link; ?>" target="_blank" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;" title="<?php echo _CMN_PRINT;?>">
						<?php echo $image;?></a>
				</td>
				<?php
			}
		}
	}

	/**
	* simple Javascript Cloaking
	* email cloacking
 	* by default replaces an email with a mailto link with email cloacked
	*/
	function html_emailCloaking( $mail, $mailto=1, $text='', $email=1 ) {
		// convert text
		$mail 			= html_encoding_converter( $mail );
		// split email by @ symbol
		$mail			= explode( '@', $mail );
		$mail_parts		= explode( '.', $mail[1] );
		// random number
		$rand			= rand( 1, 100000 );

		$replacement 	= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement 	.= "\n var path = 'hr' + 'ef' + '=';";
		$replacement 	.= "\n var addy". $rand ." = '". @$mail[0] ."' + '&#64;';";
		$replacement 	.= "\n addy". $rand ." = addy". $rand ." + '". implode( "' + '&#46;' + '", $mail_parts ) ."';";

		if ( $mailto ) {
			// special handling when mail text is different from mail addy
			if ( $text ) {
				if ( $email ) {
					// convert text
					$text 			= html_encoding_converter( $text );
					// split email by @ symbol
					$text 			= explode( '@', $text );
					$text_parts		= explode( '.', $text[1] );
					$replacement 	.= "\n var addy_text". $rand ." = '". @$text[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", @$text_parts ) ."';";
				} else {
					$replacement 	.= "\n var addy_text". $rand ." = '". $text ."';";
				}
				$replacement 	.= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement 	.= "\n document.write( addy_text". $rand ." );";
				$replacement 	.= "\n document.write( '<\/a>' );";
			} else {
				$replacement 	.= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement 	.= "\n document.write( addy". $rand ." );";
				$replacement 	.= "\n document.write( '<\/a>' );";
			}
		} else {
			$replacement 	.= "\n document.write( addy". $rand ." );";
		}
		$replacement 	.= "\n //-->";
		$replacement 	.= '\n </script>';

		// XHTML compliance `No Javascript` text handling
		$replacement 	.= "<script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n document.write( '<span style=\'display: none;\'>' );";
		$replacement 	.= "\n //-->";
		$replacement 	.= "\n </script>";
		$replacement 	.= _CLOAKING;
		$replacement 	.= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n document.write( '</' );";
		$replacement 	.= "\n document.write( 'span>' );";
		$replacement 	.= "\n //-->";
		$replacement 	.= "\n </script>";

		return $replacement;
	}

	function html_encoding_converter( $text ) {
		// replace vowels with character encoding
		$text 	= str_replace( 'a', '&#97;', $text );
		$text 	= str_replace( 'e', '&#101;', $text );
		$text 	= str_replace( 'i', '&#105;', $text );
		$text 	= str_replace( 'o', '&#111;', $text );
		$text	= str_replace( 'u', '&#117;', $text );

		return $text;
	}