/**
 * Globale Message Handler Funktion
 * Zeigt Nachrichten in einem Container an und versteckt sie nach 5 Sekunden
 * 
 * @param {string} message - Die anzuzeigende Nachricht (HTML möglich)
 * @param {string} type - Der Nachrichtentyp ('success' oder 'fail', default: 'fail')
 */
window.showMessage = function(message, type="fail") {
    jQuery('.message-container').fadeIn();
    var messageHtml = '<div class="message ' + type + '">' + message + '</div>';
    jQuery('.message-container').html(messageHtml);
    
    // Nach 5 Sekunden ausblenden
    setTimeout(function() {
        jQuery('.message-container').fadeOut();
    }, 5000);
};