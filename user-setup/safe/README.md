# Sichere Dateihochladung - Dokumentation

## Übersicht

Die `Memy_Safe_Upload` Klasse bietet eine sichere Hochladefunktion für benutzer-spezifische Dateien. Der Zugriff auf Dateien ist nur für authentifizierte Benutzer über PHP möglich, nicht via direkter URL.

---

## Ordnerstruktur & Sicherheit

### Physische Struktur

```
wp-content/uploads/safe-data/                    # Basis-Ordner
├── .htaccess                                     # Blockiert direkten HTTP-Zugriff
├── index.php                                     # Verhindert Directory Listing
│
├── user-1/                                       # Benutzer-spezifischer Ordner
│   ├── .htaccess                                 # Blockiert direkten Zugriff
│   ├── index.php                                 # Verhindert Directory Listing
│   └── files/
│       ├── index.php
│       ├── dokument_1.pdf
│       ├── bild_1.jpg
│       └── ...
│
├── user-2/
│   ├── .htaccess
│   ├── index.php
│   └── files/
│       ├── index.php
│       └── ...
│
└── user-N/
    └── ...
```

### Sicherheitsebenen

#### 1. **.htaccess-Regeln** (Apache)
```apache
# Blockiert direkten HTTP-Zugriff auf alle Dateien
<FilesMatch ".*">
    Deny from all
</FilesMatch>

# Deaktiviert PHP-Ausführung
<IfModule mod_php.c>
    php_flag engine off
</IfModule>

# Verhindert Directory Listing
Options -Indexes
```

**Effekt:** Dateien unter `wp-content/uploads/safe-data/` können nicht direkt via URL abgerufen werden.

#### 2. **index.php-Dateien**
```php
<?php // Datei-Schutz
```
Verhindert unerwartetes Directory Listing bei Konfigurationsproblemen.

#### 3. **Berechtigungen (Unix/Linux)**
```bash
chmod 700 /path/to/user-X/        # Nur Owner (Webserver) hat Zugriff
chmod 600 /path/to/user-X/file    # Nur Lesebar für Owner
```

#### 4. **WordPress-Layer**
- Benutzer-Authentifizierung erforderlich
- Benutzer können nur eigene Dateien abrufen
- Path Traversal Prevention: `realpath()` Validierung

---

## API-Referenz

### Initialisierung

```php
// Ordnerstruktur beim Login automatisch erstellen (bereits eingebaut)
Memy_Safe_Upload::create_user_folder( $user_id );
```

### Datei hochladen

```php
$result = Memy_Safe_Upload::upload_file(
    $_FILES['fieldname'],           // $_FILES Array
    $user_id,                       // Optional: Benutzer-ID (default: aktueller Benutzer)
    ['image/jpeg', 'image/png'],    // Erlaubte MIME-Typen (optional)
    5 * 1024 * 1024                 // Max. Größe in Bytes (optional)
);

if ( is_wp_error( $result ) ) {
    echo $result->get_error_message();
} else {
    echo 'Datei-ID: ' . $result['file_id'];
    echo 'Dateiname: ' . $result['original_name'];
}
```

### Dateien auflisten

```php
$files = Memy_Safe_Upload::get_user_files( $user_id );

foreach ( $files as $file ) {
    echo $file['original_name'];      // Ursprünglicher Name
    echo $file['stored_name'];        // Gespeicherter Name
    echo $file['file_size'];          // Größe in Bytes
    echo $file['upload_date'];        // Datum
    echo $file['mime_type'];          // MIME-Typ
}
```

### Datei herunterladen

```php
// Sichere Download mit Zugriffsprüfung
Memy_Safe_Upload::download_file( 'dateiname.pdf' );

// Der Download wird direkt ausgeführt mit korrekten Headers
// (Content-Type: application/octet-stream, etc.)
```

### Datei löschen

```php
$result = Memy_Safe_Upload::delete_file( 'dateiname.pdf' );

if ( is_wp_error( $result ) ) {
    echo 'Fehler: ' . $result->get_error_message();
}
```

---

## Sicherheitsmerkmale

| Feature | Beschreibung |
|---------|------------|
| **Authentifizierung** | Nur angemeldete Benutzer können Dateien hochladen/abrufen |
| **Zugriffskontrolle** | Jeder Benutzer kann nur seine eigenen Dateien abrufen |
| **Path Traversal Protection** | `realpath()` Validierung verhindert `../` Angriffe |
| **MIME-Type Validierung** | Nur erlaubte Dateitypen werden akzeptiert |
| **Dateigröße-Limit** | Maximale Upload-Größe konfigurierbar |
| **Eindeutige Dateinamen** | Automatische Umbenennung bei Duplikaten |
| **Keine direkten URLs** | .htaccess blockiert HTTP-Zugriff |
| **Keine Script-Ausführung** | Hochgeladene Dateien können nicht ausgeführt werden |
| **Metadaten in User-Meta** | Alle Dateien sind mit Benutzer verknüpft |

---

## Praktische Beispiele

### Beispiel 1: Upload-Formular mit Validierung

```php
if ( is_user_logged_in() && isset( $_POST['upload_file'] ) ) {
    $result = Memy_Safe_Upload::upload_file(
        $_FILES['document'],
        null,
        ['application/pdf', 'image/jpeg'],
        10 * 1024 * 1024 // 10 MB
    );

    if ( is_wp_error( $result ) ) {
        echo '<div class="alert alert-danger">' . $result->get_error_message() . '</div>';
    } else {
        echo '<div class="alert alert-success">Datei erfolgreich hochgeladen!</div>';
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="document" accept=".pdf,.jpg" required>
    <button type="submit" name="upload_file">Hochladen</button>
</form>
```

### Beispiel 2: Datei-Verwaltungs-Seite

```php
if ( is_user_logged_in() ) {
    $files = Memy_Safe_Upload::get_user_files();

    echo '<h3>Meine Dateien</h3>';
    echo '<table class="table">';

    foreach ( $files as $index => $file ) {
        echo '<tr>';
        echo '<td>' . esc_html( $file['original_name'] ) . '</td>';
        echo '<td>' . size_format( $file['file_size'] ) . '</td>';
        echo '<td>';
        echo '<a href="?download=' . $index . '" class="btn btn-sm btn-primary">Download</a>';
        echo '<a href="?delete=' . $index . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Wirklich löschen?\')">Löschen</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}
```

### Beispiel 3: REST-API Integration

```php
// GET /wp-json/memy/v1/files - Alle Dateien auflisten
register_rest_route( 'memy/v1', '/files', array(
    'methods'             => 'GET',
    'callback'            => function() {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authenticated', 'Not authenticated', array( 'status' => 403 ) );
        }

        $files = Memy_Safe_Upload::get_user_files();
        return wp_rest_ensure_response( $files );
    },
    'permission_callback' => '__return_true',
) );

// POST /wp-json/memy/v1/files/upload - Datei hochladen
register_rest_route( 'memy/v1', '/files/upload', array(
    'methods'             => 'POST',
    'callback'            => function( WP_REST_Request $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authenticated', 'Not authenticated', array( 'status' => 403 ) );
        }

        // $_FILES['file'] wird vom REST-Handler bereitgestellt
        $files = $request->get_file_params();
        $result = Memy_Safe_Upload::upload_file( $files['file'] );

        return wp_rest_ensure_response( $result );
    },
    'permission_callback' => '__return_true',
) );
```

---

## Konfiguration

### Erlaubte MIME-Typen erweitern

Bearbeite die Methode `get_allowed_mimes()`:

```php
private static function get_allowed_mimes() {
    return array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'pdf'          => 'application/pdf',
        'doc|docx'     => 'application/msword',
        'xls|xlsx'     => 'application/vnd.ms-excel',
        'txt'          => 'text/plain',
        'zip'          => 'application/zip',  // Neu
        'mp4'          => 'video/mp4',         // Neu
    );
}
```

### Custom Ordner-Pfad

```php
// Wenn du einen anderen Ordner verwenden möchtest, überschreibe diese Methode
public static function get_base_path() {
    return WP_CONTENT_DIR . '/my-secure-uploads';
}
```

---

## Server-Konfiguration

### Apache (.htaccess bereits vorhanden)
Stelle sicher, dass `mod_rewrite` aktiviert ist:
```bash
a2enmod rewrite
systemctl restart apache2
```

### Nginx (Falls vorhanden)

```nginx
location ~* ^/wp-content/uploads/safe-data/ {
    deny all;
}
```

### PHP-INI (Optional, aber empfohlen)

```ini
upload_max_filesize = 50M
post_max_size = 50M
```

---

## Fehlerbehebung

| Problem | Lösung |
|---------|--------|
| **Upload schlägt fehl** | Prüfe Ordner-Berechtigungen: `chmod 755 wp-content/uploads` |
| **Dateien nicht sichtbar** | Prüfe Benutzer-Authentifizierung und Benutzer-ID |
| **Download nicht möglich** | Prüfe .htaccess und PHP-Handler |
| **Path Traversal Fehler** | Aktualisiere PHP-Version (min. 7.2) |

---

## Weitere Sicherheitsempfehlungen

1. **Regelmäßige Backups**: Auch sichere Dateien sollten gesichert werden
2. **Virenscan**: Für sensitive Umgebungen zusätzlich Antivirus integrieren
3. **Audit-Logging**: Alle Downloads/Löschungen protokollieren
4. **2FA**: Für kritische Dateien zusätzliche Authentifizierung
5. **Dateiencryption**: Optional: Dateien verschlüsselt speichern

---

## Support

Bei Fragen oder Problemen: [Support kontaktieren]
