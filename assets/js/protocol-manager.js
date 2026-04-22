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
    });
})(jQuery);