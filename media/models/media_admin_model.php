<?php
Class Media_admin_model extends Model {

	function Media_admin_model(){
		parent::Model();
	}
	
	//Built in function of dirname is faulty
	//It assumes that the directory nane can not contain a . (period)
	function dir_name($dir){
		$lastSlash = intval(strrpos($dir, '/'));
		if($lastSlash == strlen($dir)-1){
			return substr($dir, 0, $lastSlash);
		}
		else {
			return dirname($dir);
		}
	}

	function draw_no_results(){
		?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">
					No Images Found
				</div>
			</td>
		</tr>
		</table>
		<?php
	}

	function draw_no_dir() {
		
		?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div align="center" style="font-size:small;font-weight:bold;color:#CC0000;font-family: Helvetica, sans-serif;">
					Configuration Problem: &quot;<?php echo $BASE_DIR.$BASE_ROOT; ?>&quot; does not exist.
				</div>
			</td>
		</tr>
		</table>
		<?php
	}


	function draw_table_header() {
		//mosCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
		function dirup(){
			var urlquery=frames['imgManager'].location.search.substring(1);
			var curdir= urlquery.substring(urlquery.indexOf('listdir=')+8);
			var listdir=curdir.substring(0,curdir.lastIndexOf('/'));
			frames['imgManager'].location.href='<?php echo site_url("admin/media/ilist");?>' + listdir;
		}
		</script>
		<div class="manager">
		<?php
	}

	function draw_table_footer() {
		?>
		</div>
		<?php
	}

	function show_image($img, $file, $info, $size, $listdir) {
		$img_file 		= basename($img);
		$img_url_link 	= COM_MEDIA_BASEURL . $listdir . '/' . rawurlencode( $img_file );
		//khalid
		$img_url_link2 	= '/assets/' . $listdir . '/' . rawurlencode( $img_file );
		$filesize 		= $this->parse_size( $size );

		if ( ( $info[0] > 70 ) || ( $info[0] > 70 ) ) {
			$img_dimensions = $this->imageResize($info[0], $info[1], 80);
		} else {
			$img_dimensions = 'width="'. $info[0] .'" height="'. $info[1] .'"';
		}

		$overlib = '<table>';
		$overlib .= '<tr>';
		$overlib .= '<td>';
		$overlib .= 'Width:';
		$overlib .= '</td>';
		$overlib .= '<td>';
		$overlib .= $info[0].' px';
		$overlib .= '</td>';
		$overlib .= '</tr>';
		$overlib .= '<tr>';
		$overlib .= '<td>';
		$overlib .= 'Height:';
		$overlib .= '</td>';
		$overlib .= '<td>';
		$overlib .= $info[1] .' px';
		$overlib .= '</td>';
		$overlib .= '</tr>';
		$overlib .= '<tr>';
		$overlib .= '<td>';
		$overlib .= 'Filesize:';
		$overlib .= '</td>';
		$overlib .= '<td>';
		$overlib .= $filesize;
		$overlib .= '</td>';
		$overlib .= '</tr>';
		$overlib .= '</table>';
		$overlib .= '<br/> *Click to Enlarge*';
		$overlib .= '<br/> *Click for Image Code*';
		?>
		<div style="float:left; padding: 5px">
			<div class="imgTotal"  onmouseover="return overlib( '<?php echo $overlib; ?>', CAPTION, '<?php echo addslashes( $file ); ?>', BELOW, LEFT, WIDTH, 150 );" onmouseout="return nd();">
				<div align="center" class="imgBorder">
					<a href="#" onclick="javascript: window.open( '<?php echo $img_url_link; ?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=<?php echo $info[0] * 1.5;?>,height=<?php echo $info[1] * 1.5;?>,directories=no,location=no,left=120,top=80'); window.top.document.forms[0].imagecode.value = '<img src=&quot;<?php echo $img_url_link;?>&quot; align=&quot;left&quot; hspace=&quot;6&quot; alt=&quot;Image&quot; />';" style="display: block; width: 100%; height: 100%">
						<div class="image">
							<img src="<?php echo $img_url_link; ?>" <?php echo $img_dimensions; ?> border="0" />
						</div></a>
				</div>
			</div>
			<div class="imginfoBorder">
				<small>
					<?php echo htmlspecialchars( substr( $file, 0, 10 ) . ( strlen( $file ) > 10 ? '...' : ''), ENT_QUOTES ); ?>
				</small>
				<div class="buttonOut">
					<a href="<?php echo site_url('admin/media/delete/'.$file.'/'.$listdir)?>" target="_top" onclick="return deleteImage('<?php echo $file; ?>');" title="Delete Item">
						
						<img src="<?php echo basepath()?>images/edit_trash.gif" width="15" height="15" border="0" alt="Delete" /></a>
					<a href="#" onclick="javascript:window.top.document.forms[0].imagecode.value = '<img src=&quot;<?php echo $img_url_link2;?>&quot; align=&quot;left&quot; hspace=&quot;6&quot; alt=&quot;Image&quot; />';" title="Image Code">
						<img src="<?php echo basepath()?>images/edit_pencil.gif" width="15" height="15" border="0" alt="Code" /></a>
					<a href="#" onclick="javascript:window.top.document.forms[0].imagecode.value = '<?php echo $img_url_link2;?>';" title="Url Code">
						<img src="<?php echo basepath()?>images/edit_url.gif" width="15" height="15" border="0" alt="Code" /></a>	
				</div>
			</div>
		</div>
		<?php
	}

	function show_dir( $path, $dir, $listdir ) {
		$count = $this->num_files( COM_MEDIA_BASE . $listdir . $path );

		$num_files 	= $count[0];
		$num_dir 	= $count[1];

		if ($listdir == '/') {
			$listdir = '';
		}

		//$link = 'index.php?mod=media&amp;task=list&amp;listdir='. $listdir . $path .'&amp;notpl=1';
		$link = site_url('admin/media/ilist'.$listdir.$path);

		$overlib = '<table>';
		$overlib .= '<tr>';
		$overlib .= '<td>';
		$overlib .= 'Files:';
		$overlib .= '</td>';
		$overlib .= '<td>';
		$overlib .= $num_files;
		$overlib .= '</td>';
		$overlib .= '</tr>';
		$overlib .= '<tr>';
		$overlib .= '<td>';
		$overlib .= 'Folders:';
		$overlib .= '</td>';
		$overlib .= '<td>';
		$overlib .= $num_dir;
		$overlib .= '</td>';
		$overlib .= '</tr>';
		$overlib .= '</table>';
		$overlib .= '<br/> *Click to Open*';
		?>
		<div style="float:left; padding: 5px">
			<div class="imgTotal" onmouseover="return overlib( '<?php echo $overlib; ?>', CAPTION, '<?php echo $dir; ?>', BELOW, RIGHT, WIDTH, 150 );" onmouseout="return nd();">
				<div align="center" class="imgBorder">
					<a href="<?php echo $link; ?>" target="imgManager" onclick="javascript:updateDir();">
						<img src="<?php echo basepath()?>images/folder.gif" width="80" height="80" border="0" /></a>
				</div>
			</div>
			<div class="imginfoBorder">
				<small>
					<?php echo substr( $dir, 0, 10 ) . ( strlen( $dir ) > 10 ? '...' : ''); ?>
				</small>
				<div class="buttonOut">
					<a href="<?php echo site_url('admin/media/deletefolder/'.$path.'/'.$listdir)?>" target="_top" onclick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);">
						
						<img src="<?php echo basepath()?>images/edit_trash.gif" width="15" height="15" border="0" alt="Delete" /></a>
				</div>
			</div>
		</div>
		<?php
	}

	function show_doc($doc, $size, $listdir, $icon) {
		$size 			= $this->parse_size( $size );
		$doc_url_link 	= COM_MEDIA_BASEURL . $listdir  .'/'. rawurlencode( $doc );

		$overlib = 'Filesize: '. $size;
		$overlib .= '<br/><br/> *Click for URL*';
		?>
		<div style="float:left; padding: 5px">
			<div class="imgTotal" onmouseover="return overlib( '<?php echo $overlib; ?>', CAPTION, '<?php echo $doc; ?>', BELOW, RIGHT, WIDTH, 200 );" onmouseout="return nd();">
				<div align="center" class="imgBorder">
				 <!-- 
				 <a href="index.php?mod=media&amp;task=list&amp;listdir=<?php echo $listdir; ?>" onclick="javascript:window.top.document.forms[0].imagecode.value = '<a href=&quot;<?php echo $doc_url_link;?>&quot;>Insert your text here</a>';">
		  		 -->
		  		 <a onclick="javascript:window.top.document.forms[0].imagecode.value = '<a href=&quot;<?php echo $doc_url_link;?>&quot;>Insert your text here</a>';">
		  		  
		  				<img border="0" src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" /></a>
		  		</div>
			</div>
			<div class="imginfoBorder">
				<small>
					<?php echo $doc; ?>
				</small>
				<div class="buttonOut">
					<a href="<?php site_url('admin/media/delete/'.$doc.'/'.$listdir)?>" target="_top" onclick="return deleteImage('<?php echo $doc; ?>');">
						<img src="<?php echo basepath()?>images/edit_trash.gif" width="15" height="15" border="0" alt="Delete" /></a>
				</div>
			</div>
		</div>
		<?php
	}

	function parse_size($size){
		if($size < 1024) {
			return $size.' bytes';
		} else if($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	}

	function imageResize($width, $height, $target) {
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image

		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		//returns the new sizes in html image tag format...this is so you
		//can plug this function inside an image tag and just get the

		return "width=\"$width\" height=\"$height\"";

	}

	function num_files($dir) {
		$total_file 	= 0;
		$total_dir 		= 0;

		if(is_dir($dir)) {
			$d = dir($dir);

			while ( false !== ($entry = $d->read()) ) {
				if ( substr($entry,0,1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos( $entry, '.html' ) === false && strpos( $entry, '.php' ) === false ) {
					$total_file++;
				}
				if ( substr($entry,0,1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry) ) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array( $total_file, $total_dir );
	}


	function imageStyle($listdir) {
		//dg($listdir);
		?>
		<script language="javascript" type="text/javascript">
		
		function updateDir(){
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++) {
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo '/'.$listdir ;} else { echo '/';}  ?>') {
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file) {
			if(confirm("Delete file \""+file+"\"?"))
			return true;

			return false;
		}
		function deleteFolder(folder, numFiles) {
			if(numFiles > 0) {
				alert("There are "+numFiles+" files/folders in \""+folder+"\".\n\nPlease delete all files/folder in \""+folder+"\" first.");
				return false;
			}

			if(confirm("Delete folder \""+folder+"\"?"))
			return true;

			return false;
		}
		updateDir();
		</script>
		<script type="text/javascript" src="../script/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
		<style type="text/css">
		<!--
		div.imgTotal {
			border-top: 1px solid #ccc;
			border-left: 1px solid #ccc;
			border-right: 1px solid #ccc;
		}
		div.imgBorder {
			height: 70px;
			vertical-align: middle;
			width: 88px;
			overflow: hidden;
		}
		div.imgBorder a {
			height: 70px;
			width: 88px;
			display: block;
		}
		div.imgBorder a:hover {
			height: 70px;
			width: 88px;
			background-color: #f1e8e6;
			color : #FF6600;
		}
		.imgBorderHover {
			background: #FFFFCC;
			cursor: hand;
		}
		div.imginfoBorder {
			background: #f6f6f6;
			width: 84px !important;
			width: 90px;
			height: 35px;
			vertical-align: middle;
			padding: 2px;
			overflow: hidden;
			border: 1px solid #ccc;
		}

		.buttonHover {
			border: 1px solid;
			border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight;
			cursor: hand;
			background: #FFFFCC;
		}

		.buttonOut {
		 	border: 0px;
		}

		.imgCaption {
			font-size: 9pt;
			font-family: "MS Shell Dlg", Helvetica, sans-serif;
			text-align: center;
		}
		.dirField {
			font-size: 9pt;
			font-family: "MS Shell Dlg", Helvetica, sans-serif;
			width:110px;
		}
		div.image {
			padding-top: 10px;
		}
		-->
		</style>
		<?php
	}
}
?>