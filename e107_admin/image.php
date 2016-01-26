<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Image Administration Area
 *
 * $URL$
 * $Id$
 *
*/

if(!empty($_GET['action']) && $_GET['action'] == 'dialog')
{
	define('e_MINIMAL',true);
}

if (!defined('e107_INIT'))
{
	require_once("../class2.php");
}

if (!getperms("A") && ($_GET['action'] != 'dialog')) 
{
	e107::redirect('admin');
	exit;
}

e107::js('core', 'plupload/plupload.full.js', 'jquery', 2);
e107::css('core', 'plupload/jquery.plupload.queue/css/jquery.plupload.queue.css', 'jquery');
e107::js('core', 'plupload/jquery.plupload.queue/jquery.plupload.queue.js', 'jquery', 2);
e107::js('core', 'core/mediaManager.js',"jquery",5);
e107::wysiwyg(true);
/*
 * CLOSE - GO TO MAIN SCREEN
 */
if(isset($_POST['submit_cancel_show']))
{
	header('Location: '.e_SELF);
	exit();
}

include_lan(e_LANGUAGEDIR.e_LANGUAGE.'/admin/lan_'.e_PAGE);

if($_GET['action'] == 'dialog')
{
	e107::css('inline', "body { background-color: #373737 } ");
}

if(vartrue($_GET['action']) == 'nav' && e_AJAX_REQUEST) //XXX Doesn't work correctly inside the class for some reason 
{
	define("e_IFRAME",true);

	// require_once(e_ADMIN."auth.php");
		$bbcodeMode = ($_GET['bbcode']=='img') ? 'bbcode=img' : FALSE;
						
		if($_GET['from'])
		{
			$bbcodeMode .= "&from=".intval($_GET['from']);
		}
		
		if($_GET['w'])
		{
			$bbcodeMode .= "&w=".intval($_GET['w']);
		}
		
		$bbcodeMode .= "&nav=1";
			
		$tag = ($bbcodeMode===false) ? false : $_GET['tagid']; // eg. news, news-thumbnail	
		
		if($_GET['search'])
		{
			$bbcodeMode .= "&search=".preg_replace("/[^a-z0-9]/i","",$_GET['search']);
		}
					
		echo e107::getMedia()->mediaSelect($_GET['for'],$tag,$bbcodeMode); 
	
	// require_once(e_ADMIN."footer.php");
	exit;	
	
}

	require(e_HANDLER.'phpthumb/ThumbLib.inc.php');	// For resizing on import. 

$e_sub_cat = 'image';



// $frm = new e_form(); //new form handler
$mes = e107::getMessage();

class media_admin extends e_admin_dispatcher
{

	protected $modes = array(
		'main'		=> array(
			'controller' 	=> 'media_admin_ui',
			'path' 			=> null,
			'ui' 			=> 'media_form_ui',
			'uipath' 		=> null
		),
		'dialog'		=> array(
			'controller' 	=> 'media_admin_ui',
			'path' 			=> null,
			'ui' 			=> 'media_form_ui',
			'uipath' 		=> null
		),
		'cat'		=> array(
			'controller' 	=> 'media_cat_ui',
			'path' 			=> null,
			'ui' 			=> 'media_cat_form_ui',
			'uipath' 		=> null
		)
	);


	protected $adminMenu = array(
		'main/list'			=> array('caption'=> LAN_IMA_M_01, 'perm' => 'A'),
	//	'main/create' 		=> array('caption'=> "Add New Media", 'perm' => 'A'), // Should be handled in Media-Import.
		'main/import' 		=> array('caption'=> LAN_IMA_M_02, 'perm' => 'A|A2'),
		'cat/list' 			=> array('caption'=> LAN_IMA_M_03, 'perm' => 'A'),
		'cat/create' 		=> array('caption'=> LAN_IMA_M_04, 'perm' => 'A'), // is automatic.
	//	'main/settings' 	=> array('caption'=> LAN_PREFS, 'perm' => 'A'), // legacy
		'main/prefs' 		=> array('caption'=> LAN_PREFS, 'perm' => 'A'),
		'main/avatar'		=> array('caption'=> LAN_IMA_M_05, 'perm' => 'A')
	);

/*
	$var['main']['text'] = IMALAN_7;
	$var['main']['link'] = e_SELF;



	$var['avatars']['text'] = IMALAN_23;
	$var['avatars']['link'] = e_SELF."?avatars";


	$var['editor']['text'] = "Image Manipulation (future release)";
	$var['editor']['link'] = e_SELF."?editor";*/



	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'
	);

	protected $menuTitle = LAN_MEDIAMANAGER;

}

class media_cat_ui extends e_admin_ui
{
		protected $pluginTitle	= LAN_IMA_M_03;
		protected $pluginName	= 'core';
		protected $table 		= "core_media_cat";
		protected $pid			= "media_cat_id";
		protected $perPage = 0; //no limit
		protected $batchDelete = false;
		
		public 	$ownerCount = array();
	//	protected $listQry = "SELECT * FROM #core_media_cat"; // without any Order or Limit.
		protected $listOrder = 'media_cat_owner asc';

	//	protected $editQry = "SELECT * FROM #faq_info WHERE faq_info_id = {ID}";

		protected $fields = array(
			//'checkboxes'				=> array('title'=> '',				'type' => null, 			'width' =>'5%', 'forced'=> TRUE, 'thclass'=>'center', 'class'=>'center'),
			'media_cat_id'			=> array('title'=> LAN_ID,			'type' => 'number',			'width' =>'5%', 'nolist'=>true, 'forced'=> TRUE, 'readonly'=>TRUE),
         	'media_cat_image' 		=> array('title'=> LAN_IMAGE,		'type' => 'image', 			'data' => 'str',		'width' => '100px',	'thclass' => 'center', 'class'=>'center', 'readParms'=>'thumb=60&thumb_urlraw=0&thumb_aw=60','readonly'=>FALSE,	'batch' => FALSE, 'filter'=>FALSE),			       	
         	'media_cat_owner' 		=> array('title'=> LAN_OWNER,		'type' => 'dropdown',		'width' => 'auto', 'thclass' => 'left', 'readonly'=>FALSE),
         	
         	'media_cat_type' 		=> array('title'=> LAN_TYPE,		'type' => 'radio',			'data'=>false,		'width' => 'auto', 'thclass' => 'left', 'validate' => true, 'nolist'=>true),
         	
			'media_cat_category' 	=> array('title'=> LAN_CATEGORY,	'type' => 'text',			'data'=>'str',		'width' => 'auto', 'thclass' => 'left', 'readonly'=>TRUE),		
			'media_cat_title' 		=> array('title'=> LAN_TITLE,		'type' => 'text',			'width' => 'auto', 'thclass' => 'left', 'readonly'=>FALSE, 'validate' => true),
         	'media_cat_sef' 		=> array('title'=> LAN_SEFURL,		'type' => 'text',			'width' => 'auto', 'thclass' => 'left', 'readonly'=>FALSE),        
         	'media_cat_diz' 		=> array('title'=> LAN_DESCRIPTION,	'type' => 'bbarea',			'width' => '30%', 'readParms' => 'expand=...&truncate=150&bb=1','readonly'=>FALSE), // Display name
			'media_cat_class' 		=> array('title'=> LAN_VISIBILITY,	'type' => 'userclass',		'width' => 'auto', 'data' => 'int'),
			'media_cat_order' 		=> array('title'=> LAN_ORDER,		'type' => 'text',			'width' => '5%', 'thclass' => 'right', 'class'=> 'right' ),										
			'options' 				=> array('title'=> LAN_OPTIONS,		'type' => 'method',			'noedit'=>true, 'width' => '10%', 'forced'=>TRUE, 'thclass' => 'center last', 'class' => 'center')
		);

	function init()
	{
	
		$restricted = array(
			"_common" 	=> "_common",
			"_icon"		=> "_icon",
			"news"		=> "news",	
			"page"		=> "page",
			"download"	=> "download"					
		);
		
		// FIXME LAN
		$this->fields['media_cat_type']['writeParms'] = array('image' => 'Image', 'file' => 'File', 'video' => 'Video');
		
		if($this->getAction() == 'list')
		{	
			$this->fields['media_cat_owner']['writeParms'] = $restricted;
		}
		
		if($this->getAction() == 'create')
		{
			$this->fields['media_cat_category']['noedit'] = true;
		}
		elseif($this->getAction() == 'edit')
		{
			$this->fields['media_cat_type']['noedit'] = true;
		}

		$sql = e107::getDb();
		
	
		if($sql->gen("SELECT media_cat_owner,  MAX(CAST(SUBSTRING_INDEX(media_cat_category, '_', -1 ) AS UNSIGNED)) as maxnum, count(media_cat_id) as number FROM `#core_media_cat`  GROUP BY media_cat_owner"))
		{
			while($row = $sql->fetch())	
			{
				$this->ownerCount[$row['media_cat_owner']] = $row['number'];
				$own = $row['media_cat_owner']; 
				if(!in_array($own,$restricted))
				{		
					$this->fields['media_cat_owner']['writeParms'][$own] = $own;	
					
					if($row['maxnum'] > 0)
					{
						$this->ownerCount[$row['media_cat_owner']] = $row['maxnum']; // $maxnum;	
					}
				}		
			}
		}
		
		e107::getMessage()->addDebug("Max value for category names: ".print_a($this->ownerCount,true));
	}





	public function createPage()
	{
		if(!count($this->fields['media_cat_owner']['writeParms'])) 
		{
			e107::getMessage()->addInfo("Category creation not available.");
			return;
		}
		
		return $this->getUI()->getCreate();	
	}


	
	public function beforeCreate($new_data)
	{
		// XXX temporary disable when there is no owners, discuss
		if(!$new_data['media_cat_owner'])
		{
			e107::getMessage()->addError('No media owner found.'); // FIXME LAN
			return false;
		}
		//$replace = array("_"," ","'",'"',"."); //FIXME Improve
		//$new_data['media_cat_category'] = str_replace($replace,"-",$new_data['media_cat_category']);
		$type = $this->getRequest()->getPosted('media_cat_type', 'image').'_';
		
		$increment = ($this->ownerCount[$new_data['media_cat_owner']] +1);
		$new_data['media_cat_category'] = $new_data['media_cat_owner'].'_'.$type.$increment;
		if(empty($new_data['media_cat_sef'])) $new_data['media_cat_sef'] = eHelper::title2sef($new_data['media_cat_title']);

		return $new_data;
	}
	
	
	public function beforeUpdate($new_data, $old_data, $id)
	{
		$mes = e107::getMessage();
	
		if($new_data['media_cat_owner']	!= "gallery")
		{
			$mes->addError(LAN_IMA_001);
			return FALSE;
		}
		
		if(empty($new_data['media_cat_sef'])) $new_data['media_cat_sef'] = eHelper::title2sef($new_data['media_cat_title']);
		
		return $new_data;
	}

}

class media_cat_form_ui extends e_admin_form_ui
{
	protected $restrictedOwners = array(
			'_common', 
			'news',
			'page',
			'download',
			'_icon'
	);		
		
	
	function options($parms, $value, $id)
	{

		if($_GET['action'] == 'create' || $_GET['action'] == 'edit')
		{
			return;
		}	
		
		$owner = $this->getController()->getListModel()->get('media_cat_owner');	
		if(!in_array($owner,$this->restrictedOwners))
		{
			return $this->renderValue('options',$value,'',$id);	
		}
			
		
		

	//	$save = ($_GET['bbcode']!='file')  ? "e-dialog-save" : "";
	// e-dialog-close
		
		
	}
}




class media_form_ui extends e_admin_form_ui
{

	function init()
	{
		/*$sql = e107::getDb();
	//	$sql->db_Select_gen("SELECT media_cat_title, media_title_nick FROM #core_media as m LEFT JOIN #core_media_cat as c ON m.media_category = c.media_cat_owner GROUP BY m.media_category");
		$sql->db_Select_gen("SELECT media_cat_title, media_cat_owner FROM #core_media_cat");
		while($row = $sql->db_Fetch())
		{
			$cat = $row['media_cat_owner'];
			$this->cats[$cat] = $row['media_cat_title'];
		}
		asort($this->cats);*/
	//	require(e_HANDLER.'phpthumb/ThumbLib.inc.php');	// For resizing on import. 
				
		if(varset($_POST['multiselect']) && varset($_POST['e__execute_batch']) && (varset($_POST['etrigger_batch']) == 'options__rotate_cw' || varset($_POST['etrigger_batch']) == 'options__rotate_ccw'))
		{
			$type = str_replace('options__','',$_POST['etrigger_batch']);
			$ids = implode(",",$_POST['multiselect']);
			$this->rotateImages($ids,$type);
		}
		
		
		if(varset($_POST['multiselect']) && varset($_POST['e__execute_batch']) && (varset($_POST['etrigger_batch']) == 'options__resize_2048' ))
		{
			$type = str_replace('options__','',$_POST['etrigger_batch']);
			$ids = implode(",",$_POST['multiselect']);
			$this->resizeImages($ids,$type);
		}
		
		
		
	}
	
	function resize_method($curval)
	{
		$frm = e107::getForm();
		
		$options = array(
			'gd1' => 'gd1',
			'gd2' => 'gd2',
			'ImageMagick' => 'ImageMagick'
		);
		
		return $frm->selectbox('resize_method',$options,$curval)."<div class='field-help'>".IMALAN_4."</div>";										
	}
	
	public function rotateImages($ids,$type)
	{
		$sql = e107::getDb();
		$tp = e107::getParser();
		$mes = e107::getMessage();
		ini_set('memory_limit', '150M');
		ini_set('gd.jpeg_ignore_warning', 1);
		
		$degrees = ($type == 'rotate_cw') ? 270 : 90;
		
	//	$mes->addDebug("Rotate Mode Set: ".$type);
		
		//TODO GIF and PNG rotation. 
		
		if($sql->db_Select("core_media","media_url","media_id IN (".$ids.") AND media_type = 'image/jpeg' "))
		{
			while($row = $sql->db_Fetch())
			{
				$original = $tp->replaceConstants($row['media_url']);

				$mes->addDebug("Attempting to rotate by {$degrees} degrees: ".basename($original));
				
				$source = imagecreatefromjpeg($original);
							
				try 
				{
					$rotate = imagerotate($source, $degrees, 0);
				} 
				catch (Exception $e) 
				{
					$mes->addError(LAN_IMA_002.": ".basename($original));
				}  
							
				$srch = array(".jpg",".jpeg");
				$cacheFile = str_replace($srch,"",strtolower(basename($original)))."_(.*)\.cache\.bin";
				
				try 
				{
					imagejpeg($rotate,$original,80);
					$mes->addSuccess(LAN_IMA_002.": ".basename($original));
					e107::getCache()->clearAll('image',$cacheFile);
					$mes->addDebug("Clearing Image cache with mask: ".$cacheFile);
				}
				catch (Exception $e) 
				{
					$mes->addError(LAN_IMA_002.": ".basename($original));
				}  	
			}
		}	
	}


	/**
	 * Resize a single image. 
	 * @param string
	 * @param int
	 * @param int
	 */
	public function resizeImage($oldpath,$img_import_w,$img_import_h)
	{
		$mes = e107::getMessage();
		try
		{
		    $thumb = PhpThumbFactory::create($oldpath);
		    $thumb->setOptions(array('correctPermissions' => true));
		}
		catch (Exception $e)
		{
		     $mes->addError($e->getMessage());
		     return FALSE;
		    // return $this;
		}
		if($WM) // TODO Add watermark prefs for alpha and position. 
		{
			$thumb->resize($img_import_w,$img_import_h)->addWatermark($watermark, 'rightBottom', 30, 0, 0)->save($oldpath); 
		}
		else
		{
		 	if($thumb->resize($img_import_w,$img_import_h)->save($oldpath))
			{
				return TRUE;
			} 
		}	
		
	}




	public function resizeImages($ids,$type)
	{
		
		$sql = e107::getDb();
		$sql2 = e107::getDb('sql2');
		$mes = e107::getMessage();
		$tp = e107::getParser();
		$fl = e107::getFile();
				
		// Max size is 6 megapixel. 
		$img_import_w = 2816;
		$img_import_h = 2112; 
			
		if($sql->db_Select("core_media","media_id,media_url","media_id IN (".$ids.") AND media_type = 'image/jpeg' "))
		{
			while($row = $sql->db_Fetch())
			{
				$path = $tp->replaceConstants($row['media_url']);

				$mes->addDebug("Attempting to resize: ".basename($path));
				
				if($this->resizeImage($path,$img_import_w,$img_import_h))
				{
					
					$info = $fl->get_file_info($path);
					$mes->addSuccess(LAN_IMA_004.": ".basename($path));
					$mes->addSuccess(print_a($info,true));
					$dim = intval($info['img-width'])." x ".intval($info['img-height']);
					$sql2->db_Update("core_media","media_dimensions = '".$dim."', media_size = '".intval($info['fsize'])."' WHERE media_id = ".intval($row['media_id'])."");
				}
				else 
				{
					$mes->addError(LAN_IMA_004.": ".basename($path));
				}	
			}
		}		
		
		
		
	}
	
	
	public function resize_dimensions($curval) // ie. never manually resize another image again!
	{

		$text = "";
	
		$frm = e107::getForm();
		$pref 	= e107::getPref();
		
		$options = array(
			"news-image" 			=> LAN_IMA_O_001,
			"news-bbcode" 			=> LAN_IMA_O_002,
			"page-bbcode" 			=> LAN_IMA_O_003,
		//	"featurebox-image" 		=> LAN_IMA_O_004,
		//	"featurebox-bbcode" 	=> LAN_IMA_O_005,
		);
		
		if(vartrue($pref['e_imageresize']) && is_array($pref['e_imageresize']))
		{
			foreach($pref['e_imageresize'] as $k=>$val)
			{
			
				$options[$k]		= ucfirst($k)." ".LAN_IMA_O_006;
			}
		}
		
		$options = $pref['resize_dimensions'];
		
		foreach($options as $key=>$title)
		{
			$title = ucwords(str_replace("-"," ",$key));
			$valW = vartrue($curval[$key]['w']);
			$valH = vartrue($curval[$key]['h']);
		
			$text .= "<div style='margin-bottomp:8px;text-align:right:width:400px'>".$title.": ";
			$text .= "<input class='e-tip e-spinner input-small' placeholder='ex. 400' style='text-align:right' type='text' name='resize_dimensions[{$key}][w]' value='$valW' size='5' title='maximum width in pixels' /> X ";
			$text .= "<input class='e-tip e-spinner input-small' placeholder='ex. 400' style='text-align:right' type='text' name='resize_dimensions[{$key}][h]' value='$valH' size='5' title='maximum height in pixels' />";
			$text .= "</div>";
		//	$text .= $frm->text("resize_dimensions[{$key}]",$val, 5, array('size'=>'5')).$title."<br />";			
		}	
		
	//	$text .= "<div><br />Warning: This feature is experimental.</div>";
		
		return $text;
		
		
	}
	

	function options($parms, $value, $id)
	{
		//return print_a($_GET,true);
		if($value == 'batch')
		{
			return array(
				"resize_2048"	=> "Reduce Oversized Images",
				"rotate_cw"		=> "Rotate 90&deg; cw",
				"rotate_ccw"	=> "Rotate 90&deg; ccw"				
			);	
		}
		
		if($_GET['action'] == 'edit')
		{
			return;
		}	
		
		$tagid = vartrue($_GET['tagid']); 
		$path = $this->getController()->getListModel()->get('media_url');
		$title = $this->getController()->getListModel()->get('media_name');
		$id = $this->getController()->getListModel()->get('media_id');
		$preview = basename($path);
		
		$bbcode = (vartrue($_GET['bbcode']) == 'file')  ? "file" : "";
	//	$save = ($_GET['bbcode']!='file')  ? "e-dialog-save" : "";
	// e-dialog-close
	
		$for = $this->getController()->getQuery('for');

		if(strpos($for, '_file') !==false)
		{
			$type = 'file';
		}
		else
		{
			$type = 'image';
		}

		// File Picker.
		if($_GET['action'] == 'dialog' && ($type == 'file'))
		{		
			$text = "<input type='button' value='Select' data-placement='left' class='e-tip e-media-select e-dialog-save e-dialog-close btn btn-primary btn-large' data-id='{$id}' data-name=\"".$title."\" data-type='file' data-target='{$tagid}' data-bbcode='{$bbcode}' data-path='{$path}' data-preview='{$preview}' title=\"".$title."\"  />";
		}
		else
		{
			$text = $this->renderValue('options',$value,'',$id);
		}
		
		return "<div class='nowrap'>".$text."</div>";
		
	}
	

	function media_preview($curVal, $mode, $attributes, $id=null)
	{
		
		$attributes['type'] = 'image';
		
		switch($mode)
		{
			case 'read':
				$attributes['readParms'] = 'thumb=60&thumb_urlraw=0&thumb_aw=60';
				$val 	= $this->getController()->getListModel()->get('media_url');	
			break;

			case 'write':
				$attributes['readParms'] = 'thumb=180&thumb_urlraw=0&thumb_aw=180';
				$val 	= $this->getController()->getModel()->get('media_url');		
			break;

			case 'filter':
			case 'batch':
				return '';
			break;
		}

		return $this->renderValue('media_preview', $val, $attributes, $id);
	}


/*
	function media_category($curVal,$mode) // not really necessary since we can use 'dropdown' - but just an example of a custom function.
	{
		
		$curVal = explode(",",$curVal);
		
		if($mode == 'read')
		{
			return $this->getController()->getMediaCategory($curVal);
			//return $this->cats[$curVal];
		}

		if($mode == 'batch') // Custom Batch List for release_type
		{
			return $this->getController()->getMediaCategory();
		}

		if($mode == 'filter') // Custom Filter List for release_type
		{
			return $this->getController()->getMediaCategory();
		}


		$text = "<select class='tbox' name='media_category[]' multiple='multiple'>";
		$cats = $this->getController()->getMediaCategory();
		
		foreach($cats as $key => $val)
		{
			$selected = (in_array($key,$curVal)) ? "selected='selected'" : "";
			$text .= "<option value='{$key}' {$selected}>".$val."</option>\n";
		}
		$text .= "</select>";
		return $text;
	}*/
}


class media_admin_ui extends e_admin_ui
{

		protected $pluginTitle = LAN_MEDIAMANAGER;
		protected $pluginName = 'core';
		protected $table = "core_media";

		protected $listQry = "SELECT SQL_CALC_FOUND_ROWS m.*,u.user_id,u.user_name FROM #core_media AS m LEFT JOIN #user AS u ON m.media_author = u.user_id "; // without any Order or Limit.

	//	//protected $editQry = "SELECT * FROM #comments WHERE comment_id = {ID}";

		protected $pid = "media_id";
		protected $perPage = 10;
		protected $batchDelete = true;
	//	protected $defaultOrder = 'desc';
		protected $listOrder = 'm.media_id desc'; // show newest images first. 
		public $deleteConfirmScreen = true;
		public $deleteConfirmMessage = 'You are about to delete [x] records and <strong>ALL CORRESPONDING FILES</strong>! Please confirm to continue!';


    	protected $preftabs			= array('General',"Watermark", "Youtube"); 
    	 
		protected $fields = array(
			'checkboxes'			=> array('title'=> '',				'type' => null,			'data'=> null,		'width' =>'5%', 'forced'=> TRUE, 'thclass'=>'center', 'class'=>'center'),
			'media_id'				=> array('title'=> LAN_ID,			'type' => 'number',		'data'=> 'int',		'width' =>'5%', 'forced'=> TRUE, 'nolist'=>TRUE),
      		'media_preview'			=> array('title'=>'Preview', 		'type'=>'method', 		'data'=>false, 	'forced'=>true, 'width' => '110px', 'thclass' => 'center', 'class'=>'center'),
      		'media_url' 			=> array('title'=> 'Path',			'type' => 'text',		'data'=> 'str',	'inline'=>false,	'thclass' => 'left', 'class'=>'left', 'width' => 'auto', 'writeParms'=>'size=xxlarge'),
			'media_category' 		=> array('title'=> LAN_CATEGORY,	'type' => 'comma',	'inline'=>false,	'data'=> 'str',		'width' => '10%', 'filter' => true, 'batch' => true, 'class'=>'left'),
			
		// Upload should be managed completely separately via upload-handler.
       	//	'media_upload' 			=> array('title'=> "Upload File",	'type' => 'upload',		'data'=> false,		'readParms' => 'hidden', 'writeParms' => 'disable_button=1', 'width' => '10%', 'nolist' => true),
			'media_name' 			=> array('title'=> LAN_TITLE,		'type' => 'text',		'data'=> 'str',		'inline'=>true, 'width' => 'auto'),
			'media_caption' 		=> array('title'=> "Caption",		'type' => 'text',		'data'=> 'str',		'inline'=>true, 'width' => 'auto'),
         	// media_description is type = textarea until bbarea can be reduced to not include youtube etc
         	'media_description' 	=> array('title'=> LAN_DESCRIPTION,	'type' => 'textarea',		'data'=> 'str',		'width' => 'auto', 'thclass' => 'left first', 'readParms' => 'truncate=100', 'writeParms' => 'counter=0'),
         	'media_type' 			=> array('title'=> "Mime Type",		'type' => 'dropdown',		'data'=> 'str',		'filter'=>true, 'width' => 'auto', 'noedit'=>TRUE),
			'media_author' 			=> array('title'=> LAN_USER,		'type' => 'user',		'data'=> 'int', 	'width' => 'auto', 'thclass' => 'center', 'class'=>'center','readParms' => 'link=1', 'filter' => true, 'batch' => true, 'noedit'=>TRUE	),
			'media_datestamp' 		=> array('title'=> LAN_DATESTAMP,	'type' => 'datestamp',	'data'=> 'int',		'width' => '10%', 'noedit'=>TRUE),	// User date
          	'media_size' 			=> array('title'=> "Size",			'type' => 'number',		'data'=> 'int',		'width' => 'auto', 'readonly'=>2),
			'media_dimensions' 		=> array('title'=> "Dimensions",	'type' => 'text',		'data'=> 'str',		'width' => '5%', 'readonly'=>TRUE, 'class'=>'nowrap','noedit'=>TRUE),
			'media_userclass' 		=> array('title'=> LAN_USERCLASS,	'type' => 'userclass',	'data'=> 'str',		'inline'=>true, 'width' => '10%', 'thclass' => 'center','filter'=>TRUE,'batch'=>TRUE ),
			'media_tags' 			=> array('title'=> "Tags/Keywords",	'type' => 'tags',		'data'=> 'str',		'width' => '10%',  'filter'=>TRUE,'batch'=>TRUE ),
			'media_usedby' 			=> array('title'=> 'Used by',		'type' => 'hidden',		'data'=> 'text', 	'width' => 'auto', 'thclass' => 'center', 'class'=>'center', 'nolist'=>true, 'readonly'=>TRUE	),

			'options' 				=> array('title'=> LAN_OPTIONS,		'type' => 'method',			'data'=> null,		'forced'=>TRUE, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center', 'batch'=>true, 'noedit'=>true)
		);


		protected $mimePaths = array(
				'text'			=> e_MEDIA_FILE,
				'multipart'		=> e_MEDIA_FILE,
				'application'	=> e_MEDIA_FILE,
			//	'audio'			=> e_MEDIA_FILE,
				'image'			=> e_MEDIA_IMAGE,
				'video'			=> e_MEDIA_VIDEO,
				'other'			=> e_MEDIA_FILE
		);
		
		protected $fieldpref = array( 'media_id', 'media_title', 'media_caption', 'media_category', 'media_datestamp','media_userclass', 'options');




	
	protected $prefs = array(
		'image_post'	   				=> array('title'=> IMALAN_1, 'tab'=>0, 'type'=>'boolean', 'data'=>'int', 'writeParms'=>'help=IMALAN_2'),
		'image_post_class' 				=> array('title'=> IMALAN_10, 'type' => 'userclass', 'data'=>'int', 'writeParms'=>'help=IMALAN_11&classlist=public,guest,nobody,member,admin,main,classes' ),
		'image_post_disabled_method'	=> array('title'=> IMALAN_12, 'type' => 'boolean','writeParms'=>'enabled=IMALAN_15&disabled=IMALAN_14'),
		'resize_method'					=> array('title'=> IMALAN_3, 'type'=>'method', 'data'=>'str'),
		'thumbnail_quality'				=> array('title'=> IMALAN_73, 'type'=>'number', 'data'=>'int', 'writeParms'=>'help=IMALAN_74'),
	
		'im_width'						=> array('title'=> IMALAN_75, 'type'=>'number', 'data'=>'int', 'writeParms'=>'help=IMALAN_76'),
		'im_height'						=> array('title'=> IMALAN_77, 'type'=>'number', 'data'=>'int', 'writeParms'=>'help=IMALAN_76'),
		'resize_dimensions'				=> array('title'=> IMALAN_79, 'type'=>'method', 'data'=>'str'),
		
		'watermark_activate'			=> array('title'=> IMALAN_80, 'tab'=>1, 'type' => 'number', 'data' => 'str', 'help'=>IMALAN_81), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		'watermark_text'				=> array('title'=> IMALAN_82,'tab'=>1, 'type' => 'text', 'data' => 'str', 'help'=>IMALAN_83), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		'watermark_font'				=> array('title'=> IMALAN_84,'tab'=>1, 'type' => 'dropdown', 'data' => 'str', 'help'=>IMALAN_85), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		'watermark_size'				=> array('title'=> IMALAN_86, 'tab'=>1,'type' => 'number', 'data' => 'int', 'help'=>IMALAN_87), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		
		'watermark_pos'					=> array('title'=> IMALAN_88,'tab'=>1, 'type' => 'dropdown', 'data' => 'str', 'help'=>IMALAN_88), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		'watermark_margin'				=> array('title'=> IMALAN_90, 'tab'=>1,'type' => 'number', 'data' => 'int', 'help'=>IMALAN_91), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		
		'watermark_color'				=> array('title'=> IMALAN_92, 'tab'=>1, 'type' => 'text', 'data' => 'str', 'help'=>IMALAN_93), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
		'watermark_shadowcolor'			=> array('title'=> IMALAN_94, 'tab'=>1,'type' => 'text', 'data' => 'str', 'help'=>IMALAN_95), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),
	
		'watermark_opacity'				=> array('title'=> IMALAN_96, 'tab'=>1, 'type' => 'number', 'data' => 'int', 'help'=>IMALAN_97), // 'validate' => 'regex', 'rule' => '#^[\d]+$#i', 'help' => 'allowed characters are a-zA-Z and underscore')),

		// https://developers.google.com/youtube/player_parameters
		'youtube_apikey'		        => array('title'=> "YouTube Public API key", 'tab'=>2, 'type' => 'text', 'data'=>'str', 'help'=>IMALAN_99, 'writeParms'=>array('post'=>" <a target='_blank' href='https://code.google.com/apis/console/'>More</a>")),

		'youtube_default_account'		=> array('title'=> IMALAN_98, 'tab'=>2, 'type' => 'text', 'data'=>'str', 'help'=>IMALAN_99),

		'youtube_rel'					=> array('title'=> IMALAN_100, 'tab'=>2, 'type' => 'boolean', 'data'=>'int', 'help'=>''),
		'youtube_showinfo'				=> array('title'=> IMALAN_101, 'tab'=>2, 'type' => 'boolean', 'data'=>'int', 'help'=>''),
		'youtube_cc_load_policy'		=> array('title'=> IMALAN_102, 'tab'=>2, 'type' => 'boolean', 'data'=>'int', 'help'=>''),
		'youtube_modestbranding'		=> array('title'=> IMALAN_103, 'tab'=>2, 'type' => 'boolean', 'data'=>'int', 'help'=>''),
		'youtube_bbcode_responsive'		=> array('title'=> IMALAN_104, 'tab'=>2, 'type' => 'boolean', 'data'=>'int', 'help'=>''),
	
		// 

	);
	

	

	/*

	<tr>
								<td>
									".IMALAN_1."
								</td>
								<td>
									<div class='auto-toggle-area autocheck'>
										".$frm->checkbox('image_post', 1, $pref['image_post'])."
										<div class='field-help'>".IMALAN_2."</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									".IMALAN_10."
								</td>
								<td>
									".r_userclass('image_post_class',$pref['image_post_class'],"off","public,guest,nobody,member,admin,main,classes")."
									<div class='field-help'>".IMALAN_11."</div>
								</td>
							</tr>
	
							<tr>
								<td>
									".IMALAN_12."
								</td>
								<td>
									".$frm->select_open('image_post_disabled_method')."
										".$frm->option(IMALAN_14, '0', ($pref['image_post_disabled_method'] == "0"))."
										".$frm->option(IMALAN_15, '1', ($pref['image_post_disabled_method'] == "1"))."
									".$frm->select_close()."
									<div class='field-help'>".IMALAN_13."</div>
								</td>
							</tr>";
							
							list($img_import_w,$img_import_h) = explode("x",$pref['img_import_resize']);

							$text .= "						
							<tr>
								<td>".IMALAN_105."<div class='label-note'>".IMALAN_106."</div></td>
								<td>
									".$frm->text('img_import_resize_w', $img_import_w,4)."px X ".$frm->text('img_import_resize_h', $img_import_h,4)."px
								</td>
							</tr>
	
							<tr>
								<td>".IMALAN_3."<div class='label-note'>".IMALAN_54." {$gd_version}</div></td>
								<td>
									".$frm->select_open('resize_method')."
										".$frm->option('gd1', 'gd1', ($pref['resize_method'] == "gd1"))."
										".$frm->option('gd2', 'gd2', ($pref['resize_method'] == "gd2"))."
										".$frm->option('ImageMagick', 'ImageMagick', ($pref['resize_method'] == "ImageMagick"))."
									".$frm->select_close()."
									<div class='field-help'>".IMALAN_4."</div>
								</td>
							</tr>";
	*/
	protected $cats = array();
	protected $owner = array();
	protected $ownercats = array();


	function init()
	{
		if(E107_DEBUG_LEVEL > 0)
		{
			$this->fields['media_url']['inline'] = true;
		}

		$sql = e107::getDb();
	//	$sql->db_Select_gen("SELECT media_cat_title, media_title_nick FROM #core_media as m LEFT JOIN #core_media_cat as c ON m.media_category = c.media_cat_owner GROUP BY m.media_category");
		$sql->gen("SELECT media_cat_title, media_cat_owner, media_cat_category FROM `#core_media_cat`");
		while($row = $sql->fetch())
		{
			$cat = $row['media_cat_category'];
			$owner = $row['media_cat_owner'];
			$this->owner[$owner] = $owner;
			$this->ownercats[$owner.'|'.$cat] = $row['media_cat_title'];
			$this->cats[$cat] = $row['media_cat_title'];
		}
		asort($this->cats);
		
		
		$tmp = $sql->retrieve('core_media','media_type','media_type !="" GROUP BY media_type',true); 
		$mimeTypes = array(); 
		foreach($tmp as $val)
		{
			$id =  $val['media_type'];
			$mimeTypes[$id] = $id; 	
		}
		asort($mimeTypes);
		
		$this->fields['media_category']['writeParms'] = $this->cats;
		$this->fields['media_type']['writeParms'] = $mimeTypes;
		
				
		$pref 	= e107::getPref();
		$tp 	= e107::getParser();
		$fl 	= e107::getFile();
		$path 	= e_THEME.$pref['sitetheme']."/fonts/";
	
		$fDir = $fl->get_files(e_THEME.$pref['sitetheme']."/fonts/",".ttf",'',2);
		$fonts = array(0=>'None');
		foreach($fDir as $f)
		{			
			$id = $tp->createConstants($f['path'].$f['fname'],'rel');
			$fonts[$id] = $f['fname'];	
		}
		
		
		$this->prefs['watermark_font']['writeParms'] 		= $fonts;	
		$this->prefs['watermark_font']['readParms'] 		= $fonts;	
		
		$wm_pos = array(
			'BR'	=> "Bottom Right",
			'BL'	=> "Bottom Left",
			'TR'	=> "Top Right",
			'TL'	=> "Top Left",
			'C'		=> "Center",
			'R'		=> "Right",
			'L'		=> "Left",
			'T'		=> "Top",
			'B'		=> "Bottom",
			'*'		=> "Tile"
		);
		
		$this->prefs['watermark_pos']['writeParms'] 		= $wm_pos;	
		$this->prefs['watermark_pos']['readParms'] 			= $wm_pos;	
	
		e107::getCache()->clearAll('image');
		
	//	print_a($_GET);
		
		if($this->getAction() == 'youtube')
		{
			$parm = array('search'=>$_GET['search']);	
			echo $this->videoTab($parm);
			exit;
		}
		
		if($this->getAction() == 'glyph')
		{
			$parm = array('search'=>$_GET['search']);	
			echo $this->glyphTab($parm);
			exit;
		}
		
		if($this->getAction() == 'nav' )
		{
			//echo $this->navPage();\
		//	$this->getResponse()->setIframeMod(); // disable header/footer menus etc. 
		//	print_a($_GET);

		}
		


		if(varset($_POST['batch_import_selected']))
		{
			$this->batchImport();
		}
		
		if(varset($_POST['batch_import_delete']))
		{
			$this->batchDelete();
		}

		if(varset($_POST['update_options']))
		{
			$this->updateSettings();
		}
		
		// filepicker stuff. 
		if($this->getQuery('mode') == 'dialog')//TODO Check this actually does something, as it was changed to 'action'. 
		{
			if(!ADMIN){ exit; }
			
			
		}
		
		
		

		if($this->getQuery('iframe'))
		{
			e107::js('tinymce4','plugins/compat3x/tiny_mce_popup.js');
 			$this->getResponse()->setIframeMod(); // disable header/footer menus etc. 
 			
 			if(!$this->getQuery('for'))
			{
				$this->setPosted('media_category', "_common");
				$this->getModel()->set('media_category', "_common");
			}
			elseif($this->getMediaCategory($this->getQuery('for')))
			{
				$this->setPosted('media_category', $this->getQuery('for'));
				if(!$this->getId())
				{
					$this->getModel()->set('media_category', $this->getQuery('for'));
				}	
			}
			
		
		}
	
	}


	function navPage() // no functioning correctly - see e_AJAX_REQUEST above. 
	{
	
		
		$bbcodeMode = ($this->getQuery('bbcode')=='img') ? 'bbcode=img' : FALSE;
						
		if($_GET['from'])
		{
			$bbcodeMode .= "&from=".intval($_GET['from']);
		}
		
		$bbcodeMode .= "&nav=1";
			
		$tag = ($bbcodeMode) ? "" : $this->getQuery('tagid');
		echo e107::getMedia()->mediaSelect($this->getQuery('for'),$this->getQuery('tagid'),$bbcodeMode); // eg. news, news-thumbnail	
	
		return;	
	}
	

		


	function dialogPage() // Popup dialogPage for Image Selection. 
	{
		$cat = $this->getQuery('for');		
		$file		= (substr($cat,-5) == "_file") ? TRUE : FALSE;
		$mes = e107::getMessage();
		$mes->addDebug("For:".$cat);
		$mes->addDebug("Bbcode: ".$this->getQuery('bbcode'));

	
		
		$this->processUploadUrl(true, $cat);
		
		if($file)
		{
			$cat = e107::getParser()->toDB($cat);
			if(!isset($this->cats[$cat]))
			{
				return;
			}
			
			$this->listQry = "SELECT m.*,u.user_id,u.user_name FROM #core_media AS m LEFT JOIN #user AS u ON m.media_author = u.user_id WHERE m.media_category = '".$cat."' "; // without any Order or Limit.
			
			unset($this->fields['checkboxes']);
			$this->fields['options']['type'] = 'method';
			$this->fields['media_category']['nolist'] = true;
			$this->fields['media_userclass']['nolist'] = true;
			$this->fields['media_dimensions']['nolist'] = true;
			$this->fields['media_description']['nolist'] = true;
			$this->fields['media_type']['nolist'] = true;
			
			foreach($this->fields as $k=>$v)
			{
				$this->fields[$k]['filter'] = false;	
			}	
						
			echo $this->mediaSelectUpload('file');	
		}
		else
		{
			echo $this->mediaSelectUpload();		
		}	
		
	}
	
	
	
	
	function uploadTab()
	{
		if(!ADMIN){ exit; }

		if(!getperms('A') && !getperms('A1'))
		{
			return '';
		}

		// if 'for' has no value, files are placed in /temp and not added to the db.
		$text = "<h4>From your computer</h4>";
		$text .= '<div id="uploader" rel="'.e_JS.'plupload/upload.php?for='.$this->getQuery('for').'">
	        <p>No HTML5 support.</p>
		</div>';
	    $text .= '<hr />';
	    $frm = e107::getForm();

	    $text .= $frm->open('upload-url-form','post');
		$text .= '<div class="plupload_header_content">';
		$text .= "<h4>From a remote location</h4>";
		$text .= "<div class='plupload_header_text form-inline' style='padding-left:20px;padding-right:20px'>";
		$text .= "<table class='table'>";

		$text .= "<tr>
				<td class='text-nowrap'>Image/File URL:</td>
				<td><input type='text' name='upload_url' size='255' style='width:100%' placeholder='eg. http://website.com/some-image.jpg' /></td>
				<td style='text-align:left'>".$frm->admin_button('upload_remote_url',1,'create','Start Upload')."</td>
				</tr>";
		$text .= "<tr><td>Caption (optional):</td><td><input type='text' name='upload_caption' size='255' style='width:100%' placeholder='eg. My Image Caption' /></td>
<td></td></tr>";

		$text .= "</table>";
	//	$text .= ;

	    $text .= "</div>";
		$text .= "</div>\n\n";
		
		$text .= $frm->close();
	   
		return $text;
	}


	function mediaSelectUpload($type='image') 
	{
		$frm = e107::getForm();
		$videoActive = 'inactive';
		
		$options = array();
		$options['bbcode'] = ($this->getQuery('bbcode')=='img') ? 'img' : FALSE;
		
						
		$text = "<ul id='admin-ui-media-manager' class='nav nav-tabs'>\n";
		
		if($this->getQuery('bbcode') != 'video' && $this->getQuery('bbcode') != 'glyph')
		{
			$text .= "<li class='active'><a data-toggle='tab' href='#core-media-select'>Choose from Library</a></li>\n";	
		}
		else
		{
			if($this->getQuery('bbcode') == 'glyph')
			{
				$glyphActive = 'active';	
			}
			else 
			{
				$videoActive = 'active';		
			}
				
					
		}
		
		if(getperms('A|A1') && ($this->getQuery('bbcode') != 'video' && $this->getQuery('bbcode') !='glyph'))
		{
			$text .= "<li><a data-toggle='tab' href='#core-media-upload'>Upload a File</a></li>";
		}
		
		if(varset($options['bbcode']) == 'img')
		{
			$text .= "<li><a data-toggle='tab' href='#core-media-style'>Appearance</a></li>\n";	
		}
		
		if($this->getQuery('glyphs') == 1 || $this->getQuery('bbcode') == 'glyph')
		{
			$text .= "<li class='{$glyphActive}'><a data-toggle='tab' href='#core-media-glyphs'>Glyphs</a></li>\n";	
		}
		
		if($this->getQuery('video') == 1 || $this->getQuery('bbcode') == 'video')
		{
			$text .= "<li class='{$videoActive}'><a data-toggle='tab' href='#core-media-video'>Youtube</a></li>\n";	
		}
		
		
		if(varset($_GET['from']))
		{
			$options['from'] .= intval($_GET['from']);
		}
		
	
		
				
		$text .= "
			</ul>
			<div class='tab-content media-manager'>";
			
		if($this->getQuery('bbcode') != 'video' && $this->getQuery('bbcode') != 'glyph')
		{	
			$text .= "<div class='tab-pane active' id='core-media-select'>			
					<div class='table' style='display:block'>";
				
			$text .= $this->imageTab($type,$options);
				
			$text .= "</div></div>";
		}	

			
		$text .= "<div class='tab-pane' id='core-media-upload'>
			";
			
		$this->fields['media_category']['readonly']	= TRUE;
		$this->fields['media_url']['noedit'] 		= TRUE;
		$this->fields['media_userclass']['noedit']	= TRUE;

		if(getperms('A|A1'))
		{
			$text .=  $this->uploadTab(); // To test upload script with plupload
		}

	//	$text .=  $this->CreatePage(); // comment me out to test plupload
				
		$text .= "	
			</div>";
		
		/* In BBCODE-Mode this dialog rerturns an [img] bbcode to the 'tagid' container with the appropriate parms generated. 
		 * eg. [img style=float:left;margin-right:3px]{e_MEDIA_IMAGE}someimage.jpg[/img]
		 * Then the dialog may be used as a bbcode img popup and within TinyMce also. 
		 * 
		 */
		
		if($options['bbcode']) //TODO LAN lan_image.php
		{
			$text .= "<div class='tab-pane' id='core-media-style'>
				
				<div class='row'>
				<div class='col-md-6 span6'>
				<table class='table'>
				<colgroup>
					<col class='col-label' />
					<col class='col-control' />
				</colgroup>
				<tbody>
					<tr>
						<td>Caption: </td>
						<td>
						<input type='text' class='e-media-attribute' id='alt' name='alt' size='4' style='width:100%' value='' />
						</td>
					</tr>
					<tr>
						<td>Dimensions: </td>
						<td>
						<input type='text' class='e-media-attribute' id='width' name='width' size='4' style='width:50px' value='' /> px &nbsp;
						&#10060; &nbsp;<input type='text' class='e-media-attribute' id='height' name='height' size='4' style='width:50px' value=''  /> px
						</td>
					</tr>
			
					<tr>
						<td>Text flow: </td>
						<td>".$frm->selectbox('float', array('default'=>'Default','left'=>"Left",'right'=>'Right'))."</td>
					</tr>
					
					<tr>
						<td>Margin-Left: </td>
						<td><input class='e-media-attribute input-mini' type='text' id='margin-left' name='margin_left' value='' /> px</td>
					</tr>
					
					<tr>
						<td>Margin-Right: </td>
						<td><input class='e-media-attribute input-mini' type='text' id='margin-right' name='margin_right' value=''  /> px</td>
					</tr>
					
					<tr>
						<td>Margin-Top: </td>
						<td><input class='e-media-attribute input-mini' type='text' id='margin-top' name='margin_top' value=''  /> px</td>
					</tr>
					
					<tr>
						<td>Margin-Bottom: </td>
						<td><input class='e-media-attribute input-mini' type='text' id='margin-bottom' name='margin_bottom' value=''  /> px</td>
					</tr>
		
			</tbody></table>
			</div>
			<div class='col-md-6 span6'>
			<h5>".LAN_PREVIEW."</h5>
		
			<img class='well' id='preview' src='".e_IMAGE_ABS."generic/blank.gif' style='min-width:220px; min-height:180px;' />
			
		
			</div>
			</div>
			</div>";
		}	
		
		if($this->getQuery('glyphs') == 1 || $this->getQuery('bbcode') == 'glyph')
		{
			//TODO 
			$text .= "<div class='tab-pane clearfix {$glyphActive}' id='core-media-glyphs' style='font-size:24px'>";
	
			$text .= $this->glyphTab();
			
			$text .= "</div>
			";
		
		
		}
		
		if($this->getQuery('video') || $this->getQuery('bbcode') == 'video')
		{
			$text .= "<div class='tab-pane clearfix {$videoActive}' id='core-media-video' >";
		//	$text .= "<div class='row-fluid'>";
			$text .= $this->videoTab();
			$text .= "</div>";	
		//	$text .= "</div>";	
		}
			
		
		
		
		
		
		$text .= "</div>";
		
		// For BBCODE/TinyMce mode.
		// e-dialog-save
				
		if($options['bbcode'] || E107_DEBUG_LEVEL > 0)
		{
						
			$text .= "<div style='text-align:right;padding:5px'>
			
			<button type='submit' class='btn btn-success submit e-dialog-save' data-bbcode='".$options['bbcode']."' data-target='".$this->getQuery('tagid')."' name='save_image' value='Save it'  >
			<span>Save</span>
			</button>
			<button type='submit' class=' btn btn-default submit e-dialog-close' name='cancel_image' value='Cancel' >
			<span>Cancel</span>
			</button>
			</div>";

		}
		
		$type = (E107_DEBUG_LEVEL > 0) ?  "text" : "hidden";
		$br = (E107_DEBUG_LEVEL > 0) ?  "<br />" : "";

		$text .= "
		".$br."<input title='bbcode' type='{$type}' readonly='readonly' class='span11' id='bbcode_holder' name='bbcode_holder' value='' />
		".$br."<input title='html/wysiwyg' type='{$type}' class='span11' readonly='readonly' id='html_holder' name='html_holder' value='' />
		".$br."<input title='(preview) src' type='{$type}' class='span11' readonly='readonly' id='src' name='src' value='' />
		".$br."<input title='path (saved to db)' type='{$type}' class='span11' readonly='readonly' id='path' name='path' value='' />				
		";		
		
		return $text;
	}



	function imageTab($type,$options)
	{
		$tag = ($options['bbcode']) ? "" : $this->getQuery('tagid');
		
		if(varset($_GET['w']))
		{
			$options['w'] = intval($_GET['w']);
		}
		
		if($type == 'file')
		{
			$this->perPage = 0;
			$this->getTreeModel()->setParam('db_query', $this->_modifyListQry(false, false, false, false, $this->listQry))->load();
			$text = $this->getUI()->getList();	
		}
		else 
		{
			$text = e107::getMedia()->mediaSelect($this->getQuery('for'),$this->getQuery('tagid'), $options); // eg. news, news-thumbnail				
		}
		
		return $text;
	}
		
		
		
		

	
	
	function glyphTab($parm='')
	{

		$parms = array(
			'width'	 	=> 32, 
			'height'	=> 32, 
			'type'		=>'glyph', 
			'tagid'		=> $this->getQuery('tagid'), 
			'action'	=>'glyph', 								// Used by AJAX to identify correct function. 
			'perPage'	=> 80,
			'gridClass'	=> 'media-carousel-item-glyph pull-left',
			'bbcode'	=>'glyph',
			'close'		=> 'true'		
		
		);
		
		//TODO FIXME Upgrade to bs3 when Bootstrap3 Admin is ready. 

		$bs2 = e107::getMedia()->getGlyphs('bs3','glyphicon-');
		
		foreach($bs2 as $val)
		{
			$items[] = array( 
					'previewUrl'	=> 'glyphicon '.$val,
					'saveValue'		=> $val.'.glyph',
					'thumbUrl'		=> $val,
					'title'			=> $val,
					'slideCaption'	=> 'Bootstrap',
					'slideCategory'	=> 'bootstrap'
			); 		
				
		}

		
		$fa4 = e107::getMedia()->getGlyphs('fa4');

		foreach($fa4 as $val)
		{
			$items[] = array( 
					'previewUrl'	=> 'fa fa-'.$val,
					'saveValue'		=> $val.'.glyph',
					'thumbUrl'		=> $val,
					'title'			=> $val,
					'slideCaption'	=> 'Font-Awesome 4',
					'slideCategory'	=> 'font-awesome'
			); 		

		}
		
		if(vartrue($parm['search']))
		{
			$filtered = array();
			foreach($items as $v)
			{
				if(strpos($v['title'], $parm['search'])!==false)
				{
					$v['slideCaption'] = '';
					$filtered[] = $v;	
					
				}	
			}	
			
			$items = $filtered;
		}
		
		return e107::getMedia()->browserCarousel($items, $parms);
	}

	/**
	 * Extract Video or Playlist code from a URL
	 * Currently works with v=xxx or list=xxxx
	 * @param $url
	 * @return string
	 */
	function getYouTubeCode($url)
	{
		$url = str_replace("url:","http://",$url);

		list($tmp,$qry) = explode("?",$url);
		parse_str($qry,$opt);


		if(!empty($opt['list']))
		{
			return 'playlist:'.$opt['list'];
		}

		if(!empty($opt['v']))
		{
			return 'video:'.$opt['v'];
		}

		$pattern = '#^(?:https?://)?(?:www\.|m\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($pattern, $url, $matches);

		return isset($matches[1]) ? 'video:'.trim($matches[1]) : false;


	}


	/**
	 * @param string $parm
	 * @return mixed|string
	 * @see https://www.googleapis.com/youtube/v3/search
	 */
	function videoTab($parm='')
	{
	//	$apiKey = e107::pref('core','youtube_apikey');

		$searchQry = $this->getQuery('search');

		if(substr($searchQry,0,4) == 'url:')
		{
			$searchQry = $this->getYouTubeCode($searchQry);

		}

		if(!empty($searchQry)) // -- Search Active.
		{
			if(substr($searchQry,0,6) == 'video:' || substr($searchQry,0,2) == 'v=') // YouTube video code
			{
				$searchQry = (substr($searchQry,0,2) == 'v=') ? trim(substr($searchQry,2)) : trim(substr($searchQry,6));
				$extension = 'youtube';
			//	$feed = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=".urlencode($searchQry)."&key=".$apiKey;

				$data = array();
				$data['items'][0]['id']['videoId'] = $searchQry;
				$data['items'][0]['snippet']['thumbnails']['medium']['url'] = "http://i.ytimg.com/vi/".$searchQry."/mqdefault.jpg";
				$data['items'][0]['snippet']['title'] = 'Specified Video';
			}
			elseif(substr($searchQry,0,9) == 'playlist:') // playlist
			{

				if(empty($apiKey))
				{
					$playlistID = substr($searchQry,9);
					$data = array();
					$data['items'][0]['id']['videoId'] = $playlistID;
					$data['items'][0]['snippet']['thumbnails']['medium']['url'] = e_IMAGE_ABS."generic/playlist_120.png"; // "http://i.ytimg.com/vi/".$playlistID."/mqdefault.jpg"; // not really possible, so it will show a generic grey image.
					$data['items'][0]['snippet']['title'] = 'Specified Playlist';
				}
				else
				{
					$searchQry = trim(substr($searchQry,9));
					$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".urlencode($searchQry)."&type=playlist&maxResults=1&key=".$apiKey;
				}

				$extension = 'youtubepl';
			}
			elseif(substr($searchQry,0,8) == 'channel:')
			{
				$searchQry = trim(substr($searchQry,8));
				$extension = 'youtube';
				$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=".urlencode($searchQry)."&type=video&maxResults=20&key=".$apiKey;
			}
			else
			{
				$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".urlencode($searchQry)."&type=video&maxResults=20&key=".$apiKey;
				$extension = 'youtube';
			}

		}
		else // -- default state.
		{

			$defaultAccount = e107::pref('core','youtube_default_account');
			if(empty($defaultAccount))
			{
				$defaultAccount = 'e107inc';
			}

			$accFeed = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=".$defaultAccount."&key=".$apiKey;
			$accData = e107::getFile()->getRemoteContent($accFeed);
			$accData = json_decode($accData,true);
			$channelID = null;

			foreach($accData['items'] as $val)
			{
				if($val['kind'] == 'youtube#channel')
				{
						$channelID = $val['id'];
						break;
				}
			}

			$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=".$channelID."&type=video&maxResults=20&key=".$apiKey;
			$extension = 'youtube';
		}


		if(!empty($feed) )
		{

			if(!empty($apiKey))
			{
				$data = e107::getFile()->getRemoteContent($feed);
				$data = json_decode($data,true);
				$items = array();
			}
			else // empty key.
			{
				$items = "<div class='alert alert-info'><p>Youtube search requires a (free) YouTube v3 api key.<br />
				This key is not required unless you wish to perform a keyword, playlist or channel search.<br />
				Entering a Youtube video URL directly into the box above will still work without having an api key. <br />
				<a style='color:black' target='_blank' href='".e_ADMIN."image.php?mode=main&action=prefs#/tab2'>Click here for more information and to enter your api key</a>.
				</p>
				</div>";
			}

		}


		if(!empty($data))
		{
			foreach($data['items'] as $value)
			{

				$id = $value['id']['videoId'];
				$thumbnail = $value['snippet']['thumbnails']['medium']['url'];

				$items[] = array(
					'previewUrl'	=> $thumbnail,
					'saveValue'		=> $id.".".$extension, // youtube",
					'thumbUrl'		=> $thumbnail,
					'title'			=> varset($value['snippet']['title'],'')
				);

				if($extension == 'youtubepl') // save Image for background.
				{
					$hiresThumbnail = $thumbnail = $value['snippet']['thumbnails']['high']['url'];
					e107::getMedia()->saveThumb($hiresThumbnail, $id); //TODO move to $tp->Video(); ?
				}
			}
		}
	//	return print_a($data,true);

		$parms = array('width' => 200, 'height'=>113, 'type'=>'image', 'bbcode'=>'video', 'tagid'=> $this->getQuery('tagid'), 'action'=>'youtube','searchPlaceholder'=>'Search Youtube. Paste any YouTube URL here for a specific video/playlist/channel' );
		$text = e107::getMedia()->browserCarousel($items, $parms);
		
		if(E107_DEBUG_LEVEL > 0 && !empty($feed))
		{
			$text .= "<div><small>Debug: ". $feed."</small></div>";
			if(!empty($data))
			{
				$text .= print_a($data,true);
			}
		}

		return $text;
		
	}	





	function importPage()
	{
		$this->processUploadUrl();
		$this->batchImportForm();
	}




	
	function processUploadUrl($import = false, $cat='_common')
	{
		$fl = e107::getFile();
		$mes = e107::getMessage();
			
		
		if(vartrue($_POST['upload_remote_url']))
		{
			$fileName = basename($_POST['upload_url']);

			if(strpos($fileName,'?')!==false)
			{
				list($fileName,$bla) = explode("?", $fileName);
			}

			// remove script extensions.
			if(substr($fileName,-4) == ".php" || substr($fileName,-4) == ".htm" || substr($fileName,-5) == ".html" || substr($fileName,-4) == ".asp")
			{
				$fileName = empty($_POST['upload_caption']) ? str_replace(array(".php",".html",".asp",".htm"),'',$fileName)."_".time() : eHelper::dasherize(strtolower($_POST['upload_caption']));
			}

			if(!$fl->getRemoteFile($_POST['upload_url'], $fileName, 'import'))
			{
				$mes->addError("There was a problem grabbing the file");
			}
			elseif($import == true)
 			{
 			    $data = array('media_caption' => e107::getParser()->filter($_POST['upload_caption'],'str'));
				$result = e107::getMedia()->importFile($fileName,$cat, null, $data);
			}
		}
	}
			
		
	
	
	

	function settingsPage()
	{
		global $pref;

		$frm = e107::getForm();
		$tp = e107::getParser();
		$sql = e107::getDb();
		$ns = e107::getRender();
		$mes = e107::getMessage();
	
		if(function_exists('gd_info'))
		{
			$gd_info = gd_info();
			$gd_version = $gd_info['GD Version'];
		}
		else
		{
			$gd_version = "<span class='error'> ".IMALAN_55."</span>";
		}
		
		if($pref['resize_method'] == "ImageMagick" && (!vartrue(e107::getFolder('imagemagick'))))
		{
			
			$mes->addWarning('Please add: <b>$IMAGEMAGICK_DIRECTORY="'.$pref['im_path'].'";</b> to your e107_config.php file');	
		}
		
			
		//$IM_NOTE = "";
		$im_path = vartrue(e107::getFolder('imagemagick'));
		if($im_path != "")
		{
		  $im_file = $im_path.'convert';
			if(!file_exists($im_file))
			{
				//$IM_NOTE = "<span class='error'>".IMALAN_52."</span>";
				$mes->addWarning(IMALAN_52);
			}
			else
			{
				$cmd = "{$im_file} -version";
				$tmp = `$cmd`;
				if(strpos($tmp, "ImageMagick") === FALSE)
				{
					//$IM_NOTE = "<span class='error'>".IMALAN_53."</span>";
					$mes->addWarning(IMALAN_53);
				}
			}
		}
	

	
	
	
	
		$text = "
			<form method='post' action='".e_SELF."?".e_QUERY."'>
				<fieldset id='core-image-settings'>
					<legend class='e-hideme'>".IMALAN_7."</legend>
					<table class='table adminform'>
						<colgroup>
							<col class='col-label' />
							<col class='col-control' />
						</colgroup>
						<tbody>
							<tr>
								<td>
									".IMALAN_1."
								</td>
								<td>
									<div class='auto-toggle-area autocheck'>
										".$frm->checkbox('image_post', 1, $pref['image_post'])."
										<div class='field-help'>".IMALAN_2."</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									".IMALAN_10."
								</td>
								<td>
									".r_userclass('image_post_class',$pref['image_post_class'],"off","public,guest,nobody,member,admin,main,classes")."
									<div class='field-help'>".IMALAN_11."</div>
								</td>
							</tr>
	
							<tr>
								<td>
									".IMALAN_12."
								</td>
								<td>
									".$frm->select_open('image_post_disabled_method')."
										".$frm->option(IMALAN_14, '0', ($pref['image_post_disabled_method'] == "0"))."
										".$frm->option(IMALAN_15, '1', ($pref['image_post_disabled_method'] == "1"))."
									".$frm->select_close()."
									<div class='field-help'>".IMALAN_13."</div>
								</td>
							</tr>";
							
							list($img_import_w,$img_import_h) = explode("x",$pref['img_import_resize']);
							
							$text .= "
							<tr>
								<td>".IMALAN_105."<div class='label-note'>".IMALAN_106."</div></td>
								<td>
									".$frm->text('img_import_resize_w', $img_import_w,4)."px X ".$frm->text('img_import_resize_h', $img_import_h,4)."px
								</td>
							</tr>
	
							<tr>
								<td>".IMALAN_3."<div class='label-note'>".IMALAN_54." {$gd_version}</div></td>
								<td>
									".$frm->select_open('resize_method')."
										".$frm->option('gd1', 'gd1', ($pref['resize_method'] == "gd1"))."
										".$frm->option('gd2', 'gd2', ($pref['resize_method'] == "gd2"))."
										".$frm->option('ImageMagick', 'ImageMagick', ($pref['resize_method'] == "ImageMagick"))."
									".$frm->select_close()."
									<div class='field-help'>".IMALAN_4."</div>
								</td>
							</tr>";
				/*			
				$text .= "
							// Removed to prevent mod_security blocks, and show only when relevant (non-GD2 users)
							<tr>
								<td>".IMALAN_5."<div class='label-note'>{$IM_NOTE}</div></td>
								<td>
									".$frm->text('im_path', $pref['im_path'])."
									<div class='field-help'>".IMALAN_6."</div>
								</td>
							</tr>";		
							
				// Removed as IE6 should no longer be supported. A 3rd-party plugin can be made for this functionality if really needed. 			
				
							
				
							$text .= "
										<tr>
											<td>".IMALAN_34."
											</td>
											<td>
												<div class='auto-toggle-area autocheck'>
													".$frm->checkbox('enable_png_image_fix', 1, ($pref['enable_png_image_fix']))."
													<div class='field-help'>".IMALAN_35."</div>
												</div>
											</td>
										</tr>";
										
							*/
							
							
			$text .= "
	
							<tr>
								<td>".IMALAN_36."</td>
								<td>
									".$frm->admin_button('check_avatar_sizes', ADLAN_145)."
								</td>
							</tr>
						</tbody>
					</table>
					<div class='buttons-bar center'>
						".$frm->admin_button('update_options', IMALAN_8, 'update')."
					</div>
				</fieldset>
			</form>";
	
			echo $mes->render().$text;
			return;
		//	$ns->tablerender(LAN_MEDIAMANAGER." :: ".IMALAN_7, $mes->render().$text);
	}


	function avatarPageDeleteChecked()
	{


		$sql = e107::getDb();
		$mes = e107::getMessage();

			if(!empty($_POST['multiaction']))
			{
				$tmp = array(); $tmp1 = array(); $message = array();

				foreach ($_POST['multiaction'] as $todel)
				{
					list($usr,$path) = explode('#', $todel);

				//	$path = basename($path);

					$path = str_replace("../","",$path); // clean the path.

					$mes->addDebug('usr: '.intval($usr).'  path: '.$path);

					$image_type = 2;
					if(strpos($path, '-upload-') === 0)
					{
						$image_type = 1;
						$path = substr($path, strlen('-upload-'));
					}

					//delete it from server
					$deletePath = e_AVATAR.$path;
					if(@unlink($deletePath))
					{
						$mes->addDebug('Deleted: '.$deletePath);
						$message[] = basename($path); //admin log & sysmessage
					}
					else
					{
						$mes->addError('Unable to delete: '.$deletePath); // Do not translate, temporary during beta stage.
					}

					//It's owned by an user
					if(!empty($usr))
					{
						switch ($image_type)
						{
							case 1: //avatar
								$tmp[] = intval($usr);
								break;

							case 2: //photo
								$tmp1[] = intval($usr);
								break;
						}
					}
					
				}

				//Reset all deleted user avatars with one query
				if(!empty($tmp))
				{
					$sql->update("user", "user_image='' WHERE user_id IN (".implode(',', $tmp).")");
					$mes->addDebug("user_image='' WHERE user_id IN (".implode(',', $tmp).")");
				}
				//Reset all deleted user photos with one query
				if(!empty($tmp1))
				{
					$sql->update("user", "user_sess='' WHERE user_id IN (".implode(',', $tmp1).")");
					$mes->addDebug("user_sess='' WHERE user_id IN (".implode(',', $tmp1).")");
				}
				unset($tmp, $tmp1);

				//Format system message
				if(!empty($message))
				{
					e107::getLog()->add('IMALAN_01', implode('[!br!]', $message), E_LOG_INFORMATIVE, '');
					$mes->addSuccess(implode(', ', $message).' '.IMALAN_28);
				}
			}



	}






	function avatarPage()
	{
		global $pref;

		if (isset($_POST['submit_show_delete_multi']))
		{
			$this->avatarPageDeleteChecked();
		}


		e107::css('inline', '
			span.avatar-label { width:100%; display:block; padding:5px; margin-bottom:5px }
			div.avatar-container { margin-bottom:5px; max-height:350px }

			');

		$sql = e107::getDb();
		$frm = e107::getForm();
		$tp = e107::getParser();
		$mes = e107::getMessage();

		$avFiles = e107::getFile()->get_files(e_MEDIA."avatars/",".jpg|.png|.gif|.jpeg|.JPG|.GIF|.PNG",null,2);

		$dirlist = array();

		foreach($avFiles as $f)
		{
			$dirlist[] = str_replace(e_MEDIA."avatars/","",$f['path']). $f['fname'];
		}

		$text = '';

		if (empty($dirlist))
		{
			$text .= IMALAN_29;
		}
		else
		{

			$tmp = $sql->retrieve('user','user_id,user_image','user_image !="" ', true);
			$imageUsed = array();

			foreach($tmp as $val)
			{
				$id = $val['user_id'];
				$imageUsed[$id] = str_replace("-upload-","", $val['user_image']);
			}

			$userImages = array_flip($imageUsed);

			$text = $frm->open('core-image-avatars');

			$text .= "<div  id='core-image-show-avatars' class='row'>";

			$count = 0;
			while (list($key, $image_name) = each($dirlist))
			{
				//$users = IMALAN_21." | ";
				$row = array('user_id' => '');
				$image_pre = '';
				$disabled = false;
				/*
				if ($sql->db_Select("user", "*", "user_image='-upload-".$tp->toDB($image_name)."' OR user_sess='".$tp->toDB($image_name)."'"))
				{
					$row = $sql->db_Fetch();
					if($row['user_image'] == '-upload-'.$image_name) $image_pre = '-upload-';
					$users .= "<a href='".$e107->url->create('user/profile/view', 'name='.$row['user_name'].'&id='.$row['user_id'])."'>{$row['user_name']}</a> <span class='smalltext'>(".($row['user_sess'] == $image_name ? IMALAN_24 : IMALAN_23).")</span>";
				}
				else
				{

				}
			*/

				// :
		//		$fileName = basename($image_name);


				$fileName = basename($image_name);



				$users = (in_array($fileName,$imageUsed)) ? "<span class='label label-warning avatar-label'>Image in use</span>" : '<span class="label label-default avatar-label" >Not in use</span>';

				//directory?
				if(is_dir(e_MEDIA."avatars/".$image_name))
				{
					//File info
					$users = "<a href='#' title='".IMALAN_69.": {$image_name}'><img class='e-tip icon S16' src='".e_IMAGE_ABS."admin_images/info_16.png' alt='".IMALAN_66.": {$image_name}' title='".IMALAN_69.": {$image_name}' /></a> <span class='error'>".IMALAN_69."</span>";

					//Friendly UI - click text to select a form element
					$img_src =  '<span class="error">'.IMALAN_70.'</span>';
					$disabled = true;
				}
				else
				{
					//File info
					//	$users = "<a class='e-tip' href='#' title='".IMALAN_66.": {$image_name}'><img src='".e_IMAGE_ABS."admin_imaXXXges/info_16.png' alt='".IMALAN_66.": {$image_name}' /></a> ".$users;

					// Control over the image size (design)
					//	$image_size = getimagesize(e_MEDIA."avatars/".$image_name);

					//Friendly UI - click text to select a form element

					// Resized on-the-fly - avatar-size no longer an issue.
				//	$attr = "aw=".$pref['im_width']."&ah=".$pref['im_height'];
					$attr = "aw=200&ah=200";
					$img_path = $tp->thumbUrl(e_MEDIA_ABS."avatars/".$image_name,$attr);

					$type = dirname($image_name);

					if($prevType != $type)
					{
						$text .= "<div class='col-md-12 clearfix'></div>
					<h4 class='clearfix col-md-12' >".$type."</h4>";
					}


					$for = $frm->name2id('multiaction-'.$image_name);

					$img_src = "
				<div class='thumbnail'>
				<label for='".$for."' ><img  class='img-responsive' src='".$img_path."' alt='{$image_name}' title='".IMALAN_66.": {$image_name}' /></label>
				</div>
				";

					$prevType = $type;

				}

				//style attribute allowed here - server side width/height control
				//autocheck class - used for JS selectors (see eCoreImage object)



				$text .= "
			<div class='col-md-2 col-sm-4 center autocheck avatar-container' >
				<div class='well'>
				<div class='image-users'>{$users}</div>
				<div class='image-preview'>{$img_src}</div>
				<div class='image-delete'>
					".$frm->checkbox('multiaction[]', intval($userImages[$fileName])."#{$image_pre}{$image_name}", false, array('id' => $for, 'disabled' => $disabled))."
				</div>

				</div>
			</div>
			";
				$count++;
			}

			$text .= "</div>

			<div class='col-md-12 spacer clearfix'>
				<div class='row buttons-bar'>
					<input type='hidden' name='show_avatars' value='1' />
					".$frm->admin_button('e_check_all', LAN_CHECKALL, 'action')."
					".$frm->admin_button('e_uncheck_all', LAN_UNCHECKALL, 'action')."
					".$frm->admin_button('submit_show_delete_multi', LAN_DELCHECKED, 'delete')."
					".$frm->admin_button('submit_show_deleteall', "Delete all unused images", 'delete')."

				</div>
			</div>


			</form>
		";
			// $frm->admin_button('submit_cancel_show', IMALAN_68, 'cancel')
		}

		return $mes->render().$text;

		// $ns->tablerender(LAN_MEDIAMANAGER." :: ".IMALAN_18, $mes->render().$text);
	}

	function iconsPage()
	{
		// $this->icon_editor();
	}



	/**
	 * Invoked just before item create event
	 * @return array
	 */
	public function beforeCreate($new_data)
	{
		// print_a($_POST);
		// return data to be merged with posted model data
		$this->getRequest()->setPosted('media_upload', null);
		//$dataFields = $this->getModel()->getDataFields();
		//unset($dataFields['media_upload']);
		//$this->getModel()->setDataFields($dataFields);
		if($this->getQuery('for') && $this->getMediaCategory($this->getQuery('for')))
		{
			$new_data['media_category'] = $this->getQuery('for');
		}
		return $this->observeUploaded($new_data);
	}

	/**
	 * Same as beforeCreate() but invoked on edit
	 * @return TBD
	 */
	public function beforeUpdate($new_data, $old_data, $id)
	{
		// return data to be merged with posted model data
	//	$new_data['media_category'] = implode(",",$new_data['media_category']);
		$this->fields['media_category']['data'] = 'str'; //XXX Quick fix for 'comma' incompatibility in Db-Update routines. 
		return $new_data;
	//	return $this->observeUploaded($new_data);
	}

	public function mediaData($sc_path)
	{
		if(!$sc_path) return array();
		$path = e107::getParser()->replaceConstants($sc_path);
		$info = e107::getFile()->get_file_info($path,true);
		return array(
			'media_type'		=> $info['mime'],
			'media_datestamp'	=> time(),
			'media_url'			=> e107::getParser()->createConstants($path, 'rel'),
			'media_size'		=> filesize($path),
			'media_author'		=> USERID,
			'media_usedby'		=> '',
			'media_tags'		=> '',
			'media_dimensions'	=> $info['img-width']." x ".$info['img-height']
		);
	}

	// XXX - strict mysql error on Create without UPLOAD!
	function observeUploaded($new_data)
	{
		$fl = e107::getFile();

		$mes = e107::getMessage();

		if(vartrue($_FILES['file_userfile'])) // CREATE
		{
			
			$pref['upload_storagetype'] = "1";
			require_once(e_HANDLER."upload_handler.php"); //TODO - still not a class!
		//	$uploaded = process_uploaded_files(e_MEDIA.'temp/'); //FIXME doesn't handle xxx.JPG (uppercase)
			$uploaded = process_uploaded_files(e_IMPORT); //FIXME doesn't handle xxx.JPG (uppercase)
			$upload = array_shift($uploaded);
			if(vartrue($upload['error']))
			{
				$mes->addError($upload['message']);
				return FALSE;
			}	

				if(!$typePath = $this->getPath($upload['type']))
				{
					$mes->addError(IMALAN_107);
					return FALSE;
				}
				$mes->addDebug(print_a($upload,TRUE));

			//	$oldpath = e_MEDIA."temp/".$upload['name'];
				$oldpath = e_IMPORT.$upload['name'];
				$newpath = $this->checkDupe($oldpath,$typePath.'/'.$upload['name']);

				if(!rename($oldpath, e_MEDIA.$newpath))
				{
					$srch = array('[x]','[y]');
					$repl = array($oldpath,$newpath);
					$text = str_replace($srch,$repl,IMALAN_108);
					$mes->add($text, E_MESSAGE_ERROR);
					return FALSE;
				};

				$img_data = $this->mediaData($newpath); // Basic File Info only
				
				$img_data['media_name'] 		= $new_data['name'];
				$img_data['media_caption'] 		= $new_data['media_caption'];
				$img_data['media_category'] 	= $new_data['media_category'];
				$img_data['media_description'] 	= $new_data['media_description'];
				$img_data['media_tags'] 		= $new_data['media_tags'];
				$img_data['media_userclass'] 	= 0;	
				$img_data['media_author']		= USERID;
				
				if(!varset($img_data['media_name']))
				{
					$img_data['media_name'] = $upload['name'];
				}			
				
				$mes->addDebug(print_a($img_data,TRUE));
		
				return $img_data;
		}
		else // Update Only ?
		{
		
			$img_data = $this->mediaData($new_data['media_url']);


			if(!($typePath = $this->getPath($img_data['media_type'])))
			{
				$mes->addError(IMALAN_109." ".$typePath);
					
				return FALSE;
			}
			
			$fname = basename($new_data['media_url']);
			// move to the required place
			if(strpos($new_data['media_url'], '{e_IMPORT}') !== FALSE)
		//	if(strpos($new_data['media_url'], '{e_MEDIA}temp/') !== FALSE)
			{
				$tp = e107::getParser();
				$oldpath = $tp->replaceConstants($new_data['media_url']);
				$newpath = $this->checkDupe($oldpath,$typePath.'/'.$fname);
				
			
				if(!rename($oldpath, $newpath))
				{
					$srch = array('[x]','[y]');
					$repl = array($oldpath,str_replace('../', '', $newpath));
					$text = str_replace($srch,$repl,IMALAN_108);
					$mes->add($text, E_MESSAGE_ERROR);
					return FALSE;
				}
				$img_data['media_url'] = $tp->createConstants($newpath, 'rel');
			}

			if(!varset($new_data['media_name']))
			{
				$img_data['media_name'] = basename($new_data['media_url']);
			}


			return $img_data;
		}
		
		
	}

	// Check for existing image path in db and rename if found. 
	function checkDupe($oldpath,$newpath)
	{
		$mes = e107::getMessage();	
		$tp = e107::getParser();
		$f = e107::getFile()->get_file_info($oldpath,TRUE);
		
		$mes->addDebug("checkDupe(): newpath=".$newpath."<br />oldpath=".$oldpath."<br />".print_r($newpath,TRUE));
		if(file_exists($newpath) || e107::getDb()->db_Select("core_media","*","media_url = '".$tp->createConstants($newpath,'rel')."' LIMIT 1") )
		{
			$mes->addWarning($newpath." already exists.");	
			$file = $f['pathinfo']['filename']."_.".$f['pathinfo']['extension'];
			$newpath = $this->getPath($f['mime']).'/'.$file;
			return false;			
		}
		
		return $newpath;	
	}


	function beforeDelete($data, $id) // call before 'delete' is executed. - return false to prevent delete execution (e.g. some dependencies check)
	{
		return true;
	}

	function afterDelete($deleted_data, $id) // call after 'delete' is successfully executed. - delete the file with the db record (optional pref)
	{

	}

	function getPath($mime)
	{

		return e107::getMedia()->getPath($mime);
		/*

		$mes = e107::getMessage();

		list($pmime,$tmp) = explode('/',$mime);

		if(!vartrue($this->mimePaths[$pmime]))
		{
			$text = str_replace('[x]',$mime,IMALAN_111);
			$mes->add($text, E_MESSAGE_ERROR);
			return FALSE;
		}

		$dir = $this->mimePaths[$pmime].date("Y-m");

		if(!is_dir($dir))
		{
			if(!mkdir($dir, 0755))
			{
				$text = str_replace('[x]',$dir,IMALAN_112);
				$mes->add($text, E_MESSAGE_ERROR);
				return FALSE;
			};
		}
		return $dir;*/
	}

	function batchImportForm()
	{
		$frm = e107::getForm();
		$mes = e107::getMessage();
		$fl = e107::getFile();
		

		
		
		$fl->setFileInfo('all');
		$rejectArray = array('^\.ftpquota$','^index\.html$','^null\.txt$','\.bak$','^.tmp','.*\.xml$','^\.$','^\.\.$','^\/$','^CVS$','thumbs\.db','.*\._$','^\.htaccess$','index\.html','null\.txt');
		$fl->setFileFilter($rejectArray);
	//	$files = $fl->get_files(e_MEDIA."temp/");
		$files = $fl->get_files(e_IMPORT);
		
	//	e107::js('core','core/admin.js','prototype');


		//TODO Detect XML file, and if found - read that instead of the directory.

		if(!vartrue($_POST['batch_import_selected']))
		{
			$mes->add(IMALAN_113." <b> ".e_IMPORT."</b>", E_MESSAGE_INFO);
		}

		if(!count($files))
		{
			if(!vartrue($_POST['batch_import_selected']))
			{
				$mes->add(IMALAN_114, E_MESSAGE_INFO);
			}
			
			$text = $this->uploadTab();
			echo $mes->render().$text;
			return;
		}

			$text = "
				<form method='post' action='".e_SELF."?".e_QUERY."' id='batch_import'>
					<fieldset id='core-mediamanager-batch'>
						<legend class='e-hideme'>".DBLAN_20."</legend>
						<table class='table adminlist'>
							<colgroup>
								<col style='width: 5%' />
								<col />
								<col />
								<col />
							</colgroup>
							<thead>
								<tr>
									<th class='center'>".e107::getForm()->checkbox_toggle('e-column-toggle', 'batch_selected')."</th>
									<th class='center' style='width:50px'>".IMALAN_121."</th>
									<th class='center'>".LAN_FILE."</th>
									<th>".IMALAN_115."</th>
									<th>".IMALAN_116."</th>
									<th>".LAN_AUTHOR."</th>
									<th>".IMALAN_118."</th>
									<th>".IMALAN_119."</th>
									<th>".LAN_DATESTAMP."</th>
									<th class='center last'>".IMALAN_120."</th>
								</tr>
							</thead>
							<tbody>";
		
	//	$c = 0;
		foreach($files as $f)
		{
			$default = $this->getFileXml($f['fname']);
			$f = $fl->cleanFileName($f,true);
			
			$c = md5($f['path'].$f['fname']);
			
			if($f['error'])
			{
				$text = str_replace('[x]', $f['fname'], IMALAN_122);
				$mes->addWarning($text);
			}
				
			$large = e107::getParser()->thumbUrl($f['path'].$f['fname'], 'w=800', true);
			$checked = empty($_POST['batch_selected']) ? true : false;

			$text .= "
			
			<tr>
				<td class='center'>".$frm->checkbox("batch_selected[".$c."]",$f['fname'],$checked)."</td>
				<td class='center'>".$this->preview($f)."</td>			
				<td><a class='e-dialog' href='".$large."'>".$f['fname']."</a></td>
				<td>".$frm->text('batch_import_name['.$c.']', ($_POST['batch_import_name'][$c] ? $_POST['batch_import_name'][$c] : $default['title']))."</td>
				<td><textarea name='batch_import_diz[".$c."]' rows='3' cols='50'>". ($_POST['batch_import_diz'][$c] ? $_POST['batch_import_diz'][$c] : $default['description'])."</textarea></td>
			
				<td><a href='mailto:".$default['authorEmail']."'>".$default['authorName']."</a><br />".$default['authorEmail']."</td>
				<td>".$f['mime']."</td>
				<td>".$f['fsize']."</td>
				<td>".e107::getDateConvert()->convert_date($f['modified'])."</td>
				<td class='center last'>".$f['img-width']." x ".$f['img-height']."</td>
			</tr>
			\n";
				
			// $c++;
			$lastMime = $f['mime'];
		}

		// <td>".$frm->textarea('batch_import_diz['.$c.']', ($_POST['batch_import_diz'][$c] ? $_POST['batch_import_diz'][$c] : $default['description']))."</td>

		if(!isset($_POST['batch_category']) && substr($lastMime,0,5)=='image')
		{
			$_POST['batch_category'] = "_common_image";
		}
		
		$text .= "
				</tbody>
						</table>
						<div class='buttons-bar center form-inline'>
						".IMALAN_123." ".$frm->selectbox('batch_category',$this->cats, $_POST['batch_category']);
			
		//	$waterMarkPath = e_THEME.e107::getPref('sitetheme')."/images/watermark.png"; // Now performed site-wide dynamically. 				
					
		//	if(is_readable($waterMarkPath))
			{
		//		$text .= $frm->checkbox_label("Add Watermark", 'batch_import_watermark',1);
			}
						
						$text .= "
						</div>
						<div class='buttons-bar center'>
							".$frm->admin_button('batch_import_selected', IMALAN_124, 'import')
							.$frm->admin_button('batch_import_delete', IMALAN_125, 'delete');
			$text .= "
						</div>
					</fieldset>
				</form>


			";


		echo $mes->render().$text;
	}



	// Check for matching XML file name and if found, return data from it during import. 
	function getFileXml($imgFile)
	{
		list($file,$ext) = explode(".",$imgFile);
		
		$xmlFile = e_IMPORT.$file.".xml";
		
		if(is_readable($xmlFile))
		{
			$data = file_get_contents($xmlFile);
			$tmp = preg_match("/<author name=(?:'|\")([^'\"]*)/i",$data,$authorName);
			$tmp = preg_match("/email=(?:'|\")([^'\"]*)/i",$data,$authorEmail);
			$tmp = preg_match("/<title>(.*)<\/title>/i",$data,$title);
			$tmp = preg_match("/<description>(.*)<\/description>/i",$data,$diz);
			
			return array(
				'title'			=> $title[1],
				'description'	=> $diz[1],
				'authorName'	=> $authorName[1],
				'authorEmail'	=> $authorEmail[1]
			);				
		}
			
		$srch = array("_","-");	
		$description = str_replace($srch," ",$file);	
		
		$file = utf8_encode($file);
		$description = utf8_encode($description); 
			
		return array('title'=>basename($file),'description'=>$description,'authorName'=>USERNAME,'authorEmail'=>'');
		
		/*
		Example: matchingfilename.xml (ie. same name as jpg|.gif|.png etc)
		 
		<?xml version='1.0' encoding='utf-8' ?>
		<e107Media>
			<item file='filename.jpg' date='2012-10-25'>
		   		<author name='MyName' url='http://mysite.com' email='email@email.com' />
		   		<title>Title of File</title>
				<description>Description of File</description>
				<category></category>
			</item>  
		</e107Media>

		*/
					
	}



	function deleteFileXml($imgFile)
	{
		list($file,$ext) = explode(".",$imgFile);
		
		$xmlFile = e_IMPORT.$file.".xml";
		
		if(file_exists($xmlFile))
		{
			unlink($xmlFile);	
		}			
	}

	
	function batchDelete()
	{
		foreach($_POST['batch_selected'] as $key=>$file)
		{
			if(trim($file) == '')
			{
				continue;
			}	
			
		//	$oldpath = e_MEDIA."temp/".$file;
			$oldpath = e_IMPORT.$file;
			if(file_exists($oldpath))
			{
				unlink($oldpath);
			}
		}
	}





	function batchImport()
	{
		$fl = e107::getFile();
		$mes = e107::getMessage();
		$sql = e107::getDb();
		$tp = e107::getParser();
	
		
		if(!count($_POST['batch_selected']))
		{
			$mes->addError(IMALAN_126);
			return;
		}
		

	
		list($img_import_w,$img_import_h) = explode("x",e107::getPref('img_import_resize'));
		
		if(vartrue($_POST['batch_import_watermark']))
		{
			$WM = TRUE;
			$watermarkPath = e_THEME.e107::getPref('sitetheme')."/images/watermark.png";	
			$watermark = PhpThumbFactory::create($watermarkPath);
		}
		else 
		{
		 	$WM = FALSE; 
		}	

		// Disable resize-on-import and watermark for now. 
		$img_import_w = 2816;
		$img_import_h = 2112; 
		
		foreach($_POST['batch_selected'] as $key=>$file)
		{
						
		//	$oldpath = e_MEDIA."temp/".$file;
			$oldpath = e_IMPORT.$file;
			
			// Resize on Import Routine ------------------------
			if(vartrue($img_import_w) && vartrue($img_import_h))
			{
				// $this->resizeImage($file,$img_import_w,$img_import_h);		
			}
			// End Resize routine. ---------------------

			$f = $fl->get_file_info($oldpath);

			if(!$f['mime'])
			{
				
				$mes->add(IMALAN_127." ".$oldpath, E_MESSAGE_WARNING);
				// $mes->add(print_a($f,true), E_MESSAGE_ERROR);
				$f['mime'] = "other/file";
			}

			if(!$newDir = $this->getPath($f['mime']))
			{
				continue;
			}

			$newpath = $this->checkDupe($oldpath,$newDir.'/'.$file);
			$newname = $tp->toDB($_POST['batch_import_name'][$key]);
			$newdiz = $tp->toDB($_POST['batch_import_diz'][$key]);
			
			$f['fname'] = $file;
			
			/*
				
						if(file_exists($newpath) || $sql->db_Select("core_media","media_url = '".$tp->createConstants($newpath,'rel')."' LIMIT 1") )
						{
							$mes->addWarning($newpath." already exists and was renamed during import.");	
							$file = $f['pathinfo']['filename']."_.".$f['pathinfo']['extension'];
							$newpath = $this->getPath($f['mime']).'/'.$file;						
						}
			*/
			
			
			
			if(rename($oldpath,$newpath))
			{
				$insert = array(
					'media_caption'		=> $newdiz,
					'media_description'	=> '',
					'media_category'	=> $_POST['batch_category'],
					'media_datestamp'	=> $f['modified'],
					'media_url'			=> $tp->createConstants($newpath,'rel'),
					'media_userclass'	=> '0',
					'media_name'		=> $newname,
					'media_author'		=> USERID,
					'media_size'		=> $f['fsize'],
					'media_dimensions'	=> $f['img-width']." x ".$f['img-height'],
					'media_usedby'		=> '',
					'media_tags'		=> '',
					'media_type'		=> $f['mime']
					);


				if($sql->insert("core_media",$insert))
				{
					$mes->add(IMALAN_128." ".$f['fname'], E_MESSAGE_SUCCESS);
					$this->deleteFileXml($f['fname']);
				}
				else
				{
					rename($newpath,$oldpath);	//move it back.
				}
			}
		}
	}



	function preview($f)
	{
		list($type,$tmp) = explode("/",$f['mime']);

		if($type == 'image')
		{
			$url = e107::getParser()->thumbUrl($f['path'].$f['fname'], 'w=100', true);
			$large = e107::getParser()->thumbUrl($f['path'].$f['fname'], 'w=800', true);
			//echo $url;
			return "<a class='e-dialog' href='".$large."'><img src='".$url."' alt=\"".$f['name']."\" width='100px' /></a>";
		}
		else
		{
			return; //TODO generic icon/image for no preview.
		}
	}

	public function getMediaCategory($id = false)
	{
		if(is_array($id))
		{
			$text = "";
			foreach($id as $val)
			{
				$text .= (isset($this->cats[$val]) ? "<span class='nowrap'>".$this->cats[$val]."</span><br />" : '');			
			}		
			return $text;						
		}

		if($id) return (isset($this->cats[$id]) ? $this->cats[$id] : 0);
		return $this->cats;
	}
	
	
	/*
 * UPDATE IMAGE OPTIONS - MAIN SCREEN
 */
 	function updateSettings()
	{
		global $pref,$admin_log,$tp;
		
		$mes = e107::getMessage();
		
		$tmp = array();
		$tmp['image_post'] = intval($_POST['image_post']);
		$tmp['resize_method'] = $tp->toDB($_POST['resize_method']);
		$tmp['im_path'] = trim($tp->toDB($_POST['im_path']));
		$tmp['image_post_class'] = intval($_POST['image_post_class']);
		$tmp['image_post_disabled_method'] = intval($_POST['image_post_disabled_method']);
		$tmp['enable_png_image_fix'] = intval($_POST['enable_png_image_fix']);
		
		if($_POST['img_import_resize_w'] && $_POST['img_import_resize_h'])
		{
			$tmp['img_import_resize'] = intval($_POST['img_import_resize_w'])."x".intval($_POST['img_import_resize_h']);		
		} 
	
		if ($admin_log->logArrayDiffs($tmp, $pref, 'IMALAN_04'))
		{
			save_prefs();		// Only save if changes
			$mes->add(IMALAN_9, E_MESSAGE_SUCCESS);
		}
		else
		{
			$mes->add(IMALAN_20, E_MESSAGE_INFO);
		}	
	}

	
	

}


new media_admin();

require_once(e_ADMIN."auth.php");

e107::getAdminUI()->runPage();











// -----------------------------------------------------------------------




$action = e_QUERY;


if(varset($_GET['action']) == "icons")
{
	// icon_editor();
}

if(varset($_GET['action']) == "avatars")
{
	// show_avatars();
}

if(varset($_GET['action']) == 'settings')
{
	// main_config();
}
/*
 * DELETE CHECKED AVATARS - SHOW AVATAR SCREEN
 */





/*
 * DELETE ALL UNUSED IMAGES - SHOW AVATAR SCREEN
 */
if (isset($_POST['submit_show_deleteall']))
{
	$handle = opendir(e_AVATAR_UPLOAD);
	$dirlist = array();
	while ($file = readdir($handle)) 
	{
		if (!is_dir(e_AVATAR_UPLOAD.$file) && $file != '.' && $file != '..' && $file != "index.html" && $file != "null.txt" && $file != '/' && $file != 'CVS' && $file != 'Thumbs.db') {
			$dirlist[] = $file;
		}
	}
	closedir($handle);

	if(!empty($dirlist))
	{
		$imgList = '';
		$count = 0;
		foreach ($dirlist as $image_name)
		{
			$image_name = basename($image_name);
			$image_todb = $tp->toDB($image_name);
			if (!$sql->db_Count('user', '(*)', "WHERE user_image='-upload-{$image_todb}' OR user_sess='{$image_todb}'")) {
				unlink(e_AVATAR_UPLOAD.$image_name);
				$imgList .= '[!br!]'.$image_name;
				$count++;
			}
		}

		$message = $count." ".IMALAN_26;
		$mes->addSuccess($message);
		e107::getLog()->add('IMALAN_02', $message.$imgList,E_LOG_INFORMATIVE, '');
		unset($imgList);
	}
}


/*
 * DELETE ALL CHECKED BAD IMAGES - VALIDATE SCREEN
 */
if (isset($_POST['submit_avdelete_multi']))
{
	require_once(e_HANDLER."avatar_handler.php");
	$avList = array();
	$tmp = array();
	$uids = array();
	//Sanitize
	$_POST['multiaction'] = $tp->toDB($_POST['multiaction']);

	//sql queries significant reduced
	if(!empty($_POST['multiaction']) && $sql->db_Select("user", 'user_id, user_name, user_image', "user_id IN (".implode(',', $_POST['multiaction']).")"))
	{
		$search_users = $sql->db_getList('ALL', FALSE, FALSE, 'user_id');
		foreach($_POST['multiaction'] as $uid)
		{
			if (vartrue($search_users[$uid]))
			{
				$avname = avatar($search_users[$uid]['user_image']);
				if (strpos($avname, "http://") === FALSE)
				{ // Internal file, so unlink it
					@unlink($avname);
				}

				$uids[] = $uid;
				$tmp[] = $search_users[$uid]['user_name'];
				$avList[] = $uid.':'.$search_users[$uid]['user_name'].':'.$search_users[$uid]['user_image'];
			}
		}

		//sql queries significant reduced
		if(!empty($uids))
		{
			$sql->db_Update("user", "user_image='' WHERE user_id IN (".implode(',', $uids).")");
		}

		$mes->addSuccess(IMALAN_51.'<strong>'.implode(', ', $tmp).'</strong> '.IMALAN_28);
		e107::getLog()->add('IMALAN_03', implode('[!br!]', $avList), E_LOG_INFORMATIVE, '');

		unset($search_users);
	}
	unset($avList, $tmp, $uids);

}




/*
 * CHECK AVATARS SCREEN
 */
if (isset($_POST['check_avatar_sizes']))
{
	// Set up to track what we've done
	//
	$iUserCount  = 0;
	$iAVinternal = 0;
	$iAVexternal = 0;
	$iAVnotfound = 0;
	$iAVtoobig   = 0;
	require_once(e_HANDLER."avatar_handler.php");

	$text = "
	<form method='post' action='".e_SELF."'>
		<fieldset id='core-image-check-avatar'>
			<legend class='e-hideme'>".CACLAN_3."</legend>
			<table class='table adminlist'>
				<colgroup>
					<col style='width:10%' />
					<col style='width:20%' />
					<col style='width:25%' />
					<col style='width:45%' />
				</colgroup>
				<thead>
					<tr>
						<th class='center'>".LAN_OPTIONS."</th>
						<th class='center'>".LAN_USER."</th>
						<th class='center'>".IMALAN_62."</th>
						<th class='center last'>".LAN_URL."</th>
					</tr>
				</thead>
				<tbody>
	";


	//
	// Loop through avatar field for every user
	//
	$iUserCount = $sql->db_Count("user");
	$found = false;
	$allowedWidth = intval($pref['im_width']);
	$allowedHeight = intval($pref['im_width']);
	if ($sql->db_Select("user", "*", "user_image!=''")) {

		while ($row = $sql->db_Fetch())
		{
			//Check size
			$avname=avatar($row['user_image']);
			if (strpos($avname,"http://")!==FALSE)
			{
				$iAVexternal++;
				$bAVext=TRUE;
			} else {
				$iAVinternal++;
				$bAVext=FALSE;
			}

			$image_stats = getimagesize($avname);
			$sBadImage="";

			if (!$image_stats)
			{
				$iAVnotfound++;
				// allow delete
				$sBadImage=IMALAN_42;
			}
			else
			{
				$imageWidth = $image_stats[0];
				$imageHeight = $image_stats[1];

				if ( ($imageHeight > $allowedHeight) || ($imageWidth > $allowedWidth) )
				{ // Too tall or too wide
					$iAVtoobig++;
					if ($imageWidth > $allowedWidth)
					{
						$sBadImage = IMALAN_40." ($imageWidth)";
					}

					if ($imageHeight > $allowedHeight)
					{
						if (strlen($sBadImage))
						{
							$sBadImage .= ", ";
						}
						$sBadImage .= IMALAN_41." ($imageHeight)";
					}
				}
			}

			//If not found or too large, allow delete
			if (strlen($sBadImage))
			{
				$found = true;
				$text .= "
				<tr>
					<td class='autocheck center'>
						<input class='checkbox' type='checkbox' name='multiaction[]' id='avdelete-{$row['user_id']}' value='{$row['user_id']}' />
					</td>
					<td>
						<label for='avdelete-{$row['user_id']}' title='".IMALAN_56."'>".IMALAN_51."</label><a href='".e_BASE."user.php?id.{$row['user_id']}'>".$row['user_name']."</a>
					</td>
					<td>".$sBadImage."</td>
					<td>".$avname."</td>
				</tr>";
			}
		}
	}

	//Nothing found
	if(!$found)
	{
		$text .= "
				<tr>
					<td colspan='4' class='center'>".IMALAN_65."</td>
				</tr>
		";
	}

	$text .= "
				</tbody>
			</table>
			<div class='buttons-bar'>
				<input type='hidden' name='check_avatar_sizes' value='1' />
				".$frm->admin_button('check_all', LAN_CHECKALL, 'action')."
				".$frm->admin_button('uncheck_all', LAN_UNCHECKALL, 'action')."
				".$frm->admin_button('submit_avdelete_multi', LAN_DELCHECKED, 'delete')."
			</div>
		</fieldset>
	</form>

	<table class='table adminform'>
	<colgroup>
		<col class='col-label' />
		<col class='col-control' />
	</colgroup>
		</colgroup>
		<tbody>
			<tr>
				<td>".IMALAN_38."</td>
				<td>{$allowedWidth}</td>
			</tr>
			<tr>
				<td>".IMALAN_39."</td>
				<td>{$allowedHeight}</td>
			</tr>
			<tr>
				<td>".IMALAN_45."</td>
				<td>{$iAVnotfound}</td>
			</tr>
			<tr>
				<td>".IMALAN_46."</td>
				<td>{$iAVtoobig}</td>
			</tr>
			<tr>
				<td>".IMALAN_47."</td>
				<td>{$iAVinternal}</td>
			</tr>
			<tr>
				<td>".IMALAN_48."</td>
				<td>{$iAVexternal}</td>
			</tr>
			<tr>
				<td>".IMALAN_49."</td>
				<td>".($iAVexternal+$iAVinternal)." (".(int)(100.0*(($iAVexternal+$iAVinternal)/$iUserCount)).'%, '.$iUserCount." ".IMALAN_50.")</td>
			</tr>
		</tbody>
	</table>
	";

	$ns->tablerender(IMALAN_37, $mes->render().$text);
}


//Just in case...
if(!e_AJAX_REQUEST) require_once("footer.php"); 



?>