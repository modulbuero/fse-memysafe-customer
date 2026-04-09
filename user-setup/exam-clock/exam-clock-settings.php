<?php 
/**
 * Title: Manage Benachrichtigungsuhr
 */
?>

<div id="manage-exam-clock-wrapper" style="height:100%">
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon zyklus"></i> 
            Einstellungen Uhr
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

    
    <div id="exam-clock-zyklus-input" class="step-2 flex-one settings-labels">
        <h4>Zyklus</h4>
        <div class="spalte">
            <?php numberInput("exam-clock-zyklus-one", esc_attr($exam_clock_zyklus_one), 2, 14, "1. Timer", "Tage"); ?>
            <span><?php echo $eskalation_stufe_one ?></span>
        </div>
        <div class="spalte">
            <?php numberInput("exam-clock-zyklus-two", esc_attr($exam_clock_zyklus_two), 1, 7, "2. Timer", "Tage"); ?>
            <span><?php echo $eskalation_stufe_two ?></span>
        </div>
        <div class="spalte">
            <?php numberInput("exam-clock-zyklus-three", esc_attr($exam_clock_zyklus_three), 1, 3, "3. Timer", "Tage"); ?>
            <span><?php echo $eskalation_stufe_three ?></span>
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