<?php
# TO ECHO, USE echoctmip() in notes.txt
function tmip_load_css(){
	global $tmip_plugin_dir_url;
	wp_enqueue_style(
		'tmip_cmstyle',
		$tmip_plugin_dir_url.'css/common.css',
		false,
		TMIP_VERSION,
		'all'
	);
}
function tmip_load_js(){
	global $tmip_plugin_dir_url;
	wp_enqueue_script(
		'tmip_cmjavasc',
		$tmip_plugin_dir_url.'js/common.js',
		array(), //array( 'jquery', 'jquery-ui-dialog' ), // Add jquery
		TMIP_VERSION,
		true
	);
}

function tmip_plugins_dirpath($file) {
	global 	$pluginRootFilepath,$tmip_plugin_dir_url,$tmip_plugin_root_dir,
			$tmip_plugin_dir_name,$tmip_plugin_basename,$tmip_plugin_pathbase,$tmip_plugin_admin_url,
			$tmip_plugin_sett_url,$tmip_rate_wp_url,$tmip_rate_pl_url;
	
	$pluginRootFilepath=	$file;
	$tmip_plugin_dir_url=	plugin_dir_url($file);
	$tmip_plugin_root_dir=	plugin_dir_path($file);
	$tmip_plugin_dir_name=	tmip_plugin_dir_name;
	$tmip_plugin_basename=	basename($file);
	$tmip_plugin_pathbase=	$tmip_plugin_dir_name.'/'.$tmip_plugin_basename;
	$tmip_plugin_admin_url=	get_admin_url();
	
	// Functions URLS
	$tmip_plugin_sett_url=	$tmip_plugin_admin_url.'admin.php?page=tmip_lnk_wp_settings';
	$tmip_rate_wp_url=		'//wordpress.org/support/plugin/'.$tmip_plugin_dir_name.'/reviews/?rate=';
	$tmip_rate_pl_url=		$tmip_plugin_admin_url.'admin.php?page=tmip_lnk_wp_settings&showdivtarget=sec_rate&rate=';
}
function tmip_static_urls() {
	if ($_SERVER['HTTPS']) {
		$tmip_prot='https';
		$tmip_rFsrc='wordpressDBS';
	} else {
		$tmip_prot='http';
		$tmip_rFsrc='wordpressDB';
	}
	
	$wp_user_info_ae=base64_encode(get_bloginfo('admin_email',''));
	$wp_wp_version=urlencode(get_bloginfo('version',''));
	$wp_pl_version=urlencode(TMIP_VERSION);
	
	// 'false' to set constants case sensitive
	define("tmip_home_page_url", 			
		   $tmip_prot.'://www.'.tmip_domain_name.'');
	
	define("tmip_go_to_projects", 		
		   $tmip_prot.'://www.'.tmip_domain_name.'/members/index.php?rnDs=197201798&page=home&wMx=1&rFsrc='.$tmip_rFsrc);
	
	define("tmip_free_signup_page_url", 
		   tmip_service_url.'/tools/website-visitors-counter-traffic-tracker-statistics/index.php?sto=1');
	
	define("tmip_premium_signup_page_url", 
			tmip_service_url.'/members/index.php?page=spm_checkout&type=ssub&ntc=1');
	
	define("tmip_chart_tracker_url",     'https://tools.tracemyip.org/ipbox/md=1&pID=1-324473478-1-1600~1587599353~300*100*1*1*20~8CFFD1*33313B~0*0*0&uID=3-042220&pgB=013201&ttC=FF0000&trT=FFFFFF&erA=AED99E&orA=DEEFD7&nrC=666666&vrC=006666&sOP=icstzdlrpob&plVer='.$wp_pl_version.'&wpVer='.$wp_wp_version.'&wpAem='.$wp_user_info_ae);
	
	// wpPv: WP plugin version wpIf: wordpress iframe token to disable break out of frames
	define("tmip_ip_tools_index",     		'https://tools.tracemyip.org/home/'.UVAR.'wpPv='.$wp_pl_version.'&wpIf=1');
	
	define("tmip_visit_tracker_val", 		'tmip_visit_tracker', 	false);
	define("tmip_visit_tracker_default", 	'', 					false);
	
	define("tmip_position_val", 			'tmip_visit_tracker_position', 		false);
	define("tmip_position_default", 		'footer', 				false);
	
	define("tmip_page_tracker_val", 		'tmip_page_tracker', 	false);
	define("tmip_page_tracker_default", 	'', 					false);
}

function tmip_get_url_vars() {
	global $show_div_target;
	$show_div_target=array();
	if ($v=sanitize_text_field($_GET['showdivtarget'])) {
		$show_div_target=explode(',',$v);
	}
	global $hide_div_target;
	$hide_div_target=array();
	if ($v=sanitize_text_field($_GET['showdivtarget'])) {
		$hide_div_target=explode(',',$v);
	}
}
function tmip_wp_settings() {
	tmip_settings_page();
}
function tmip_load_fontawesome_cloufare() {
    wp_enqueue_style( 'style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css');
}
function tmip_rate_serv() { // 1234567890
	tmip_load_fontawesome_cloufare();
	$output .='<div id="tmip_sett_wrap_1">';
	$output .=tmip_rate_review_section();
	$output .='</div>';
	echo $output;
}
function tmip_access_reports(){
	global $tmip_plugin_dir_url,$tmip_plugin_sett_url;
	
	$menuID='tmip_admpanel_menu';
	$position=25; 
	$icon_url=$tmip_plugin_dir_url.'images/tmip_icon_admin_menu.png';
	$vis_tr_stats=tmip_log_stat_data(array('type'=>'vis_tr_stats'));

	// Main admin menu
	add_menu_page(
		'', 						// menu browser title
		tmip_service_Nname, 		// menu link name
		'manage_options',   		// capability
		$menuID, 					// main menu link
		'',							// call function - called by submenu item
		$icon_url,					// Icon class 'dashicons-list-view' or URL
		$position					// Menu position
	);  
	add_submenu_page(				// Replace top menu name with another
		$menuID,					// Parent menu tree slug
		tmip_service_Nname.' > '.ucwords(tmip_submenu_reports),		// page title
		ucwords(tmip_submenu_reports),	// submenu link name
		'manage_options',			// capability
		$menuID,					// main menu link - make same as primary menu name to create and replace submenu
		'tmip_reports_page'			// callback function
	);  
	add_submenu_page(				// Replace top menu name with another
		$menuID,					// Parent menu tree slug
		tmip_service_Nname.' > '.ucwords(tmip_submenu_settings),		// page title
		ucwords(tmip_submenu_settings),	// submenu link name
		'manage_options',			// capability
		'tmip_lnk_wp_settings',		// main menu link - make same as primary menu name to create and replace submenu
		'tmip_wp_settings'		// callback function
	);  
	add_submenu_page(				// Replace top menu name with another
		$menuID,					// Parent menu tree slug
		tmip_service_Nname.' > '.ucwords(tmip_submenu_my_ipv46_adr),		// page title
		ucwords(tmip_submenu_my_ipv46_adr),	// submenu link name
		'manage_options',			// capability
		'tmip_lnk_myipv_46_adr',	// main menu link - make same as primary menu name to create and replace submenu
		'tmip_myipv_46_adr'			// callback function
	);  
	add_submenu_page(				// Replace top menu name with another
		$menuID,					// Parent menu tree slug
		tmip_service_Nname.' > '.ucwords(tmip_submenu_ip_tools),		// page title
		ucwords('IP Tools'),	// submenu link name
		'manage_options',			// capability
		'tmip_lnk_ip_tools_idx',	// main menu link - make same as primary menu name to create and replace submenu
		'tmip_ip_tools_idx'			// callback function
	);
	if ($vis_tr_stats['vis_tr_queries_cnt']>=tmip_codes_usage_rate_thresh) {
		if (tmip_enable_meta_rating) {
			add_submenu_page(
				$menuID,				// Parent menu tree slug
				ucwords(tmip_submenu_rate_service),	// page title
				ucwords(tmip_submenu_rate_service),	// submenu link name
				'manage_options',		// capability
				'tmip_rate_serv_lnk',	// main menu link - make same as primary menu name to create and replace submenu
				'tmip_rate_serv'		// callback function
			); 
		}
	}
}
function tmip_reports_page() {
	tmip_wp_iframe_page(tmip_go_to_projects);
}
function tmip_myipv_46_adr() {
	tmip_wp_iframe_page(tmip_chart_tracker_url);
}
function tmip_ip_tools_idx() {
	tmip_wp_iframe_page(tmip_ip_tools_index);
}
function tmip_wp_iframe_page($url) {
	global $tmip_plugin_dir_url;
	tmip_load_css();
	
	// ajLoader_05.gif, ajLoader_06.gif
	echo '<style>
		body::-webkit-scrollbar {
			width: 0.7em;
		}
		body::-webkit-scrollbar-thumb {
			background-color: black;
		}	
		.tmip_iframe_container {
			height:95vh;
			width:99%;
			border:none;
			background-color:#333;	
			background-image: url(\''.$tmip_plugin_dir_url.'images/ajLoader_06.gif\');
			background-repeat: no-repeat;
  			background-attachment: fixed;
  			background-position: center 40%;
		}	
	</style>';
	
/*	
		.tmip_iframe_loading_console {
			height:120px;
			text-align:center;
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0,0.5);
			z-index: 2;
			cursor: pointer;
		}
		<div id="tmip_loadconmsg" class="tmip_iframe_loading_console">
        	<div>loading console...</div>
    	</div>
		<iframe src="'.$tmip_link.'" class="tmip_iframe_container" onload="document.getElementById(\'tmip_loadconmsg\').style.display=\'none\';"></iframe>
*/
	echo '
		<iframe src="'.$url.'" class="tmip_iframe_container"><p>'.tmip_iframe_javascript_n.'</p></iframe>
	';
}
function tmip_plugin_row_add_rating($links,$file) {
	global 	$tmip_plugin_dir_name,$tmip_plugin_pathbase,$tmip_plugin_sett_url,$tmip_plugin_dir_name,
			$tmip_rate_wp_url,$tmip_rate_pl_url;
	
	if ($tmip_plugin_pathbase!==$file ) return $links;
	$rating=array();
	$rating[1]='poor';
	$rating[2]='works';
	$rating[3]='good';
	$rating[4]='great';
	$rating[5]='awesome';
	
	$linkSet='';
	$linkSet=__(ucfirst(tmip_lang_please_rate_us).': ',$tmip_plugin_dir_name);
	$linkSet .='<span class="rate-tmip-stars">';
	foreach ($rating as $value=>$name) {
		$rate_url=$tmip_rate_wp_url;
		$url_target='target="_blank"';
		if (tmip_enable_meta_rating==2 and $value<5) {
			//$name=$rating[$value];
			$rate_url=$tmip_rate_pl_url;
			$url_target='';
		}
		$linkSet .="<a href='".$rate_url.$value."#new-post' ".$url_target." data-rating='".$value."' title='" . __($name, $tmip_plugin_dir_name)."'><span class='dashicons dashicons-star-filled' style='color:#ffb900!important;'></span></a>";
	}
	$linkSet .='<span>';
	$links[] = $linkSet;
	return $links;
}
function tmip_setting_link($links) {
	global $tmip_plugin_admin_url;
	if (is_multisite() && !is_main_site () && !multisite_settings_page_enabled () && !current_user_can('manage_network_plugins')) return $links;
	$settings_link='<a href="'.$tmip_plugin_sett_url.'">'._x('Settings', 'Menu item', 'tracemyip-visitor-analytics-ip-tracking-control') . '</a>';
	array_unshift ($links, $settings_link);
	return $links;
}
function tmip_plugin_action_links($links) {
	global $tmip_plugin_sett_url;
	if (is_multisite() && !is_main_site () && !multisite_settings_page_enabled () && !current_user_can ('manage_network_plugins')) return $links;
	$settings_link = '<a href="'.$tmip_plugin_sett_url.'">'._x('Settings', 'Menu item', 'tracemyip-visitor-analytics-ip-tracking-control') . '</a>';
	array_unshift ($links, $settings_link);
	return $links;
}
function tmip_insert_visitor_tracker() {
    $code=get_option(tmip_visit_tracker_val);
    if ($code and strpos($code,'4684NR-IPIB') !== false and strpos($code,'/tracker/') !== false) {
		$code=stripslashes($code);
		$code=str_replace('src="http:','src="',$code);
		$code=str_replace('src="https:','src="',$code);
		
		// Convert an HTML code to JavaScript code in real time
		if (tmip_html_to_js_format_realti==1 and $code and 
				strpos($code,'<script')==false and strpos($code,'rgtype=4684NR')==false) {
			
			// Extract tracker code
			$successVTC=0;
			$input=array();
			$input['code_source']=$code;
			$input['code_tag']='img';
			$r=tmip_get_html_tag_string($input);
			$tmip_tag_attributes=$r[1]['tag_attributes'];
			$tmip_code_source=$r['code_source'];

			// Check and convert the tracker code
			if ($tmip_tag_attributes and $tmip_code_source) {
				$input=array();
				$input['convert_mode']=1; // 1-realtime, 2-on submit
				$input['code_source']=$tmip_code_source;
				$input['tag_attributes']=$tmip_tag_attributes;
				$res=tmip_convert_html_code_to_javascript($input);
				$successVTC=$res['success'];
				if ($successVTC) $code=$res['code_tracker'];
			}
		}
			
		$js_conversion=NULL;
		if ($successVTC) $js_conversion=tmip_stats_optimi_pagntra; elseif ($res['alerts']) $js_conversion=$res['alerts'];
		tmip_log_stat_data(array('type'=>'vis_tr_query','js_conversion'=>$js_conversion));
		echo $code;
    }
}
function tmip_insert_page_tracker() {
    $code=get_option(tmip_page_tracker_val);
    if ($code and strpos($code,'tP_lanPTl') !== false) {
		$code=stripslashes($code);
		$code=str_replace('src="http:','src="',$code);
		$code=str_replace('src="https:','src="',$code);
		tmip_log_stat_data(array('type'=>'pag_tr_query'));
		echo $code;
    }
}
function tmip_log_stat_data($input) {
	foreach ($input as $a=>$b) ${$a}=$b; unset($a,$b,$input);

	$output=array();
	$action=NULL;
	$database_column1=NULL; 
	$database_column2=NULL;

	// Log stats
	
	if ($type=='vis_tr_query' or $type=='vis_tr_reset' or $type=='pag_tr_query' or $type=='pag_tr_reset' or $type=='reset_all_data') {
		if ($type=='vis_tr_query' or $type=='vis_tr_reset' or $type=='reset_all_data') {
			$database_column1='tmip_stat_vis_tr_query';
			$database_column2='tmip_stat_vis_tr_hjconv';
			$database_column3='tmip_stat_vis_tr_total_qry';
			$database_column4='tmip_stat_vis_tr_first_used_unix';
						
		} elseif ($type=='pag_tr_query' or $type=='pag_tr_reset') {
			$database_column1='tmip_stat_pag_tr_query';
		}
		if ($type=='vis_tr_query' or $type=='pag_tr_query') {
			$action='write';
		} elseif ($type=='vis_tr_reset' or $type=='pag_tr_reset') {
			$action='reset';
		}
		
		if ($action=='write') {
			if ($database_column1) update_option($database_column1,(int)get_option($database_column1)+1);
			if ($database_column2) update_option($database_column2, strip_tags(esc_js($js_conversion))); // This was previously sanitized. strip tags is not intended to sanitize and is used for a different purpose.
			if ($database_column3) update_option($database_column3,(int)get_option($database_column3)+1);
			if ($database_column4 and empty(get_option($database_column4))) update_option($database_column4, time());// Nothing to sanitize here, its unix time();
		} elseif ($action=='reset') {
			if ($database_column1) update_option($database_column1,	0);
			if ($database_column2) update_option($database_column2, '');
		} elseif ($type=='reset_all_data') {
			if ($database_column1) update_option($database_column1,	0);
			if ($database_column2) update_option($database_column2, '');
			if ($database_column3) update_option($database_column3,	0);
			if ($database_column4) update_option($database_column4, 0);
		}
	

	# READ internal plugin stats
	// Visitor tracker
	} elseif (tmip_codes_usage_stats_data and $type=='vis_tr_stats') {
		$output['vis_tr_queries_cnt']=(int)get_option('tmip_stat_vis_tr_query');
		if ($v=get_option('tmip_stat_vis_tr_hjconv')) {
			$v=stripslashes($v);
		} else {
			$v=NULL;
		}
		$output['vis_tr_htmljs_conv']=$v;
		$output['vis_tr_first_use_unix']=(int)get_option('tmip_stat_vis_tr_first_used_unix');
		$output['vis_tr_total_queries']=(int)get_option('tmip_stat_vis_tr_total_qry');
		
		if ($v=$output['vis_tr_queries_cnt']) {
			$activity=tmip_stats_receive_trdata;
			if (tmip_codes_usage_stats_data==2) $activity .=' ['.($v/1000).']';
		} else {
			$activity=tmip_stats_pending_trdata.'';
		}
		$output['vis_tr_stats']='<b>'.tmip_stats_activi_pagntra.'</b>: '.$activity;
		if ($v=$output['vis_tr_htmljs_conv']) {
			$output['vis_tr_stats'] .=', <b>'.tmip_stats_status_pagntra.'</b>: '.$v;
		}
		return $output;
	
	// Page tracker
	} elseif (tmip_codes_usage_stats_data and $type=='pag_tr_stats') {
		$output['pag_tr_queries_cnt']=(int)get_option('tmip_stat_pag_tr_query');
		if ($v=$output['pag_tr_queries_cnt']) {
			$activity=tmip_stats_activet_pgdata;
			if (tmip_codes_usage_stats_data==2) $activity .=' ['.($v/1000).']';
		} else {
			$activity=tmip_stats_pending_pgdata.'';
		}
		$output['pag_tr_stats']='<b>'.tmip_stats_status_pagntra.'</b>: '.$activity;
		return $output;
	}
}
		

function tmip_addToTags($pid){
   if (is_single()) {
       global $post;
	   $queried_post=get_post($pid);
       $authorId=$queried_post->post_author;
	   ?>
		<script type="text/javascript">
            var _tmip=_tmip || [];
            _tmip.push({"tags": {"author": "<?php the_author_meta( 'nickname', $authorId); ?>"}});
        </script>
	   <?php
   }
}
// Add to Dashboard menu
function tmip_admin_menu() {
	$hook=add_submenu_page(
		'index.php',	// index,php to attached to dashboard menu
		__(tmip_service_Nname.' Reports'),
		__(tmip_service_Nname.' Reports'),
		'publish_posts',
		'tmip',			// tmip to highlight the dashboard link
		'tmip_reports_page'
	);
	add_action(
		'load-$hook',
		'tmip_reports_load'
	);
/*
	// Add to plugins drop down 
	$hook=add_submenu_page(
		'plugins.php',
		__(''.tmip_service_Nname.' Settings'),
		__(''.tmip_service_Nname.' Settings'),
		'manage_options',
		'tmip_admin',
		'tmip_reports_page1'
	);
*/
}
// Add link to WP settings
function add_tmip_option_page() {
	global $tmip_plugin_sett_url;
	global $tmip_plugin_basename;
	add_options_page(tmip_service_Nname.' Settings', tmip_service_Nname, "manage_options",  $tmip_plugin_sett_url, '');
// add_options_page(tmip_service_Nname.' Settings', tmip_service_Nname, "manage_options", $tmip_plugin_basename, 'tmip_settings_page');
}
function tmip_reports_load() {
	add_action(
		'admin_head',
		'tmip_reports_head'
	);
}
function tmip_reports_head() {
	echo '<style type="text/css">body { height: 100%; }</style>';
}

function tmip_get_html_tag_string($input,$returnrs=NULL) {
	foreach ($input as $a=>$b) ${$a}=$b; unset($a,$b,$input);
	
	$array_result=array();
	$output=array();
	
	$code_source=trim($code_source);
	$output['code_source']=$code_source;
	if ($code_source) {
		preg_match_all('/<'.$code_tag.'[^>]+>/i',$code_source, $array_result); 
	}
	if (is_array($array_result) and is_array($array_result[0]) and array_filter($array_result[0])) {
		$i=1;
		foreach ($array_result[0] as $tag_html) {
			if ($tag_html) {
				$output[$i]['tag_html']=$tag_html;
				$output[$i]['tag_preview']=htmlentities($tag_html);
				
				// All Attributes
				$data=array();
				if (!$code_attrib) {
					$dom = new DOMDocument();
					$dom->loadHTML($tag_html);
					$ul = $dom->getElementsByTagName($code_tag)->item(0);
					if ($ul->hasAttributes()) {
						foreach ($ul->attributes as $attr) {
							$name = $attr->nodeName;
							$value = $attr->nodeValue;    
							$data[$name] = $value;
						}
					}
					$output[$i]['tag_attributes']=$data;
				
				// Specific Attributes
				} else {
										preg_match_all('/('.trim($code_attrib).')=("[^"]*")/i', $tag_html, $matches);
					if (!$matches[0]) 	preg_match_all("/(".trim($code_attrib).")=('[^\']*')/i", $tag_html, $matches);
					if (is_array($matches) and array_filter($matches[1])) {
						$t=0;
						foreach ($matches[1] as $tag_key) {
							$tag_key=trim($tag_key);
							$raw_value=trim($matches[2][$t]);
							$value=trim($raw_value,'"');
							if ($value===$raw_value) {
								$value=trim($raw_value,"'");
							}
							$data[trim($tag_key)]=$value;
							$t++;
						}
					}
					$output[$i]['tag_attributes']=$data;
				}
			}
			$i++;
		}
	}
	if (isset($returnrs)) return $output[$returnrs]; else return $output;
}
function tmip_convert_html_code_to_javascript($input,$returnrs=NULL) {
	foreach ($input as $a=>$b) ${$a}=$b; unset($a,$b,$input);
	$output=array();
	$alerts=array();
	$comments=array();
	$success=0;
	$tVr=900; // Code version - reserved range 900-999 for WP plugin. Calculated in pSum.php
	
	$lanCompNameURL=tmip_service_Dname;
	$alert_1=tmip_prov_trackerc_valid;
	$alert_2=tmip_trk_code_inst_refrm;
	$alert_5=tmip_prov_trackerc_valid;
	$alert_3=tmip_prov_trk_code_notva;
	$alert_4=tmip_check_trk_code_inst;
	
	$srsString=$tag_attributes['src'];
	$urlParse=parse_url($srsString);
	$host=$urlParse['host'];
	$e=explode('.',$host);

	if ($e[1] and $e[2] and $srs=$srsString and stristr($srs,'/tracker/') and 
			(!stristr($code_source,'stlVar2=') and !stristr($code_source,'pidnVar2=') and !stristr($code_source,'prtVar2=')) and 
			stristr($srs,'/4684NR-IPIB/') and (stristr($srs,'/njsUrl/') or stristr($srs,'/ans/'))
		) {
		$trkDomnURL=$e[1].'.'.$e[2];
		$e=preg_split('/'.$trkDomnURL.'/i', $srs);
		$trkSD=trim($e[0],'//.');
		$trkST=trim($e[1]);
		$e=explode('/tracker/',$srs);
		$e=explode('/',$e[1]);
		$styleN=trim($e[0]);
		$codeID=trim($e[1]);
		$ProjID=trim($e[2]);
		$ProjPrt=trim($e[3]);
		$curYear=date("Y");
		$timeStamp=date("His");
		$dateStamp=date("mdY");
		$imgAlt=trim($tag_attributes['alt']);

		if ($styleN and $codeID and $ProjID and $ProjPrt and $curYear and $timeStamp and $dateStamp) {
			$converted=0;
			// Does not include code version tracking
			# /tracker/1500~1587394715~14*2~0F5999*08EFFF*537899*000000~1*1*0*1*1/4684NR-IPIB/124352683/1/njsUrl/
			$html_srs_code='';
			if (stristr($srs,'/njsUrl/')) {
				$converted=1;
				$html_srs_code='//'.$trkSD.'.'.$trkDomnURL.'/tracker/'.$styleN.'/'.$codeID.'/'.$ProjID.'/'.$ProjPrt.'/njsUrl/';
			
			// Includes code version tracking
			# /tracker/1500~1587394715~14*2~0F5999*08EFFF*537899*000000~1*1*0*1*1/4684NR-IPIB/124352683/1/12/ans/
			} elseif (stristr($srs,'/ans/')) {
				$converted=2;
				$html_srs_code='//'.$trkSD.'.'.$trkDomnURL.'/tracker/'.$styleN.'/'.$codeID.'/'.$ProjID.'/'.$ProjPrt.'/'.$tVr.'/ans/';
			}
			if ($converted) {
				$code_comment='';
				if ($convert_mode==1) {
					$code_comment='HTML>JS Realtime';
				} elseif ($convert_mode==2) {
					$code_comment='HTML>JS On-Install';
				}
				$code=('
<!-- Start: Copyright '.trim($curYear).' '.$lanCompNameURL.' Service Code ('.$timeStamp.'-'.$dateStamp.') '.$code_comment.' - DO NOT MODIFY //-->
<div style="line-height:16px;text-align:center;"><script type="text/javascript" src="//'.$trkSD.'.'.$trkDomnURL.'/tracker/lgUrl.php?stlVar2='.$styleN.'&amp;rgtype='.$codeID.'&amp;pidnVar2='.$ProjID.'&amp;prtVar2='.$ProjPrt.'&amp;scvVar2='.$tVr.'"></script><noscript><a href="https://www.'.$trkDomnURL.'"><img src="'.$html_srs_code.'" alt="'.$imgAlt.'" style="border:0px;"></a></noscript></div>
<!-- End: '.$lanCompNameURL.' Service Code //-->');
				$code=tmip_unify_new_lines(trim($code));

				if (tmip_html_to_js_format_onsubm==1) $v=$alert_2; else $v=$alert_5;
				$alerts[]=ucfirst($v.'<hr>'.$alert_4);
				$success=1;
			}
		} else {
			$alerts[]=str_replace('%ERR_NUM%','ER-0419200934',ucfirst($alert_3));
		}
	} elseif (!stristr($code_source,'stlVar2=') or !stristr($code_source,'pidnVar2=') or !stristr($code_source,'prtVar2=')) {
		$alerts[]=str_replace('%ERR_NUM%','ER-0419200929',ucfirst($alert_3));
	} else {
		$alerts[]=ucfirst($alert_1);
		$comments[]=ucfirst($alert_4);
		$code=$code_source;
		$success=2;
	}
	$output['success']=trim($success);
	$output['alerts']=implode('<br>',$alerts);
	$output['comments']=implode('<br>',$comments);
	$output['code_tracker']=trim($code);
	$output['code_preview']=htmlentities($code);
	if (isset($returnrs)) return $output[$returnrs]; else return $output;
}

function tmip_alert_box($input) {
	foreach ($input as $a=>$b) ${$a}=$b; unset($a,$b,$input);
	if ($title[0])		$_title=trim($title[0]); 		elseif ($title[1])		$_title=trim($title[1]);
	if ($comments[0])	$_comments=trim($comments[0]); 	elseif ($comments[1])	$_comments=trim($comments[1]);
	$codeAlert_title=tmip_settings_hv_updated;
	$output.='<div class="'.$box_class.'">';
	if ($_title)				$output .='<p class="'.$title_class.'">'.$_title.'</p>';
	if ($_title and $_comments)	$output .='<hr>';
	if ($_comments)				$output .=$_comments;
	$output .='</div><br>';
	return $output;
}

function tmip_reset_plugin_settings() {
	// Reset settings
	update_option(tmip_position_val, ''); // clear to trigger first time install state
	update_option(tmip_visit_tracker_val,'');
	update_option(tmip_page_tracker_val,'');
}
function tmip_remove_tabs_new_lines($str,$option=1) {
	if ($str) {
		$str=preg_replace('/\r|\n/', '', $str);
		// preserve new lines after comments
		if ($option and $option<>1) {
			$str=str_replace('//-->','//-->'."\n",$str);
			$str=str_replace('<!--',"\n".'<!--',$str);
		}
		if ($option==2) {
			$str=preg_replace("/\s+/",' ', $str);
		}
		$str=trim($str);
	}
	return $str;
}
function tmip_settings_page() {

	global $show_div_target,$hide_div_target,$tmip_plugin_dir_url;
	
	// Override section by main menu URL token
	$menu_url_show_div_target=array();
	if (is_array($show_div_target) and array_filter($show_div_target)) {
		$menu_url_show_div_target=$show_div_target;
	}
	tmip_load_css();
	tmip_load_fontawesome_cloufare();
	
	// Open panel
	$output .='<div id="tmip_sett_wrap_1">';
	
	$codeAlert_neutralTitle=NULL;;
	$codeAlert_neutral_text=NULL;;
	$codeAlert_green_text=NULL;
	$codeAlert_red_text=NULL;
	$proceedToUpdate=0;
	$haltUpdate=NULL;
	$noChanges=NULL;
	$visTRpsDbVar=NULL;
	$pagTRpsDbVar=NULL;
	$info_update=sanitize_text_field($_POST['info_update']);
	$tmip_posted_nonce=sanitize_text_field($_POST['nonce_tmip_check']);
	
	$failedNonceCheck=0;
	$allowUpdate=1; // 1-Update on submit. 0-debug
	$tmip_genert_nonce=wp_create_nonce(plugin_basename(__FILE__));
	$tmip_verify_nonce=wp_verify_nonce($tmip_posted_nonce,plugin_basename(__FILE__));
	if ($info_update and $tmip_posted_nonce and $tmip_verify_nonce<>1) {
		$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_invalid_nonce_check.'</div><br>';
		$failedNonceCheck=1;
	}

	// tmip_log_stat_data(array('type'=>'reset_all_data')); // reset all stats data, leave the codes

	
	// USAGE STATS
	$vis_tr_stats=tmip_log_stat_data(array('type'=>'vis_tr_stats'));
	$pag_tr_stats=tmip_log_stat_data(array('type'=>'pag_tr_stats'));

	$vtpTRpsDbVar=!empty($postVarVTpos) ? $postVarVTpos : get_option(tmip_position_val); // NULL before first saved settings

	if (!$vtpTRpsDbVar or ($info_update and !trim(sanitize_text_field($_POST[tmip_visit_tracker_val])))) {
		$hide_div_target=array('sec_rate');
	}
	
	if ($vis_tr_stats['vis_tr_total_queries']<tmip_codes_usage_rate_thresh) {
		$hide_div_target=array('sec_rate');
	}

	###### CHECK PROJECT CODE ALERT ###########################################################
	// Project code is not present
	$vistracker_db_source_code=trim(stripslashes(get_option(tmip_visit_tracker_val)));
	
	$allowed_protocols=array();
	$allowed_html=array(
    'div' =>	 	array('style' => array('style'),'script' => array('type','src')),
    'script' => 	array('type' => array(),'src' => array()),
    'noscript' => 	array(),
    'img' => 		array('src' => array(),'alt' => array(),'alt' => array('style')),
    'a' => 			array('href' => array(),'title' => array()),
	);
	$postVarVisTr=wp_kses($_POST[tmip_visit_tracker_val],$allowed_html,$allowed_protocols);
	$postVarVisTr=tmip_unify_new_lines(trim(stripslashes($postVarVisTr)));
	$postVarVisTr=tmip_remove_tabs_new_lines($postVarVisTr);
	
	if ($vtpTRpsDbVar or $postVarVisTr or ($info_update and !$postVarVisTr)) {
		$visTRpsDbVar=!empty($postVarVisTr) ? $postVarVisTr : get_option(tmip_visit_tracker_val);
		if (!$visTRpsDbVar) {
			$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_no_code_entered_alr.'</div><br>';
			if (empty($postVarVisTr) and $info_update) {
				$proceedToUpdate=10; // Alow to clear the code input box
			} else {
				//$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_no_code_entered_alr.'</div><br>';
			}
			
		// Project code contains page tracker code
		} elseif ($visTRpsDbVar and strpos($visTRpsDbVar,'tP_lanPTl') !== false) {
			if (empty($postVarVisTr) and $info_update) {
				$proceedToUpdate=11;
			} else {
				$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_pagetr_into_vistrak.'</div><br>';
				$haltUpdate='hu_visitor_tracker_1';
			}
		
		// Project tracker code is not valid - quick check
		} elseif ($visTRpsDbVar and (strpos($visTRpsDbVar,'4684NR-IPIB')==false or strpos($visTRpsDbVar,'/tracker/'))==false) {
			if (empty($postVarVisTr) and $info_update) {
				$proceedToUpdate=12;
			} else {
				$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_tracker_code_ent_nv.'</div>';
				$v=str_replace('%ERR_NUM%','ER-0423200718',tmip_prov_trk_code_notva);
				$codeAlert_red_text .='<p class="tmip_alert_comments">'.$v.'</p>';
				$haltUpdate='hu_visitor_tracker_2';
			}
		}

		// ###### Verify and convert an HTML code into JavaScript code to enhance statistics #####
		//  or $source_code=get_option(tmip_visit_tracker_val)
		if (!$haltUpdate and (!$postVarVisTr or (
				$postVarVisTr and ($postVarVisTr<>$vistracker_db_source_code or 
				(tmip_html_to_js_format_onsubm and $postVarVisTr==$vistracker_db_source_code and 
				 	$vis_tr_stats['vis_tr_htmljs_conv']=='optimized')))
			)) {
			if ($postVarVisTr)					$code_source=$postVarVisTr;
			elseif ($vistracker_db_source_code)	$code_source=$vistracker_db_source_code;
					
				
			// Extract tracker code
			$input=array();
			$input['code_source']=$code_source;
			$input['code_tag']='img';
			$r=tmip_get_html_tag_string($input);
			$tmip_tag_attributes=$r[1]['tag_attributes'];
			$tmip_code_source=$r['code_source'];

			// Check and convert the tracker code
			if ($tmip_tag_attributes and $tmip_code_source) {
				$input=array();
				$input['convert_mode']=2; // 1-realtime, 2-on submit
				$input['code_source']=$tmip_code_source;
				$input['tag_attributes']=$tmip_tag_attributes;
				$res=tmip_convert_html_code_to_javascript($input);
				$successVTC=$res['success'];

				if ($info_update and $successVTC and $postVarVisTr) {
					$codeAlert_green_text .='<div class="tmip_alert_subtitle">'.tmip_fa_checkmark_lg.' '.$res['alerts'].'</div>';
					if (tmip_html_to_js_format_onsubm==1) {
						$postVarVisTr=$res['code_tracker'];
					}
				} elseif (!$successVTC) {
					$haltUpdate='hu_visitor_tracker_3';
					$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.$res['alerts'].'</div>';
				}
				if ($info_update and $successVTC and $postVarVisTr) {
					$codeAlert_green_text .='<p class="tmip_alert_comments">'.$res['comments'].'</p>';
					$proceedToUpdate=$res['success']; // $proceedToUpdate=2;
				}
			}

		} elseif ($info_update and $postVarVisTr==$vistracker_db_source_code) {
			$proceedToUpdate=9;
			$noChanges[]=1;
		}
	}
	

	
	###### CHECK CODE POSITION ASSIGNMENT ###########################################################
	$vis_tracker_pos_val=get_option(tmip_position_val);
	$postVarVTpos=trim(sanitize_text_field($_POST[tmip_position_val])); 
	if ($info_update and $postVarVTpos and $postVarVTpos==$vis_tracker_pos_val) {
		$proceedToUpdate=30;
		$noChanges[]=1;
	} else {
		if (!$postVarVisTr and $info_update) {
			//$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_no_code_entered_alr.'</div><br>';
		} elseif ($postVarVTpos and strpos($postVarVTpos,'header')!== false) {
			$codeAlert_green_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_vistr_set_to_headA.'</div><br>';
		} elseif ($postVarVTpos and strpos($postVarVTpos,'footer')!== false) {
			$codeAlert_green_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_vistr_set_to_footA.'</div><br>';
		}
	}
	
	###### CHECK PAGE TRACKER CODE ###########################################################
	// Page tracker code contains visitor tracker code
	$pagtracker_db_source_code=trim((get_option(tmip_page_tracker_val)));
	$postVarPagTr=tmip_sanitize_rebuild_page_tracker_code($_POST[tmip_page_tracker_val]);
	$postVarPagTr=str_replace('/script>"','\\\/script>"',$postVarPagTr);
	//$postVarPagTr=esc_js(tmip_remove_tabs_new_lines($_POST[tmip_page_tracker_val]));
	//$postVarPagTr=tmip_unify_new_lines(trim(stripslashes($postVarPagTr)));
	//$postVarPagTr=html_entity_decode($postVarPagTr);
	//$postVarPagTr=str_replace('/script>"','\\\/script>"',$postVarPagTr);

	$pagTRpsDbVar=!empty($postVarPagTr) ? $postVarPagTr : get_option(tmip_page_tracker_val);
	if ($pagTRpsDbVar and strpos($pagTRpsDbVar,'rgtype=4684NR-IPIB')!== false) {
		if (empty($postVarPagTr) and $info_update) {
			$proceedToUpdate=20;
		} else {
			$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_vistrk_into_pagetrk.'</div><br>';
			$haltUpdate='hu_page_tracker_1';
		}
	
	// Page Tracker code is not a JavaScript version of the code
	} elseif ($pagTRpsDbVar and 
			(	strpos($pagTRpsDbVar,'echo') !== false or 
				strpos($pagTRpsDbVar,'scr\"') !== false )
		) {
		if (empty($postVarPagTr) and $info_update) {
			$proceedToUpdate=21;
		} else {
			$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_pagetr_code_notjava.'</div><br>';
			$haltUpdate='hu_page_tracker_2';
		}
	// Page Tracker code is not valid
	} elseif ($pagTRpsDbVar and strpos($pagTRpsDbVar,'tP_lanPTl')==false) {
		if (empty($postVarPagTr) and $info_update) {
			$proceedToUpdate=22;
		} else {
			$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_pagetr_cd_not_valid.'</div>';
			$codeAlert_red_text .='<p class="tmip_alert_comments">'.tmip_pagetr_generate_npl.'</p>';
			$haltUpdate='hu_page_tracker_3';
		}
	// No changes in Visitor Tracker code on submit
	} elseif ($info_update and $postVarPagTr==$pagtracker_db_source_code) {
		$proceedToUpdate=19;
		$noChanges[]=1;
	
	// Page Tracker code valid and entered into DB
	} elseif ($postVarPagTr and $pagTRpsDbVar) {
		$proceedToUpdate=23;
		$codeAlert_green_text .='<div class="tmip_alert_subtitle">'.tmip_fa_checkmark_lg.' '.tmip_prov_trackerp_valid.'</div>';
	
	} elseif (!$postVarVisTr and $postVarPagTr) {
		$codeAlert_red_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_pagetr_no_vis_tralrt.'</div>';
	}



	###### CODE UPDATED ALERT ###########################################################
	if ($haltUpdate) $proceedToUpdate=0;
	if ($failedNonceCheck<>1) {

		// Remove visitor tracker code
		if ($info_update and (!$postVarVisTr and get_option(tmip_visit_tracker_val)) or stristr($haltUpdate,'hu_visitor_tracker'))	{
			if ($failedNonceCheck<>1 and $allowUpdate) {
				update_option(tmip_visit_tracker_val,'');
				tmip_log_stat_data(array('type'=>'vis_tr_reset'));
			}
			if (!$postVarVisTr) {
				$codeAlert_neutralTitle=tmip_settings_hv_updated;
				$codeAlert_neutral_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_vistr_cd_not_valid.'</div>';
			}
		}
		// Remove page tracker code
		if ($info_update and (!$postVarPagTr and get_option(tmip_page_tracker_val)) or stristr($haltUpdate,'hu_page_tracker')) {
			if ($allowUpdate) {
				update_option(tmip_page_tracker_val,'');
				tmip_log_stat_data(array('type'=>'pag_tr_reset'));
			}
			if (!$postVarPagTr) {
				$codeAlert_neutralTitle=tmip_settings_hv_updated;
				$codeAlert_neutral_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_pagtr_cd_not_valid.'</div>';
			}
		}
		
		
		// Update settings
		if ($proceedToUpdate and isset($info_update) and 
				 check_admin_referer('update_tmip_visit_tracker_nonce','tmip_visit_tracker_nonce')) {
			// Visitor Tracker Code
			$tmip_visit_tracker=$postVarVisTr;
			if ($tmip_visit_tracker=='') {
				$tmip_visit_tracker=tmip_visit_tracker_default;
			}
			if ($allowUpdate) {
				update_option(tmip_visit_tracker_val,$tmip_visit_tracker);
				tmip_log_stat_data(array('type'=>'vis_tr_reset'));
			}
	
			// Visitor Tracker Position
			$tmip_visit_tracker_position=$vis_tracker_pos_val;
			if (($tmip_visit_tracker_position != 'header') && ($tmip_visit_tracker_position != 'footer')) {
				$tmip_visit_tracker_position=tmip_position_default;
			}
			if ($allowUpdate) {
				update_option(tmip_position_val, $tmip_visit_tracker_position);
			}
			
			// Page Tracker Code
			$tmip_page_tracker=$postVarPagTr;
			if ($tmip_page_tracker=='') {
				$tmip_page_tracker=tmip_page_tracker_default;
			}
			if ($allowUpdate) {
				update_option(tmip_page_tracker_val,$tmip_page_tracker);
				tmip_log_stat_data(array('type'=>'pag_tr_reset'));
			}
			
			// Tracking stats update
			if ($allowUpdate) {
				$vis_tr_stats=tmip_log_stat_data(array('type'=>'vis_tr_stats'));
				$pag_tr_stats=tmip_log_stat_data(array('type'=>'pag_tr_stats'));
			}
		}
	
		if (is_array($noChanges) and count($noChanges)==3) {
			$codeAlert_neutralTitle=tmip_settings_no_changes;
			$codeAlert_neutral_text .='<div class="tmip_alert_subtitle">'.tmip_fa__hand_point_right_lg.' '.tmip_settings_reman_same.'</div>';
			if ($vistracker_db_source_code) {
				$show_div_target=array('sec_rate','sec_settings');
			}
			if (!$visTRpsDbVar) {
				$hide_div_target[]='sec_rate'; // 'sec_demotracker',
			}
		}
	}
	if ($codeAlert_red_text) {
		$show_div_target=array('sec_usage','sec_settings');
		$output .=tmip_alert_box(array(
			//				text, 							default
			'title'=>		array(tmip_fa_excl_triangle_lg,	''),
			'comments'=>	array($codeAlert_red_text,		''),
			'box_class'=>	'tmip_alertRed_div',
			'title_class'=>	'tmip_alert_red_title',
		));
	}
	if ($codeAlert_neutral_text) {
		$output .=tmip_alert_box(array(
			//				text, 							default
			'title'=>		array($codeAlert_neutralTitle,	''),
			'comments'=>	array($codeAlert_neutral_text,	''),
			'box_class'=>	'tmip_alertNeutral_div',
			'title_class'=>	'tmip_alert_neutral_title',
		));
	}
	if ($proceedToUpdate and $codeAlert_green_text) {
		$show_div_target=array('sec_rate','sec_settings');
		$output .=tmip_alert_box(array(
			//				text, 							default
			'title'=>		array($codeAlert_greenTitle,	tmip_settings_hv_updated),
			'comments'=>	array($codeAlert_green_text,	''),
			'box_class'=>	'tmip_alertGreen_div',
			'title_class'=>	'tmip_alert_green_title',
		));
	}
	

	// Show section requested by main menu selection
	if ($menu_url_show_div_target and array_filter($menu_url_show_div_target)) {
		$show_div_target=$menu_url_show_div_target;
		$hide_div_target=array();
	}

	###### FEATURES ###########################################################
	$logo_url=$tmip_plugin_dir_url.'images/tmip_logo_60x60_sq.png';		
	if ((!$show_div_target or in_array('sec_about',$show_div_target)) and !in_array('sec_about',$hide_div_target)) {
		$output .='<img src="'.$logo_url.'" style="vertical-align:middle;float:right;padding-bottom:20px;margin-top:-10px;">
			<h1 class="text-center">
			<i class="fa fa-chart-bar fa-sm" style="color: #097E71; opacity: 0.5;"></i> '.ucfirst(tmip_sectl_what_is_plugin).'
			</h1>
			<blockquote>
				<h2 class="tmip_sec_title">
				<i class="fa fa-angle-double-right fa-sm" style="color: #7E0929; opacity: 0.5;"></i>
				<a href="'.tmip_home_page_url.'" target="_blank"><b>'.tmip_service_Dname.'</b></a> is a free and premium website visitor tracking service that provides the following to wordpress users:</h2>
				<section>
					<ul class="tmip_sett_list_ol">
						<li><span>Full featured individual visitor <a href="'.tmip_home_page_url.'/website-analytics.htm" target="_blank">website statistics</a> IP tracker with individual person tagging</span></li>
						<li><span>Visitor IP address, visitor IP address changes and computer IDs tracking</span></li>
						<li><span>Visitor redirecting, alerts and blocking based on custom rules by an IP address, location, number of visits or page views, browser type, operating system, referrers, computer hardware specifications, etc.</span></li>
						<li><span>Mobile and desktop device statistics, targeting, redirection and browsing path control</span></li>
						<li><span>Individual Real-Time one-way message delivery to selected website visitors currently browsing a website</span></li>
						<li><span>Web page, links, document and contact forms protection, IP based tracking and complete access control</span></li>
						<li><span>Integrated IP tracking data visitor control interface within WordPress dashboard</span></li>
						<li><span>Fully compliant EU GDPR Data Processing with customizable level of restrictions per jurisdiction</span></li>
						<li><span><b>FREE</b> & advanced premium service <a href="'.tmip_premium_signup_page_url.'" target="_blank">subscriptions</a></span></li>
					</ul>
				</section>
			</blockquote>
		';
	}



	###### QUICK SETUP STEPS ###########################################################
	if ((!$show_div_target or in_array('sec_usage',$show_div_target)) and !in_array('sec_usage',$hide_div_target)) {
		$output .='<br>
			<h1 class="text-center">
				<i class="fa fa-file-alt fa-sm" style="color: #097E71; opacity: 0.5;"></i> '.ucfirst(tmip_sectl_easy_steps_set).'
			</h1>	
			<blockquote>
				<h2 class="tmip_sec_title">
				<i class="fa fa-angle-double-right fa-sm" style="color: #7E0929; opacity: 0.5;"></i>
				To activate '.tmip_service_Nname.' tracking for WordPress, follow these steps:</h2>
				<section>
					<ol tmip_sett_list_ul>
						<li><span><a href="'.tmip_free_signup_page_url.'" target="_blank"><b>Select a '.tmip_lang_visitr_track_ic.' style</b></a> and generate a <b>JavaScript</b> version of '.tmip_service_Dname.' '.tmip_lang_visitor_tr_code.'. Confirm your '.tmip_service_Dname.' account. If you already have a '.tmip_service_Dname.' account, simply login to your account and click on "Add a New Project" to generate a website '.tmip_lang_visitor_tr_code.' for your new WordPress website. 
						<br>* If you need to change a tracker style for an existing project, click on the "tracker code" link located to the right of the project\'s name.</span></li>
						
						<li><span><b>Copy the '.tmip_lang_visitor_tr_code.'</b> and paste it into the '.tmip_lang_visitor_tr_code.' input box below.</span></li>
						
						<li><span><b>Select the '.tmip_lang_tracker_icon_ps.'</b> using the drop down menu below and click on the ['.tmip_lang_update_settings.'] button</span></li>
						
						<li><span><b>Verify</b> that a '.tmip_lang_visitr_track_ic.' appears on <b>ALL</b> pages of your WordPress website. If you are a Level 2+ '.tmip_service_Dname.' subscriber, you can enable a hidden visitor tracker mode by clicking on the "edit" link located under your '.tmip_service_Dname.' account project page to the right of your project name.</span></li>

						<li><span><b>Access</b> your '.tmip_service_Dname.' console, login to WordPress and go to [Dashboard] => [TraceMyIP > Reports]</span></li>
					</ol>
				* <span>If you would like to control visitor access to your pages and/or send one-way messages to specific visitors, first, ensure that the Visitor Tracker code is installed properly and working. Next, go to the Page Tracker menu option under your project, generate a  ['.tmip_lang_page_tr_code.'] and place it into the ['.tmip_lang_page_tr_code.'] input box below.</span>
				</section>
			</blockquote>
			
		';
	}
	
	
	
	###### RATE BUTTON / REVIEW ###########################################################
	if ((!$show_div_target or $this_section=in_array('sec_rate',$show_div_target)) and !in_array('sec_rate',$hide_div_target)) {
		$output .='<br>'.tmip_rate_review_section();
		if (!$this_section) $output .='<br>';
	}


	
	
	
	// Close panel
	$output .='</div>';



	###### VISITOR TRACKER AND PAGE TRACKER SETTINGS ###########################################################
	if ((!$show_div_target or $this_section=in_array('sec_settings',$show_div_target)) and 
													 !in_array('sec_settings',$hide_div_target)) {
		global $tmip_plugin_sett_url;
		
		$vis_tr_used_since=NULL;
		if (($v=$vis_tr_stats['vis_tr_first_use_unix'])>10000) {
			$vis_tr_used_since='<br><b>'.(tmip_stats_used_since_unx).'</b>: '.date('F j, Y',$v);
		}
		
		$output .='<br><br><br>';
		$output .='<div id="tmip_sett_wrap_2">';
	
			$output .='<form method="post" action="'.$tmip_plugin_sett_url.'">';
			$output .=wp_nonce_field( 'update_tmip_visit_tracker_nonce', 'tmip_visit_tracker_nonce' );
		
		
			$output .='
				<h1>
				<i class="fa fa-cogs fa-lg" style="color: #7E0929; opacity: 0.5;"></i>
				'.tmip_service_Nname.' Settings</h1>
				<blockquote>
				<fieldset class="options">
		
					<table id="tmip_sett_area">
						<tr>
							<td>
								<label for="'.tmip_position_val.'"><b>'.tmip_lang_tracker_icon_ps.':</b></label>
							</td>
							<td>';
							$output .='<select name="'.tmip_position_val.'" id="'.tmip_position_val.'">';
							$output .='<option value="header"';
							if (get_option(tmip_position_val)=="header") {
								$output .=' selected="selected"';
							}
							$output .='>Header</option>';
							$output .='<option value="footer"';
							if (get_option(tmip_position_val)=="footer" or (!get_option(tmip_position_val) and tmip_position_default=='footer')) {
								$output .=' selected="selected"';
							}
							$output .='>Footer</option>';
							$output .='</select>';
							$output .='
							</td>
						</tr>
						<tr>
							<td width="200">
								<label for="'.tmip_visit_tracker_val.'" class="tmip_input_box_name">'.tmip_lang_visitor_tr_code.':</label>
								<br><div class="tmip_note">The '.tmip_lang_visitor_tr_code.' is used to track visitor IPs, computer IDs, traffic sources, location and the rest of website stats.</div>
								<br><div class="tmip_tip_important">Use <b>JavaScript</b> '.tmip_lang_visitor_tr_code.' only.</div>
							</td>
							<td>
								<textarea id="'.tmip_visit_tracker_val.'" name="'.tmip_visit_tracker_val.'" onClick="tmip_select_all(\''.tmip_visit_tracker_val.'\');" class="tmip_textarea" placeholder="'.tmip_vis_trk_inp_placehl.'">'.htmlentities(stripslashes(get_option(tmip_visit_tracker_val))).'</textarea>
								'.$vis_tr_stats['vis_tr_stats'].$vis_tr_used_since.'
							</td>
						</tr>
						<tr>
							<td width="200">
								<label for="'.tmip_lang_page_tr_code.'" class="tmip_input_box_name">'.tmip_lang_page_tr_code.':</label>
								<br><div class="tmip_note">The '.tmip_lang_page_tr_code.' is used to control access to pages, protect contact forms and send alerts and one-way messages to visitors</div>
								<br><div class="tmip_tip_important">Use <b>JavaScript</b> '.tmip_lang_page_tr_code.' only.</div>
							</td>
							<td>
								<textarea id="'.tmip_page_tracker_val.'" name="'.tmip_page_tracker_val.'" onClick="tmip_select_all(\''.tmip_page_tracker_val.'\');" class="tmip_textarea" placeholder="'.tmip_pag_trk_inp_placehl.'">'.stripslashes(get_option(tmip_page_tracker_val)).'</textarea>
								'.$pag_tr_stats['pag_tr_stats'].'
							</td>
						</tr>
					</table>
				</fieldset>
				</blockquote>
		
				<div class="tmip_submit_button_wrap">
					<input type="submit" class="tmip_submit_button1" name="info_update" value="'.tmip_lang_update_settings.'" />
				</div>
				<input type="hidden" id="check" name="nonce_tmip_check" value="'.$tmip_genert_nonce.'">
				';
			$output .='</form>';
		$output .='</div>';
	}
	
	
	###### DEMO FLAG TRACKER ########################################################### 
	if (1==2 and (!$show_div_target or $this_section=in_array('sec_demotracker',$show_div_target)) and !in_array('sec_demotracker',$hide_div_target)) {
		$output .='<br><br><br>';
		$output .='<div id="tmip_sett_wrap_3">';
			$output .='
				<h1 class="text-center">
					<i class="fa fa-file-alt fa-sm" style="color: #097E71; opacity: 0.5;"></i> Demo Flag Tracker
				</h1>	
				<blockquote>
					<section>
					<i class="fa fa-angle-double-right fa-sm" style="color: #7E0929; opacity: 0.5;"></i>
					The flag tracker is just an example of many <a href="'.tmip_free_signup_page_url.'" target="_blank">tracker styles</a> available. All visitor tracker styles capture exactly the same information about your website traffic. All data captured can be accessed via your own '.tmip_service_Dname.' account. This flag tracker gets reset periodically for data rotation.
					Premium subscribers can enable an invisible tracker option to track the computer IPs and IDs anonymously.
					<br><br>
					</section>
				</blockquote>';
		$output .='</div>';
	}
	echo $output;
}
function tmip_unify_new_lines($string) {
	if ($string) {
		$string=str_replace("\r\n", "\n", $string);
		$string=str_replace("\n\r", "\n", $string);
		$string=str_replace("\r", "\n", $string);
	}
	return $string;
}
function tmip_rate_review_section() {
		tmip_load_css();

		$r=array('dove','heart','child'); $thankYouIcon=$r[array_rand($r)];
		$r=array('hands-helping','leaf'); $helpNRateSec=$r[array_rand($r)];
		
		$output='
			<h2 class="tmip_sec_title">
			<i class="fa fa-'.$helpNRateSec.' fa-lg" style="color: #02970C; opacity: 0.8;"></i>
			Help and rate '.tmip_service_Nname.'</h2>
			<div class="ratehelp">
				<i class="fa fa-star fa-1x tmip_outline-icon1" style="color: #FFFF00; opacity: 1.0;"></i>
				<b>We need your help please!</b>
				<blockquote>
					As we all know, WordPress is alive thanks to the support from its users! However, the plugins development is what makes WordPress so different. As developers, we have spent <i>an enormouns amount of time</i> to develop '.tmip_service_Nname.' (since 2008). And up until recently, we have not asked for reviews, yet, it\'s an absolutely vital factor for us in order to maintain the plugin for WordPress.</blockquote>
				<blockquote>
					<b>Our review counts are <u>low</u>.</b>
					<i class="fa fa-smile-beam fa-lg" style="color: #02970C; opacity: 0.8;"></i>
				</blockquote>
				If you find '.tmip_service_Nname.' useful, <u>please</u> help and <b>vote 5 stars</b> so we could keep '.tmip_service_Nname.' running. <b>Thank you!</b> <i class="fa fa-'.$thankYouIcon.' fa-lg" style="color: #02970C; opacity: 0.8;"></i>
				<hr>
				<button type="button" class="tmip_submit_button1" margin: -5px 0px 0px 15px; min-width; 124px; width: 124px; outline-color: transparent;" onclick="window.open(\'https://wordpress.org/support/plugin/tracemyip-visitor-analytics-ip-tracking-control/reviews/?rate=5#new-post\')"> Click to Rate '.tmip_service_Nname.'</button>
			</div>
	';
	return $output;
}
function tmip_extract_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
function tmip_extract_url_vars($url){
	$output=array();
	if ($url) {
		$parts=parse_url($url);
		$query=$parts['query'];
		parse_str($query,$array);	 /// rawurlencode
		if (is_array($array)) {
			foreach ($array as $k=>$v) {
				$output[$k]=str_replace(' ','+',$v);
			}
		}
	}
	return $output;
}
function tmip_sanitize_rebuild_page_tracker_code($script) {
	$output=NULL;
	$script=stripslashes($script);
	$str=tmip_extract_string_between($script,'src=\'//',"' ");
	$str=tmip_unify_new_lines(trim($str));
	$str=tmip_remove_tabs_new_lines($str);
	$r=explode('/tracker/lkh.php?c',$str,2);$host=$r[0];
	$v=tmip_extract_url_vars('http://www.domain.com/var'.$str);
	if ($host and is_array($v) and array_filter($v)) {
		$output='<script language="javascript" type="text/javascript">
		var tP_lanPTl=""+document.title;tP_lanPTl=tP_lanPTl.substring(0,180);
		tP_lanPTl=escape(tP_lanPTl);var tP_lanPrl=""+location.toString();
		tP_lanPrl=tP_lanPrl.substring(0,500);tP_lanPrl=escape(tP_lanPrl);
		var tP_refRrl=""+document.referrer;tP_refRrl=tP_refRrl.substring(0,500);
		tP_refRrl=escape(tP_refRrl);
		document.writeln("<script src=\'//'.
			$host.'/tracker/lkh.php?c='.$v['c'].'&s='.$v['s'].'&l='.$v['l'].'&p='.
			$v['p'].'&d='.$v['d'].'&i='.$v['i'].'&x='.$v['x'].'&prefr='.
			$v['prefr'].'&lpTT='.$v['lpTT'].'&lpUL='.$v['lpUL'].'&pVer='.
			$v['pVer'].'\' type=\'text/javascript\'><\/script>");</script>';
	}
	$output=tmip_remove_tabs_new_lines($output,2);
	$output=tmip_unify_new_lines($output);
	return $output;
}


?>