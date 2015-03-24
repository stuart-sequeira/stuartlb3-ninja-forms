<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_register_field_datetimebox(){
	$args = array(
		'name' => __( 'Date Time Field' , 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_datetime_edit',
		'display_function' => 'ninja_forms_field_datetime_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
			'action' => array(
				'change_value' => array(
					'name'        => __( 'Change Value', 'ninja-forms' ),
					'js_function' => 'change_value',
					'output'      => 'text',
				),
			),
		),
		'display_label' => false,
		'sub_edit_function' => 'ninja_forms_field_datetime_edit_sub',
	);

	ninja_forms_register_field('_datetime', $args);
}

add_action('init', 'ninja_forms_register_field_datetimebox');

function ninja_forms_field_datetime_edit($field_id, $data){
	$custom = '';
	// Date Time Format
	if(isset($data['datetime_format'])){
		$datetime_format = $data['datetime_format'];
	}else{
		$datetime_format = 'Y-m-d';
	}

	?>
	<p class="description description-thin">
		<label for="">
			<?php _e( 'Date Time Format' , 'ninja-forms'); ?><br />
			<select name="ninja_forms_field_<?php echo $field_id;?>[datetime_format]" id="ninja_forms_field_<?php echo $field_id;?>_datetime_format" class="widefat ninja-forms-_text-default-value" rel="<?php echo $field_id;?>">
				<option value="Y-m-d" <?php if( $datetime_format == 'Y-m-d'){ echo 'selected'; $custom = 'no';}?>><?php _e('Date 1999-12-31', 'ninja-forms'); ?></option>
				<option value="Y-m-d H:i:s" <?php if($datetime_format == 'Y-m-d H:i:s'){ echo 'selected'; $custom = 'no';}?>><?php _e('Date Time 1999-12-31 23:59:59', 'ninja-forms'); ?></option>
				<option value="m/d/Y" <?php if($datetime_format == 'm/d/Y'){ echo 'selected'; $custom = 'no';}?>><?php _e('Date 12/31/1999', 'ninja-forms'); ?></option>
				<option value="H:i" <?php if($datetime_format == 'H:i'){ echo 'selected'; $custom = 'no';}?>><?php _e('Time 23:59', 'ninja-forms'); ?></option>	
			</select>
		</label>
	</p>
	

	
	<?php
}

function ninja_forms_field_datetime_display( $field_id, $data, $form_id = '' ){
	global $current_user;

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	
	if(isset($data['datetime_format'])){
		$datetime_format = $data['datetime_format'];
	}else{
		$datetime_format = 'Y-m-d';
	}

	$datetime = new DateTime(); // get a datetimestamp
   
    $formatted_datetime = $datetime->format( $datetime_format ); // format the date time
	
	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" class="<?php echo $field_class;?>" value="<?php echo $formatted_datetime;?>" rel="<?php echo $field_id;?>" />
	<?php

}

function ninja_forms_field_datetime_edit_sub( $field_id, $data ) {
	
	if(isset($data['datetime_format'])){
		$datetime_format = $data['datetime_format'];
	}else{
		$datetime_format = 'Y-m-d';
	}

	$datetime = new DateTime(); // get a datetimestamp
   
    $formatted_datetime = $datetime->format( $datetime_format ); // format the date time
	
	
	if(isset($data['label'])){
		$label = $data['label'];
	}else{
		$label = '';
	}
	?>
	<label>
		<?php echo $label; ?>
	</label>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $formatted_datetime;?>" rel="<?php echo $field_id;?>" />
	<?php
}

