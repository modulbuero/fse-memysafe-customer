(function($){
    function saveOptionRadiobox(buttonID, fieldID, isID = true) {
        var buttonSelector = '#' + buttonID;
        var fieldSelector  = isID ? '#' + fieldID : '[name="' + fieldID + '"]';

        $(document).on('click', buttonSelector, function(e){
            e.preventDefault();

            var value = $(fieldSelector + ':checked').val();
            if (typeof value === 'undefined') {
                console.error('Feld nicht gefunden: ' + fieldSelector);
                return;
            }

            wp.ajax.post('memy_save_option', {
                nonce: memyOption.nonce,
                key: fieldID,
                value: value
            })
            .done(function(response){
                console.log(response)
                showMessage(response.message, 'success');
            })
            .fail(function(response){
                console.log(response)
                showMessage('Speichern fehlgeschlagen', 'fail');
            });
        });
    }

    function saveOptionCheckbox(fieldID, isID = true, resetButtonID = null) {
        var fieldSelector = isID ? '#' + fieldID : '[name="' + fieldID + '"]';

        $(document).on('change', fieldSelector, function(){
            var checked = $(this).is(':checked');
            var value = checked ? '1' : '0';

            wp.ajax.post('memy_save_option', {
                nonce: memyOption.nonce,
                key: fieldID,
                value: value
            })
            .done(function(response){
                console.log("saveOptionCheckbox")
                console.log(response)
                showMessage(response.message, 'success');
                setTimeout(function() {
                    location.reload();
                }, 500);
            })
            .fail(function(response){
                showMessage('Speichern fehlgeschlagen', 'fail');
            });
        });

        if (resetButtonID) {
            $(document).on('click', '#' + resetButtonID, function(e){
                e.preventDefault();
                if(resetButtonID == 'wieder-da-modus'){
                    wp.ajax.post('memy_save_option', {
                        nonce: memyOption.nonce,
                        key: fieldID,
                        value: '0'
                    })
                    .done(function(response){
                        showMessage('Modus zurückgesetzt', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    })
                    .fail(function(response){
                        showMessage('Fehler beim Zurücksetzen', 'fail');
                    });
                }
            });
        }
    }

    $(document).ready(function(){
        //Radio-Buttons
        saveOptionRadiobox('exam-clock-reset-option', 'examclock-reset', false);
        //Checkboxen
        saveOptionCheckbox('exam_clock_urlaubsmodus');
        saveOptionCheckbox('exam_clock_urlaubsmodus', true, 'wieder-da-modus');
    });

    //window.saveOptionValue = saveOptionValue;
})(jQuery);
