(function($) {
    'use strict';

    $(document).ready(function() {
        console.log('Helpermodus JS geladen');
        $('#finish-helper-mode').on('click', function() {
            $.ajax({
                url: memy_ajax_object.ajax_url,
                method: 'POST',
                data: {
                    action: 'finish_helper_mode',
                    nonce: memy_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Helfer-Modus erfolgreich beendet!');
                        //$('#exam-clock-reset-button').click();
                        let userid = $('#memy-dashboard').data('user-id');
                        var formData = {
                            _wpnonce: ajax_object_deathman.nonce,
                            user_id: userid
                        };

                        wp.ajax.post('handle_update_exam_clock_reload', formData)
                        .done(function(response) {
                            location.reload();
                        }).fail(function(response) {
                            console.log(response)
                        });
                        
                    } else {
                        alert('Fehler beim Beenden des Helfer-Modus: ' + response.data);
                    }
                },
                error: function() {
                    alert('Ein Fehler ist aufgetreten. Bitte versuche es erneut.');
                }
            });
        });
    });
})(jQuery);
