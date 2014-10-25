<?php
function ninja_forms_register_field_payment_options() {

    $args = array(
        'name' => __( 'Payment Options', 'ninja-forms' ),
        'sidebar' => 'template_fields',
        'edit_function' => 'ninja_forms_field_payment_options_edit',
        'display_function' => 'ninja_forms_field_payment_options_display',
        'save_function' => '',
        'group' => 'standard_fields',
        'edit_label' => true,
        'edit_label_pos' => true,
        'edit_req' => false,
        'edit_custom_class' => true,
        'edit_help' => true,
        'edit_desc' => true,
        'edit_meta' => false,
        'edit_conditional' => true,
        'conditional' => array(
            'value' => array(
                'type' => 'text',
            ),
        ),
        'pre_process' => 'ninja_forms_field_text_pre_process',
        'edit_sub_value' => 'nf_field_text_edit_sub_value',
        'sub_table_value' => 'nf_field_text_sub_table_value'
    );



    ninja_forms_register_field( '_payment_options', $args );
    add_filter( 'ninja_forms_field_wrap_class', 'ninja_forms_field_filter_list_wrap_class', 10, 2 );
    add_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_list_type', 10, 2 );
}

add_action( 'init', 'ninja_forms_register_field_payment_options' );
/**
 * Display form edit field settings of _payment_options field type
 * 
 * @param type $field_id
 * @param type $data
 */
function ninja_forms_field_payment_options_edit( $field_id, $data ) {

    // If allowable_payments is already set, pull in its value
    if ( isset( $data[ 'allowable_payments' ] ) ) {
        $allowable_payments = $data[ 'allowable_payments' ];
    } else {
        $allowable_payments = array();
    }

    // If default_value is already set, pull in its value
    if ( isset( $data[ 'default_value' ] ) ) {
        $default_value = $data[ 'default_value' ];
    } else {
        $default_value = '';
    }
    if ( $default_value == 'none' ) {
        $default_value = '';
    }

   
    // set standard payment options
    $standard_payment_options_array = ninja_forms_create_standard_payment_options();

    // create a filter of a blank array through which extensions can add their payment options
    $extensions_array = apply_filters( 'ninja_forms_extend_payment_options', array() );

    $payment_options_array = array_merge( $standard_payment_options_array, $extensions_array );

    // create a lookup array of value/labels for easy output of the field html
    $lookup_array = array();

    foreach ( $payment_options_array as $temp_array ) {
        $lookup_array[ $temp_array[ 'value' ] ] = $temp_array[ 'label' ];
    }
    
    // Output the field edit html
    // Checkboxes enable which payment options are availble for the site visitor
    // Dropdown selector picks an optional default payment option
    // A hidden text field passes the lookup array of value/labels to build the form output
    ?>
    <div class="description description-wide">
        <span class="field-option">
            <label for="">
    <?php _e( 'Allow Payments By:', 'ninja-forms' ); ?>
            </label><br />

                <?php
                foreach ( $payment_options_array as $option ) {
                    ?>                   

                <input type ="checkbox" name ="ninja_forms_field_<?php echo $field_id; ?>[allowable_payments][]" <?php if ( in_array( $option[ 'value' ], $allowable_payments ) ) {
            echo 'checked';
        } ?> value="<?php echo $option[ 'value' ]; ?>" ><?php echo $option[ 'label' ] . ' - ' .$option[ 'description' ]  ; ?><br /> 
    <?php }
    ?>               

        </span>
    </div>  


    <div class="description description-wide">
        <span class="field-option">
            <label for="">
    <?php _e( 'Default Payment', 'ninja-forms' ); ?>
            </label><br />
            <select id="ninja_forms_field_<?php echo $field_id; ?>_default_value" name="ninja_forms_field_<?php echo $field_id; ?>[default_value]" class="widefat ninja-forms-_text-default-value">
                <option value="" <?php
            if ( $default_value == '' ) {
                echo 'selected';
            }
    ?>><?php _e( 'None', 'ninja-forms' ); ?></option>                 

                        <?php
                        foreach ( $payment_options_array as $option ) {
                            ?>                   

                    <option value="<?php echo $option[ 'value' ] ?>" <?php
            if ( $default_value == $option[ 'value' ] ) {
                echo 'selected';
            }
                            ?>><?php echo $option[ 'label' ]; ?></option>

                        <?php }
                        ?>               
            </select>
        </span>
    </div>

    <input type = "hidden" class="widefat code" name="ninja_forms_field_<?php echo $field_id; ?>[payment_options_lookup_array]" id="ninja_forms_field_<?php echo $field_id; ?>_payment_options_lookup_array" value="<?php echo esc_html( serialize( $lookup_array ) ); ?>" />

    <?php
}


/**
 * 
 * @global type $wpdb
 * @global type $ninja_forms_fields
 * @param type $field_id
 * @param type $data
 */
function ninja_forms_field_payment_options_display( $field_id, $data ) {
    global $wpdb, $ninja_forms_fields;

    if ( isset( $data[ 'show_field' ] ) ) {
        $show_field = $data[ 'show_field' ];
    } else {
        $show_field = true;
    }

    $field_class = ninja_forms_get_field_class( $field_id );


    $field_row = ninja_forms_get_field_by_id( $field_id );


    $type = $field_row[ 'type' ];
    $type_name = $ninja_forms_fields[ $type ][ 'name' ];

    if ( isset( $data[ 'list_type' ] ) ) {
        $list_type = $data[ 'list_type' ];
    } else {
        $list_type = '';
    }

    if ( isset( $data[ 'list_show_value' ] ) ) {
        $list_show_value = $data[ 'list_show_value' ];
    } else {
        $list_show_value = 0;
    }

    if ( isset( $data[ 'allowable_payments' ] ) AND $data[ 'allowable_payments' ] != '' ) {
        $options = $data[ 'allowable_payments' ];
    } else {
        $options = array();
    }

    if ( isset( $data[ 'payment_options_lookup_array' ] ) AND $data[ 'payment_options_lookup_array' ] != '' ) {
        $option_lookup = unserialize( $data[ 'payment_options_lookup_array' ] );
    } else {
        $option_lookup = array();
    }
    
    if ( isset( $data[ 'label_pos' ] ) ) {
        $label_pos = $data[ 'label_pos' ];
    } else {
        $label_pos = 'left';
    }

    if ( isset( $data[ 'label' ] ) ) {
        $label = $data[ 'label' ];
    } else {
        $label = $type_name;
    }

    if ( isset( $data[ 'default_value' ] ) AND ! empty( $data[ 'default_value' ] ) ) {
        $selected_value = $data[ 'default_value' ];
    } else {
        $selected_value = '';
    }

    $list_options_span_class = apply_filters( 'ninja_forms_display_list_options_span_class', '', $field_id );

    // output field html
    $x = 0;
    if ( $label_pos == 'left' OR $label_pos == 'above' ) {
        ?><?php
    }
    ?><input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" value=""><span id="ninja_forms_field_<?php echo $field_id; ?>_options_span" class="<?php echo $list_options_span_class; ?>" rel="<?php echo $field_id; ?>"><ul><?php
    foreach ( $options as $option ) {

        $value = $option;
        $value = htmlspecialchars( $value, ENT_QUOTES );

        if ( isset( $option_lookup[ $value ] ) ) {
            $label = $option_lookup[ $value ];
        } else {
            $label = '';
        }

        if ( isset( $option[ 'display_style' ] ) ) {
            $display_style = $option[ 'display_style' ];
        } else {
            $display_style = '';
        }



        $label = stripslashes( $label );

        if ( $selected_value == $value OR ( is_array( $selected_value ) AND in_array( $value, $selected_value ) ) ) {
            $selected = 'checked';
        } else if ( $selected_value == '' AND isset( $option[ 'selected' ] ) AND $option[ 'selected' ] == 1 ) {
            $selected = 'checked';
        } else {
            $selected = '';
        }
        ?><li><label id="ninja_forms_field_<?php echo $field_id; ?>_<?php echo $x; ?>_label" class="ninja-forms-field-<?php echo $field_id; ?>-options" style="<?php echo $display_style; ?>" for="ninja_forms_field_<?php echo $field_id; ?>_<?php echo $x; ?>"><input id="ninja_forms_field_<?php echo $field_id; ?>_<?php echo $x; ?>" name="ninja_forms_field_<?php echo $field_id; ?>" type="radio" class="<?php echo $field_class; ?>" value="<?php echo $value; ?>" <?php echo $selected; ?> rel="<?php echo $field_id; ?>" /><?php echo $label; ?></label></li><?php
        $x++;
    }
    ?></ul></span><li style="display:none;" id="ninja_forms_field_<?php echo $field_id; ?>_template"><label><input id="ninja_forms_field_<?php echo $field_id; ?>_" name="" type="radio" class="<?php echo $field_class; ?>" value="" rel="<?php echo $field_id; ?>" /></label></li>
            <?php
        
}

 /**
  * 
  * Create an array of standard payment options than can be added
  * Each payment option is an associative array with three fields
  * 'label' is displayed on the form
  * 'value' must be unique; is the form value each extension will look for to trigger processing
  * 'description' is used in the field editing to help the form designer
  * 
  * @return array
  */               
function ninja_forms_create_standard_payment_options(){
    
    $cod =  array(
            'label' => 'Cash On Delivery',
            'value' => '_COD',
            'description'=>'Payment is made at time of delivery'
        );
    
    $check = array(
            'label' => 'Check',
            'value' => '_check',
            'description' => 'Customer will send payment by check'
        );
    
    $standard_payment_options_array[] = $cod;
    
    $standard_payment_options_array[] = $check;

    return apply_filters( 'ninja_forms_modify_standard_payment_options', $standard_payment_options_array);
}                
                
                
/**
 * Sample for adding a new payment option
 * Add this code to your extension, replace values to your own
 * @param array $extension_array
 * @return string
 */                
function sample_add_elavon_payment_option( $extension_array ) {

    $extension_array[] = array(
        'label' => 'Credit Card',
		// Label appears on the form and on the field edit
        'value' => '_elavon',
		// Value is what the field is set to if your option is chosen; you create a unique value for your extension
        'description' => 'Process credit cards using your Elavon account'
		// Displayed during field editing to assist the form designer
    );

    return $extension_array;
}

add_filter( 'ninja_forms_extend_payment_options', 'sample_add_elavon_payment_option' );
