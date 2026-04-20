<?php 
/**
 *  HTML für Checkbox-Gruppe
 *  @groupName  = "key" String
 *  @checkboxes = "options" Array
 *  @checked    = String
 */
function checkboxGroup($groupName, $checkboxes, $checked=""){
    
    $checkboxGroup = "<div class='checkbox-group'>";
        
        foreach ($checkboxes as $name => $label) {
            $check         = ($checked == $name) ? "checked" : "";
            $checkboxGroup .= "<div>
                <label class='slide-checkbox'>
                    <input type='checkbox' name='{$groupName}[]' value='$name' $check>
                    <span class='slider'></span>
                    <span class='checkbox-label'>$label</span>
                </label>
            </div>";
        }

    $checkboxGroup .= "</div>";
    return $checkboxGroup;
}

/**
 *  HTML für Range-Slider
 */
function rangeSlider($name, $value = 2, $label = "", $min = 1, $max = 5, $step = 1) {
    $rangeSlider = "<div class='range-slider'>";
    
    if (!empty($label)) {
        $rangeSlider .= "<label class='range-label' for='$name'>$label</label>";
    }
    
    $rangeSlider .= "
        <div class='range-slider-container'>
            <input type='range' 
                id='$name' 
                name='$name' 
                min='$min' 
                max='$max' 
                value='$value' 
                step='$step' 
                class='range-input'>
            <div class='slider-value'>
                <span id='range-slider-value'>$value Tage</span>
            </div>
        </div>";
    
    $rangeSlider .= "</div>";
    
    return $rangeSlider;
}

/**
 *  HTML für Input-Numner
 */
function numberInput($name, $value = 2, $min = 1, $max = 50, $label = "", $after= "", $step = 1, $placeholder = "") {
    $numberInput = "<div class='number-input'>";

    if (!empty($label)) {
        $numberInput .= "<label class='number-label' for='$name'>$label</label>";
    }

    $numberInput .= "
        <div class='number-input-container'>
            <input type='number' 
                id='$name' 
                name='$name'
                placeholder='$placeholder' 
                min='$min' 
                max='$max' 
                value='$value' 
                step='$step'>
            <div class='input-number-arrows'>
                <i class='mmsi-icon pfeil pfeil-oben input-number-arrow-up'></i>
                <i class='mmsi-icon pfeil pfeil-unten input-number-arrow-down'></i>
            </div>
        </div>";
        

    if (!empty($after)) {
        $numberInput .= "<span class='input-after'>$after</span>";
    }

    $numberInput .= "</div>";

    echo $numberInput;
}

/**
 *  MS-Titel als Block
*/
function register_main_blog_title_block() {
    register_block_type('custom/main-blog-title', [
        'render_callback' => function() {
            switch_to_blog(1);
            $title = get_bloginfo('name');
            restore_current_blog();
            return esc_html($title);
        },
    ]);
}
add_action('init', 'register_main_blog_title_block');

function contactIsActive($person_email){
    // Wenn Benutzer mit dieser Email existiert, Status auf "Aktiv" setzen
    if (!empty($person_email) && email_exists($person_email)) {
        $person_status = 'Aktiv';
    }else{
        $person_status = 'Ausstehend';
    }
    return $person_status;
}