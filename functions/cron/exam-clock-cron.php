<?php
// 1. Eigenes Cron-Intervall von 5 Minuten registrieren
function add_memy_cron_interval($schedules) {
    $schedules['every_memy_hours'] = array(
        'interval' => 3 * MINUTE_IN_SECONDS,
        'display'  => 'Alle 5 Minuten (MeMySafe)',
    );
    return $schedules;
}
add_filter('cron_schedules', 'add_memy_cron_interval');

// 2. Cron Event beim Aktivieren des Themes/Plugins planen
function schedule_my_cron_job() {
    if (!wp_next_scheduled('memy_safety_cron_hook')) {
        wp_schedule_event(time(), 'every_memy_hours', 'memy_safety_cron_hook');
    }
}
// Geändert von 'wp' auf 'init', damit der Cron auch im Admin-Bereich eingeplant wird
add_action('init', 'schedule_my_cron_job');

// 3. Die Funktion, die ausgeführt wird
function memy_deathman_query_function() {
    // Zu spezifischer Site wechseln
    if (is_multisite()) {
        // Hinweis: get_current_blog_id() ist innerhalb des Crons der Site bereits korrekt.
        //Benutzerinfos abrufen
        $query = new WP_User_Query([
            'number'  => 1,
            'orderby' => 'registered',
            'order'   => 'ASC',
        ]);

        $users = $query->get_results();

        if (!empty($users)) {
            $adminuser = $users[0];
            $adminID   = $adminuser->ID;
            $adminEmail = $adminuser->user_email;
            $adminName  = $adminuser->first_name . ' ' . $adminuser->last_name;
            error_log("MeMySafe_Cron: Benutzer gefunden für Blog " . get_current_blog_id());
        } else {
            error_log("MeMySafe_Cron: Kein Benutzer gefunden für Blog " . get_current_blog_id());
            return;
        }

        $curr_date_obj         = date_create(current_time('Y-m-d H:i'));
        $curr_date_string      = $curr_date_obj->format('d.m.Y H:i');
       
        //Eskalationsstufen
        $eskalation_stufe_one   = get_user_meta( $adminID, 'eskalation_stufe_one', true );
        $eskalation_stufe_two   = get_user_meta( $adminID, 'eskalation_stufe_two', true );
        $eskalation_stufe_three = get_user_meta( $adminID, 'eskalation_stufe_three', true );
        $hasSendReminderOne     = get_option('has_send_reminder_one');
        $hasSendReminderTwo     = get_option('has_send_reminder_two');
        $hasSendReminderThree   = get_option('has_send_reminder_three');

        //Notfallkontakt 1
        $notfall_email         = get_user_meta($adminID, 'contact-person-1', true)['email'] ?? '';
        $hasSendNotfall        = get_option('has_send_notfall');

        error_log("MeMySafe_Cron: ESK | currDate: " . $curr_date_string);
        
        if(!empty($adminEmail) && !empty($notfall_email)
            ){
            
            if(empty($hasSendReminderOne)){
                // 1. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_one_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_one);

                if ($esk_one_obj && $curr_date_obj >= $esk_one_obj ) {
                    $subject = "Erinnerung: Bestätige deine Sicherheit";
                    $message = "Hallo, bitte logge dich ein, um deinen Sicherheits-Timer zu aktualisieren.";
                    wp_mail($adminEmail, $subject, $message);
                    
                    update_option('has_send_reminder_one', $curr_date_string);
                    error_log("MeMySafe_Cron: Reminder Mail 1 gesendet an " . $adminEmail);
                    return;
                }
            }

            if(empty($hasSendReminderTwo) && $hasSendReminderOne){
                // 2. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_two_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_two);

                if ($esk_two_obj && $curr_date_obj >= $esk_two_obj ) {
                    $subject = "Erinnerung 2: Bestätige deine Sicherheit";
                    $message = "Hallo, bitte logge dich ein, um deinen Sicherheits-Timer zu aktualisieren.";
                    wp_mail($adminEmail, $subject, $message);
                    
                    update_option('has_send_reminder_two', $curr_date_string);
                    error_log("MeMySafe: Reminder Mail 2 gesendet an " . $adminEmail);
                    return;
                }
            }

            if(empty($hasSendReminderThree) && $hasSendReminderTwo){
                // 3. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_three_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_three);

                if ($esk_three_obj && $curr_date_obj >= $esk_three_obj ) {
                    $subject = "Erinnerung 3: Bestätige deine Sicherheit";
                    $message = "Hallo, bitte logge dich ein, um deinen Sicherheits-Timer zu aktualisieren.";
                    wp_mail($adminEmail, $subject, $message);
                    
                    update_option('has_send_reminder_three', $curr_date_string);
                    error_log("MeMySafe: Reminder Mail 3 gesendet an " . $adminEmail);
                    return;
                }
            }

            if(empty($hasSendNotfall) && $hasSendReminderThree){
                //Sende an den Notfallkontakt, wenn Eskalation 3 erreicht ist
                $subject = "Hinweis: Sicherheits-Timer erreicht Eskalationsstufe";
                $message = "Hallo, der Benutzer " . $adminName . " hat die Eskalationsstufe 3 erreicht.";
                wp_mail($notfall_email, $subject, $message);
                
                update_option('has_send_notfall', $curr_date_string);
                error_log("MeMySafe: Notfall Mail gesendet an " . $notfall_email);
                return;
            }

        }else{
            error_log("MeMySafe_Cron: Cron läuft, aber fehlende Daten (Zeiten/Email) für Blog " . get_current_blog_id());
        }
    }
}
add_action('memy_safety_cron_hook', 'memy_deathman_query_function');

// 4. Cron Job beim Deaktivieren des Themes/Plugins entfernen
add_action('switch_theme', 'unschedule_my_cron_job');
function unschedule_my_cron_job() {
    wp_clear_scheduled_hook('memy_safety_cron_hook');
}

// Zeigt den Cron-Status im WordPress-Adminbereich an
add_action('admin_notices', 'memy_display_cron_status');
function memy_display_cron_status() {
    if (!current_user_can('administrator')) return;

    // Manueller Trigger zum Testen via URL-Parameter ?run_memy_cron=1
    if (isset($_GET['run_memy_cron'])) {
        do_action('memy_safety_cron_hook');
        echo '<div class="notice notice-success"><p>MeMySafe Cron manuell ausgeführt!</p></div>';
    }

    $timestamp = wp_next_scheduled('memy_safety_cron_hook');
    if ($timestamp) {
        $diff = $timestamp - time();
        $wait = ($diff > 0) ? round($diff / 60) . ' Min.' : 'fällig (warte auf Seitenaufruf)';
        echo '<div class="notice notice-info"><p><strong>MeMySafe Cron:</strong> Nächster Lauf am ' . date('d.m.Y H:i:s', $timestamp) . ' (in ca. ' . $wait . ') 
        | <a href="' . add_query_arg('run_memy_cron', '1') . '">Jetzt manuell triggern</a></p></div>';
    } else {
        echo '<div class="notice notice-error"><p><strong>MeMySafe Cron:</strong> Kein Cron-Job geplant! Versuche die Seite neu zu laden.</p></div>';
    }
}
add_action( 'wp_login', 'mein_login_callback', 10, 2 );
function mein_login_callback( $user_login, $user ) {
    // $user_login = Benutzername
    // $user = WP_User-Objekt
    error_log( "BenutzerObj: ".print_r($user) );
    error_log( "Benutzer: {$user_login} hat sich eingeloggt." );
}