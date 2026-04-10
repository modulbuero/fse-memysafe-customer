(($) => {
	$(document).ready(()=>{    
        /**
         * KUNDEN: LOAD DATA VIA AJAX AND FILL FORM
         */
        function loadKundenForm(kunden_id = 'new') {
            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                kunden_id: kunden_id
            };

            wp.ajax.post('load_kunden_data', formData)
                .done(function(response) {
                    // Formular anzeigen
                    //$('#setup-kunden-new').slideDown(200);
                    // Felder füllen
                    $('#kunden-name').val(response.name);
                    $('#kunden-email').val(response.email);
                    $('#kunden-tel').val(response.tel);
                    $('#kunden-firma').val(response.firma);
                    $('#kunden-status').val(response.status);
                    
                    // Aktuelle ID speichern im div
                    $('.setup-kunden-data').attr('id', 'setup-kunden-' + response.kunden_id);
                    
                    // Delete Button nur bei Bearbeitung anzeigen
                    if (response.kunden_id === 'new') {
                        $('#delete-kunden').hide();
                    } else {
                        $('#delete-kunden').show();
                    }
                    
                    // Zur Formular-Position scrollen
                    //$('html, body').animate({ scrollTop: $('.setup-kunden-data').offset().top - 100 }, 300);
                }).fail(function(response) {
                    console.log('Fehler beim Laden der Daten:', response);
                });
        }

        /**
         * KUNDEN: ADD BUTTON - Show Form
         */
        $(document).on('click', '#btn-add-kunden', function(e) {
            e.preventDefault();
            loadKundenForm('new');
        });

        /**
         * KUNDEN: EDIT FROM LIST - Show Form
         */
        $(document).on('click', '.kunden-person-mail', function(e) {
            e.preventDefault();
            let kunden_id = $(this).attr('data-kunden');
            loadKundenForm(kunden_id);
        });

        /**
         * KUNDEN: SAVE/EDIT
         */
        $(document).on('click', '#save-kunden', function(e) {
            e.preventDefault();
            let $container   = $(this).closest('.setup-kunden-data');
            let kunden_id = $container.attr('id').replace('setup-kunden-','');
            let name         = $('#kunden-name').val();
            let email        = $('#kunden-email').val();
            let tel          = $('#kunden-tel').val();
            let firma        = $('#kunden-firma').val();
            let status       = $('#kunden-status').val();

            // Validierung
            if (name.trim() === '' || email.trim() === '') {
                showMessage('Name und E-Mail sind erforderlich');
                return;
            }

            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                kunden_id: kunden_id,
                name:   name,
                email:  email,
                tel:    tel,
                firma:  firma,
                status: status
            };

            wp.ajax.post('handle_save_kunden', formData)
                .done(function(response) {
                    showMessage(response.message, 'success');
                    reloadKundenContainer();
                    closeKundenForm();
                }).fail(function(response) {
                    showMessage('Fehler beim Speichern:' + response.debug);
                });
        });

        /**
         * KUNDEN: DELETE
         */
        $(document).on('click', '#delete-kunden', function(e) {
            e.preventDefault();

            let $container = $(this).closest('.setup-kunden-data');
            let kunden_id  = $container.attr('id').replace('setup-kunden-','');

            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                kunden_id: kunden_id
            };

            wp.ajax.post('handle_delete_kunden', formData)
                .done(function(response) {
                    console.log('Kunde gelöscht:', response);
                    reloadKundenContainer();
                    closeKundenForm();
                }).fail(function(response) {
                    console.log('Fehler beim Löschen:', response);
                });
        });

        /**
         * KUNDEN: Close Form Helper
         */
        function closeKundenForm() {
            // Felder leeren
            $('#kunden-name').val('');
            $('#kunden-email').val('');
            $('#kunden-tel').val('');
            $('#kunden-firma').val('');
            $('#kunden-status').val('Aktiv');
            // ID zurücksetzen
            $('.setup-kunden-data').attr('id','setup-kunden-new');
            $('#goback').click()
        }

        /**
         * KUNDEN: Reload Container nach Speichern
         */
        function reloadKundenContainer() {
            $('#kunden-section').load(location.href + ' #kunden-section .settings-labels');
        }
    })
})(jQuery)
