<?php
// 1. Eigenes Cron-Intervall von 15 Minuten registrieren
function add_memy_cron_interval($schedules) {
    $schedules['every_memy_hours'] = array(
        'interval' => 15 * MINUTE_IN_SECONDS,
        'display'  => 'Alle 15 Minuten (MeMySafe)',
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
            $adminuser  = $users[0];
            $adminID    = $adminuser->ID;
            $adminEmail = $adminuser->user_email;
            $adminName  = $adminuser->first_name;
            #error_log("MeMySafe_Cron: Benutzer gefunden für Blog " . get_current_blog_id());
        } else {
            error_log("MeMySafe_Cron: Kein Benutzer gefunden für Blog " . get_current_blog_id());
            return;
        }

        //Urlaubsomodus prüfen
        $exam_clock_urlaubsmodus = MemyOptionManager::get('exam_clock_urlaubsmodus'); 
        if($exam_clock_urlaubsmodus){
            return; // Wenn Urlaubsmodus aktiviert ist, Cron nicht weiter ausführen
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

        // Notfallkontakte
        $notfall_contacts = array();
        for ($i = 1; $i <= 3; $i++) {
            $contact_meta = get_user_meta($adminID, 'contact-person-' . $i, true);
            $contact_email = $contact_meta['email'] ?? '';
            if (empty($contact_email)) {
                continue;
            }

            $contact_first_name = $contact_meta['first_name'] ?? '';
            $contact_last_name = $contact_meta['last_name'] ?? '';
            $contact_name = trim($contact_first_name . ' ' . $contact_last_name);

            $notfall_contacts[] = array(
                'email' => $contact_email,
                'name'  => $contact_name,
            );
        }
        $hasSendNotfall = get_option('has_send_notfall');

        #error_log("MeMySafe_Cron: ESK | currDate: " . $curr_date_string);
        
        if (!empty($adminEmail) && !empty($notfall_contacts)) {
            
            $mail_headers = array('Content-Type: text/html; charset=UTF-8');
            $mail_footer  = emailParts('footer');            
            $login_button = emailParts('button');
            $grusz_admin  = emailParts('head') . "<p>Hallo ".$adminName.",<br></p>";
            
            //1. Stufe
            if(empty($hasSendReminderOne)){
                // 1. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_one_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_one);

                if ($esk_one_obj && $curr_date_obj >= $esk_one_obj ) {
                    $token = generateToken($adminID, get_current_blog_id(), "mmsi-is-salty");
                    $url = add_query_arg(
                        'mmsi-token',
                        $token,
                        trailingslashit(
                            get_home_url(get_main_site_id(), '/reloader/', 'https')
                        )
                    );
                    $reload_button = emailParts('button', $url, 'Erreichbarkeit bestätigen');

                    $subject = "Erinnerung: Bitte bestätige kurz deine Erreichbarkeit";
                    
                    $message = $grusz_admin;
                    $message .= "<p>dein Sicherheits-Timer läuft bald ab.</p>";
                    $message .= "<p>Bitte bestätige mit einem Klick auf den Button Deine Aktivität.</p>";
                    $message .= "<p>Dein Timer wird dann zurückgesetzt.</p>";
                    $message .= $reload_button;
                    $message .= "<p>So stellst du sicher, dass keine weiteren Schritte ausgelöst werden</p><br>";
                    $message .= "<p>Alternativ: Logge dich kurz bei Me, My Safe and I ein und bestätige deine Erreichbarkeit.";
                    $message .= "<a href='".network_home_url() . 'login/' ."' style='text-decoration:underline; color:#000000'>Zum Login</a></p>";
                    $message .= $mail_footer;
                    wp_mail($adminEmail, $subject, $message, $mail_headers);
                    
                    update_option('has_send_reminder_one', $curr_date_string);
                    #error_log("MeMySafe_Cron: Reminder Mail 1 gesendet an " . $adminEmail);
                    return;
                }
            }

            //2. Stufe
            if(empty($hasSendReminderTwo) && $hasSendReminderOne){
                // 2. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_two_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_two);

                if ($esk_two_obj && $curr_date_obj >= $esk_two_obj ) {
                    $subject = "Wichtige Erinnerung: Dein Sicherheits-Timer läuft ab";
                    $message = $grusz_admin;
                    $message .= "<p>wir haben bislang keine Bestätigung von dir erhalten.</p>";
                    $message .= "<p>Bitte logge dich zeitnah bei Me, My Safe and I ein und bestätige deine Erreichbarkeit, damit keine Sicherheitsprozesse gestartet werden.</p>";
                    $message .= $login_button;
                    $message .= $mail_footer;
                    wp_mail($adminEmail, $subject, $message, $mail_headers);
                    
                    update_option('has_send_reminder_two', $curr_date_string);
                    #error_log("MeMySafe: Reminder Mail 2 gesendet an " . $adminEmail);
                    return;
                }
            }

            //3. Stufe
            if(empty($hasSendReminderThree) && $hasSendReminderTwo){
                // 3. Check: Sende Reminder an den User selbst
                // String aus Meta in ein Objekt umwandeln, um es mit $curr_date_obj vergleichen zu können
                $esk_three_obj = date_create_from_format('d.m.Y H:i', $eskalation_stufe_three);

                if ($esk_three_obj && $curr_date_obj >= $esk_three_obj ) {
                    $subject = "Dein Sicherheits-Timer ist abgelaufen";
                    $message = $grusz_admin;
                    $message .= "<p>dein Sicherheits-Timer ist abgelaufen und wir konnten keine Bestätigung von dir erhalten.</p>";
                    $message .= "<p>Deshalb wurden die in deinem Account definierten Sicherheitsprozesse gestartet.</p>";
                    $message .= "<p>Wenn du wieder Zugriff hast, logge dich bitte in deinen Account ein.</p>";
                    $message .= $login_button;
                    $message .= $mail_footer;

                    wp_mail($adminEmail, $subject, $message, $mail_headers);
                    
                    update_option('has_send_reminder_three', $curr_date_string);
                    #error_log("MeMySafe: Reminder Mail 3 gesendet an " . $adminEmail);
                    return;
                }
            }

            //Notfall erreicht
            if (empty($hasSendNotfall) && $hasSendReminderThree) {
                //Sende an die Notfallkontakte, wenn Eskalation 3 erreicht ist
                //Todo: Hinweise auf gesendet im Dashboard mit Maik klären

                $subject = "Hinweis: " . $adminName . " hat nicht auf seinen Sicherheits-Timer reagiert";

                foreach ($notfall_contacts as $notfall_contact) {
                    $message = emailParts('head') . "<p>Hallo " . esc_html($notfall_contact['name']) . ",</p>
                    <p>" . esc_html($adminName) . " hat innerhalb des festgelegten Zeitraums nicht auf seinen Sicherheits-Timer reagiert.</p>
                    <p>Deshalb erhältst du diese Nachricht als hinterlegter Notfallkontakt.</p>
                    <p>Bitte versuche, " . esc_html($adminName) . " zu erreichen oder prüfe gemeinsam mit weiteren Kontaktpersonen, ob alles in Ordnung ist.</p>";
                    
                    $contact_user = get_user_by('email', $notfall_contact['email']);

                    if ($contact_user) {
                        $message .= "<p>Weitere Informationen findest du in deinem Account.</p>";
                        $new_password = wp_generate_password(8, false);
                        $message .= "<p>Passwort: <strong>" . esc_html($new_password) . "</strong></p>";
                        wp_set_password($new_password, $contact_user->ID);

                        $message .= $login_button;
                    }else{
                        $message .= "<p>Deine Account wurde nicht bestätigt. Melde dich gerne beim Support von MMSI.</p>";
                    }

                    $message .= $mail_footer;

                    MemyProtocolManager::add_protocol_backoffice(0, 'Helfer-Modus aktiviert', 'system');
                    wp_mail($notfall_contact['email'], $subject, $message, $mail_headers);
                    error_log("MeMySafe: Notfall Mail gesendet an " . $notfall_contact['email']);
                }

                update_option('has_send_notfall', $curr_date_string);
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
    #error_log( "BenutzerObj: ".print_r($user) );
    #error_log( "Benutzer: {$user_login} hat sich eingeloggt." );
}

/**
 * Token für sofort Reload via URL öffnen
 * in erster Reminder Mail enthalten.
 * gesendet an frontpage "/reload/"
*/
function generateToken(int $userId, int $blogId, string $secret): string {
    $timestamp = time();
    
    // Alle Werte zusammenführen
    $data = implode('|', [$userId, $blogId, $timestamp]);
    
    // Signatur über alle 3 Werte
    $signature = hash_hmac('sha256', $data, $secret);
    
    // Alles zusammenpacken + base64
    return base64_encode($data . '|' . $signature);
}