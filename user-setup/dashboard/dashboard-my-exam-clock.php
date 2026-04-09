<?php 
/**
 * Title: Totmanschalter Uhrzeit Anzeige
 */
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
//Urlaubs-Modus
$exam_clock_urlaubsmodus = MemyOptionManager::get('exam_clock_urlaubsmodus', '0');
//ResetOption
$selected_reset = MemyOptionManager::get('examclock-reset', 'button-klick');
//HTML ausgeben
if(!empty($exam_clock_zyklus_one)) : ?>
    
    <div class="dashboard-item">
        <?php wp_nonce_field('exam_clock_manager_nonce', '_wpnonce'); ?>

        <div class="exam-clock-wrapper">
            <div class="time-information-wrapper spalte">
                <div class="exam-clock-animation">
                    <!-- Fortschrittsanzeige Stufe 1 -->
                    <div class="progress-container">
                        <div class="progress-bg"></div>
                        <div class="progress-fill" id="progressFill1"></div>
                    </div>

                    <!-- Fortschrittsanzeige Stufe 2 -->
                    <div class="progress-container is-2">
                        <div class="progress-bg is-2"></div>
                        <div class="progress-fill is-2" id="progressFill2"></div>
                    </div>

                    <!-- Fortschrittsanzeige Stufe 3 -->
                    <div class="progress-container is-3">
                        <div class="progress-bg is-3"></div>
                        <div class="progress-fill is-3" id="progressFill3"></div>
                    </div>
                </div>
                
                <div class="time-information">
                    <?php 
                    echo "<p><i></i> <span>Timer 1:</span> <span>". tillEscalation($eskalation_stufe_one)."</span></p>";
                    echo "<p><i></i> <span>Timer 2:</span> <span>". tillEscalation($eskalation_stufe_two)."</span></p>";
                    echo "<p><i></i> <span>Timer 3:</span> <span>". tillEscalation($eskalation_stufe_three)."</span></p>";
                    ?>
                </div>

                <button id="exam-clock-reset-button"><i class="mmsi-icon timer-reset"></i>Timer Reseten</button>
            </div>

            <!-- Daten zur Kreis Animation -->
            <div style="display:none">
                <input type="text" id="startDate1" value="<?php echo $exam_clock_start_date; ?>" readonly>
                <input type="text" id="targetDate1" value="<?php echo $eskalation_stufe_one; ?>" readonly>
                <input type="text" id="startDate2" value="<?php echo $eskalation_stufe_one; ?>" readonly>
                <input type="text" id="targetDate2" value="<?php echo $eskalation_stufe_two; ?>" readonly>
                <input type="text" id="startDate3" value="<?php echo $eskalation_stufe_two; ?>" readonly>
                <input type="text" id="targetDate3" value="<?php echo $eskalation_stufe_three; ?>" readonly>            
            </div>
        </div>
    </div>

    <span id="exam-clock-setting-dashboard" data-goto="manage-exam-clock" class="dash-goto-btn">
        <button class="dash-clock-menu">
            <span></span><span></span><span></span>
        </button>
    </span>
    
<?php else: ?>
    <button data="exam-clock-setting" class="goto-settings wp-element-button">Zyklen einrichten</button>
<?php endif; ?>