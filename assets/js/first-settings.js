(($) => {
	$(document).ready(()=>{
        setStepsFreeByCheckbox('#mmsi-verstanden')
        setStepsFreeByCheckbox('#mmsi-uploadcheck')
        setStepsFreeByRadioGroup()
        nextStepButton()
        hasInputValues('adress')
        hasInputValues('kontakt') 
        //hasInputValues('safeinfo')                
        setStepsFree('willkommen')
        setStepsFree('einrichten')
        //setStepsFree('safe-info')
        setStepsFree('zweifaktor')
        saveFirstSettings()
        saveSafeInfo()

        $('#zyklus-ersteinrichtung').on('click', function(event) {
            event.preventDefault();
            setStepsFree('timer')
        });
	});

    function saveFirstSettings(){
        $('#save-first-settings').on('click', function(event) {
            event.preventDefault();

            const nonce = $('#fsettingn-wp').val();

            // Sammle User Metas aus step-03.php
            const userMeta = {
                strasze: $('#checkvalues-adress #strasze').val(),
                plz: $('#checkvalues-adress #plz').val(),
                ort: $('#checkvalues-adress #ort').val(),
                telefon: $('#checkvalues-adress #telefon').val()
            };

            // Sammle Kontakt-Daten aus step-04.php
            const contactMeta = {
                name: $('#checkvalues-kontakt #contact-name-1').val(),
                email: $('#checkvalues-kontakt #contact-email-1').val(),
                tel: $('#checkvalues-kontakt #contact-tel-1').val(),
                typ: $('#checkvalues-kontakt #contact-typ-1').val()
            };

            $.ajax({
                url: memyFirstSettingsAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'save_first_settings_meta',
                    nonce: nonce,
                    first_settings: 'done',
                    user_meta: userMeta,
                    contact_meta: contactMeta
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

        $firstSettings.on('click', '.first-step-button', function(event) {
            event.preventDefault();

            const $currentContainer = $(this).closest('.container');
            
            // Prüfe, ob Radio-Gruppe "mmsi-uploadcheck" vorhanden ist
            const $radioGroup = $currentContainer.find('input[name="mmsi-uploadcheck"]:checked');
            let $nextContainer;
            
            if ($radioGroup.length) {
                const selectedValue = $radioGroup.val();
                
                switch(selectedValue) {
                    case 'mmsi-file-later':
                        $nextContainer = $firstSettings.find('.container.zweifaktor');
                        break;
                    case 'mmsi-file-entry':
                        $nextContainer = $firstSettings.find('.container.safe-file-2');
                        break;
                    case 'mmsi-file-completed':
                        $nextContainer = $firstSettings.find('.container.safe-file-1');
                        break;
                    default:
                        $nextContainer = $currentContainer.nextAll('.container').first();
                }
            } else {
                $nextContainer = $currentContainer.nextAll('.container').first();
            }

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

    function setStepsFreeByRadioGroup(){
        $('.mmsi-uploadcheck.radio-boxes').on('change', 'input[type="radio"]', function() {
            setStepsFree('safe-info');
        });
    }

    function hasInputValues(inputwrapper){
        const sel = '#checkvalues-'+inputwrapper

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

    function setStepsFree(container){
        const $stepButtons = $('.container.'+container+' .first-step-button');
        $stepButtons.prop('disabled', false);
    }

    function saveSafeInfo(){
        $('#safe-info-save').on('click', function(event) {
            event.preventDefault();

            // Nutze das Nonce aus der Lokalisierung
            const nonce = memyFirstSettingsAjax.nonce;

            // Sammle Safe-Info Daten mit Labels
            const safeInfoData = [];

            // Sammle Daten aus #checkvalues-safeinfo
            $('#checkvalues-safeinfo input').each(function() {
                const $input = $(this);
                const $wrapper = $input.closest('.input-wrapper');
                const label = $wrapper.find('label').text();
                const value = $input.val();
                
                if (label && value) {
                    safeInfoData.push({
                        label: label,
                        value: value
                    });
                }
            });

            // Sammle Daten aus #checkvalues-safeinfo-soft
            $('#checkvalues-safeinfo-soft input').each(function() {
                const $input = $(this);
                const $wrapper = $input.closest('.input-wrapper');
                const label = $wrapper.find('label').text();
                const value = $input.val();
                
                if (label && value) {
                    safeInfoData.push({
                        label: label,
                        value: value
                    });
                }
            });

            $.ajax({
                url: memyFirstSettingsAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'save_safe_info_txt',
                    nonce: nonce,
                    safe_info_data: safeInfoData
                },
                success(response) {
                    if (response.success) {
                        setStepsFree('safe-file-2')
                    } else {
                        console.error(response.data);
                        alert('Speichern fehlgeschlagen: ' + response.data.message);
                    }
                },
                error(response) {
                    console.log(response)
                    alert('Ein Fehler ist aufgetreten. Bitte versuche es erneut.');
                }
            });
        });
    }
})(jQuery);