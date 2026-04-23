<?php 
    $get_user_id             = get_current_user_id();
    $today                   = new DateTime('now', new DateTimeZone('Europe/Berlin'));
    //den Tag für die Zyklus Starten holen
    $exam_clock_start_date   = get_user_meta( $get_user_id, 'exam-clock-start', true );
    //Die Tage für die Zyklus Einstellungen holen
    $exam_clock_zyklus_one   = get_user_meta( $get_user_id, 'exam-clock-zyklus-one', true );
    $exam_clock_zyklus_two   = get_user_meta( $get_user_id, 'exam-clock-zyklus-two', true );
    $exam_clock_zyklus_three = get_user_meta( $get_user_id, 'exam-clock-zyklus-three', true );
    //Datum für die Eskalationsstufen holen
    $eskalation_stufe_one    = get_user_meta( $get_user_id, 'eskalation_stufe_one', true );
    $eskalation_stufe_two    = get_user_meta( $get_user_id, 'eskalation_stufe_two', true );
    $eskalation_stufe_three  = get_user_meta( $get_user_id, 'eskalation_stufe_three', true );
?>
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
        <h4>
            Timer aktivieren
        </h4>
        <p>
            Der Timer sorgt dafür, dass deine Notfallkontakte informiert werden, wenn du nicht mehr reagierst.
        </p>    
        <p>
            Erst durch die Aktivierung wird MMSI wirksam.
        </p>

        <p class="label-like">Timer</p>
        
        <div class="spalte">
            <?php numberInput("fs-exam-clock-zyklus-one", esc_attr($exam_clock_zyklus_one), 2, 14, "1. Timer", "Tage"); ?>
        </div>
        <div class="spalte">
            <?php numberInput("fs-exam-clock-zyklus-two", esc_attr($exam_clock_zyklus_two), 1, 7, "2. Timer", "Tage"); ?>
        </div>
        <div class="spalte txt-distance-bottom">
            <?php numberInput("fs-exam-clock-zyklus-three", esc_attr($exam_clock_zyklus_three), 1, 3, "3. Timer", "Tage"); ?>
        </div>

        <button id="zyklus-ersteinrichtung" class="half-width">Zeit übernehmen <i class="mmsi-icon speichern"></i></button>
    </div>

    <?php firstStepNavi('8') ?>