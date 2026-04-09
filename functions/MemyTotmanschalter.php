<?php 
/**
 * Title: Manage Totmanschalter | Exam-Clock
 * Author: Modulbüro
 */
if (!defined('ABSPATH')) {
    exit;
}

class MemyTotmanschalter {
    
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        #add_shortcode('exam_clock_manager', array($this, ''));
        add_action('wp_ajax_handle_update_exam_clock_data', array($this, 'handle_update_exam_clock_data'));
        add_action('wp_ajax_handle_reload_exam-clock_final', array($this, 'handle_reload_exam-clock_final'));
        add_action('wp_ajax_handle_update_exam_clock_reload', array($this, 'handle_update_exam_clock_reload'));
        add_action('wp_ajax_handle_update_exam_clock_cycles', array($this, 'handle_update_exam_clock_cycles'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('exam-clock-manager', get_stylesheet_directory_uri() . '/assets/js/exam-clock-manager.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('exam-clock-manager', 'ajax_object_deathman', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('exam_clock_manager_nonce')
        ));
    }
    
    /*
     *   Ausgabe der Profildaten in einem Formular
     
    public function exam_clock_manager() {

        if (!is_user_logged_in()) {
            return '<p>Sie müssen angemeldet sein, um Ihre Daten zu bearbeiten.</p>';
        }
        
        //Daten
        $current_user = wp_get_current_user();
        $user_metas   = get_user_meta($current_user->ID);
        $currentDate  = date('d.m.Y H:i:s');
        $notfallMail  = (!empty(get_option('notfall_email'))) ? get_option('notfall_email') : "";
        $notfallName  = (!empty(get_option('notfall_name'))) ? get_option('notfall_name') : "";
        $notfallTel   = (!empty(get_option('notfall_telefon'))) ? get_option('notfall_telefon') : "";

        $isReminderDate = get_option('send_reminder_time');
        $isNotfallDate  = get_option('send_notfall_time');

        

        //Benachrichtigungsberechnung:
        $stempel_start = (isset($user_metas['deathman_start'])) ? get_option($user_metas['deathman_start'][0]) : "";
        $stempel_days  = (isset($user_metas['deathman_days'])) ? get_option($user_metas['deathman_days'][0]) : "";
        $neues_datum   = date('d.m.Y', strtotime($stempel_start . ' +'.$stempel_days.' days'));

        ob_start();
        ?>
        
        <form id="exam-clock-manager-form">
            <?php wp_nonce_field('exam_clock_manager_nonce', '_wpnonce'); ?>        

            <div>
                <h4>Notfall Kontakt:</h4>
                <label for="notfall_email">Notfall Email:</label>
                <input type="text" id="notfall_email" name="notfall_email" value="<?php echo esc_attr($notfallMail); ?>" />
            </div>

            <div>
                <label for="notfall_name">Notfall Name:</label>
                <input type="text" id="notfall_name" name="notfall_name" value="<?php echo esc_attr($notfallName); ?>" />
            </div>

            <div>
                <label for="notfall_telefon">Notfall Telefon:</label>
                <input type="text" id="notfall_telefon" name="notfall_telefon" value="<?php echo esc_attr($notfallTel); ?>" />
            </div>

            <div id="lebenszeichen-chooser">
                <h4>1. Lebenszeichen-Abfrage</h4>
                <p>Du legst fest, wie oft du ein Lebenszeichen gibst – z. B. durch einen einfachen Login.<br>
                    Der erste mögliche Rhythmus ist alle 2 Tage. Danach kannst du ein Intervall nach deinen Bedürfnissen wählen – bis maximal alle 30 Tage.</p>
                <label for="deathman_days">Auswahl Lebenszeichen-Abfrage:</label>
                <?php
                $getDays = get_option("deathman_days");
                $benachrichtigungszeit = [
                    'all_3_days' => 'Alle 3 Tage',
                    'all_7_days' => 'Wöchentlich',
                    'all_14_days' => 'alle 14 Tage',
                    'all_30_days' => 'Monatlich',
                ];
                echo checkboxGroup('deathman_days', $benachrichtigungszeit, $getDays);
                ?>
            </div>
           
            <div id="reminder-chooser">
                <h4>2. Erinnerung bei ausbleibendem Login</h4>
                <p>
                    Bleibt ein Lebenszeichen aus, wirst du zunächst nach 24 Stunden erinnert.
                    Ab dann kannst du die Erinnerungsabstände individuell festlegen – zwischen 1 und 30 Tagen.
                </p>
                <label for="deathman_reminder">Erinnerung:</label>
                <?php
                $getReminder = get_option("deathman_reminder");
                $reminder_zeit = [
                    '24h' => '24 Stunden',
                    '48h' => '48 Stunden',
                ];
                echo checkboxGroup('deathman_reminder', $reminder_zeit, $getReminder);
                ?>
            </div>

             <div id="eskalation-chooser">
                <h4>3. Eskalationsstufen</h4>
                <p>
                    Wenn du auf Erinnerungen nicht reagierst, startet die Eskalation.<br>
                    Du entscheidest selbst, nach wie vielen erfolglosen Erinnerungen sich dein Safe automatisch öffnet – mindestens nach 2, höchstens nach 5 Erinnerungen.
                </p>
                <?php
                $getEskalationTime = (!empty(get_option("eskalation-time"))) ? get_option("eskalation-time"): "";
                echo rangeSlider('eskalation_setting', $getEskalationTime, "Erinnerung in Tage:");
                ?>
            </div>


            <div>
                <button type="submit" id="save-button">Daten speichern</button>
                <span id="loading" style="display: none;">Speichere...</span>
            </div>
        </form>
        
        <?php if(!empty('isNotfallDate') ): ?>
        <p id="exam-clock-final-info">Datum der Benachrichtigung ist der <span id="finale-zeit"><?php echo $isNotfallDate ?></span>.
        <?php endif; ?>
        <?php
        $output = ob_get_contents();
        ob_get_clean();
        return $output;
    }
    */
    
    /**
     * AjX Handling zum Update der Daten in der gesamtübersicht
     */
    public function handle_update_exam_clock_data() {
        /**
         *  Safety first
         */
        if (!wp_verify_nonce($_POST['_wpnonce'], 'exam_clock_manager_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsüberprüfung fehlgeschlagen.'));
        }
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Sie müssen angemeldet sein.'));
        }
        
        /*
            //Userdata
            $current_user = wp_get_current_user();
            $user_id      = $current_user->ID;
            
            // Postdaten sammeln
            $user_meta = [
                'deathman_days' => sanitize_text_field($_POST['deathman_days']),
                'deathman_start'=> sanitize_text_field($_POST['deathman_start']),
                'deathman_final'=> sanitize_text_field($_POST['deathman_final']),
            ];

            
            // Benutzerdaten aktualisieren
            $wpUserMeta = $this->wp_update_user_metas($user_id, $user_meta);
        */
        
        $notfall_email      = $_POST['notfall_email'];
        $deathman_reminder  = $_POST['deathman_reminder'][0];
        $deathman_days      = $_POST['deathman_days'][0];
        $tage               = 7;
        $reminder_tage      = 1;
        
        switch ($deathman_reminder) {
            case "48h":
                $deathman_reminder = "48h";
                $reminder_tage     = 2;
                break;
            default:
                $deathman_reminder =  "24h";
                break;
        }

        switch ($deathman_days) {
            case "all_14_days":
                $deathman_days = "all_14_days";
                $tage = 14;
                break;
            case "all_3_days":
                $deathman_days =  "all_3_days";
                $tage = 3;
                break;
            case "taeglich":
                $deathman_days =  "taeglich";
                $tage = 1;
                break;
            default:
                $deathman_days =  "all_7_days";
                break;
        }

        /*
            wp_send_json_success(array(
                'Metas' => array(
                    $addDays,
                    $addMail,
                    $addReminder
                ),
                'Values' => array(
                    $deathman_days,
                    $notfall_email,
                    $addReminder
                ),
            ));
        */

        $sendDeathMailTime = $this->getBerlinDateTime()->modify('+'.$tage.' days')->format('d.m.Y');
        $sendReminderMail  = $this->getBerlinDateTime()->modify('+'.($tage - $reminder_tage).' days')->format('d.m.Y');

        update_option('notfall_email', $notfall_email);
        update_option('deathman_days', $deathman_days);
        update_option('deathman_reminder', $deathman_reminder);
        update_option('send_notfall_time',$sendDeathMailTime);
        update_option('send_reminder_time',$sendReminderMail);

        if(!empty(get_option('send_notfall_time'))){
            wp_send_json_success(array(
                'message'     => 'Die Daten wurden eingetragen',
                'reminderday' => 'Ihre Erinnerung erfolgt am '. $sendReminderMail,
                'notfallday'  => 'Ihre Notfallkontakt wird am '. $sendDeathMailTime . ' benachrichtigt',
                'finalday'    => $sendDeathMailTime,
            ));
        }else{
            wp_send_json_error(array(
                'message'     => 'Ein fehler ist aufgetreten, versuche es nach einem neustart der Seite nochmal.',
            ));
        }
    }


    /**
     * AjX Handling zum Reload der Zeit (Exam-Clock clicked)
     */
    function handle_update_exam_clock_reload(){
        /**
         *  Safety first
         */
        if (!wp_verify_nonce($_POST['_wpnonce'], 'exam_clock_manager_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsüberprüfung fehlgeschlagen.'));
        }
        
        $userID = $_POST['user_id'];

        if(!is_numeric($userID) || empty($userID)){
            wp_send_json_error(array('message' => 'Ungültige User ID.'));
        }else{
            
            $cyclusOne = get_user_meta( $userID, 'exam-clock-zyklus-one', true );
            if(!empty($cyclusOne)){
                $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-one', $cyclusOne);
                $cyclusTwo = get_user_meta( $userID, 'exam-clock-zyklus-two', true );
                $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-two', $cyclusTwo);
                $cyclusThree = get_user_meta( $userID, 'exam-clock-zyklus-three', true );
                $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-three', $cyclusThree);
            }else{
                wp_send_json_error(array('message' => 'Zyklus 1 nicht gesetzt.'));
            }

            //neu
            update_option('has_send_reminder_one', '');
            update_option('has_send_reminder_two', '');
            update_option('has_send_reminder_three', '');
            update_option('has_send_notfall', '');
            
            wp_send_json_success(array(
                'message' => 'Update erfolgreich durchgeführt.',
            ));
        }
    }

    /**
     * AjX Handling zum Update der Zyklen
     */
    function handle_update_exam_clock_cycles(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'exam_clock_manager_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsüberprüfung fehlgeschlagen.'));
        }
        
        $userID = $_POST['user_id'];
        
        if(!is_numeric($userID) || empty($userID)){
            wp_send_json_error(array('message' => 'Ungültige User ID.'));
        }else{
            $zyklus_one   = sanitize_text_field($_POST['zyklus_one']);
            $zyklus_two   = sanitize_text_field($_POST['zyklus_two']);
            $zyklus_three = sanitize_text_field($_POST['zyklus_three']);
            
            // Update all three cycles
            update_user_meta($userID, 'exam-clock-zyklus-one', $zyklus_one);
            update_user_meta($userID, 'exam-clock-zyklus-two', $zyklus_two); 
            update_user_meta($userID, 'exam-clock-zyklus-three', $zyklus_three);
                
                // Update escalation times for all cycles
            $uzOne   = $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-one', $zyklus_one);
            $uzTwo   = $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-two', $zyklus_two);
            $uzThree = $this->update_escalation_for_cycle($userID, 'exam-clock-zyklus-three', $zyklus_three);

            //Erfolgreich
            wp_send_json_success(array(
                'message' => 'Zyklen erfolgreich aktualisiert.',
                'debug' => array(
                    'uzOne'   => $uzOne,
                    'uzTwo'   => $uzTwo,
                    'uzThree' => $uzThree,
                )
            ));
        }
    }

    /** 
     * Eskalationszeiten eintragen
     * 
     * @param int $userID
     * @param int $stufe    'string' one, two, three
     * @param int $wert     in Tagen
     */
    function escalationsZeiten($userID, $stufe, $wert){
        if (!is_numeric($userID) || empty($stufe) || !is_numeric($wert)) {
            return false;
        }
        //Entwicklung | dev -> minutes
        //Live              -> days
        $datum = $this->getBerlinDateTime()->modify('+'.$wert.' minutes')->format('d.m.Y H:i');

        update_user_meta($userID, 'eskalation_stufe_' . $stufe, $datum);

        return true;
    }

    /**
     *  Hilfsfkt.: Metas speichern
     */
    public function wp_update_user_metas($user_id, $meta_array){
        if (!is_numeric($user_id) || empty($meta_array) || !is_array($meta_array)) {
            return false;
        }

        foreach ($meta_array as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }

        return true;
    }

    protected function getBerlinDateTime(string $time = 'now'): DateTime {
        return new DateTime($time, new DateTimeZone('Europe/Berlin'));
    }

    /**
     * Helper: berechnet und setzt Eskalationszeiten basierend auf Zyklus-ID.
     *
     * @param int $userID
     * @param string $zyklus_id
     * @param int $tage
     * @return bool
     */
    protected function update_escalation_for_cycle($userID, $zyklus_id, $tage) {
        $tage = (int) $tage;
    
        //Startzeit erstellen
        $datum = $this->getBerlinDateTime()->format('d.m.Y H:i');
        update_user_meta($userID, 'exam-clock-start', $datum);

        if ($zyklus_id === "exam-clock-zyklus-one") {
            return $this->escalationsZeiten($userID, "one", $tage);
        }

        if ($zyklus_id === "exam-clock-zyklus-two") {
            $one = (int) get_user_meta($userID, 'exam-clock-zyklus-one', true);
            if ($one > 0) {
                $addOneAndTwo = $one + $tage;
                return $this->escalationsZeiten($userID, "two", $addOneAndTwo);
            }
            return false;
        }

        if ($zyklus_id === "exam-clock-zyklus-three") {
            $one = (int) get_user_meta($userID, 'exam-clock-zyklus-one', true);
            $two = (int) get_user_meta($userID, 'exam-clock-zyklus-two', true);
            
            if ($two > 0) {
                $addFinal = $one + $two + $tage;
                return $this->escalationsZeiten($userID, "three", $addFinal);
            }
            return false;
        }

        return false;
    }
}

new MemyTotmanschalter();

/**
 * Globale-Hilfsfunktion: Berechnet die verbleibende Zeit bis zur Eskalation.
 *
 * @param string $date Ziel-Datum im Format 'd.m.Y H:i:s'
 * @return string Verbleibende Zeit in Tagen, Stunden und Minuten
 */
function tillEscalation($date){
    $jetzt = new DateTime('now', new DateTimeZone('Europe/Berlin'));

    // Ziel-Datum, z. B. 24. Dezember 2025 um 18:30 Uhr
    $zielDatum = new DateTime($date, new DateTimeZone('Europe/Berlin'));

    // Zeitdifferenz berechnen
    $differenz = $jetzt->diff($zielDatum);

    // Ergebnis ausgeben
    if ($zielDatum > $jetzt) {
        $output = "";
        if ($differenz->days > 0) {
            $output .= $differenz->days . " Tage ";
        }
        $output .= $differenz->h . " Std. " . $differenz->i . " Min.";

        return trim($output);
    } else {
        return "Abgelaufen.";
    }

}

function getNextEscalationDate($date){
    $jetzt = new DateTime('now', new DateTimeZone('Europe/Berlin'));

    // Ziel-Datum, z. B. 24. Dezember 2025 um 18:30 Uhr
    $zielDatum = new DateTime($date, new DateTimeZone('Europe/Berlin'));

    // Ergebnis ausgeben
    if ($zielDatum > $jetzt) {
        return $zielDatum->format('d.m.Y H:i:s');
    } else {
        return "Eskalation bereits erfolgt.";
    }
}