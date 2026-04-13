/**
 * Safe Upload AJAX Manager
 * 
 * Verwaltet Datei-Uploads via AJAX
 */

(function($) {
    'use strict';

    const SafeUpload = {

        /**
         * Initialisierung
         */
        init: function() {
            this.setupEventListeners();
            this.loadFileList();
            this.loadFileListShort();
        },

        /**
         * Event Listener registrieren
         */
        setupEventListeners: function() {
            // Upload-Button
            $(document).on('click', '.memy-upload-trigger', function(e) {
                e.preventDefault();
                $('#memy-file-input').click();
            });

            // Datei-Input ändern
            $(document).on('change', '#memy-file-input', function() {
                SafeUpload.handleFileSelect(this);
            });

            // Datei-Löschen, Confirm Popup
            $(document).on('click', '#delete-safe-file.delete-btn', function(e) {
                e.preventDefault();
                const fileName = $(this).data('file');
                SafeUpload.deleteFile(fileName);
            });

            // Datei-Download
            $(document).on('click', '.memy-file-download', function(e) {
                e.preventDefault();
                const fileName = $(this).data('file');
                SafeUpload.downloadFile(fileName);
            });

            // Datei-Öffnen
            $(document).on('click', '.memy-file-open', function(e) {
                e.preventDefault();
                const fileName = $(this).data('file');
                SafeUpload.openFile(fileName);
            });

            // Drag & Drop
            const dropZone = $('#memy-upload-zone');
            if (dropZone.length) {
                dropZone.on('dragenter dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('drag-over');
                });

                dropZone.on('dragleave drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('drag-over');
                });

                dropZone.on('drop', function(e) {
                    // preventDefault/stopPropagation wird bereits oben behandelt
                    const files = e.originalEvent.dataTransfer.files;
                    SafeUpload.handleFiles(files);
                });
            }
        },

        /**
         * Datei aus Input-Feld verarbeiten
         */
        handleFileSelect: function(input) {
            const files = input.files;
            this.handleFiles(files);
            // Input zurücksetzen
            $(input).val('');
        },

        /**
         * Mehrere Dateien verarbeiten
         */
        handleFiles: function(files) {
            for (let i = 0; i < files.length; i++) {
                this.uploadFile(files[i]);
            }
        },

        /**
         * Datei hochladen via AJAX
         */
        uploadFile: function(file) {
            const formData = new FormData();
            formData.append('action', 'memy_upload_file');
            formData.append('file', file);
            formData.append('nonce', memySafeUpload.uploadNonce);

            // Optional: MIME-Typen und Größe
            if ($('#memy-allowed-types').length) {
                formData.append('allowed_types', $('#memy-allowed-types').val());
            }
            if ($('#memy-max-size').length) {
                formData.append('max_size', $('#memy-max-size').val());
            }

            // Upload-Fortschritt vorbereiten
            const uploadId = 'upload-' + Date.now();
            this.showUploadProgress(uploadId, file.name);

            $.ajax({
                url: memySafeUpload.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    // Upload-Fortschritt
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            SafeUpload.updateProgress(uploadId, percentComplete);
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        SafeUpload.showNotification('Datei erfolgreich hochgeladen!', 'success');
                        SafeUpload.completeProgress(uploadId, true, SafeUpload.escapeHtml(file.name));
                        SafeUpload.loadFileList(); // Liste aktualisieren
                        SafeUpload.loadFileListShort();
                    } else {
                        SafeUpload.showNotification(response.data.message || 'Fehler beim Upload', 'error');
                        SafeUpload.completeProgress(uploadId, false, SafeUpload.escapeHtml(file.name));
                    }
                },
                error: function() {
                    SafeUpload.showNotification('AJAX-Fehler beim Upload', 'error');
                    SafeUpload.completeProgress(uploadId, false, SafeUpload.escapeHtml(file.name));
                }
            });
        },

        /**
         * Datei löschen via AJAX
         */
        deleteFile: function(fileName) {
            $.ajax({
                url: memySafeUpload.ajaxurl,
                type: 'POST',
                data: {
                    action: 'memy_delete_file',
                    file_name: fileName,
                    nonce: memySafeUpload.deleteNonce
                },
                success: function(response) {
                    console.log("response");
                    console.log(response);
                    if (response.success) {
                        showMessage(response.data.message, 'success');
                        SafeUpload.loadFileList(); // Liste aktualisieren
                        SafeUpload.loadFileListShort();
                    } else {
                        showMessage(response.data.message);
                    }
                },
                error: function() {
                    SafeUpload.showNotification('AJAX-Fehler beim Löschen', 'error');
                }
            });
        },

        /**
         * Datei herunterladen
         */
        downloadFile: function(fileName) {
            // Erstelle einen temporären Form für den Download
            const form = $('<form></form>')
                .attr('action', memySafeUpload.ajaxurl)
                .attr('method', 'POST')
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'action')
                    .val('memy_download_file'))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'file_name')
                    .val(fileName))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'mode')
                    .val('download'))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'nonce')
                    .val(memySafeUpload.downloadNonce));

            $('body').append(form);
            form.submit();
            form.remove();
        },

        /**
         * Datei in neuem Tab öffnen
         */
        openFile: function(fileName) {
            // Erstelle einen temporären Form für das Öffnen in neuem Tab
            const form = $('<form></form>')
                .attr('action', memySafeUpload.ajaxurl)
                .attr('method', 'POST')
                .attr('target', '_blank')
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'action')
                    .val('memy_download_file'))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'file_name')
                    .val(fileName))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'mode')
                    .val('open'))
                .append($('<input></input>')
                    .attr('type', 'hidden')
                    .attr('name', 'nonce')
                    .val(memySafeUpload.downloadNonce));

            $('body').append(form);
            form.submit();
            form.remove();
        },

        /**
         * Datei-Liste laden
         */
        loadFileList: function() {
            $.ajax({
                url: memySafeUpload.ajaxurl,
                type: 'POST',
                data: {
                    action: 'memy_get_files',
                    nonce: memySafeUpload.filesNonce
                },
                success: function(response) {
                    if (response.success) {
                        SafeUpload.renderFileList(response.data.files);
                    }
                }
            });
        },

        /**
         * Datei-Liste laden (Short - Max 3)
         */
        loadFileListShort: function() {
            $.ajax({
                url: memySafeUpload.ajaxurl,
                type: 'POST',
                data: {
                    action: 'memy_get_files',
                    nonce: memySafeUpload.filesNonce
                },
                success: function(response) {
                    if (response.success) {
                        const files = response.data.files.slice(0, 3);
                        SafeUpload.renderFileList(files, '#memy-file-list-short');
                    }
                }
            });
        },

        /**
         * Datei-Liste rendern
         */
        renderFileList: function(files, targetSelector='#memy-file-list') {
            const container = $(targetSelector);
            if (!container.length) {
                return;
            }

            if (files.length === 0) {
                container.html('<p class="memy-no-files">Keine Dateien vorhanden.</p>');
                return;
            }

            let html = '<ul class="memy-file-list">';

            files.forEach(function(file, index) {
                const fileSize = SafeUpload.formatFileSize(file.file_size);
                const uploadDate = new Date(file.upload_date).toLocaleDateString('de-DE');

                html += '<li class="memy-file-item dash-item">';
                html += '<div class="memy-file-info">';
                    html += '<span class="memy-file-name-wrap"><i class="mmsi-icon datei"></i><span class="memy-file-name">' + SafeUpload.escapeHtml(file.original_name) + '</span></span>';
                    html += '<span class="memy-file-size">(' + fileSize + ')</span>';
                    html += '<span class="memy-file-date">' + uploadDate + '</span>';
                    html += '<button class="memy-file-open" data-file="' + SafeUpload.escapeHtml(file.stored_name) + '"></button>';
                html += '</div>';
                html += '<div class="memy-file-actions">';                    
                    html += '<button class="memy-file-download" data-file="' + SafeUpload.escapeHtml(file.stored_name) + '"><i class="mmsi-icon download"></i></button>';
                    html += '<button class="delete-btn-pop" data-file="' + SafeUpload.escapeHtml(file.stored_name) + '"><i class="mmsi-icon delete"></i></button>';
                html += '</div>';
                html += '</li>';
            });

            html += '</ul>';
            container.html(html);
        },

        /**
         * Upload-Fortschritt anzeigen
         */
        showUploadProgress: function(uploadId, fileName) {
            const progressContainer = $('#memy-upload-progress');
            if (!progressContainer.length) {
                return;
            }

            const progressHtml = `
                <div class="memy-upload-item" id="${uploadId}">
                    <!--
                    <div class="memy-upload-name">${SafeUpload.escapeHtml(fileName)} <span class='upload-file-status'>hochgeladen</span></div>
                    -->
                    <div class="memy-upload-bar">
                        <div class="memy-upload-fill" style="width: 0%"></div>
                    </div>
                    <div class="memy-upload-percent">0%</div>
                </div>
            `;

            progressContainer.append(progressHtml);
        },

        /**
         * Upload-Fortschritt aktualisieren
         */
        updateProgress: function(uploadId, percent) {
            const item = $('#' + uploadId);
            if (item.length) {
                item.find('.memy-upload-fill').css('width', percent + '%');
                item.find('.memy-upload-percent').text(Math.round(percent) + '%');
            }
        },

        /**
         * Upload abgeschlossen
         */
        completeProgress: function(uploadId, success, filename = '') {

            const item = $('#' + uploadId);
            if (item.length) {
                item.addClass(success ? 'success' : 'error');
                setTimeout(function() {
                    item.fadeOut(function() {
                        $(this).remove();
                    });
                }, 2000);

                if(success == true) {
                    showMessage('Upload der Datei '+filename+' abgeschlossen!', 'success');
                } else {
                    showMessage('Upload fehlgeschlagen', 'fail');
                }
            }
        },

        /**
         * Benachrichtigung anzeigen
         * @deprecated showMessage() verwenden, da es bereits eine zentrale Funktion dafür gibt
         */
        showNotification: function(message, type) {
            const notification = $('<div></div>')
                .addClass('memy-notification')
                .addClass('memy-notification-' + type)
                .text(message);

            $('body').append(notification);

            setTimeout(function() {
                notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },

        /**
         * Dateigröße formatieren
         */
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        /**
         * HTML escapen (XSS-Schutz)
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }        
    };

    // Beim Document Ready initialisieren
    $(document).ready(function() {
        SafeUpload.init();
    });

})(jQuery);
