<?php
/**
 * Plugin Name: Multisite Cron Trigger
 * Description: Ruft automatisch jede Webseite im Multisite-Netzwerk 2x täglich auf, um WP-Cron zu triggern.
 * Version:     1.0.0
 * Author:      Your Name
 * Network:     true
 */

defined( 'ABSPATH' ) || exit;

class Multisite_Cron_Trigger {

    /**
     * Name des Cron-Events (Netzwerk-weit, nur auf Hauptseite registriert)
     */
    const CRON_HOOK = 'mct_trigger_all_sites';

    public function __construct() {
        // Cron-Intervall registrieren
        add_filter( 'cron_schedules', [ $this, 'add_twice_daily_schedule' ] );

        // Cron-Event einplanen (nur auf Hauptseite)
        add_action( 'init', [ $this, 'schedule_event' ] );

        // Callback für den Cron-Job
        add_action( self::CRON_HOOK, [ $this, 'trigger_all_sites' ] );

        // Deaktivierungs-Hook (MU-Plugins haben keinen Standard-Deaktivierungs-Hook,
        // daher nutzen wir Shutdown wenn das Plugin nicht mehr existiert)
        // register_shutdown_function( [ $this, 'maybe_unschedule' ] );
        
        // TEMPORÄR: korruptes Event aufräumen. nur für ein paar minuten aktivieren
		//add_action( 'init', [ $this, 'fix_cron_event' ] );

        error_log("Multisite_Cron_Trigger_Constructor");
    }

    /**
     * Fügt einen "zweimal täglich"-Zeitplan hinzu (falls wp_twice_daily nicht reicht)
     * Standard WP hat bereits 'twicedaily' – wir nutzen es direkt.
     */
    public function add_twice_daily_schedule( $schedules ) {
        // Nur zur Sicherheit hinzufügen, falls mmsi_twicedaily nicht vorhanden
        if ( ! isset( $schedules['mmsi_twicedaily'] ) ) {
            $schedules['mmsi_twicedaily'] = [
                #'interval' => 1 * HOUR_IN_SECONDS,
                'interval' => 15 * MINUTE_IN_SECONDS,
                'display'  => 'Alle 10 Minuten (Löst die Unterseiten-Crons aus)',
            ];
        }
        return $schedules;
    }

    /**
     * Plant das Cron-Event ein – nur auf der Hauptseite des Netzwerks.
     */
    public function schedule_event() {        
	    
	    $schedules = wp_get_schedules();
    error_log( '[MCT] mmsi_twicedaily vorhanden: ' . var_export( isset( $schedules['mmsi_twicedaily'] ), true ) );
    
    if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
        $result = wp_schedule_event( time(), 'mmsi_twicedaily', self::CRON_HOOK );
        error_log( '[MCT] wp_schedule_event Ergebnis: ' . var_export( $result, true ) );
    }
    
    error_log( '[MCT] wp_next_scheduled nach schedule: ' . var_export( wp_next_scheduled( self::CRON_HOOK ), true ) );

    }

    /**
     * Ruft jede aktive Seite im Netzwerk via HTTP auf.
     */
    public function trigger_all_sites() {
        if ( ! is_multisite() ) {
            return;
        }

		error_log('[Cron - trigger_all_sites_2]');
        
        $sites = get_sites( [
            'public'   => 1,      // nur öffentliche Seiten
            'archived' => 0,
            'deleted'  => 0,
            'spam'     => 0,
            'number'   => 500,    // ggf. erhöhen bei sehr großen Netzwerken
        ] );

        foreach ( $sites as $site ) {
            $this->ping_site( $site );
        }
    }

    /**
     * Sendet einen nicht-blockierenden HTTP-Request an eine Seite.
     *
     * @param WP_Site $site
     */
    private function ping_site( WP_Site $site ) {
	    
	    error_log("Multisite_Cron_Trigger_PING");
	    
        $url = trailingslashit( get_site_url( $site->blog_id ) );

        // Cron-Spawn-URL direkt ansprechen (zuverlässiger als die Startseite)
        $cron_url = $url . 'wp-cron.php';

        $args = [
            'timeout'   => 0.01,   // Nicht blockierend – Fire & Forget
            'blocking'  => false,
            'sslverify' => apply_filters( 'mct_sslverify', false, $site ),
            'headers'   => [
                'User-Agent' => 'WP Multisite Cron Trigger/1.0',
            ],
        ];

        /**
         * Filter: URL anpassen (z. B. interne IP statt Domain)
         *
         * @param string  $cron_url  Die aufzurufende URL
         * @param WP_Site $site      Das Site-Objekt
         */
        $cron_url = apply_filters( 'mct_cron_url', $cron_url, $site );

        $response = wp_remote_get( $cron_url, $args );

        // Logging (optional, nur wenn WP_DEBUG_LOG aktiv)
        error_log('[Cron - ping_site] Blog ID: ' . $site->blog_id);
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            if ( is_wp_error( $response ) ) {
                error_log( sprintf(
                    '[Multisite Cron Trigger] Fehler bei Site %d (%s): %s',
                    $site->blog_id,
                    $cron_url,
                    $response->get_error_message()
                ) );
            } else {
                error_log( sprintf(
                    '[Multisite Cron Trigger] Ping gesendet an Site %d (%s)',
                    $site->blog_id,
                    $cron_url
                ) );
            }
        }
    }

    /**
     * Cron-Event entfernen, wenn die Plugin-Datei nicht mehr existiert.
     * (Sicherheitsnetz für MU-Plugin-Deinstallation)
     */
    public function maybe_unschedule() {
	    
        if ( ! file_exists( __FILE__ ) ) {
            $timestamp = wp_next_scheduled( self::CRON_HOOK );
            if ( $timestamp ) {
	            error_log("Multisite_Cron_Trigger__unschedule");
                wp_unschedule_event( $timestamp, self::CRON_HOOK );
            }
        }
    }
    
    

	/**
	*	Temporär falls Cron defekt
	*/
	public function fix_cron_event() {
	    if ( ! is_main_site() ) return;
	    
	    $timestamp = wp_next_scheduled( self::CRON_HOOK );
	    error_log( '[MCT] wp_next_scheduled: ' . var_export( $timestamp, true ) );
	    
	    // Korruptes Event löschen und neu anlegen
	    wp_clear_scheduled_hook( self::CRON_HOOK );
	    wp_schedule_event( time(), 'mmsi_twicedaily', self::CRON_HOOK );
	    
	    $new_timestamp = wp_next_scheduled( self::CRON_HOOK );
	    error_log( '[MCT] Neuer Timestamp: ' . var_export( $new_timestamp, true ) );
	}
}

// Nur im Multisite-Kontext laden
if ( is_multisite() ) {
    new Multisite_Cron_Trigger();
}