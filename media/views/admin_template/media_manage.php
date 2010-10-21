<?php getBlock('admin','header');?>
<div align="center" class="centermain">
 <div class="main">
   <table class="adminheading" border="0">
		<tr>
			<th class="mediamanager">
			Manage Media
			</th>
		</tr>
   </table>
	<table class="adminform">
	  <tr>
		 <td width="100%" valign="top">
			<div id="cpanel" style="text-align:left">			
					<script language="javascript" type="text/javascript">
		function dirup(){
			var urlquery=frames['imgManager'].location.search.substring(1);
			var curdir= urlquery.substring(urlquery.indexOf('listdir=')+8);
			var listdir=curdir.substring(0,curdir.lastIndexOf('/'));
			frames['imgManager'].location.href='<?php echo site_url('admin/media/ilist/');?>' + listdir;
		}


		function goUpDir() {
			var selection = document.forms[0].dirPath;
			var dir = selection.options[selection.selectedIndex].value;
			frames['imgManager'].location.href='<?php echo site_url('admin/media/ilist/');?>' + dir;
		}
	
		function submitbutton(pressbutton) {
		 submitform(pressbutton);
		}
		
		function submitform(pressbutton){
		 document.adminForm.task.value=pressbutton;
		 try {
		 document.adminForm.onsubmit();
		 }
		 catch(e){}
		 document.adminForm.submit();
		}
		</script>
<button onclick="submitbutton('upload')">Upload File</button>
<button onclick="submitbutton('newdir')">Create Folder</button>

		<form action="<?php site_url('admin/media')?>" name="adminForm" method="post" enctype="multipart/form-data" >
		<table width="100%" align="center">
		<tr>
			<th style="background:none;">
				<table>
				<tr>
					
					<td>
						<table border="0" align="right" cellpadding="0" cellspacing="4" width="600">
						<tr>
							<td align="right" width="200" style="padding-right:10px;white-space:nowrap">
								Create Directory
							</td>
							<td>
								<input class="inputbox" type="text" name="foldername" style="width:400px" />
							</td>
						</tr>
						<tr>
							<td align="right" style="padding-right:10px;;white-space:nowrap">
								Image/URL Code
							</td>
							<td>
								<input class="inputbox" type="text" name="imagecode" style="width:400px" />
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</th>
		<tr>
			<td align="center">
				<fieldset>
					<table width="99%" align="center" border="0" cellspacing="2" cellpadding="2">
					<tr>
						<td>
							<table border="0" cellspacing="1" cellpadding="3"  class="adminheading">
							<tr>
								<td>
									Directory
								</td>
								<td>
									<?php echo $dirpath?>
								</td>
								<td class="buttonOut" width="10">
									<a href="javascript:dirup()">
										<img src="<?php echo basepath();?>images/btnFolderUp.gif" width="15" height="15" border="0" alt="Up" />
									</a>
								</td>
								<td align="right">
									File Upload 
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input class="inputbox" type="file" name="upload" id="upload" size="63" />&nbsp;
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" bgcolor="white">
							<div class="manager">
								<iframe height="360" src="<?php echo site_url('admin/media/ilist/'.$listdir);?>" name="imgManager" id="imgManager" width="100%" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
							</div>
						</td>
					</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>

			</td>
		</tr>
		<tr>
			<td>
				<div style="text-align: right;">
				</div>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="com_media" />
		<input type="hidden" name="task" value="" />
		</form>
		
			</div>
			<div style="clear:both;"> </div>
		 </td>
	  </tr>
	</table>
 </div>
</div>
<?php getBlock('admin','footer');?>