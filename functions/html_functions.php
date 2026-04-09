<?php 

/**
 * Box-Überschriften
 *  @deprecated 
*/
function boxHeading($heading, $typ = '3'){
    echo "<div class='spalte memy-add-heading'><i class='bi bi-people-fill'></i><h$typ>$heading</h$typ></div>";
}

/**
 * Label-Input-Felder
 * return echo
*/
function addInput($label, $value, $id='1', $placeholder='', $type='text'){
    $htmlLabel = empty($label) ? '' : "<label>$label</label>";
    $value     = esc_attr($value); //Security
    echo "<div class='input-wrapper $id'>
        $htmlLabel
        <input id='$id' type='$type' value='$value' placeholder='$placeholder'/>
        </div>";
}

/**
 * Checkbox-Feld
 * @width ob das Label die volle Breite ausnutzt
 * return echo
*/
function addCheckbox($label, $value, $id='1', $width=''){    
    $checked = ($value == 1) ? 'checked' : '';
    echo "<label class='is-checkbox $width'><span class='checkbox-label'>$label</span><input type='checkbox' id='$id' value='1' $checked/><span class='slider'></span></label>";
}

/**
 * Checkbox-Gruppe
 * @label Label der Gruppe
 * @options Array mit Optionen (key => value)
 * @selected Ausgewählte Werte (Array oder String)
 * @id ID-Präfix für die Checkboxen
 * return echo
*/
function addCheckboxGroup($label, $options, $selected='', $id='1'){
    echo "<div class='$id'><label>$label</label><div class='checkbox-group'>";
    if(!is_array($selected)){
        $selected = $selected ? [$selected] : [];
    }
   
    $name = esc_attr($id) . '[]';
    foreach ($options as $value => $optionLabel) {
        $checked = in_array($value, $selected) ? 'checked' : '';
        echo "<label class='is-checkbox $checked'><span class='checkbox-label'>" . esc_html($optionLabel) . "</span><input type='checkbox' name='$name' class='" . esc_attr($id) . "' value='" . esc_attr($value) . "' $checked/><span class='slider'></span></label>";
    }
    echo "</div></div>";
}

/**
 * Radio-Gruppe
 * @label Label der Gruppe
 * @options Array mit Optionen (key => value)
 * @selected Ausgewählter Wert (String)
 * @id ID-Präfix für die Radiobuttons
 * return echo
*/
function addRadioGroup($label, $options, $selected='', $id='1'){
    echo "<div class='$id radio-boxes'><label>$label</label><div class='radio-group'>";
   
    foreach ($options as $value => $optionLabel) {
        $checked = ($selected === $value) ? 'checked' : '';
        echo "<label class='is-radio $checked' for='$value'>" . esc_html($optionLabel) . "
        <input type='radio' id='$value' name='$id' value='" . esc_attr($value) . "' $checked/><span class='slider'></span></label>";
    }
    echo "</div></div>";
}

/**
 * Select-Feld
 * @label Label der Gruppe
 * @options Array mit Optionen (key => value)
 * @selected Ausgewählter Wert (String)
 * @id ID-Präfix für die Radiobuttons
 * return echo
*/
function addSelect($label, $options, $selected='', $id='1', $is_select=true){

    $is_select = ($is_select)?'':'nobox';
    echo "<div class='selectbox $id $is_select'><label>$label</label><select id='$id'>";
    foreach ($options as $value => $optionLabel) {
        $sel = ($selected === $value) ? 'selected' : '';
        echo "<option value='$value' $sel>$optionLabel</option>";
    }
    echo "</select></div>";
}

/**
 * Textarea-Feld
 * @label Label des Feldes
 * @value Wert des Feldes
 * @id ID des Feldes
 * @rows Anzahl der Zeilen
 * return echo
*/
function addTextarea($label, $value='', $id='1', $placeholder='',$rows='5'){
    $value = esc_textarea($value); //Security
    echo "<div class='$id'><label>$label</label><textarea id='$id' rows='$rows' placeholder='$placeholder'>$value</textarea></div>";
}

function infoPopup($text, $title){
    $popy = "<i class='mmsi-icon info'></i>";
    $popy .= "<div class='info-popup-wrap'>
        <div class='close-btn'><i class='bi bi-x-lg'></i></div>
        <div class='content'>";
            if(!empty($title)){
                $popy .= "<p class='title'>$title</p>";
            }
            $popy .="<p>$text</p>
            </div>                
        </div>";
    echo $popy;
}