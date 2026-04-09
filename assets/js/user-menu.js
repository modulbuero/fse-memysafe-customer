(($) => {
	$(document).ready(()=>{
        desktopMenuHandler();
        popupHandler();        
    })

    /**
     *  Menü-Handler für Desktop
     * 
     *  Klick auf ein Menü-Item blendet die jeweilige Seite ein
     */
    function desktopMenuHandler() {
        let pagewrap = '#memy-user-content-wrapper>div'
        let pageitem = $(pagewrap)
        let menuitem = $('#memy-user-menu-wrapper ul li')
        menuitem.on('click', function(){
            menuName = $(this).attr("id")
            pageitem.hide()
            $(pagewrap+'[data-menu="' + menuName + '"]').show()
        })

        $('.goto-memy-menu-deathmansetting').on('click', ()=>{
            pageitem.hide()
            $(pagewrap+'[data-menu="memy-menu-deathmansetting"]').show()
        })
    }

    /**
     *  Popup-Handler
     * 
     *  mit '.goto-settings' wird das jeweilige Popup geöffnet
     *  mit dem dazugehörigen data-Attribut
     */
    function popupHandler() {
        $('.goto-settings').on('click', function(){
            let popupId = $(this).attr('data')
            $('#' + popupId).show()
        })
    }
})(jQuery)