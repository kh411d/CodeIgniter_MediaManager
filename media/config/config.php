<?php
$config['admin_top_navigation'] =  array(null,'Media',null,null,'media',
												       array('','Manage Media',site_url("admin/media"),null,'Manage Media')
												  );
$config['admin_panel_navigation'][] = array('image'=>cpimage_url("mediamanager.png"),'title'=>'Add Media','url'=>site_url("admin/media"));

