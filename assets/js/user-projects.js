(($) => {
	$(document).ready(()=>{
        /*Projekt anlegen /speichern */
        $('#project-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'save_project',
                nonce: ajax_object_project.nonce,
                projekt_id: $('#projekt-id').val(),
                projekt_titel: $('#projekt-titel').val(),
                projekt_beschreibung: $('#projekt-beschreibung').val(),
                projekt_contact_person: $('#projekt-contact-person').val(),
                projekt_status: $('#projekt-status').val(),
                edit_id: $('#project-edit-id').val()
            };
            
            $.ajax({
                url: ajax_object_project.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log(response)
                    if (response.success) {
                        showMessage('Projekt erfolgreich gespeichert!', 'success');
                        resetForm();
                        loadProjects();
                    } else {
                        showMessage('Fehler: ' + response.data, 'error');
                    }
                },
                error: function() {
                    showMessage('Ein Fehler ist aufgetreten.', 'error');
                }
            });
        });
        
        // Projekt bearbeiten
        $(document).on('click', '.edit-btn', function() {
            var projectId = $(this).data('id');
            
            $.ajax({
                url: ajax_object_project.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_project',
                    nonce: ajax_object_project.nonce,
                    id: projectId
                },
                success: function(response) {
                    if (response.success) {
                        var project = response.data;
                        $('#project-edit-id').val(project.id);
                        $('#projekt-id').val(project.projekt_id);
                        $('#projekt-titel').val(project.projekt_titel);
                        $('#projekt-beschreibung').val(project.projekt_beschreibung);
                        $('#projekt-contact-person').val(project.projekt_contact_person || '');
                        $('#projekt-status').val(project.projekt_status);
                        
                        $('#form-title').text('Projekt bearbeiten');
                        $('#cancel-edit').show();
                        
                    }
                }
            });
        });
        
        // Projekt löschen
        $(document).on('click', '.delete-btn', function() {
            if (!confirm('Sind Sie sicher, dass Sie dieses Projekt löschen möchten?')) {
                return;
            }
            
            var projectId = $(this).data('id');
            
            $.ajax({
                url: ajax_object_project.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_project',
                    nonce: ajax_object_project.nonce,
                    id: projectId
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Projekt erfolgreich gelöscht!', 'success');
                        loadProjects();
                    } else {
                        showMessage('Fehler beim Löschen: ' + response.data, 'error');
                    }
                }
            });
        });
        
        // Bearbeitung abbrechen
        $('#cancel-edit').on('click', function() {
            resetForm();
        });
        
        function resetForm() {
            $('#project-form')[0].reset();
            $('#project-edit-id').val('');
            $('#projekt-contact-person').val('');
            $('#form-title').text('Neues Projekt hinzufügen');
            $('#cancel-edit').hide();
        }
        
        // function showMessage(message, type) {
        //     var messageHtml = '<div class="message ' + type + '">' + message + '</div>';
        //     $('#message-container').html(messageHtml);
            
        //     setTimeout(function() {
        //         $('#message-container').fadeOut();
        //     }, 5000);
        // }
        
        function loadProjects() {
            $.ajax({
                url: ajax_object_project.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_projects',
                    nonce: ajax_object_project.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#projects-list').html(response.data);
                    } else {
                        showMessage('Fehler beim Laden der Projekte: ' + response.data, 'error');
                    }
                },
                error: function() {
                    showMessage('Fehler beim Laden der Projekte.', 'error');
                }
            });
        }

        loadProjects();
    })
})(jQuery)