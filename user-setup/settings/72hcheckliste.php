<?php 
/**
 * Title: 72h Checkliste 
 * Nutzt die MemyOptionManager zum speichern
 */
?>

<div class="spalte inner-main-heading">
    <h3><i class='mmsi-icon fertig'></i> 72h Orientierung — Hilf deinen Helfern</h3>
</div>

<div class="overflow-wrapper full-height checklist-bearbeiten-wrap">
    <p>
        Wenn du nicht erreichbar bist, müssen andere möglicherweise in deinem Sinne handeln. Dafür ist MMSI gedacht. Damit das gelingt, brauchen deine Helfer Orientierung.
    </p>
    <p>
        Du hast bereits zahlreiche Informationen in MMSI hinterlegt. Diese werden deinen Notfallkontakten im Ernstfall als Nachricht angezeigt. Diese Liste hilft dabei, diesen Informationen Struktur zu geben. Denn sie hilft deinen Helfern dabei zu verstehen, was wichtig ist, wo sie anfangen sollen und worauf sie achten müssen.
    </p>
    <p>
        Je besser die Informationen vorbereitet sind, desto schneller können andere in deinem Sinne handeln.
        Geh die Liste in Ruhe durch und halte sie aktuell. MMSI erinnert dich regelmäßig daran.
    </p>
    <p>
        Unterstütze deine Notfallkontakte, indem du diese Liste durchgehst. Je vollständiger dein Safe ist, desto handlungsfähiger sind deine Kontakte im Ernstfall.
    </p>

    <h4>KONTAKTE UND ROLLEN</h4>
    <p>
        Sind die Rollenverteilungen für deine Notfallkontakte, Vertrauenspersonen und Vertretungen noch aktuell?
    </p>
    <?php
    wp_nonce_field('user_data_nonce', '_wpnonce');
    $user_id = get_current_user_id();
    
    //Kontakt_Rolle
    $get_rolle_a = get_user_meta($user_id, 'checkliste-rolla-a', true) ?? '0';
    $get_rolle_b = get_user_meta($user_id, 'checkliste-rolla-b', true) ?? '0';
    $get_rolle_c = get_user_meta($user_id, 'checkliste-rolla-c', true) ?? '0';

    addCheckbox('Notfallkontakte sind hinterlegt und aktuell', $get_rolle_a, 'checkliste-rolla-a', 'simple');
    addCheckbox('Vertrauenspersonen sind hinterlegt und wissen von ihrer Rolle', $get_rolle_b, 'checkliste-rolla-b', 'simple');
    addCheckbox('Vertretungen sind für relevante Projekte definiert', $get_rolle_c, 'checkliste-rolla-c', 'simple');
    ?>


    <h4>SAFE UND ZUGRIFF</h4>
    <p>
        Sind die Angaben in deinem Safe aktuell und hast du folgende Informationen hinterlegt?
    </p>

    <?php
    $get_safe_a = get_user_meta($user_id, 'checkliste-safe-a', true) ?? '0';
    $get_safe_b = get_user_meta($user_id, 'checkliste-safe-b', true) ?? '0';
    $get_safe_c = get_user_meta($user_id, 'checkliste-safe-c', true) ?? '0';
    $get_safe_d = get_user_meta($user_id, 'checkliste-safe-d', true) ?? '0';
    addCheckbox('Wichtige Unterlagen — Ort und Ablage sind beschrieben', $get_safe_a, 'checkliste-safe-a', 'simple');
    addCheckbox('Passworthinweise — nicht die Passwörter selbst, sondern wo sie zu finden sind', $get_safe_b, 'checkliste-safe-b', 'simple');
    addCheckbox('Gerätezugänge — wie andere auf Computer, Smartphone oder Tablet zugreifen könnent', $get_safe_c, 'checkliste-safe-c', 'simple');
    addCheckbox('Ersatzschlüssel — wer einen hat oder wo sie hinterlegt sind', $get_safe_d, 'checkliste-safe-d', 'simple');
    ?>
        
    <h4>PROJEKTE UND KOMMUNIKATION</h4>
    <p>
        Ist für deine Notfallkontakte ersichtlich, ob Kunden informiert werden müssen, wie Projekte fortzuführen sind und ob es wichtige Termine oder Fristen gibt?
    </p>
    <?php
    $get_projekte_a = get_user_meta($user_id, 'checkliste-projekte-a', true) ?? '0';
    $get_projekte_b = get_user_meta($user_id, 'checkliste-projekte-b', true) ?? '0';
    $get_projekte_c = get_user_meta($user_id, 'checkliste-projekte-c', true) ?? '0';
    $get_projekte_d = get_user_meta($user_id, 'checkliste-projekte-d', true) ?? '0';
    $get_projekte_e = get_user_meta($user_id, 'checkliste-projekte-e', true) ?? '0';
    addCheckbox('Kunden — wer informiert werden muss und wie', $get_projekte_a, 'checkliste-projekte-a', 'simple');
    addCheckbox('Projekte — aktueller Stand und nächste Schritte', $get_projekte_b, 'checkliste-projekte-b', 'simple');
    addCheckbox('Kommunikation — wie mit eingehenden Anfragen umgegangen werden soll', $get_projekte_c, 'checkliste-projekte-c', 'simple');
    addCheckbox('Termine — kurzfristige Deadlines oder vereinbarte Gespräche', $get_projekte_d, 'checkliste-projekte-d', 'simple');
    addCheckbox('Automatische Buchungen und Zahlungen — was läuft im Hintergrund weiter', $get_projekte_e, 'checkliste-projekte-e', 'simple');
    ?>

    <h4>FINANZEN UND ABSICHERUNG</h4>
    <p>
        Sind die wichtigsten finanziellen Informationen und Vorsorgedokumente hinterlegt?
    </p>
    <?php
    $get_finanzen_a = get_user_meta($user_id, 'checkliste-finanzen-a', true) ?? '0';
    $get_finanzen_b = get_user_meta($user_id, 'checkliste-finanzen-b', true) ?? '0';
    $get_finanzen_c = get_user_meta($user_id, 'checkliste-finanzen-c', true) ?? '0';
    addCheckbox('Bankverbindungen und Vollmachten sind dokumentiert', $get_finanzen_a, 'checkliste-finanzen-a', 'simple');
    addCheckbox('Versicherungen sind erfasst — mit Ansprechpartnern', $get_finanzen_b, 'checkliste-finanzen-b', 'simple');
    addCheckbox('Vorsorgedokumente sind auffindbar — Patientenverfügung, Vorsorgevollmacht, Testament', $get_finanzen_c, 'checkliste-finanzen-c', 'simple');
    ?>

    <h4>PERSÖNLICHE HINWEISE</h4>
    <p>
        Möchtest du noch weitere Angaben machen, die dein persönliches Umfeld betreffen? Müssen Angehörige informiert werden, gibt es Haustiere, die versorgt werden müssen, oder andere Dinge, die unbedingt beachtet werden sollten? Schreib sie hier auf.
    </p>
    <?php
    $get_persoenliches_a = get_user_meta($user_id, 'checkliste-persoenliches-a', true) ?? '0';
    $get_persoenliches_b = get_user_meta($user_id, 'checkliste-persoenliches-b', true) ?? '0';
    $get_persoenliches_c = get_user_meta($user_id, 'checkliste-persoenliches-c', true) ?? '0';
    $get_persoenliches_d = get_user_meta($user_id, 'checkliste-persoenliches-d', true) ?? '0';
    $get_persoenliches_e = get_user_meta($user_id, 'checkliste-persoenliches-e', true) ?? '0';
    addCheckbox('Haustiere — Versorgung und Ansprechperson', $get_persoenliches_a, 'checkliste-persoenliches-a', 'simple');
    addCheckbox('Medikamente oder laufende Behandlungen', $get_persoenliches_b, 'checkliste-persoenliches-b', 'simple');
    addCheckbox('Kinder oder pflegebedürftige Angehörige', $get_persoenliches_c, 'checkliste-persoenliches-c', 'simple');
    addCheckbox('Fahrzeuge, geplante Reisen oder offene Verpflichtungen', $get_persoenliches_d, 'checkliste-persoenliches-d', 'simple');
    addCheckbox('Alles andere, das wichtig ist und woanders keinen Platz hat', $get_persoenliches_e, 'checkliste-persoenliches-e', 'simple');
    ?>

    <h4>VORBEREITUNG DEINER HELFER</h4>
    <p>
        Hast du mit deinem Notfallkontakt und deiner Vertrauensperson bereits persönlich über MMSI gesprochen?
    </p>
    <?php
    $get_vorbereitung_a = get_user_meta($user_id, 'checkliste-vorbereitung-a', true) ?? '0';
    $get_vorbereitung_b = get_user_meta($user_id, 'checkliste-vorbereitung-b', true) ?? '0';
    $get_vorbereitung_c = get_user_meta($user_id, 'checkliste-vorbereitung-c', true) ?? '0';
    addCheckbox('Ja, meine Helfer wissen über MMSI Bescheid.', $get_vorbereitung_a, 'checkliste-vorbereitung-a', 'simple');
    addCheckbox('Teilweise', $get_vorbereitung_b, 'checkliste-vorbereitung-b', 'simple');
    addCheckbox('Noch nicht', $get_vorbereitung_c, 'checkliste-vorbereitung-c', 'simple');
    ?>
</div>
<div class='save-wrapper'>
    <button id="einstellung-checkliste-save" class="short-button">
        <i class="mmsi-icon speichern"></i>Änderungen speichern
    </button>
</div>