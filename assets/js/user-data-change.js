(($) => {
	$(document).ready(()=>{    
        let userSaveBtn = '#user-data-save'
        $(document).on('click', userSaveBtn, function(e) {
            e.preventDefault();
            var formData = {
                _wpnonce:       ajax_object_userdata.nonce,
                first_name:     $('#first_name').val(),
                last_name:      $('#last_name').val(),
                strasze:        $('#strasze').val(),
                plz:            $('#plz').val(),
                ort:            $('#ort').val(),
                webseite:       $('#webseite').val(),
                description:    $('#description').val(),
                firmenname:     $('#firmenname').val(),
                telefon:        $('#telefon').val(),
                description:    $('#description').val(),
                berufsbezeichnung: $('#berufsbezeichnung').val(),
                
                /*
                user_email:     $('#user_email').val(),
                current_password:  $('#current_password').val(),
                new_password:      $('#new_password').val(),
                confirm_password:  $('#confirm_password').val(),
                display_name:      $('#display_name').val(),
                */
            };

            /*
            // Validierung
            if (formData.new_password && formData.new_password !== formData.confirm_password) {
                showMessage('Die Passwort-Bestätigung stimmt nicht überein.', 'error');
                return;
            }
            
            if (formData.new_password && !formData.current_password) {
                showMessage('Bitte geben Sie Ihr aktuelles Passwort ein.', 'error');
                return;
            }
            */

            var $button = $(userSaveBtn);
            $button.prop('disabled', true);
            $('#loading').show();

            wp.ajax.post('handle_update_user_data', formData)
            .done(function(response) {
                $('#loading').hide();
                $button.prop('disabled', false);
                // Erfolg: Nachricht anzeigen und 'success' Klasse nutzen
                showMessage(response.message, 'success');
            }).fail(function(response) {
                $('#loading').hide();
                $button.prop('disabled', false);
                // Fehler: Nachricht anzeigen (Fallback falls response.message fehlt)
                var errorMsg = response.message || 'Ein Fehler ist aufgetreten.';
                showMessage(errorMsg, 'error');
            });
        });
        

        //Speicher Darstellungen
        $(document).on('click', '#einstellung-darstellung-save', function(e) {
            e.preventDefault();
            var $button = $(this);
            const selectedClockstyle = $('input[name="clockstyle"]:checked').val();
            
            var formData = {
                _wpnonce:       ajax_object_userdata.nonce,
                opt_darkmode:   $('#opt_darkmode').is(':checked') ? 1 : 0,
                clockstyle:     selectedClockstyle,
            };
            
            $button.prop('disabled', true);
            $('#loading').show();
            
            wp.ajax.post('handle_update_settings_darstellung', formData)
            .done(function(response) {
                $('#loading').hide();
                console.log(response)
                $button.prop('disabled', false);
                showMessage(response.message, 'success');
                if(response.darkmode == "1"){
                    $("body").addClass("darkmode")
                }else{
                    $("body").removeClass("darkmode")
                }
            }).fail(function(response) {
                $('#loading').hide();
                console.log(response)
                $button.prop('disabled', false);
                var errorMsg = response.message || 'Ein Fehler ist aufgetreten.';
                showMessage(errorMsg, 'error');
            });
        })
    })
})(jQuery)