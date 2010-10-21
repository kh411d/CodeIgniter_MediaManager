<?php
function recursive_listdir( $base ,$exclude='') {
	static $filelist = array();
	static $dirlist = array();

   if(is_dir($base)) {
		$dh = opendir($base);
		while (false !== ($dir = readdir($dh))) {
			if ($dir !== $exclude && $dir !== '.' && $dir !== '..' && is_dir($base .'/'. $dir) && strtolower($dir) !== 'cvs' && strtolower($dir) !== '.svn') {
				$subbase = $base .'/'. $dir;
				$dirlist[] = $subbase;
				$subdirlist = recursive_listdir($subbase,$exclude);
			}
		}
		closedir($dh);
	}

	return $dirlist;
 }
 
 function rm_all_dir($dir) {
	if(is_dir($dir)) {
		$d = @dir($dir);

		while ( false !== ( $entry = $d->read() ) ) {
			if($entry != '.' && $entry != '..') {
				$node = $dir.'/'.$entry;
				if(is_file($node)) {
					unlink($node);
				} else if(is_dir($node)) {
					rm_all_dir($node);
				}
			}
		}
		$d->close();

		rmdir($dir);
	}
}

function MakePath($base, $path='', $mode = NULL) {
	global $mosConfig_dirperms;

	// convert windows paths
	$path = str_replace( '\\', '/', $path );
	$path = str_replace( '//', '/', $path );
	// ensure a clean join with a single slash
	$path = ltrim( $path, '/' );
	$base = rtrim( $base, '/' ).'/';

	// check if dir exists
	if (file_exists( $base . $path )) return true;

	// set mode
	$origmask = NULL;
	if (isset($mode)) {
		$origmask = @umask(0);
	} else {
		if ($mosConfig_dirperms=='') {
			// rely on umask
			$mode = 0777;
		} else {
			$origmask = @umask(0);
			$mode = octdec($mosConfig_dirperms);
		} // if
	} // if

	$parts = explode( '/', $path );
	$n = count( $parts );
	$ret = true;
	if ($n < 1) {
		if (substr( $base, -1, 1 ) == '/') {
			$base = substr( $base, 0, -1 );
		}
		$ret = @mkdir($base, $mode);
	} else {
		$path = $base;
		for ($i = 0; $i < $n; $i++) {
			// don't add if part is empty
			if ($parts[$i]) {
				$path .= $parts[$i] . '/';
			}
			if (!file_exists( $path )) {
				if (!@mkdir(substr($path,0,-1),$mode)) {
					$ret = false;
					break;
				}
			}
		}
	}
	if (isset($origmask)) {
		@umask($origmask);
	}

	return $ret;
}

	function get_filenames_basename($source_dir, $fext,$output = 'ARRAY_A',$check_dir = FALSE, $include_path=FALSE,$_recursion = FALSE)
	{
		static $_filedata = array();
				
		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}
			
			while (FALSE !== ($file = readdir($fp)))
			{ // echo $file;
				
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0 && $check_dir === TRUE)
				{
					 get_filenames_basename($source_dir.$file.DIRECTORY_SEPARATOR, $output,$include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{  
				   preg_match("/(.*)\.".$fext."/i",$file,$match);
				   if($match[1])$file = $match[1];else continue;
				   	
				   if($output == 'ARRAY_A')
					$_filedata[$file] = ($include_path == TRUE) ? $source_dir.$file : $file;
				   elseif($output == 'ARRAY_N')
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;	
				   
				}
			}
			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}