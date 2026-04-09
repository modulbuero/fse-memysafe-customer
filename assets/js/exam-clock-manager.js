(($) => {
	$(document).ready(()=>{    
        $('#exam-clock-manager-form').on('submit', function(e) {
            e.preventDefault();
            
            /*Checkboxen abfragen*/
            let selDays = [];
            $('input[name="deathman_days[]"]:checked').each(function() {
                selDays.push($(this).val());
            });

            let selReminds = [];
            $('input[name="deathman_reminder[]"]:checked').each(function() {
                selReminds.push($(this).val());
            });

            /*Formulardaten sammeln*/
            var formData = {
                _wpnonce:          ajax_object_deathman.nonce,
                deathman_days:     selDays,
                deathman_reminder: selReminds,
                notfall_email:     $('#notfall_email').val(),
                notfall_name:      $('#notfall_name').val(),
                notfall_telefon:   $('#notfall_telefon').val(),
                deathman_start:    $('#deathman_start').val(),
                deathman_final:    $('#deathman_final').val()
            };

            /* Button und loader */
            $('#save-button').prop('disabled', true);
            $('#loading').show();

            /* Ajax operation */
            wp.ajax.post('handle_update_exam_clock_data', formData)
            .done(function(response) {
                console.log(response);
                $('#loading').hide();
                let nachricht = response.message +"<br>"+ response.reminderday +"<br>"+ response.notfallday
                showMessage(nachricht, 'success')
                $('#finale-zeit').html(response.finalday)
            }).fail(function(response) {
                console.log(response)
                showMessage(response.message)
            });
        });
        
        ShowRangeSliderValue()
        oneatthetimeCheckboxGroup('#lebenszeichen-chooser')
        oneatthetimeCheckboxGroup('#reminder-chooser')

        //Dashboard Exam Clock
        updateExamClockDashboard()
        updateExamClockCycles()
        animateTimeTillEscalate("1")
        animateTimeTillEscalate("2")
        animateTimeTillEscalate("3")
    })

    function oneatthetimeCheckboxGroup(wrapper=""){
        let wrap = (wrapper != "")?wrapper:""
        $(wrap + ' .checkbox-group input[type="checkbox"]').on('change', function () {
            $(wrap + ' .checkbox-group input[type="checkbox"]').not(this).prop('checked', false);
        });
    }

    function ShowRangeSliderValue(){
        let rangeSlider = ".range-slider-container"
        if($(rangeSlider) ){
            $(rangeSlider  + " input").on('change', function(){
                let val = $(this).val()

                $(this).parent().find("#range-slider-value").html(val + " Tage")
                //console.log($(this).val())
            })
        }
    }

    /**
     * Reset/Reload der Zykluszeit
     */
    function updateExamClockDashboard(){
        $(document).on("click", "#exam-clock-reset-button", function(e) {
            e.preventDefault();
            let userid = $('#memy-dashboard').data('user-id');
            
            var formData = {
                _wpnonce:   ajax_object_deathman.nonce,
                user_id:    userid
            };

            wp.ajax.post('handle_update_exam_clock_reload', formData)
            .done(function(response) {
                console.log("success");
                console.log(response);
                $('#chooser-exam-clock').load(location.href + ' #chooser-exam-clock');
                // wp.ajax.post('handle_update_exam_clock_reload', formData)
                // .done(function(response) {
                //     $('#exam-clock-wrapper').html(response.html);
                // });
            }).fail(function(response) {
                console.log("fail");
                console.log(response)
            });
        });
    }

    /**
     * Update Zyklen
     */
    function updateExamClockCycles(){
        $('#exam-clock-save-zyklus').on('click', function(e) {
            e.preventDefault();
            let userid = $('#memy-dashboard').data('user-id');
            
            let formData = {
                _wpnonce:       ajax_object_deathman.nonce,
                user_id:        userid,
                zyklus_one:     $('#exam-clock-zyklus-one').val(),
                zyklus_two:     $('#exam-clock-zyklus-two').val(),
                zyklus_three:   $('#exam-clock-zyklus-three').val(),
            };
            
            wp.ajax.post('handle_update_exam_clock_cycles', formData)
            .done(function(response) {
                console.log('handle_update_exam_clock_cycles: ' + JSON.stringify(response.debug));
                showMessage(response.message, 'success');
                // Reload des Chooser-Exam-Clock
                $('#chooser-exam-clock').load(location.href + ' #chooser-exam-clock');
                $('#manage-exam-clock').load(location.href + ' #manage-exam-clock .step1');
                
            }).fail(function(response) {
                console.log('handle_update_exam_clock_cycles: ' + response.debug);
                showMessage('Fehler beim Speichern der Zyklen');
            });
        });
    }

    /**
     * Kreisanzeige Zyklen / 'Uhr'
     * */
    function animateTimeTillEscalate(targetDateId="1"){
        //Deutsches Datumsformat parsen: "DD.MM.YYYY HH:MM"
        function parseGermanDate(dateString) {
            const parts = dateString.trim().split(' ');
            if (parts.length !== 2) return null;
            
            const dateParts = parts[0].split('.');
            const timeParts = parts[1].split(':');
            
            const day    = parseInt(dateParts[0]);
            const month  = parseInt(dateParts[1]) - 1;
            const year   = parseInt(dateParts[2]);
            const hour   = parseInt(timeParts[0]);
            const minute = parseInt(timeParts[1]);
            
            return new Date(year, month, day, hour, minute);
        }

        // Animationsfunktion
        function animateValue(start, end, duration, callback) {
            const startTime = performance.now();
            
            function update(currentTime) {
                const elapsed  = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = start + (end - start) * easeOut;
                
                callback(current);
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }
            
            requestAnimationFrame(update);
        }

        // Hauptfunktion zur Berechnung und Animation
        function calculateProgress(targetData, targetDateId) {
            const startDateString  = $('#startDate'+targetDateId).val();
            const targetDateString = $(targetData).val();
            
            if (!startDateString || !targetDateString) return;
            
            const startDate  = parseGermanDate(startDateString);
            const targetDate = parseGermanDate(targetDateString);
            const now = new Date();
            
            if (!startDate || !targetDate) return;
            
            // Wenn Ziel bereits erreicht dann Kreis ausblenden
            if (targetDate <= now) {
                $('#progressFill'+targetDateId).prev().hide();
                return;
            }

            // Wenn noch nicht gestartet
            if (now < startDate) {
                $('#progressPercent'+targetDateId).text('0%');
                $('#progressFill'+targetDateId).css('--progress-angle', '0deg');
                $('#timeRemaining'+targetDateId).text('Noch nicht gestartet');
                return;
            }

            // Berechne Progress zwischen Start und Ziel
            const totalTime   = targetDate - startDate;
            const elapsedTime = now - startDate;
            const percent     = Math.min(100, Math.max(0, (elapsedTime / totalTime) * 100));
            //const angle = (percent / 100) * 360;

            //console.log('Prozent:', percent, '%');

            // Animiere von 0 bis zum aktuellen Wert (2 Sekunden)
            animateValue(0, percent, 2000, function(value) {
                $('#progressFill'+targetDateId).css('--progress-angle', (value * 3.6) + 'deg');
            });
        }

        // Initiale Berechnung
        calculateProgress('#targetDate'+targetDateId, targetDateId);
    }
})(jQuery)