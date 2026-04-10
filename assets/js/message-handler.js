(($) => {
/**
 * Globale Message Handler Funktion
 * Zeigt Nachrichten in einem Container an und versteckt sie nach 5 Sekunden
 * 
 * @param {string} message - Die anzuzeigende Nachricht (HTML möglich)
 * @param {string} type - Der Nachrichtentyp ('success' oder 'fail', default: 'fail')
 */
window.showMessage = function(message, type="fail") {
    $('.message-container').addClass('show');
    var messageHtml = '<div class="message ' + type + '">' + message + '</div>';
    $('.message-container').html(messageHtml);
    
    // Nach 4 Sekunden ausblenden
    setTimeout(function() {
        jQuery('.message-container').removeClass('show');
    }, 4000);
};

/**
 * Delete Popup
 * needs:
 *      class delete-btn-pop
 *      id delete-popup
 */
window.deletePopup = function(){
    let delWrap = '.info-popup-wrap.delete-popup'
    $(document).on('click', '.delete-btn-pop', function(){
        
        let $popup = $(this).parent().nextAll(delWrap).first();
        
        //Safe Upload: Popup ist außerhalb der Button-Struktur, deshalb übergeordnetes Element suchen attribut hinzufügen
        if (!$popup.length) {
            let filename = $(this).data('file');
            $popup = $('#memy-file-list').nextAll(delWrap).first();
            $popup.find('.delete-btn').attr('data-file', filename);
        }
        
        $popup.addClass('show');
    });

    $(document).on('click', delWrap + ' .close-btn, ' + delWrap + ' .cancel-btn, ' + delWrap + ' .delete-btn', function(){
        $(this).closest(delWrap).removeClass('show');
    });
}
})(jQuery)