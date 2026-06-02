<?php 
/**
 * Title: Manage Benachrichtigungsuhr
 */
$get_user_id             = get_current_user_id();
//Die Tage für die Zyklus Einstellungen holen
$exam_clock_zyklus_one   = get_user_meta( $get_user_id, 'exam-clock-zyklus-one', true );
$exam_clock_zyklus_two   = get_user_meta( $get_user_id, 'exam-clock-zyklus-two', true );
$exam_clock_zyklus_three = get_user_meta( $get_user_id, 'exam-clock-zyklus-three', true );
//Datum für die Eskalationsstufen holen
$eskalation_stufe_one    = get_user_meta( $get_user_id, 'eskalation_stufe_one', true );
$eskalation_stufe_two    = get_user_meta( $get_user_id, 'eskalation_stufe_two', true );
$eskalation_stufe_three  = get_user_meta( $get_user_id, 'eskalation_stufe_three', true );
//Urlaubs-Modus
$exam_clock_urlaubsmodus = MemyOptionManager::get('exam_clock_urlaubsmodus', '0');
?>

<div id="manage-exam-clock-wrapper" style="height:100%">
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon zyklus"></i> 
            <span class="hide-mobile">Einstellungen</span> Timer
        </h3>
        <?php 
            addCheckbox('URLAUBSMODUS',$exam_clock_urlaubsmodus,'exam_clock_urlaubsmodus');
        ?>
    </div>
    
    
    <div class="settings-labels" id="manage-exam-clock-buttons">
        <div id="exam-clock-zyklus" class="memy-button button-arrow short-button">
            Zyklus ändern
        </div>
        <div id="exam-clock-reset" class="memy-button button-arrow short-button">
            Einstellungen
        </div>
    </div>

    
    <div id="exam-clock-zyklus-input" class="step-2 flex-one settings-labels overflow-wrapper">
        <h4>Zyklus</h4>
        <div class="spalte">
            <?php numberInput("exam-clock-zyklus-one", esc_attr($exam_clock_zyklus_one), 2, 14, "Erste Erinnerung nach", "Tage"); ?>
            <span class="hide"><?php echo $eskalation_stufe_one ?></span>
        </div>
        <div class="spalte">
            <?php 
            $daystring = ($exam_clock_zyklus_two == 1) ? "Tag" : "Tage";
            numberInput("exam-clock-zyklus-two", esc_attr($exam_clock_zyklus_two), 1, 7, "Zweite Erinnerung", $daystring." später"); ?>
            <span class="hide"><?php echo $eskalation_stufe_two ?></span>
        </div>
        <div class="spalte">
            <?php 
            $daystring = ($exam_clock_zyklus_three == 1) ? "Tag" : "Tage";
            numberInput("exam-clock-zyklus-three", esc_attr($exam_clock_zyklus_three), 1, 3, "Notfallkontakt(e) informieren", $daystring." später"); ?>
            <span class="hide"><?php echo $eskalation_stufe_three ?></span>
        </div>
        <button id="exam-clock-save-zyklus" class="save-wrapper short-button"><i class="mmsi-icon speichern"></i>Änderungen speichern</button>
    </div>   

    <div id="exam-clock-reset-input" class="step-2 flex-one">
        <h4>Reset</h4>
        <?php
        $reset_otions = [
            'login-reset'  => 'Reset bei Login',
            'button-klick' => 'Reset bei Klick auf Button',
        ];

        addRadioGroup('', $reset_otions, $selected_reset, 'examclock-reset');
        ?>
        <button id="exam-clock-reset-option" class="save-wrapper short-button"><i class="mmsi-icon speichern"></i>Änderungen speichern</button>
    </div>
</div>