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
        var $popup = $(this).parent().nextAll(delWrap).first();
        if (!$popup.length) {
            $popup = $(this).closest('.setup-project-data').find(delWrap).first();
        }
        $popup.addClass('show');
    });

    $(document).on('click', delWrap + ' .close-btn, ' + delWrap + ' .cancel-btn, ' + delWrap + ' .delete-btn', function(){
        $(this).closest(delWrap).removeClass('show');
    });
}
})(jQuery)