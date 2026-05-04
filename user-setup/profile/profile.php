<?php 
/**
 * Title: Benutzerprofil - Daten anzeigen und bearbeiten
 * Nutzt die MemyUserDataEditor zum speichern
 */

$current_user = wp_get_current_user();
$user_metas   = get_user_meta($current_user->ID);          
$telefon      = (!empty($user_metas['telefon'][0])) ? $user_metas['telefon'][0] : "";
$strasze      = (!empty($user_metas['strasze'][0])) ? $user_metas['strasze'][0] : "";
$plz          = (!empty($user_metas['plz'][0])) ? $user_metas['plz'][0] : "";
$ort          = (!empty($user_metas['ort'][0])) ? $user_metas['ort'][0] : "";
?>

<h3>Profil bearbeiten</h3>

<div class="overflow-wrapper full-height profile-bearbeiten-wrap">
    <?php
    wp_nonce_field('user_data_nonce', '_wpnonce'); 
    ?>
    <h4>Persönliche Daten</h4>

    <div class="spalte">
        <div id="profile-persoenliche-daten" class="three-quarters-width">

            <div>
                <h5 class="memy-label">Vorname und Nachname</h5>
                <div class="spalte">
                    <?php 
                    addInput('', $current_user->first_name, 'first_name', 'Vorname');
                    addInput('', $current_user->last_name, 'last_name', 'Nachname');
                    ?>
                </div>
            </div>

            <div>
                <h5 class="memy-label">Adresse</h5>
                <div class="settings-labels">
                    <?php 
                    addInput('', $strasze, 'strasze', 'Straße');
                    addInput('', $plz, 'plz', 'PLZ', 'number');
                    addInput('', $ort, 'ort', 'Ort');
                    ?>
                </div>
            </div>

            <div>
                <h5 class="memy-label">Telefonnummer</h5>
                <?php 
                addInput('', $telefon, 'telefon', 'Telefonnummer');
                ?>
            </div>

            <div>
                <h5>E-Mail-Adresse</h5>
                <div><?php echo $current_user->user_email; ?></div>
                <?php 
                #addInput('', $current_user->user_email, 'user_email', 'E-Mail', 'hidden');
                ?>
            </div>
        </div>

        <div id="profile-persoenliche-daten-imgs">

        </div>
    </div>

    <!--    
    <div>
        <h4>Passwort ändern (optional):</h4>
        <label for="current_password">Aktuelles Passwort:</label>
        <input type="password" id="current_password" name="current_password" />
    </div>
    
    <div>
        <label for="new_password">Neues Passwort:</label>
        <input type="password" id="new_password" name="new_password" />
    </div>
    
    <div>
        <label for="confirm_password">Neues Passwort bestätigen:</label>
        <input type="password" id="confirm_password" name="confirm_password" />
    </div>
    
    <div>
        <h4>Abo Modell:</h4>
        <label for="abo_model">Abo Modell:</label>
        <input type="text" id="abo_model" name="abo_model" value="<?php #echo esc_attr(ucwords(str_replace("_", " ", substr($abo, 4)))); ?>" disabled/>
    </div>
    -->
</div>

<div class="spalte">
    <button id="user-data-save"><i class="mmsi-icon speichern"></i>Speichern</button>
    <span id="loading" style="display: none;">Speichere...</span>
</div>