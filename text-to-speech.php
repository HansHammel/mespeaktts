<?php
/*
 *  Plugin Name: Text to Speech
 *  Plugin URI: 
 *  Description: TTS-Widget for your website reading text
 *  Author: Oliver Bleckmann
 *  Version: 0.1
 *  Author URI: 
 *  License: GPLv3
 *
 */

define(TTS_TEXTDOMAIN, 'text-to-speech');

//----------------------------------------------------------------------------------------

function text_to_speech_init() {
  load_plugin_textdomain( TTS_TEXTDOMAIN, false, 'text-to-speech/languages' );
}

add_action('plugins_loaded', 'text_to_speech_init');

//----------------------------------------------------------------------------------------

function text_to_speech_activate() {
  add_option('tts_text', 'This widget transform text to speech');
  add_option('tts_amplitude', 90);
  add_option('tts_workdgap', 5);
  add_option('tts_pitch', 50);
  add_option('tts_speed', 150);
  $all_options = '?';
  $all_options .= 't='.get_option('tts_text');
  $all_options .= '&a='.get_option('tts_amplitude');
  $all_options .= '&w='.get_option('tts_workdgap');
  $all_options .= '&p='.get_option('tts_pitch');
  $all_options .= '&s='.get_option('tts_speed');
  add_option('tts_all_options', $all_options, '?');
}

//----------------------------------------------------------------------------------------

function text_to_speech_deactivate() {
  delete_option('tts_text');
  delete_option('tts_amplitude');
  delete_option('tts_workdgap');
  delete_option('tts_pitch');
  delete_option('tts_speed');
  delete_option('tts_all_options');
}

register_activation_hook( __FILE__, 'text_to_speech_activate' );
register_deactivation_hook( __FILE__, 'text_to_speech_deactivate' );

class TextToSpeechWidget extends WP_Widget {

	function TextToSpeechWidget() {
		// Instantiate the parent object
		parent::__construct( 
		  false, 
		  'Text to Speech',
		  array('description' => __('This widget transform text to speech', TTS_TEXTDOMAIN) . '.' )
		);
	}

  	// Widget output
	function widget( $args, $instance ) {
		extract( $args );

		$tts_widget_title = apply_filters( 'widget_title', $instance['tts_widget_title'] );
		
		$tts_widget_text = apply_filters( 'widget_title', $instance['tts_widget_text'] );
		$tts_widget_amplitude = apply_filters( 'widget_title', $instance['tts_widget_amplitude'] );
		$tts_widget_workdgap = apply_filters( 'widget_title', $instance['tts_widget_workdgap'] );
		$tts_widget_pitch = apply_filters( 'widget_title', $instance['tts_widget_pitch'] );
		$tts_widget_speed = apply_filters( 'widget_title', $instance['tts_widget_speed'] );

		$tts_widget_text_out = $tts_widget_text; 
		if ( $tts_widget_text == '' ) $tts_widget_text_out = get_option('tts_text');

		$tts_widget_amplitude_out = $tts_widget_amplitude; 
		if ( $tts_widget_amplitude == '' ) $tts_widget_amplitude_out = get_option('tts_amplitude');

		$tts_widget_workdgap_out = $tts_widget_workdgap; 
		if ( $tts_widget_workdgap == '' ) $tts_widget_workdgap_out = get_option('tts_workdgap');

		$tts_widget_pitch_out = $tts_widget_pitch; 
		if ( $tts_widget_pitch == '' ) $tts_widget_pitch_out = get_option('tts_pitch');

		$tts_widget_speed_out = $tts_widget_speed; 
		if ( $tts_widget_speed == '' ) $tts_widget_speed_out = get_option('tts_speed');

	// speak( 't', { amplitude: 'a', wordgap: 'w', pitch: 'p', speed: 's' });
	//meSpeak.speak(text.value, { amplitude: amplitude.value, wordgap: wordgap.value, pitch: pitch.value, speed: speed.value, variant: variant.options[variant.selectedIndex].value }); return false
  	//$widget_tts = "meSpeak.speak(jQuery('.entry-content').text(), { amplitude: 'a', wordgap: 'w', pitch: 'p', speed: 's' }); return false";
  	//$widget_tts = "meSpeak.speak(jQuery('p').text()); return false";
  	/*
  	$widget_tts = "speak( ";
  	$widget_tts .= "'$tts_widget_text_out', { ";
  	$widget_tts .= "amplitude: $tts_widget_amplitude_out, ";
  	$widget_tts .= "wordgap: $tts_widget_workdgap_out, ";
  	$widget_tts .= "pitch: $tts_widget_pitch_out, ";
  	$widget_tts .= "speed: $tts_widget_speed_out }); return false";
  	*/
		
		echo $before_widget;
		if ( ! empty( $tts_widget_title ) )
			echo $before_title . $tts_widget_title . $after_title;
			
		//echo $widget_tts;
		//echo'<div style="background-color:transparent;" onmouseover="'.$widget_tts.'" >'.$tts_widget_text_out.'<div id="audio"></div></div>';
		echo'<button title="Read" onclick="tts_read();return false" >Read</button><div id="audio"></div><button title="Stop" onclick="tts_stop(); return true;">Stop</button>';
		echo $after_widget;
	}

  	// Save widget options
	function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['tts_widget_title'] = strip_tags( $new_instance['tts_widget_title'] );
		
		$instance['tts_widget_text'] = strip_tags( $new_instance['tts_widget_text'] );
		$instance['tts_widget_amplitude'] = strip_tags( $new_instance['tts_widget_amplitude'] );
		$instance['tts_widget_workdgap'] = strip_tags( $new_instance['tts_widget_workdgap'] );
		$instance['tts_widget_pitch'] = strip_tags( $new_instance['tts_widget_pitch'] );
		$instance['tts_widget_speed'] = strip_tags( $new_instance['tts_widget_speed'] );
		
		return $instance;
	}

  	// Output admin widget options form
	function form( $instance )
	{
		// title - can be empty
	    if ( isset( $instance[ 'tts_widget_title' ] ) ) {
			$tts_widget_title = $instance[ 'tts_widget_title' ];
		}
		else {
			$tts_widget_title = __( 'Text to Speech', TTS_TEXTDOMAIN ).'!';
		    $instance[ 'tts_widget_title' ] = $tts_widget_title;
		}

		// text
	    if ( isset( $instance[ 'tts_widget_text' ] ) ) {
			$instance[ 'tts_widget_text' ] = trim( $instance[ 'tts_widget_text' ] );
			$tts_widget_text = $instance[ 'tts_widget_text' ];
			if ( $instance[ 'tts_widget_text' ] == '' ) $tts_widget_text = get_option('tts_text');
		} 
	    else {
	    	$tts_widget_text = get_option('tts_text');
		    $instance[ 'tts_widget_text' ] = $tts_widget_text;
		}

		// amplitude
	    if ( isset( $instance[ 'tts_widget_amplitude' ] ) ) {
			$instance[ 'tts_widget_amplitude' ] = trim( $instance[ 'tts_widget_amplitude' ] );
			$tts_widget_amplitude = $instance[ 'tts_widget_amplitude' ];
			if ( $instance[ 'tts_widget_amplitude' ] == '' ) $tts_widget_amplitude = get_option('tts_amplitude');
		} 
	    else {
	    	$tts_widget_amplitude = get_option('tts_amplitude');
		    $instance[ 'tts_widget_amplitude' ] = $tts_widget_amplitude;
		}

		// workdgap
	    if ( isset( $instance[ 'tts_widget_workdgap' ] ) ) {
			$instance[ 'tts_widget_workdgap' ] = trim( $instance[ 'tts_widget_workdgap' ] );
			$tts_widget_workdgap = $instance[ 'tts_widget_workdgap' ];
			if ( $instance[ 'tts_widget_workdgap' ] == '' ) $tts_widget_workdgap = get_option('tts_workdgap');
		} 
	    else {
	    	$tts_widget_workdgap = get_option('tts_workdgap');
		    $instance[ 'tts_widget_workdgap' ] = $tts_widget_workdgap;
		}

		// pitch
	    if ( isset( $instance[ 'tts_widget_pitch' ] ) ) {
			$instance[ 'tts_widget_pitch' ] = trim( $instance[ 'tts_widget_pitch' ] );
			$tts_widget_pitch = $instance[ 'tts_widget_pitch' ];
			if ( $instance[ 'tts_widget_pitch' ] == '' ) $tts_widget_pitch = get_option('tts_pitch');
		} 
	    else {
	    	$tts_widget_pitch = get_option('tts_pitch');
		    $instance[ 'tts_widget_pitch' ] = $tts_widget_pitch;
		}

		// speed
	    if ( isset( $instance[ 'tts_widget_speed' ] ) ) {
			$instance[ 'tts_widget_speed' ] = trim( $instance[ 'tts_widget_speed' ] );
			$tts_widget_speed = $instance[ 'tts_widget_speed' ];
			if ( $instance[ 'tts_widget_speed' ] == '' ) $tts_widget_speed = get_option('tts_speed');
		} 
	    else {
	    	$tts_widget_speed = get_option('tts_speed');
		    $instance[ 'tts_widget_speed' ] = $tts_widget_speed;
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_title' ); ?>"><?php echo __( 'Title', TTS_TEXTDOMAIN); ?>:</label> 
		  <input class="widefat" id="<?php echo $this->get_field_id( 'tts_widget_title' ); ?>" name="<?php echo $this->get_field_name( 'tts_widget_title' ); ?>" type="text" value="<?php echo esc_attr( $tts_widget_title ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_text' ); ?>"><?php echo __( 'Text', TTS_TEXTDOMAIN); ?>:</label> 
		<textarea class="widefat" rows="6" id="<?php echo $this->get_field_id('tts_widget_text'); ?>" name="<?php echo $this->get_field_name('tts_widget_text'); ?>"><?php echo $tts_widget_text; ?></textarea>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_amplitude' ); ?>"><?php echo __( 'Amplitude', TTS_TEXTDOMAIN); ?>:</label> 
		  <input style="width:50px;" class="widefat" id="<?php echo $this->get_field_id( 'tts_widget_amplitude' ); ?>" name="<?php echo $this->get_field_name( 'tts_widget_amplitude' ); ?>" type="text" value="<?php echo esc_attr( $tts_widget_amplitude ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_workdgap' ); ?>"><?php echo __( 'Workdgap', TTS_TEXTDOMAIN); ?>:</label> 
		  <input style="width:50px;" class="widefat" id="<?php echo $this->get_field_id( 'tts_widget_workdgap' ); ?>" name="<?php echo $this->get_field_name( 'tts_widget_workdgap' ); ?>" type="text" value="<?php echo esc_attr( $tts_widget_workdgap ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_pitch' ); ?>"><?php echo __( 'Pitch', TTS_TEXTDOMAIN); ?>:</label> 
		  <input style="width:50px;" class="widefat" id="<?php echo $this->get_field_id( 'tts_widget_pitch' ); ?>" name="<?php echo $this->get_field_name( 'tts_widget_pitch' ); ?>" type="text" value="<?php echo esc_attr( $tts_widget_pitch ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'tts_widget_speed' ); ?>"><?php echo __( 'Speed', TTS_TEXTDOMAIN); ?>:</label> 
		  <input style="width:50px;" class="widefat" id="<?php echo $this->get_field_id( 'tts_widget_speed' ); ?>" name="<?php echo $this->get_field_name( 'tts_widget_speed' ); ?>" type="text" value="<?php echo esc_attr( $tts_widget_speed ); ?>" />
		</p>

		<?php 
	}
}

function text_to_speech_register_widgets() {
	register_widget( 'TextToSpeechWidget' );
}

add_action( 'widgets_init', 'text_to_speech_register_widgets' );

//----------------------------------------------------------------------------------------------

function admin_tts_options() {
  ?><div class="wrap"><h2>Text to Speech</h2><?php

  if ($_REQUEST['submit']) {
     update_tts_options();
  }
  print_tts_form();

  ?></div><?php
}

//----------------------------------------------------------------------------------------------

function update_tts_options() {
  $all_options = '?';
  $eroare = '';
  
  $ok = false; 
  if ($_REQUEST['tts_text']) { update_option('tts_text', $_REQUEST['tts_text']);  $ok = true; $all_options .= 't='.$_REQUEST['tts_text'];} 
  else {$eroare.='2';}
  
  $ok = false; 
  if ($_REQUEST['tts_amplitude']) { update_option('tts_amplitude', $_REQUEST['tts_amplitude']);  $ok = true; $all_options .= 'a='.$_REQUEST['tts_amplitude'];} 
  else {$eroare.='3';}
  
  $ok = false; 
  if ($_REQUEST['tts_workdgap']) { update_option('tts_workdgap', $_REQUEST['tts_workdgap']);  $ok = true; $all_options .= 'w='.$_REQUEST['tts_workdgap'];} 
  else {$eroare.='4';}
  
  $ok = false; 
  if ($_REQUEST['tts_pitch']) { update_option('tts_pitch', $_REQUEST['tts_pitch']);  $ok = true; $all_options .= 'p='.$_REQUEST['tts_pitch'];} 
  else {$eroare.='5';}
  
  $ok = false; 
  if ($_REQUEST['tts_speed']) { update_option('tts_speed', $_REQUEST['tts_speed']);  $ok = true; $all_options .= 's='.$_REQUEST['tts_speed'];} 
  else {$eroare.='6';}
  
  $ok = false; if ($all_options) { update_option('tts_all_options', $all_options);  $ok = true; } else {$eroare.='0';}
 
  if ($ok) {
    ?><div id="message" class="updated fadee">
       <p><?php echo __('Message', TTS_TEXTDOMAIN); ?>: <strong> <?php echo __('Saved options', TTS_TEXTDOMAIN); ?>!</strong></p>
      </div><?php
  } else {
       ?><div id="message" class="error fade">
         <p><?php echo __('Message', TTS_TEXTDOMAIN); ?>: <strong> <?php echo __('Error saving options', TTS_TEXTDOMAIN); ?>! (<?php echo $eroare;?>) </strong></p>
         </div><?php
  }
}

//----------------------------------------------------------------------------------------------

function print_tts_form() {
  $default_tts_text = get_option('tts_text');
  $default_tts_amplitude = get_option('tts_amplitude');
  $default_tts_workdgap = get_option('tts_workdgap');
  $default_tts_pitch = get_option('tts_pitch');
  $default_tts_speed = get_option('tts_speed');
  $default_tts_options = get_option('tts_all_options');
  ?>
<div class="tabber">

     <div class="tabbertab">
	  <h2><?php echo __('Settings', TTS_TEXTDOMAIN); ?></h2>
	  
	  <h1><?php echo __('Settings', TTS_TEXTDOMAIN); ?></h1>

  <div class="postbox" style="float:left; width:auto; height:auto; padding:10px;margin:10px;" >
  <form method="POST">
  <table>
  <tbody>

  <tr>
    <td><label for="tts_text"><?php echo __('Text', TTS_TEXTDOMAIN); ?>:</label></td>
    <td><textarea cols="60" rows="10" name="tts_text"><?php echo$default_tts_text;?></textarea></td>
    <td></td>
  </tr>

  <tr>
    <td><label for="tts_amplitude"><?php echo __('Amplitude', TTS_TEXTDOMAIN); ?>:</label></td>
    <td><input type="text" name="tts_amplitude" value="<?php echo$default_tts_amplitude;?>" /></td>
    <td></td>
  </tr>

  <tr>
    <td><label for="tts_workdgap"><?php echo __('Workdgap', TTS_TEXTDOMAIN); ?>:</label></td>
    <td><input type="text" name="tts_workdgap" value="<?php echo$default_tts_workdgap;?>" /></td>
    <td></td>
  </tr>

  <tr>
    <td><label for="tts_pitch"><?php echo __('Pitch', TTS_TEXTDOMAIN); ?>:</label></td>
    <td><input type="text" name="tts_pitch" value="<?php echo$default_tts_pitch;?>" /></td>
    <td></td>
  </tr>

  <tr>
    <td><label for="tts_speed"><?php echo __('Speed', TTS_TEXTDOMAIN); ?>:</label></td>
    <td><input type="text" name="tts_speed" value="<?php echo$default_tts_speed;?>" /></td>
    <td></td>
  </tr>
  
  <tr>
    <td colspan="3"><hr></td>
  </tr>

  <tr>
    <td colspan="3"><input type="submit" name="submit" value="<?php echo __('Save', TTS_TEXTDOMAIN); ?>" /></td>
  </tr>
  </tbody>
  </table>
  </form>
  </div>
     </div>
	 
     <div class="tabbertab">
	  <h2><?php echo __('Documentation', TTS_TEXTDOMAIN); ?></h2>
	  
	  <h1><?php echo __('Documentation', TTS_TEXTDOMAIN); ?></h1>

  <div class="postbox" style="float:left; width:auto; height:auto; padding:10px;margin:10px;" >
  
	  <p><?php echo __('To use this plugin just use \'Text to Speech\' Widget', TTS_TEXTDOMAIN) . '.'; ?></p>
	  
<p>
<table border="1" cellspacing="0" cellpadding="5">
<tbody>
<tr>
	<th></th>
	<th><?php echo __('Explanation', TTS_TEXTDOMAIN); ?></th>
	<th><?php echo __('Description', TTS_TEXTDOMAIN); ?></th>
</tr>

<tr style="background-color:aliceblue;"><td><strong>1.</strong></td><td><?php echo __('Amplitude', TTS_TEXTDOMAIN); ?></td><td><?php echo __('volume', TTS_TEXTDOMAIN); ?></td></tr>

<tr><td><strong>2.</strong></td><td><?php echo __('Workdgap', TTS_TEXTDOMAIN); ?></strong></td><td><?php echo __('delay between words', TTS_TEXTDOMAIN); ?></td></tr>

<tr style="background-color:aliceblue;"><td><strong>3.</strong></td><td><?php echo __('Pitch', TTS_TEXTDOMAIN); ?></strong></td><td><?php echo __('the pitch of voice', TTS_TEXTDOMAIN); ?></td></tr>

<tr><td><strong>4.</strong></td><td><?php echo __('Speed', TTS_TEXTDOMAIN); ?></strong></td><td><?php echo __('the speed of voice', TTS_TEXTDOMAIN); ?></td></tr>

</tbody>
</table>
</p>

	  <p><?php echo __('For more examples visit', TTS_TEXTDOMAIN) . ': <a href="http://wordpress.org/extend/plugins/text-to-speech/" target="_blank">http://wordpress.org/extend/plugins/text-to-speech/</a>'; ?></p>
 
    </div>
	 
     </div>
     </div>
  <?php
}

//----------------------------------------------------------------------------------------------

function text_to_speech_style_for_front_end() {
	/*
	echo'
	<script src="'.plugins_url( 'mespeak/mespeak.js', __FILE__ ).'"></script>
	';
	*/

?>

  <script type="text/javascript">

   // meSpeak.loadConfig("<?php echo plugins_url( 'mespeak/mespeak_config.json', __FILE__ ); ?>");
   // meSpeak.loadVoice("<?php echo plugins_url( 'voices/en/en.json', __FILE__ ); ?>");
function tts_read(){if(typeof meSpeak === 'undefined') {
	jQuery.getScript( "<?php echo plugins_url( 'mespeak/mespeak.js', __FILE__ ); ?>", function( data, textStatus, jqxhr ) {
    meSpeak.loadConfig("<?php echo plugins_url( 'mespeak/mespeak_config.json', __FILE__ ); ?>");
    meSpeak.loadVoice("<?php echo plugins_url( 'voices/en/en.json', __FILE__ ); ?>");
  //console.log( data ); // Data returned

  //console.log( textStatus ); // Success

  //console.log( jqxhr.status ); // 200

  //console.log( "Load was performed." );
meSpeak.speak(jQuery('p').text());
});


	
	} else
	{
	meSpeak.speak(jQuery('p').text());
	}
	}
function tts_stop(){if (typeof meSpeak !== 'undefined') meSpeak.stop();}
  </script>
<?php

}

add_action('wp_footer', 'text_to_speech_style_for_front_end');

//----------------------------------------------------------------------------------------------

function text_to_speech_menu() {
  add_options_page(
    'Text to Speech - ' . __('Settings', TTS_TEXTDOMAIN), // page title
    'Text to Speech', // submenu title
    'manage_options', // access/capability
    __FILE__, // file
    'admin_tts_options' // function
  );
}

add_action('admin_menu', 'text_to_speech_menu');

?>