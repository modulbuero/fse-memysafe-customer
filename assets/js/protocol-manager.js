(($) => {
    $(document).ready(() => {
        /**
         * SAVE Protocol
         */
        $('#save-protocol').on('click', function(e) {
            e.preventDefault();
            let aktivitaet = $('#protocol-aktivitaet').val();
            let status = $('#protocol-status').val();
            let editId = $(this).data('edit-id') || 0;

            if (!aktivitaet.trim()) {
                showMessage('Bitte geben Sie eine Aktivität ein.', 'error');
                return;
            }

            var formData = {
                _wpnonce: ajax_object_protocol.nonce,
                aktivitaet: aktivitaet,
                status: status
            };

            let action = 'handle_add_protocol';
            if (editId > 0) {
                formData.id = editId;
                action = 'handle_update_protocol';
            }

            wp.ajax.post(action, formData)
            .done(function(response) {
                console.log(response);
                showMessage(response.message, 'success');
                // Zurück zur Liste oder neu laden
                $('#goback').click();
                $('#protocol-list-container').load(location.href + ' #protocol-list-container table');

            }).fail(function(response) {
                console.log(response);
                showMessage(editId > 0 ? 'Fehler beim Aktualisieren der Aktivität.' : 'Fehler beim Speichern der Aktivität.', 'error');
            });
        });

        /**
         * Formular zurücksetzen, wenn ein neues Protokoll angelegt wird
         */
        $(document).on('click', '.dash-goto-btn[data-goto="setup-protocol-new"], #btn-add-protocol', function() {
            $('#protocol-aktivitaet').val('');
            $('#protocol-status').val('');
            $('#save-protocol').data('edit-id', 0).attr('data-edit-id', 0).html('<i class="mmsi-icon speichern"></i> SPEICHERN');
            $('#setup-protocol-new h3').text('HELFER-AKTIVITÄT');
        });

        /**
         * Protokoll BEARBEITEN
         */
        $(document).on('click', '.edit-protocol', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Verhindert das Auslösen des Zeilen-Popups

            const id = $(this).data('edit-id'); // Korrektur: 'data-edit-id' statt 'data-id' lesen
            const $row = $(this).closest('tr');
            
            // Daten aus den Tabellenzellen extrahieren (Index 2: Aktivität, Index 3: Status)
            const aktivitaet = $row.find('td').eq(2).text().trim();
            const status = $row.find('td').eq(3).text().trim();

            // Formularfelder befüllen
            $('#protocol-aktivitaet').val(aktivitaet);
            $('#protocol-status').val(status);
            
            // Button und Überschrift anpassen
            $('#save-protocol').data('edit-id', id).attr('data-edit-id', id);
            //$('#setup-protocol-new h3').text('HELFER-AKTIVITÄT BEARBEITEN');

            // Navigation zum Formular-Container auslösen
            $('.container-wrapper > div').removeClass('show');
            $('div[data-target="setup-protocol-new"]').addClass('show');
            $('#goback').attr('data-from', 'manage-protocol').attr('data-step', '2').removeClass('hide');
            $('.user-content').addClass('no-tiles');
        });

        
        /**
         * View Protocol
         * Todo: nach speichern einer Aktivität kein popup möglich
         */
        function viewAktivitaetInPopup(){
            const protocolRows = document.querySelectorAll('.clickable-row');
            
            protocolRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Verhindern, dass der Button klick das auslöst
                    if(e.target.closest('.edit-protocol')) {
                        return;
                    }

                    // Werte aus den TD auslesen
                    const cells      = this.querySelectorAll('td');
                    //const nr         = cells[0].textContent.trim();
                    const datum      = cells[0].textContent.trim();
                    const aktivitaet = cells[1].textContent.trim();
                    const status     = cells[2].textContent.trim();

                    // Popup-HTML aufbauen
                    const popupHTML = `
                        <div class='info-popup-wrap activitaeten-info'>
                            <div class='close-btn'><i class='bi bi-x-lg'></i></div>
                            <div class='content'>
                                <p><strong>Protokoll Details</strong></p>
                                <br>
                                <p><strong>Datum</strong><br>${datum}</p>
                                <br>
                                <p><strong>Aktivität</strong><br>${aktivitaet}</p>
                                <br>
                                <p><strong>Status</strong><br>${status}</p>
                            </div>
                        </div>
                    `;

                    // Popup container erstellen oder wiederverwenden
                    let popupContainer = document.getElementById('protocol-popup-container');
                    if(!popupContainer) {
                        popupContainer = document.createElement('div');
                        popupContainer.id = 'protocol-popup-container';
                        document.body.appendChild(popupContainer);
                    }

                    popupContainer.innerHTML = popupHTML;
                    const popup = popupContainer.querySelector('.info-popup-wrap');
                    popup.style.display = 'block';

                    // Close-Button Funktionalität
                    const closeBtn = popup.querySelector('.close-btn');
                    closeBtn.addEventListener('click', function() {
                        popup.style.display = 'none';
                    });

                    // Schließen beim Klick außerhalb
                    popup.addEventListener('click', function(e) {
                        if(e.target === this) {
                            this.style.display = 'none';
                        }
                    });
                });
            });
        }
        viewAktivitaetInPopup();

        /**
         * Reload Nachrichten
         */
        window.reloadNachrichtenDashboard = function(){
            $('#memy-dashboard-my-notifications .item-content').load(location.href + ' #memy-dashboard-my-notifications .item-content > *');
            
        }
    });
})(jQuery);