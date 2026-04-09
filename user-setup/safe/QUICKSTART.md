# AJAX Upload - Schnelleinstieg

## 🚀 Quick Start

### 1. HTML-Template einfügen

```html
<!-- Nur für eingeloggte Benutzer -->
<?php if ( is_user_logged_in() ) : ?>
    <div id="memy-upload-zone" class="memy-upload-zone">
        <p>Dateien hier ablegen oder klicken zum Hochladen</p>
        <button type="button" class="memy-upload-trigger">Datei auswählen</button>
    </div>
    <input type="file" id="memy-file-input" multiple>
    <div id="memy-upload-progress"></div>
    <div id="memy-file-list"></div>
<?php endif; ?>
```

### 2. CSS einbinden

```html
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/scss/safe-upload.css">
```

### 3. Fertig! 

JavaScript wird automatisch geladen (enqueued in `enqueue-scripts.php`).

---

## 📋 Dateien in diesem Package

| Datei | Zweck |
|-------|--------|
| `safe-upload.php` | Core Upload-Klasse |
| `safe-upload-ajax.php` (functions/) | AJAX-Handler |
| `safe-upload.js` (assets/js/) | Frontend JavaScript |
| `safe-upload.css` (assets/scss/) | Styling |
| `AJAX-INTEGRATION.md` | Ausführliche Integration |
| `README.md` | Dokumentation |

---

## 🔧 Konfiguration

### MIME-Typen limitieren

In `safe-upload.php` in der Klasse:

```php
private static function get_allowed_mimes() {
    return array(
        'pdf'  => 'application/pdf',
        'jpg|jpeg' => 'image/jpeg',
        // Nur PDF und JPG erlauben
    );
}
```

### Dateigröße limitieren

Im HTML:
```html
<input type="hidden" id="memy-max-size" value="<?php echo 5 * 1024 * 1024; ?>"> <!-- 5MB -->
```

---

## 💻 JavaScript-API

```javascript
// Upload Statusänderung
SafeUpload.uploadFile(file);

// Datei-Liste neu laden
SafeUpload.loadFileList();

// Datei löschen
SafeUpload.deleteFile('dokument.pdf');

// Datei herunterladen
SafeUpload.downloadFile('dokument.pdf');

// Benachrichtigung
SafeUpload.showNotification('Erfolg!', 'success');
```

---

## 🔒 Sicherheit

- ✅ Nonce-Validierung
- ✅ Benutzer-Authentifizierung
- ✅ MIME-Type Validierung
- ✅ Dateigröße-Limit
- ✅ Path Traversal Prevention
- ✅ XSS-Schutz
- ✅ Dateien nicht direkt zugänglich

---

## 📊 AJAX Endpoints

```javascript
// GET Dateien
POST /wp-admin/admin-ajax.php?action=memy_get_files

// Upload
POST /wp-admin/admin-ajax.php?action=memy_upload_file
Body: FormData with file

// Delete
POST /wp-admin/admin-ajax.php?action=memy_delete_file
Body: { file_name, nonce }

// Download
POST /wp-admin/admin-ajax.php?action=memy_download_file
Body: { file_name, nonce }
```

---

## 🐛 Troubleshooting

| Problem | Lösung |
|---------|--------|
| Upload funktioniert nicht | Prüfe Ordner-Berechtigungen |
| JavaScript lädt nicht | Prüfe Browser Console |
| Dateien nicht sichtbar | Prüfe Benutzer-Login |
| 403 Forbidden | Prüfe .htaccess |

---

## 📁 Ordnerstruktur

```
wp-content/uploads/safe-data/
└── user-{ID}/
    └── files/
        ├── dokument.pdf
        ├── bild.jpg
        └── ...
```

Nur für angemeldete Benutzer zugänglich!

---

## ✨ Tipps

- Verwende Drag & Drop für bessere UX
- Zeige Upload-Fortschritt an
- Validiere Dateien auf Client-Seite
- Logge größere Uploads
- Backup kritischer Dateien

---

**Weitere Hilfe:** Siehe `AJAX-INTEGRATION.md` und `README.md`
