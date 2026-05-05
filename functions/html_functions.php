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
function addCheckbox($label, $value, $id='1', $class=''){    
    $checked = ($value == 1) ? 'checked' : '';
    echo "<label class='is-checkbox $class'><span class='checkbox-label'>$label</span><input type='checkbox' id='$id' value='1' $checked/><span class='slider'></span></label>";
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
    echo "<div class='$id radio-boxes'>";
    echo (!empty($label)) ? "<label>$label</label>" : '';
    echo  "<div class='radio-group'>";
   
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

/**
 * Einfaches Info-Popup
 * @text Inhalt des Popups
 * @title Titel des Popups (optional)
 * return echo
 */
function infoPopup($text, $title){
    $popy = "<i class='mmsi-icon info haspopup-info'></i>";
    $popy .= "<div class='info-popup-wrap'>
        <div class='close-btn'><i class='bi bi-x-lg'></i></div>
        <div class='content'>";
            if(!empty($title)){
                $popy .= "<p class='title'><strong>$title</strong></p>";
            }
            $popy .="<p>$text</p>
            </div>                
        </div>";
    echo $popy;
}

/**
 * Lösch-Confirm-Popup
 * @btn_id ID des Lösch-Buttons (wird für die JS-Eventbindung benötigt)
 * @title Titel des Popups (optional)
 * return echo
 */
function deletePopup($btn_id, $title="Löschen"){
    $delete_popy = "<div class='info-popup-wrap delete-popup'>
        <div class='close-btn'><i class='bi bi-x-lg'></i></div>
        <div class='content'>";
            if(!empty($title)){
                $delete_popy .= "<p class='title'><i class='mmsi-icon info'></i> $title</p>";
            }
            $delete_popy .="<p>Sind Sie sicher?<br>Diese Aktion kann nicht rückgängig gemacht werden.</p>";
            $delete_popy .="<div class='spalte'>";
                $delete_popy .="<button id='$btn_id' class='delete-btn'>Löschen</button>";
                $delete_popy .="<button class='cancel-btn'>Abbrechen</button>";
            $delete_popy .="</div>";

    $delete_popy .="</div></div>";
    echo $delete_popy;
}

/**
 * First Step Navigationbuttons
 */
function firstStepNavi($step='1', $next=true, $back=true, $backtext='Zurück', $nexttext='Weiter'){
    $html = "<div class='fs-naviwrapper'>
        <div class='progress' data-step='$step' style='--step:$step'></div>";
    
    if($back == true){
        $html .= '<button class="goback"><i class="mmsi-icon pfeil pfeil-links"></i>'.$backtext.'</button>';
    }
    if($next == true){
        $html .= '<button class="first-step-button" disabled="disabled">'.$nexttext.'<i class="mmsi-icon pfeil"></i></button>';
    }
    
    $html .= '</div>';
    echo $html;

}

/**
 * Speichern und Löschen Buttons, die nur der Admin sehen kann.
 */
function saveDeleteButton($typ){
    $html ="";

    if(get_current_user_id() === getAdminUserID()){
        $html = '<div class="spalte save-wrapper">';
        $html .= "<button id='save-".$typ."'><i class='mmsi-icon speichern'></i> Speichern</button>
            <button data-id='delete-".$typ."' class='delete-btn-pop'><i class='mmsi-icon delete'></i> Löschen</button>
        </div>";

        
    }
    echo $html;
}

/**
 *  Eingabefelder zur Erstellung der Textdatei
 */
function inputFieldsTxtFile(){
   echo "
    <p>Zwei Angaben sind entscheidend. Sie ermöglichen anderen, im Bedarfsfall zu handeln.</p>
    <div id='checkvalues-safeinfo' class='txt-distance-bottom'>";
        
        $quest_1 = 'Wo befinden sich deine wichtigsten Unterlagen und Informationen?';
        addInput($quest_1, '', 'upload-txt-1');
        $quest_2 = 'Wie kann auf deine digitalen Daten zugegriffen werden? (Keine Passwörter angeben.)';
        addInput($quest_2, '', 'upload-txt-2');
    
    echo "
    </div>
    <br>
    <div id='checkvalues-safeinfo-soft' class='txt-distance-bottom'>
        <p class='no-distance-bottom'>Zusätzliche Hilfsinformationen</p>";
    
        $quest_3 = 'Wie kann deine Buchhaltung eingesehen werden?';
        addInput($quest_3, '', 'upload-txt-3');
        $quest_4 = 'Wer kann beim Zugang zu wichtigen Informationen unterstützen?';
        addInput($quest_4, '', 'upload-txt-4');
        $quest_5 = 'Gibt es etwas, das zuerst geklärt oder gesichert werden sollte?';
        addInput($quest_5, '', 'upload-txt-5');
        $quest_6 = 'Welche laufenden Verpflichtungen dürfen nicht übersehen werden?';
        addInput($quest_6, '', 'upload-txt-6');
        $quest_7 = 'Gibt es wichtige Hinweise, die unbedingt beachtet werden müssen?';
        addInput($quest_7, '', 'upload-txt-7');
    
    echo "
    </div>
    ";
}