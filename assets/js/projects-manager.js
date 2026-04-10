(($) => {
	$(document).ready(()=>{    
        /**
         * PROJEKTE: LOAD DATA VIA AJAX AND FILL FORM
         */
        function loadProjectForm(project_id = 'new') {
            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                project_id: project_id
            };

            wp.ajax.post('load_project_data', formData)
                .done(function(response) {
                    // Felder füllen
                    $('#project-name').val(response.projektname);
                    $('#project-mandant').val(response.mandant);
                    $('#project-mandant-ansprechpartner').val(response.mandant_ansprechpartner);
                    $('#project-mandant-telefon').val(response.mandant_telefon);
                    $('#project-mandant-mobile').val(response.mandant_mobile);
                    $('#project-mandant-email').val(response.mandant_email);
                    $('#project-dienstleister-name').val(response.dienstleister_name);
                    $('#project-dienstleister-funktion').val(response.dienstleister_funktion);
                    $('#project-dateizugriff').val(response.dateizugriff);
                    $('#project-anmerkung').val(response.anmerkung);
                    $('#project-anmerkungen').val(response.anmerkungen);
                    
                    // Select-Felder laden
                    loadVertreterSelect(response.vertreter);
                    loadNotfallSelect(response.kontakt);
                    loadProjectStatusCheckboxes(response.status);

                    // Aktuelle ID speichern im div
                    $('.setup-project-data').attr('id', 'setup-project-' + response.project_id);
                    
                    // Delete Button nur bei Bearbeitung anzeigen
                    if (response.project_id === 'new') {
                        $('#delete-project').hide();
                    } else {
                        $('#delete-project').show();
                    }
                }).fail(function(response) {
                    console.log('Fehler beim Laden der Daten:', response);
                });
        }

        /**
         * Vertreter-Select-Feld laden
         */
        function loadVertreterSelect(selected = '') {
            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                selected: selected
            };

            wp.ajax.post('get_vertreter_select', formData)
                .done(function(response) {
                    $('#project-vertreter-container').html(response);
                }).fail(function(response) {
                    console.log('Fehler beim Laden der Vertreter:', response);
                });
        }

        /**
         * Notfall-Kontakt-Select-Feld laden
         */
        function loadNotfallSelect(selected = '') {
            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                selected: selected
            };

            wp.ajax.post('get_notfall_select', formData)
                .done(function(response) {
                    $('#project-kontakt-container').html(response);
                }).fail(function(response) {
                    console.log('Fehler beim Laden der Kontakte:', response);
                });
        }

        /**
         * Projekt-Status-Checkboxes setzen
         */
        function loadProjectStatusCheckboxes(selected = '') {
            // Sicherstellen, dass selected ein Array ist
            if (!Array.isArray(selected)) {
                selected = selected ? [selected] : [];
            }
            
            // Alle Checkboxes der project-status Gruppe deselektieren
            $('.project-status').prop('checked', false);
            
            // Entsprechende Checkboxes selektieren
            selected.forEach(function(value) {
                $('label.is-checkbox').removeClass('checked');
                $('.project-status[value="' + value + '"]').prop('checked', true).parent('label').addClass('checked');
            });
        }

        /**
         * PROJEKTE: ADD BUTTON - Show Form
         */
        $(document).on('click', '#btn-add-project', function(e) {
            e.preventDefault();
            loadProjectForm('new');
        });

        /**
         * PROJEKTE: EDIT FROM LIST - Show Form
         */
        $(document).on('click', '.project-person-mail', function(e) {
            e.preventDefault();
            let project_id = $(this).attr('data-project');
            loadProjectForm(project_id);
        });

        /**
         * PROJEKTE: SAVE/EDIT
         */
        $(document).on('click', '#save-project', function(e) {
            e.preventDefault();
            let $container      = $(this).closest('.setup-project-data');
            let project_id      = $container.attr('id').replace('setup-project-','');
            let projektname     = $('#project-name').val();
            let status          = $('.project-status:checked').map(function() {
                                        return $(this).val();
                                  }).get();
            let mandant         = $('#project-mandant').val();
            let mandant_telefon = $('#project-mandant-telefon').val();
            let mandant_mobile  = $('#project-mandant-mobile').val();
            let mandant_email   = $('#project-mandant-email').val();
            let dateizugriff    = $('#project-dateizugriff').val();
            let anmerkung       = $('#project-anmerkung').val();
            let anmerkungen     = $('#project-anmerkungen').val();
            let vertreter       = $('#project-vertreter').val();
            let kontakt         = $('#project-kontakt').val();
            let mandant_ansprechpartner = $('#project-mandant-ansprechpartner').val();
            let dienstleister_name      = $('#project-dienstleister-name').val();
            let dienstleister_funktion  = $('#project-dienstleister-funktion').val();
            
            // Validierung
            if (projektname.trim() === '') {
                console.log('Projektname ist erforderlich');
                return;
            }

            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                project_id: project_id,
                projektname: projektname,
                status: status,
                mandant: mandant,
                mandant_ansprechpartner: mandant_ansprechpartner,
                mandant_telefon: mandant_telefon,
                mandant_mobile: mandant_mobile,
                mandant_email: mandant_email,
                dienstleister_name: dienstleister_name,
                dienstleister_funktion: dienstleister_funktion,
                dateizugriff: dateizugriff,
                anmerkung: anmerkung,
                anmerkungen: anmerkungen,
                vertreter: vertreter,
                kontakt: kontakt
            };

            wp.ajax.post('handle_save_project', formData)
                .done(function(response) {
                    console.log(response.debug);
                    showMessage(response.message, 'success');
                    reloadProjectContainer();
                    reloadDashboardProjects();
                    closeProjectForm();
                }).fail(function(response) {
                    console.log('Fehler beim Speichern:', response);
                });
        });

        /**
         * PROJEKTE: DELETE
         */
        $(document).on('click', '#delete-project', function(e) {
            e.preventDefault();
            
            let $container   = $(this).closest('.setup-project-data');
            let project_id   = $container.attr('id').replace('setup-project-','');
            let project_name = $container.find('#project-name').val();

            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                project_id: project_id,
                project_name: project_name 
            };

            wp.ajax.post('handle_delete_project', formData)
                .done(function(response) {
                    console.log('Projekt gelöscht:', response);
                    showMessage(response.message, 'success');
                    reloadProjectContainer();
                    reloadDashboardProjects();
                    closeProjectForm();
                }).fail(function(response) {
                    console.log('Fehler beim Löschen:', response);
                });
        });

        /**
         * PROJEKTE: Close Form Helper
         */
        function closeProjectForm() {
            // Felder leeren
            $('#project-name').val('');
            $('.project-status').prop('checked', false);
            $('#project-mandant').val('');
            $('#project-mandant-ansprechpartner').val('');
            $('#project-mandant-telefon').val('');
            $('#project-mandant-mobile').val('');
            $('#project-mandant-email').val('');
            $('#project-dienstleister-name').val('');
            $('#project-dienstleister-funktion').val('');
            $('#project-dateizugriff').val('');
            $('#project-anmerkung').val('');
            $('#project-anmerkungen').val('');
            $('#project-vertreter').val('');
            $('#project-kontakt').val('');
            // ID zurücksetzen
            $('.setup-project-data').attr('id','setup-project-new');
            $('#goback').click()
        }

        /**
         * PROJEKTE: Reload Container nach Speichern
         */
        function reloadProjectContainer() {
            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                action: 'get_projects_list'
            };

            wp.ajax.post('get_projects_list', formData)
                .done(function(response) {
                    // Container mit neuer Liste aktualisieren
                    const $container = $('#projects-list-container');
                    if ($container.length) {
                        $container.html(response);
                    }
                    //console.log('Projekte-Liste aktualisiert');
                }).fail(function(response) {
                    console.log('Fehler beim Aktualisieren der Liste:', response);
                });
        }

        /**
         * PROJEKTE: Reload Dashboard Project List nach Löschen
         */
        function reloadDashboardProjects() {
            var formData = {
                _wpnonce: ajax_object_projects.nonce,
                action: 'get_projects_list'
            };

            wp.ajax.post('get_projects_list', formData)
                .done(function(response) {
                    const $dashboardContent = $('#memy-dashboard-my-projects .item-content');
                    if ($dashboardContent.length) {
                        $dashboardContent.html(response);
                    }
                }).fail(function(response) {
                    console.log('Fehler beim Aktualisieren des Dashboard-Projekte-Widgets:', response);
                });
        }

        /**
         * Eingabefelder anzeigen/ausblenden
        */
       $(document).on('click', '.project-data-show-hide', function() {
            let $wrap = $(this).next('.project-data-container');
            $(this).toggleClass('active');
            $wrap.find('input, .selectbox, textarea, label.is-checkbox').toggleClass('show');
        });
    })
})(jQuery)
