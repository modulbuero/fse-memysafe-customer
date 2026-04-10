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
    $(document).on('click', '.delete-btn-pop', function(){
        var $popup = $(this).parent().nextAll('.info-popup-wrap.delete-popup').first();
        if (!$popup.length) {
            $popup = $(this).closest('.setup-project-data').find('.info-popup-wrap.delete-popup').first();
        }
        $popup.addClass('show');
    });

    $(document).on('click', '.info-popup-wrap.delete-popup .close-btn, .info-popup-wrap.delete-popup .cancel-btn', function(){
        $(this).closest('.info-popup-wrap.delete-popup').removeClass('show');
    });
}
})(jQuery)