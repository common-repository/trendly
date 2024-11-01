<?php
/*
Plugin Name: trendly widget
Plugin URI: http://trend.ly
Description: discover the world's current trends, right now.
Version: 0.0.3
Author: trend.ly
Author URI: http://trend.ly
*/

function widget_Trendly($args) {
	extract($args);

	$options = get_option("widget_Trendly");
	$polls = $options['polls'];
	$random = $options['random'];
	
	if (!is_array( $options ))
		$options = array(
			'polls' => 'polls',
			'random' => 'false',
		);
		
	if ($width == 'auto') {
		$width = "'auto'";
	} 

	$title = "&nbsp;";
	// Our Widget Title
	echo $before_widget;
	echo $before_title;
	echo $title;
	echo $after_title;
	if($polls) {
		//Our Widget Content
		shuffle_assoc($polls);
		$pid = each($polls);
		echo "<script type=\"text/javascript\">var trendly_poll = '".$pid['key']."';var trendly_bgcolor = 'transparent';var trendly_textcolor = '666666';var trendly_likecolor = '000000';var trendly_unlikecolor = 'ff0d00';var trendly_barcolor='000000';var trendly_font='Verdana';var trendly_wp=1;</script><script type=\"text/javascript\" src=\"http://trend.ly/embed.js\"></script>";
	}
	echo $after_widget;
}

function shuffle_assoc(&$array) {
    $keys = array_keys($array);
    shuffle($keys);
    foreach($keys as $key)
        $new[$key] = $array[$key];
    $array = $new;
    return true;
}

// Settings form
function Trendly_control()
{
	
	print_r($args);
	global $trendlyitem;
	// Get options
	$options = get_option("widget_trendly");
	// options exist? if not set defaults
	if ( !is_array( $options ) )
		$options = array(
			//'item1' => 'item1',
			'polls' => 'polls',
			'random' => 'false',
		);

 
	 // form posted?
	if ($_POST['Trendly-Submit']) {
		$options['polls'] = $_POST['Trendly-polls'];
		$options['random'] = strip_tags(stripslashes($_POST['Trendly-Random']));
		// update our options
		update_option("widget_trendly", $options);
	}
	
	$polls = $options['polls'];
	$random = htmlspecialchars($options['random'], ENT_QUOTES);
	if($trendlyitem) { $trendlyitem++; } else { $trendlyitem = 1; }
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '/wp-content/plugins/trendly/js/jquery-1.4.2.min.js');
	wp_enqueue_script( 'jquery' );

	#DEREGISTER DEFAULT JQUERY INCLUDES
	wp_deregister_script('jquery-ui-core');
	wp_deregister_script('jquery-ui-tabs');
	wp_deregister_script('jquery-ui-sortable');
	wp_deregister_script('jquery-ui-draggable');
	wp_deregister_script('jquery-ui-droppable');
	wp_deregister_script('jquery-ui-selectable');
	wp_deregister_script('jquery-ui-resizable');
	wp_deregister_script('jquery-ui-dialog');

	#LOAD THE GOOGLE API JQUERY INCLUDES
	wp_register_script( 'jquery-ui-core', '/wp-content/plugins/trendly/js/jquery-ui-1.8.custom.min.js');
	
	#REGISTER CUSTOM JQUERY INCLUDES
	wp_enqueue_script('jquery_ui');

?>
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/trendly/css/ui-lightness/jquery-ui-1.8.custom.css" />
<style type="text/css">
.trendly-items { font-size:12px;min-height:20px;float: left;width:225px;cursor:pointer;padding:5px 0 0 0px; }
.trendly-items:hover { background-color:#EFEFEF;}
.trendly-close-link { float:right;text-decoration:none;padding-right:5px; }
.trendlyautoitem { width:193px;padding-left:0px;margin-left:0px; }
</style>
<div id="formWrap">
		<div style="height:24px;font-size:12px;">search trendly for available polls:</div><input type="text" onKeyPress="if(event.keyCode == 13) { return false; }" id="Trendly-item<?php echo $trendlyitem; ?>" name="Trendly-item1" class="trendlyautoitem" value="<?php echo $item1 ?>" /><br/><br/>
			<div id="log<?php echo $trendlyitem; ?>" class="tlog">
				<?php if($polls && $polls != 'polls') { 
				foreach($polls as $key => $value) { ?>
				<div class="trendly-items" id="Trendly-polls-span<?php echo $key; ?>"><?php echo $value; ?> <a href="javascript:void(0);" class="trendly-close-link" onclick="jQuery('#hiddendata<?php echo $trendlyitem; ?>').children('#Trendly-polls<?php echo $key; ?>').remove();jQuery('#log<?php echo $trendlyitem;?>').children('#Trendly-polls-span<?php echo $key; ?>').remove();">X</a></div>
				<?php } } ?>
			</div>
			<div id="hiddendata<?php echo $trendlyitem; ?>" class="thiddendata">
				<?php if($polls && $polls != 'polls') { 
				foreach($polls as $key => $value) { ?>
					<input type="hidden" id="Trendly-polls<?php echo $key; ?>" name="Trendly-polls[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
				<?php } } ?>
			</div>
		<div style="padding-top:5px;margin-top:6px; border-top: 1px solid rgb(204, 204, 204); float: left;height:50px;">polls that you selected above will be visible in a random order.</div>
		<!--
		<label for="Trendly-Random">Random: </label><br/>
			<input type="radio" id="Trendly-Random" name="Trendly-Random" <?php if($random == 'true') echo 'checked="checked"'; ?> value="true" />yes
			&nbsp;<input type="radio" id="Trendly-Random" <?php if($random == 'false') echo 'checked="checked"'; ?> name="Trendly-Random" value="false" />no<br/>-->
		<input type="hidden" id="Trendly-Submit" name="Trendly-Submit" value="1" />
</div>
<script type="text/javascript">
jQuery(function() {
	function log( message ) {
		jQuery( "<div/>" ).text( message ).prependTo( ".trendlylog:last" );
		jQuery( ".trendlylog:last" ).attr( "scrollTop", 0 );
	}

	jQuery( ".trendlyautoitem:last" ).autocomplete({
		minLength: 2,
		source: function( request, response ) {
			jQuery.ajax({
				url: "http://trend.ly/ajax.php?do=WordpressPollIds&jsoncallback=?",
				dataType: "jsonp",
				data: {
					q: request.term
				},
				success: function( data ) {
					response( jQuery.map( data, function( item ) {
						return {
							label: item.name,
							value: item.name,
							tid  : item.id
						}
					}));
				}
			});
		},
		select: function( event, ui ) {
				
			jQuery(".trendlyautoitem:last").val('');
			jQuery(".thiddendata:last").append('<input type=\"hidden\" name=\"Trendly-polls['+ui.item.tid+']\" value=\"'+ui.item.label+'\" id=\"Trendly-polls'+ui.item.tid+'\" />');
			jQuery('.tlog:last').append('<div class=\"trendly-items\" id=\"Trendly-polls-span'+ui.item.tid+'\">'+ui.item.label+' <a href="javascript:void(0);" onclick="jQuery(\'#Trendly-polls'+ui.item.tid+'\').remove();jQuery(\'#Trendly-polls-span'+ui.item.tid+'\').remove();" class="trendly-close-link">X</a></div>');
		},
		open: function() {
			jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			jQuery(".trendlyautoitem:last").val('');
		}
	});
});
</script>
<?php
}

function trendly_init()
{
	register_sidebar_widget(__('trendly'), 'widget_trendly');
	register_widget_control(   'trendly', 'trendly_control', 250, 200 );
}
add_action("plugins_loaded", "trendly_init");
?>