/*
* e107 website system (c) 2001-2008 Steve Dunstan (e107.org)
* $Id: admin_alt_nav.sc,v 1.7 2008-12-10 22:39:43 e107steved Exp $
*/

if (ADMIN) 
{
	global $sql, $pref, $tp;
	parse_str($parm);
	require(e_ADMIN.'ad_links.php');
	require_once(e_HANDLER.'admin_handler.php');
	function adnav_cat($cat_title, $cat_link, $cat_img, $cat_id=FALSE) {
		$cat_link = ($cat_link ? $cat_link : "javascript:void(0);");
		$text = "<a class='menuButton' href='".$cat_link."' style='background-image: url(".$cat_img."); background-repeat: no-repeat;  background-position: 3px 1px' ";
		if ($cat_id) {
			$text .= "onclick=\"return buttonClick(event, '".$cat_id."');\" onmouseover=\"buttonMouseover(event, '".$cat_id."');\"";
		}
		$text .= ">".$cat_title."</a>";
		return $text;
	}

	function adnav_main($cat_title, $cat_link, $cat_img, $cat_id=FALSE, $cat_highlight='') {
		$text = "<a class='menuItem ".$cat_highlight."' href='".$cat_link."' ";
		if ($cat_id) {
			$text .= "onclick=\"return false;\" onmouseover=\"menuItemMouseover(event, '".$cat_id."');\"";
		}
			$text .= "><span class='menuItemBuffer'>".$cat_img."</span><span class='menuItemText'>".$cat_title."</span>";
		if ($cat_id) {
			$text .= "<span class=\"menuItemArrow\">&#9654;</span>";
		}
			$text .= "</a>";
		return $text;
	}
	if (file_exists(THEME.'nav_menu.js')) {
		$text = "<script type='text/javascript' src='".THEME_ABS."nav_menu.js'></script>";
	} else {
		$text = "<script type='text/javascript' src='".e_FILE_ABS."nav_menu.js'></script>";
	}

	$text .= "<div style='width: 100%'><table border='0' cellspacing='0' cellpadding='0' style='width: 100%'>
	<tr><td>
	<div class='menuBar' style='width: 100%'>";

	$text .= adnav_cat(ADLAN_151, e_ADMIN.'admin.php', E_16_NAV_MAIN);

	for ($i = 1; $i < 5; $i++) 
	{
		$ad_tmpi = 0;
		$ad_links_array = asortbyindex($array_functions, 1);
		$nav_main = adnav_cat($admin_cat['title'][$i], '', $admin_cat['img'][$i], $admin_cat['id'][$i]);
		$ad_texti = "<div id='".$admin_cat['id'][$i]."' class='menu' onmouseover=\"menuMouseover(event)\">";
		while(list($key, $nav_extract) = each($ad_links_array))
		{
			if($nav_extract[4]==$i)
			{
				if(getperms($nav_extract[3]))
				{
					$ad_texti .= adnav_main($nav_extract[1], $nav_extract[0], $nav_extract[5]);
					$ad_tmpi = 1;
				}
			}
		}
		$ad_texti .= "</div>";
		if ($ad_tmpi == 1) 
		{
			$text .= $nav_main;
			$text .= $ad_texti;
		}
	}

	$render_plugins = FALSE;
	include_once(e_HANDLER.'plugin_class.php');
	$plug = new e107plugin;
	if($sql -> db_Select("plugin", "*", "plugin_installflag=1 ORDER BY plugin_path"))
	{
		while($row = $sql -> db_Fetch())
		{
			if(getperms('P'.$row['plugin_id']))
			{
				if($plug->parse_plugin($row['plugin_path']))
				{
					$plug_vars = $plug->plug_vars;
					loadLanFiles($row['plugin_path'], 'admin');
					if($plug_vars['administration']['configFile'])
					{
						$plug_vars['@attributes']['name'] = $tp->toHTML($plug_vars['@attributes']['name'], FALSE, "defs");
						$icon_src = (isset($plug_vars['plugin_php']) ? e_PLUGIN_ABS : e_PLUGIN_ABS.$row['plugin_path'].'/') .$plug_vars['administration']['iconSmall'];
						$plugin_icon = $plug_vars['administration']['iconSmall'] ? "<img src='{$icon_src}' alt='".$plug_vars['administration']['caption']."' style='border:0px; vertical-align:bottom; width: 16px; height: 16px' />" : E_16_PLUGIN;
						$plugin_array[ucfirst($plug_vars['@attributes']['name'])] = adnav_main($plug_vars['@attributes']['name'], e_PLUGIN.$row['plugin_path']."/".$plug_vars['administration']['configFile'], $plugin_icon);
					}
					$render_plugins = TRUE;
					$active_plugs = TRUE;
				}
			}
		}
		ksort($plugin_array, SORT_STRING);
		$plugs_text = '';
		foreach ($plugin_array as $plugin_compile) 
		{
			$plugs_text .= $plugin_compile;
		}
	}

	if (getperms('Z')) 
	{
		$pclass_extended = $active_plugs ? 'header' : '';
		$plugin_text = adnav_main(ADLAN_98, e_ADMIN.'plugin.php', E_16_PLUGMANAGER, FALSE, $pclass_extended);
		$render_plugins = TRUE;
	}

	if ($render_plugins) {
		$text .= adnav_cat(ADLAN_CL_7, '', E_16_CAT_PLUG, 'plugMenu');
		$text .= "<div id='plugMenu' class='menu' onmouseover=\"menuMouseover(event)\">";
		$text .= $plugin_text.$plugs_text;
		$text .= "</div>";
	}

	$text .= adnav_cat(ADLAN_CL_8, '', E_16_NAV_DOCS, 'docsMenu');
	$text .= "<div id='docsMenu' class='menu' onmouseover=\"menuMouseover(event)\">";
	if (!$handle=opendir(e_DOCS.e_LANGUAGE."/")) 
	{
		$handle=opendir(e_DOCS."English/");
	}
	$i=1;
	while ($file = readdir($handle)) 
	{
		if ($file != "." && $file != ".." && $file != "CVS") 
		{
			$text .= adnav_main(str_replace("_", " ", $file), e_ADMIN."docs.php?".$i, E_16_DOCS);
			$i++;
		}
	}
	closedir($handle);
	$text .= "</div>";


	$text .= "</div>
	</td>";

	if ($exit != 'off') 
	{
		$text .= "<td style='width: 160px; white-space: nowrap'>
		<div class='menuBar' style='width: 100%'>";

		$text .= adnav_cat(ADLAN_53, e_BASE.'index.php', E_16_NAV_LEAV);
		$text .= adnav_cat(ADLAN_46, e_ADMIN.'admin.php?logout', E_16_NAV_LGOT);

		$text .= "</div>
		</td>";
	}

	$text .= "</tr>
	</table>
	</div>";

	return $text;
}
