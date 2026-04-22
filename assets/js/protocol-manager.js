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
         * View Protocol
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
                    const nr         = cells[0].textContent.trim();
                    const datum      = cells[1].textContent.trim();
                    const aktivitaet = cells[2].textContent.trim();
                    const status     = cells[3].textContent.trim();

                    // Popup-HTML aufbauen
                    const popupHTML = `
                        <div class='info-popup-wrap activitaeten-info'>
                            <div class='close-btn'><i class='bi bi-x-lg'></i></div>
                            <div class='content'>
                                <p class='title'><strong>Protokoll Details</strong></p>
                                <p><strong>Nr.:</strong>${nr}</p>
                                <p><strong>Datum:</strong>${datum}</p>
                                <br>
                                <p><strong>Aktivität:</strong><br>${aktivitaet}</p>
                                <br>
                                <p><strong>Status:</strong><br>${status}</p>
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
    });
})(jQuery);