(($) => {
	$(document).ready(()=>{
        setStepsFreeByCheckbox('#mmsi-verstanden')
        setStepsFreeByCheckbox('#mmsi-uploadcheck')
        setStepsFreeByRadioGroup()
        nextStepButton()
        hasInputValues('adress')
        hasInputValues('kontakt')              
        setStepsFree('willkommen')
        setStepsFree('einrichten')
        setStepsFree('zweifaktor')
        hasInputValuesTXT() 
        saveSafeInfo()//Wird auch vom Safe genutzt
        addInputvaluesToSession()
        saveFirstSettings()
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
                plz:     $('#checkvalues-adress #plz').val(),
                ort:     $('#checkvalues-adress #ort').val(),
                telefon: $('#checkvalues-adress #telefon').val()
            };

            // Sammle Kontakt-Daten aus step-04.php
            const k_f_name     = $('#checkvalues-kontakt #contact_fname').val()
            const k_l_name     = $('#checkvalues-kontakt #contact_lname').val()  
            const contact_mail = $('#checkvalues-kontakt #contact_mail').val()
            const contact_tel  = $('#checkvalues-kontakt #contact_tel').val()            
            
            const contactMeta = {
                contact_fname:  k_f_name,
                contact_lname:  k_l_name,
                contact_mail:   contact_mail,
                contact_tel:    contact_tel,
                contact_typ:    $('#checkvalues-kontakt #contact_typ').val()
            };

            $.ajax({
                url: memyFirstSettingsAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'save_first_settings_meta',
                    nonce: nonce,
                    _wpnonce:ajax_object_contacts.nonce,
                    first_settings: 'done',
                    user_meta: userMeta,
                    contact_meta: contactMeta,
                    //Für MemyContacts->handle_send_contact_invitation
                    contact_mail:   contact_mail,
                    contact_fname:  k_f_name,
                    contact_lname:  k_l_name,
                    
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
            
            //Prüfungen für bestimmte Container, bevor zum nächsten Schritt gewechselt wird
            if ($currentContainer.is('.einrichten.show')) {
                hasInputValues('adress')
            }
            
            if ($currentContainer.is('.kontakt.show')) {
                hasInputValues('kontakt')
            }

            if ($currentContainer.is('.notfallkontakt.show')) {
                if (!validateNotfallKontakt($currentContainer)) {
                    return;
                }
            }

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
            // }  else if ($(document).find('.safe-file-1 .first-step-button').prop('disabled', true)) {
            //     $nextContainer = $currentContainer.nextAll('.container').eq(1);
             
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

    function hasInputValuesTXT(){
        const $textInputs = $('#anweisung-von-ersteinrichtung #checkvalues-safeinfo input');

        function updateStepButton() {
            
            const allFilled = $textInputs.toArray().every((input) => {
                return $(input).val().trim().length > 0;
            });

            $('#anweisung-von-ersteinrichtung #safe-info-save').prop('disabled', !allFilled);
        }

        $textInputs.on('input change', updateStepButton);
        updateStepButton();
    }
    
    function saveSafeInfo(){
        $(document).on('click', '#safe-info-save', function(event) {
            event.preventDefault();

            let isFirstStep = ($(this).hasClass('from-safe-upload')) ? false: true;
            let parent      = (isFirstStep) ? "#anweisung-von-ersteinrichtung" : "#anweisung-vom-safe";

            // Nutze das Nonce aus der Lokalisierung
            const nonce = memyFirstSettingsAjax.nonce;

            // Sammle Safe-Info Daten mit Labels
            const safeInfoData = [];

            // Sammle Daten aus #checkvalues-safeinfo
            $(parent + ' #checkvalues-safeinfo input').each(function() {
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
            $(parent + ' #checkvalues-safeinfo-soft input').each(function() {
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
                        showMessage(response.data.message, 'success')
                        
                        if (!isFirstStep && typeof SafeUpload !== 'undefined') {
                            // Safe-Dateien via AJAX neu laden
                            SafeUpload.loadFileList();
                            SafeUpload.loadFileListShort();
                            reloadNachrichtenDashboard()
                            //Zurück zum Dashboard
                            $('#memy-menu-dashboard').click()
                        }
                    } else {
                        console.error(response.data);
                        showMessage('Speichern fehlgeschlagen: ' + response.data.message);
                    }
                },
                error(response) {
                    console.log(response)
                    showMessage(response.message);
                }
            });
        });
    }

    function validateNotfallKontakt($container){
        const $emailInput = $container.find('#contact_mail');
        const email = $emailInput.val().trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (regex.test(email)) {
            console.log("isMail")
            return true;
        }

        showMessage('ungültige E-Mail');
        $emailInput.focus();
        
        return false;
    }

    function validateNotfallPLZ($container){
        const $plzInput = $container.find('#plz');
        const plz = $plzInput.val().trim();
        const regex = /^\d{5}$/;

        if (regex.test(plz)) {
            console.log("isPLZ")
            return true;
        }

        showMessage('ungültige PLZ');
        $plzInput.focus();
        
        return false;
    }

    function addInputvaluesToSession(){
        const addressKey = 'firstSettingsAddress';
        const contactKey = 'firstSettingsContact';
        const expiryMs = 12 * 60 * 60 * 1000; // 12 Stunden

        const $addressInputs = $('#checkvalues-adress input');
        const $contactInputs = $('#checkvalues-kontakt input');

        function saveValues(key, $inputs) {
            const values = {};

            $inputs.each(function() {
                const $input = $(this);
                const name = $input.attr('id') || $input.attr('name');
                if (!name) {
                    return;
                }
                values[name] = $input.val();
            });

            const payload = {
                timestamp: Date.now(),
                values: values
            };

            try {
                sessionStorage.setItem(key, JSON.stringify(payload));
            } catch (error) {
                console.warn('SessionStorage write failed', error);
            }
        }

        function restoreValues(key, $inputs) {
            let stored = sessionStorage.getItem(key);
            if (!stored) {
                return;
            }

            try {
                const payload = JSON.parse(stored);
                if (typeof payload !== 'object' || payload === null || typeof payload.timestamp !== 'number' || typeof payload.values !== 'object') {
                    return;
                }

                if (Date.now() - payload.timestamp > expiryMs) {
                    sessionStorage.removeItem(key);
                    return;
                }

                const values = payload.values;
                $inputs.each(function() {
                    const $input = $(this);
                    const name = $input.attr('id') || $input.attr('name');
                    if (!name || values[name] == null) {
                        return;
                    }
                    $input.val(values[name]);
                });
            } catch (error) {
                console.warn('SessionStorage read failed', error);
            }
        }

        function attachSaveHandler($container, key) {
            $container.on('input change', 'input', function() {
                saveValues(key, $container.find('input'));
            });
        }

        restoreValues(addressKey, $addressInputs);
        restoreValues(contactKey, $contactInputs);

        attachSaveHandler($('#checkvalues-adress'), addressKey);
        attachSaveHandler($('#checkvalues-kontakt'), contactKey);
    }
})(jQuery);