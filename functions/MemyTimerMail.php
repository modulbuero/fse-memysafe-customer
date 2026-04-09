<?php
/**
 * Title: Timer Mailer
 * Description: Prüft verzögerte Eskalationszeiten (3 Stufen) und sendet bei Erreichen eine Mail an contact-person-1.
 * Author: Modulbüro
 */

if (!defined('ABSPATH')) {
    exit;
}

class MemyTimerMail {
    const META_PREFIX = 'eskalation_stufe_';
    const META_SENT_SUFFIX = '_sent';

    public function __construct() {
        add_action('memy_safety_cron_hook', array($this, 'checkAndSend'));
    }

    /**
     * Prüft alle Benutzer mit Eskalationsdaten und sendet bei Erreichen eine Mail.
     */
    public function checkAndSend() {
        $now = new DateTime(current_time('Y-m-d H:i:s'));

        $users = get_users(array(
            'meta_query' => array(
                array(
                    'key'     => self::META_PREFIX . 'one',
                    'compare' => 'EXISTS',
                ),
            ),
            'fields' => 'ID',
        ));

        if (empty($users)) {
            return;
        }

        foreach ($users as $user_id) {
            $this->checkUserTimers((int) $user_id, $now);
        }
    }

    protected function checkUserTimers(int $user_id, DateTime $now) {
        $contact = get_user_meta($user_id, 'contact-person-1', true);
        $email   = !empty($contact['email']) ? sanitize_email($contact['email']) : '';

        if (empty($email) || !is_email($email)) {
            return;
        }

        foreach (['one', 'two', 'three'] as $stage) {
            $meta_key = self::META_PREFIX . $stage;
            $sent_key = $meta_key . self::META_SENT_SUFFIX;

            $date_string = get_user_meta($user_id, $meta_key, true);
            $sent_value  = get_user_meta($user_id, $sent_key, true);

            if (empty($date_string) || !empty($sent_value)) {
                continue;
            }

            $target = DateTime::createFromFormat('d.m.Y H:i', $date_string);
            if (!$target) {
                // Fallback: versuchen mit Sekunden
                $target = DateTime::createFromFormat('d.m.Y H:i:s', $date_string);
            }

            if (!$target) {
                continue;
            }

            if ($now >= $target) {
                $this->sendMail($user_id, $email, $stage, $target);
                update_user_meta($user_id, $sent_key, 1);
            }
        }
    }

    protected function sendMail(int $user_id, string $email, string $stage, DateTime $target) {
        $user = get_userdata($user_id);
        $username = $user ? $user->display_name : ('User #' . $user_id);

        $subject = sprintf('Eskalationsstufe %s erreicht bei %s', ucfirst($stage), $username);
        $message = sprintf(
            "Hallo,%s

bei Benutzer %s (%s) wurde die Eskalationsstufe %s erreicht.\n\nDatum/Zeit: %s\n\nBitte kontaktiere den Nutzer.",
            "",
            $username,
            get_site_url(),
            ucfirst($stage),
            $target->format('d.m.Y H:i')
        );

        wp_mail($email, $subject, $message);
    }
}

new MemyTimerMail();
