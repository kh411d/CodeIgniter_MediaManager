<?php
define( 'COM_MEDIA_BASE', BASEDIR.'assets/' );
define( 'COM_MEDIA_BASEURL', base_url() . '/assets/' );
Class Admin extends Controller{
var $listdir = '';
var $dirPath = '';	
var $media_template = 'media_manage.php';
var $mosConfig_live_site = '';
	
	function Admin(){
		parent::Controller();
		$this->load->model('media_admin_model','modMedia');
		$this->listdir 	= $_REQUEST['listdir'];
		$this->dirPath 	= $this->input->post('dirPath');
		$this->mosConfig_live_site = site_url();
	}
	
	function index(){
		if($task = $this->input->post('task')){
			$this->$task();
		}
		$this->showMedia( $this->listdir );
	}
	
	function upload(){
		if (isset($_FILES['upload']) && is_array($_FILES['upload']) && isset($this->dirPath)) {
			$dirPathPost 	=$this->dirPath;
			$file 			= $_FILES['upload'];
	
			if (strlen($dirPathPost) > 0) {
				if (substr($dirPathPost,0,1) == '/') {
					$IMG_ROOT .= $dirPathPost;
				} else {
					$IMG_ROOT = $dirPathPost;
				}
			}
	
			if (strrpos( $IMG_ROOT, '/' ) != strlen( $IMG_ROOT )-1) {
				$IMG_ROOT .= '/';
			}
	
			$this->do_upload( $file, COM_MEDIA_BASE . $dirPathPost . '/');
		}
	}
	
	function newdir(){
		if (ini_get('safe_mode')=='On') {
			die( "Directory creation not allowed while running in SAFE MODE as this can cause problems." );
		} else {
			$folder_name = $this->input->post('foldername');
		
			if(strlen($folder_name) >0) {
				if (eregi("[^0-9a-zA-Z_]", $folder_name)) {
					die( "Directory name must only contain alphanumeric characters and no spaces please." );
				}
				$folder = COM_MEDIA_BASE . $dirPath . DIRECTORY_SEPARATOR . $folder_name;
				
				if(!is_dir( $folder ) && !is_file( $folder )) {
					MakePath( $folder );
					$fp = fopen( $folder . "/index.html", "w" );
					fwrite( $fp, "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>" );
					fclose( $fp );
					chmod( $folder."/index.html" , 777 );
					$refresh_dirs = true;
				}
			}
		}
		$this->showMedia( $this->dirPath );
	}
	
	function delete($delFile,$listdir){
		$fullPath = COM_MEDIA_BASE . $listdir . DIRECTORY_SEPARATOR . stripslashes( $delFile );
	
		if (file_exists( $fullPath )) {
			unlink( $fullPath );
		}
		$this->showMedia( $listdir );
	}
	
	
	function deletefolder($delFolder,$listdir=''){
		$del_html 	= COM_MEDIA_BASE . $listdir . $delFolder . DIRECTORY_SEPARATOR . 'index.html';
		$del_folder = COM_MEDIA_BASE . $listdir . $delFolder;
	
	
		
		$entry_count = 0;
		$dir = opendir( $del_folder );
		while ($entry = readdir( $dir )) {
			if( $entry != "." & $entry != ".." & strtolower($entry) != "index.html" )
			$entry_count++;
		}
		closedir( $dir );

		if ($entry_count < 1) {
			//chmod( $del_html , 777 );
			@unlink( $del_html );
			rmdir( $del_folder );
		} else {
			echo '<font color="red">Unable to delete: not empty!</font>';
		}
		$this->showMedia( $listdir );
	}
	
	function ilist($listdir){
		$this->listImages( $listdir );		
	}
	
	function createthumb(){
		 $thumburl = $this->input->post('thumbcode'); 
	  
	         $dirPath = dirName($thumburl)."/";
	        $thumbfile = basename($thumburl);
	         $ext = explode('.',$thumbfile); 
	         
		$allowable = array ('bmp','gif','ico','jpg','png');
	
	    $noMatch = 0;
		foreach( $allowable as $extx ) {
			if ( strcasecmp( $ext[1], $extx ) == 0 ) {
				$noMatch = 1;
			}
		}
	    if(!$noMatch){
			die( 'This file type is not supported for thumbnail' );
	    }
	         
	        $thumbsize = $_POST['thumbsize'] ; 
	        
	        chmod(ROOT_DIR.$dirPath.$thumbfile,0777);
	        if(!file_exists(ROOT_DIR."image/".$ext[0]."_thumbnail.".$ext[1])){
	         thumb(ROOT_DIR.$dirPath.$thumbfile, ROOT_DIR.$dirPath.$ext[0]."_thumbnail.".$ext[1], $thumbsize);
	         sleep(1);
	         chmod(ROOT_DIR.$dirPath.$ext[0]."_thumbnail.".$ext[1],0777);      
	        }
	        
	        
	        $dirPath = dirName($thumburl);
	        $dirPath = "/".basename($dirPath);
	}
	
	/**
	* Show media manager
	* @param string The image directory to display
	*/
	function showMedia( $listdir ) {
		
		// get list of directories
		$imgFiles 	= recursive_listdir( COM_MEDIA_BASE ,'admin_template');
		$images 	= array();
		$folders 	= array();
		$folders[] 	= html_makeOption( "/" );
	   
		$len = strlen( COM_MEDIA_BASE );

		foreach ($imgFiles as $file) {
			$folders[] = html_makeOption( substr( $file, $len ) );
		}
		if (is_array( $folders )) {
			sort( $folders );
		}
			
		// create folder selectlist
		$dirPath = html_selectList( $folders, 'dirPath', "class=\"inputbox\" size=\"1\" onchange=\"goUpDir()\" ", 'value', 'text', $listdir );

		$this->load->view('admin_template/'.$this->media_template,array('dirpath'=>$dirPath,'listdir'=>$listdir));
	 
	}
	
	function do_upload($file, $dest_dir) {
		global $clearUploads;
	
		
	
		if (empty($file['name'])) {
			die( "Upload file not selected" );
		}
		if (file_exists($dest_dir.$file['name'])) {
			die( "Upload FAILED. File already exists" );
		}
	
		$format = substr( $file['name'], -3 );
	
		$allowable = array (
			'bmp',
			'csv',
			'doc',
			'epg',
			'gif',
			'ico',
			'jpg',
			'odg',
			'odp',
			'ods',
			'odt',
			'pdf',
			'png',
			'ppt',
			'swf',
			'txt',
			'xcf',
			'xls'
		);
	
	    $noMatch = 0;
		foreach( $allowable as $ext ) {
			if ( strcasecmp( $format, $ext ) == 0 ) {
				$noMatch = 1;
			}
		}
	    if(!$noMatch){
			die( 'This file type is not supported' );
	    }
	
		if (!move_uploaded_file($file['tmp_name'], $dest_dir.strtolower($file['name']))){
			die( "Upload FAILED" );
		} else {
			chmod($dest_dir.strtolower($file['name']),0777);
			
			header(site_url('admin/media/'.$this->input->post('dirPath')));			
			
		}
	
		$clearUploads = true;
	}
	
	function listImages($listdir) {
		
	
		// get list of images
		$d = @dir( COM_MEDIA_BASE . DIRECTORY_SEPARATOR .$listdir);
	
	//echo $listdir;
		if($d) {
			//var_dump($d);
			$images 	= array();
			$folders 	= array();
			$docs 		= array();
			$allowable 	= '\.xcf$|\.odg$|\.gif$|\.jpg$|\.png$|\.bmp$';
	
			while (false !== ($entry = $d->read())) {
				$img_file = $entry;
				if(is_file( COM_MEDIA_BASE .$listdir.'/'.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html' ) {
					
					
					if (@eregi( $allowable, $img_file )) {
						$image_info 				= @getimagesize( COM_MEDIA_BASE ."/".$listdir.'/'.$img_file);
						$file_details['file'] 		= COM_MEDIA_BASE . $listdir."/".$img_file;
						$file_details['img_info'] 	= $image_info;
						$file_details['size'] 		= filesize( COM_MEDIA_BASE .$listdir."/".$img_file);
						$images[$entry] 			= $file_details;
					} else {
						// file is document
						$file_details['size'] 	= filesize( COM_MEDIA_BASE .$listdir."/".$img_file);
						$file_details['file'] 	= COM_MEDIA_BASE .$listdir."/".$img_file;
						$docs[$entry] 			= $file_details;
					}
				} else if($img_file != 'admin_template' && is_dir( COM_MEDIA_BASE .'/'.$listdir.'/'.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();

			$this->modMedia->imageStyle($listdir);
	
			if(count($images) > 0 || count($folders) > 0 || count($docs) > 0) {
				//now sort the folders and images by name.
				ksort($images);
				ksort($folders);
				ksort($docs);
	
	
				$this->modMedia->draw_table_header();
	
				for($i=0; $i<count($folders); $i++) {
					$folder_name = key($folders);
					$this->modMedia->show_dir('/'.$folders[$folder_name], $folder_name,$listdir);
					next($folders);
				}
	
				for($i=0; $i<count($docs); $i++) {
					$doc_name = key($docs);
					$iconfile= 'images/'.substr($doc_name,-3).'_16.png';
					if (file_exists($iconfile))	{
						$icon = 'images/'.(substr($doc_name,-3)).'_16.png'	;
					} else {
						$icon = 'images/con_info.png';
					}
					$this->modMedia->show_doc($doc_name, $docs[$doc_name]['size'],$listdir, $icon);
					next($docs);
				}
	
				for($i=0; $i<count($images); $i++) {
					$image_name = key($images);
					$this->modMedia->show_image($images[$image_name]['file'], $image_name, $images[$image_name]['img_info'], $images[$image_name]['size'],$listdir);
					next($images);
				}
	
				$this->modMedia->draw_table_footer();
			} else {
				$this->modMedia->draw_no_results();
			}
		} else {
			$this->modMedia->draw_no_dir();
		}
	}
}