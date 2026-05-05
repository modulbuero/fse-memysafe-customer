(($) => {
	$(document).ready(()=>{    
        /**
         * SAVE/EDIT Contact Information
         */
        $('.setup-contact-person-data button#save-contact').on('click', function(e) {
            e.preventDefault();
            let $container = $(this).closest('.setup-contact-person-data');
            let id         = $container.attr('id').replace('setup-contact-person-','');
            let name       = $container.find('#contact-name-'+id).val();
            let email      = $container.find('#contact-email-'+id).val();
            let tel        = $container.find('#contact-tel-'+id).val();
            let firma      = $container.find('#contact-firma-'+id).val();
            let typ        = $container.find('#contact-typ-'+id).val();
            let status     = $container.find('#contact-status-'+id).val();
            let mmsi_safe  = $container.find('#contact-mmsi_safe-'+id).val();
            //let is_main    = $container.find('#contact-hauptkontakt-'+id).is(':checked') ? 1 : 0;
            let mmsi_can   = $container.find('#contact-mmsi-can-'+id).is(':checked') ? 1 : 0;

            var formData = {
                _wpnonce:   ajax_object_contacts.nonce,
                contact_id: id,
                name:       name,
                email:      email,
                tel:        tel,
                firma:      firma,
                typ:        typ,
                status:     status,
                mmsi_safe:  mmsi_safe,
                //is_main:    is_main,
                mmsi_can:   mmsi_can
            };
            
            /**
             * @Todo: Ausgabe der Response in der UI
             * Erfolgsmeldung oder Fehlermeldung
             * Daten in Feldern aktualisieren feld.html(response)
             */
            wp.ajax.post('handle_update_contacts', formData)
            .done(function(response) {
                console.log(response)
                showMessage(response.message, 'success');
                $('#goback').click()
                reloadContactsListDashboard()
            }).fail(function(response) {
                console.log(response)
            });
        });

        /**
         * DELETE Contact Information
         */
        $('.setup-contact-person-data button#delete-contact').on('click', function(e) {
            e.preventDefault();
            let $container   = $(this).closest('.setup-contact-person-data');
            let id           = $container.attr('id').replace('setup-contact-person-','');
            let contact_name = $container.find('#contact-name-'+id).val();
            var formData = {
                _wpnonce:   ajax_object_contacts.nonce,
                contact_id: id,
                contact_name: contact_name
            };
            
            wp.ajax.post('handle_delete_contacts', formData)
            .done(function(response) {
                // Formularfelder leeren
                $container.find('input[type="text"]').val('');
                $container.find('input[type="email"]').val('');
                $container.find('input[type="number"]').val('');
                $container.find('input[type="checkbox"]').prop('checked', false);
                $container.find('select').val('');
                console.log(response);
                showMessage(response.message, 'success');
                reloadContactsListDashboard()
                $('#goback').click()
            }).fail(function(response) {
                console.log(response);
            });
            
        });

        function reloadContactsListDashboard() {
            // Nur den Inhalt des Dashboard-Widgets neu laden
            $('#memy-dashboard-my-contacts .item-content').load(location.href + ' #memy-dashboard-my-contacts .item-content > *');
        }
        /**
         * make Fields EDITABLE         
        $('.edit-inputs').on('click', function () {
            const $container = $(this).parent();
            const $inputs = $container.find('input');

            const isReadonly = $inputs.first().prop('readonly');

            $inputs.prop('readonly', !isReadonly);
        });
        */

        /**
         * Send Invitation zu Email
         */
        $('.send-invitation').on('click', function(e) {
            e.preventDefault();
            let $container   = $(this).closest('.setup-contact-person-data');
            let id           = $container.attr('id').replace('setup-contact-person-','');
            let contact_name = $container.find('#contact-name-'+id).val();
            let contact_mail = $container.find('#contact-email-'+id).val();

            var formData = {
                _wpnonce:     ajax_object_contacts.nonce,
                contact_id:   id,
                contact_mail: contact_mail,
                contact_name: contact_name
            };

            console.log(formData)

            wp.ajax.post('handle_send_contact_invitation', formData)
            .done(function(response) {
                console.log(response);
                showMessage(response.message || 'Einladung wurde versendet.', 'success');
            }).fail(function(response) {
                console.log(response);
                showMessage(response.message || 'Fehler beim Senden der Einladung.', 'error');
            });

        })
    })
})(jQuery)