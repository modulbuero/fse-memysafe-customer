<?php 
    $get_user_id             = get_current_user_id();
    $today                   = new DateTime('now', new DateTimeZone('Europe/Berlin'));
    //den Tag für die Zyklus Starten holen
    $exam_clock_start_date   = get_user_meta( $get_user_id, 'exam-clock-start', true );
    //Die Tage für die Zyklus Einstellungen holen
    $exam_clock_zyklus_one   = (get_user_meta( $get_user_id, 'exam-clock-zyklus-one', true )) ? get_user_meta( $get_user_id, 'exam-clock-zyklus-one', true ) : 7;
    $exam_clock_zyklus_two   = get_user_meta( $get_user_id, 'exam-clock-zyklus-two', true ) ? get_user_meta( $get_user_id, 'exam-clock-zyklus-two', true ) : 3;
    $exam_clock_zyklus_three = get_user_meta( $get_user_id, 'exam-clock-zyklus-three', true ) ? get_user_meta( $get_user_id, 'exam-clock-zyklus-three', true ) : 4;
    //Datum für die Eskalationsstufen holen
    /*
    $eskalation_stufe_one    = get_user_meta( $get_user_id, 'eskalation_stufe_one', true );
    $eskalation_stufe_two    = get_user_meta( $get_user_id, 'eskalation_stufe_two', true );
    $eskalation_stufe_three  = get_user_meta( $get_user_id, 'eskalation_stufe_three', true );
    */
?>
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
        <h4>
            Timer aktivieren
        </h4>
        <p>
            Der Timer wird aktiv, wenn du über einen festgelegten Zeitraum nicht reagierst.
        </p>    
        <p>
            Erst durch die Aktivierung wird MMSI wirksam.
        </p>
        <p>
            Die Timer bestimmen, wann Erinnerungen und Benachrichtigungen ausgelöst werden.
        </p>

        
        
        <div class="spalte">
            <?php numberInput("fs-exam-clock-zyklus-one", esc_attr($exam_clock_zyklus_one), 7, 14, "Erste Erinnerung nach", "Tagen"); ?>
        </div>
        <div class="spalte">
            <?php numberInput("fs-exam-clock-zyklus-two", esc_attr($exam_clock_zyklus_two), 2, 7, "Zweite Erinnerung", "Tage später"); ?>
        </div>
        <div class="spalte txt-distance-bottom">
            <?php numberInput("fs-exam-clock-zyklus-three", esc_attr($exam_clock_zyklus_three), 1, 3, "Notfallkontakte informieren", "Tag später"); ?>
        </div>

        <p>
            Beispiel:<br>
            Reagierst du 7 Tage lang nicht, erhältst du die erste Erinnerung.<br>
            Nach weiteren 2 Tagen folgt die zweite Erinnerung.<br>
            Einen Tag später werden deine Notfallkontakte informiert.<br>
        </p>
        <button id="zyklus-ersteinrichtung" class="half-width">Timer-Einstellungen speichern <i class="mmsi-icon speichern"></i></button>
    </div>

    <?php firstStepNavi('8') ?>