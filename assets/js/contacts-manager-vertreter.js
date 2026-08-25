(($) => {
	$(document).ready(()=>{    
        /**
         * VERTRETER: LOAD DATA VIA AJAX AND FILL FORM
         */
        function loadVertreterForm(vertreter_id = 'new') {
            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                vertreter_id: vertreter_id
            };

            wp.ajax.post('load_vertreter_data', formData)
                .done(function(response) {
                    // Formular anzeigen
                    // Felder füllen
                    $('#vertreter-name').val(response.name);
                    $('#vertreter-email').val(response.email);
                    $('#vertreter-tel').val(response.tel);
                    $('#vertreter-firma').val(response.firma);
                    $('#vertreter-status').val(response.status);
                    
                    // Aktuelle ID speichern im div
                    let containerId = response.vertreter_id === 'new' ? '#setup-vertreter-new' : '#setup-vertreter-' + response.vertreter_id;
                    $('.setup-vertreter-data').attr('id', 'setup-vertreter-' + response.vertreter_id );
                    
                    // Delete Button nur bei Bearbeitung anzeigen
                    if (response.vertreter_id === 'new') {
                        $(containerId).find('.save-wrapper button').prop('disabled', true);
                    } else {
                        $(containerId).find('.save-wrapper .delete-btn-pop').prop('disabled', false);
                    }

                    /**
                     *  Set SaveButton free if changes are made in the form
                     */
                    let projectWrap = containerId
                    $(projectWrap).on('input change', 'input', function() {
                        $(projectWrap + ' #save-vertreter').prop('disabled', false);
                        if($(this).val().trim() === '') {
                            $(projectWrap + ' #save-vertreter').prop('disabled', true);
                        }
                    });

                    
                }).fail(function(response) {
                    console.log('Fehler beim Laden der Daten:', response);
                    //alert('Fehler beim Laden der Daten');
                });
        }

        /**
         * VERTRETER: ADD BUTTON - Show Form
         */
        $(document).on('click', '#btn-add-vertreter', function(e) {
            e.preventDefault();
            loadVertreterForm('new');
        });

        /**
         * VERTRETER: EDIT FROM LIST - Show Form
         */
        $(document).on('click', '.vertreter-person-mail', function(e) {
            e.preventDefault();
            let vertreter_id = $(this).attr('data-vertreter');
            //console.log("Clicked vertreter ID: " + vertreter_id);
            loadVertreterForm(vertreter_id);
        });

        /**
         * VERTRETER: SAVE/EDIT
         */
        $(document).on('click', '#save-vertreter', function(e) {
            e.preventDefault();
            let $container   = $(this).closest('.setup-vertreter-data');
            let vertreter_id = $container.attr('id').replace('setup-vertreter-','');
            let name         = $('#vertreter-name').val();
            let email        = $('#vertreter-email').val();
            let tel          = $('#vertreter-tel').val();
            let firma        = $('#vertreter-firma').val();
            let status       = $('#vertreter-status').val();

            // Validierung
            if (name.trim() === '' || email.trim() === '') {
                console.log('Name und E-Mail sind erforderlich');
                return;
            }

            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                vertreter_id: vertreter_id,
                name:   name,
                email:  email,
                tel:    tel,
                firma:  firma,
                status: status
            };

            wp.ajax.post('handle_save_vertreter', formData)
                .done(function(response) {
                    console.log(response.debug);
                    showMessage(response.message, 'success');
                    reloadVertreterContainer();
                    closeVertreterForm();
                }).fail(function(response) {
                    console.log('Fehler beim Speichern:', response);
                });
        });

        /**
         * VERTRETER: DELETE
         */
        $(document).on('click', '#delete-vertreter', function(e) {
            e.preventDefault();
            
            let $container = $(this).closest('.setup-vertreter-data');
            let vertreter_id = $container.attr('id').replace('setup-vertreter-','');

            var formData = {
                _wpnonce: ajax_object_contacts.nonce,
                vertreter_id: vertreter_id
            };

            wp.ajax.post('handle_delete_vertreter', formData)
                .done(function(response) {
                    console.log('Vertreter gelöscht:', response);
                    reloadVertreterContainer();
                    closeVertreterForm();
                }).fail(function(response) {
                    console.log('Fehler beim Löschen:', response);
                });
        });

        /**
         * VERTRETER: Close Form Helper
         */
        function closeVertreterForm() {
            // Felder leeren
            $('#vertreter-name').val('');
            $('#vertreter-email').val('');
            $('#vertreter-tel').val('');
            $('#vertreter-firma').val('');
            $('#vertreter-status').val('Aktiv');
            // ID zurücksetzen
            $('.setup-vertreter-data').attr('id','setup-vertreter-new');
            $('.goback').click()
        }

        /**
         * VERTRETER: Reload Container nach Speichern
         */
        function reloadVertreterContainer() {
            $('#vertreter-section').load(location.href + ' #vertreter-section .settings-labels');
        }
    })
})(jQuery)