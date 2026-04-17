(($) => {
	$(document).ready(()=>{
        saveFirstSettings()
        setStepsFreeByCheckbox('#mmsi-verstanden')
        nextStepButton()
        hasInputValues('adresse')
        setStepsFreeByCheckbox('#mmsi-uploadcheck')
        hasInputValues('kontakt')
        
	});

    function saveFirstSettings(){
        $('#save-first-settings').on('click', function(event) {
            event.preventDefault();

            const nonce = $('#fsettingn-wp').val();

            $.ajax({
                url: memyFirstSettingsAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'save_first_settings_meta',
                    nonce: nonce,
                    first_settings: 'done'
                },
                success(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        console.error(response.data);
                        alert('Speichern fehlgeschlagen. Bitte versuche es erneut.');
                    }
                },
                error() {
                    alert('Ein Fehler ist aufgetreten. Bitte Seite neu laden und erneut versuchen.');
                }
            });
        });
    }

    function nextStepButton(){
        const $firstSettings = $('#first-settings');
        const $containers = $firstSettings.find('.container');

        if (!$containers.length) {
            return;
        }

        $('#first-settings .welcome button').attr('disabled', false)

        // $containers.each(function(index) {
        //     const $container = $(this);

        //     if (index > 0 && $container.find('.goback').length === 0) {
        //         const $backButton = $('<button type="button" class="goback">Zurück</button>');
        //         $container.prepend($backButton);
        //     }
        // });

        $firstSettings.on('click', '.first-step-button', function(event) {
            event.preventDefault();

            const $currentContainer = $(this).closest('.container');
            const $nextContainer = $currentContainer.nextAll('.container').first();

            if ($nextContainer.length) {
                $nextContainer.addClass('show');
            }
        });

        $firstSettings.on('click', '.goback', function(event) {
            event.preventDefault();

            const $currentContainer = $(this).closest('.container');
            $currentContainer.removeClass('show');
        });
    }

    function setStepsFreeByCheckbox(id){
        const $checkbox    = $(id);
        const $stepButton  = $checkbox.parent().closest('.full-height').next().find('.first-step-button')
        function updateStepButtons() {
            const enabled = $checkbox.is(':checked');
            $stepButton.prop('disabled', !enabled);
        }

        $checkbox.on('change', updateStepButtons);
        updateStepButtons();
    }

    function hasInputValues(inputwrapper){
        const sel = '.checkvalues-'+inputwrapper

        const $stepButton = $(sel).parent().closest('.full-height').next().find('.first-step-button');
        const $textInputs = $(sel + ' input');

        function updateStepButton() {
            if (!$stepButton.length) {
                return;
            }

            const allFilled = $textInputs.toArray().every((input) => {
                return $(input).val().trim().length > 0;
            });

            $stepButton.prop('disabled', !allFilled);
        }

        $textInputs.on('input change', updateStepButton);
        updateStepButton();
    }

    
})(jQuery);