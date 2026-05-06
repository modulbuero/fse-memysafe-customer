(($) => {
    /**
     * Funktion zur Navigation im Dashboard (Tile)
     * Weitere kleiner Funktionen zur Element-Steuerung
     */
	$(document).ready(()=>{
        dashNavigation()
        getNaviSpaceToDashboard()
        examClockNavigation()
        showDasboard()
        manageShortInfoPopup()
        numberInputArrow()
        deletePopup()
    })

    function dashNavigation(){
        let dashContainer = '.container-wrapper';
        let $goback       = $('#goback')

        /**
         * Dashboard Chooser
         *  needs:
         *      class item-headline
         *      attr data-goto, data-step
         */
        $(document).on('click','.dashboard-item > .item-headline, .dash-goto-btn', function(){
            let target  = $(this).attr('data-goto')
            let step    = $(this).attr('data-step')
            $(dashContainer + ' > div').removeClass('show');
            manageDashcontainer(target, step)
        })

        /**
         * inner Container Chooser
         * needs: 
         *      class goto-btn
         *      attr data-goto, data-step
         */
        $(document).on('click', '.goto-btn', function(){
            let target = $(this).attr('data-goto')
            let step   = $(this).attr('data-step')
            manageDashcontainer(target, step)
        })

        /**
         * Zurück Button
         */
        $(document).on('click','#goback', function () {
            let target = $(this).attr('data-from')
            
            if($(this).attr('data-step') == "1"){
                $(dashContainer+' div').removeClass('show')
            }else{
                //Aktuellen Wrap schließen
                let $openWrap = $(dashContainer + ' div[data-target='+target+']')
                $openWrap.removeClass('show');
                //Letzer Eintragen, "Historie"
                let prevTarget = $openWrap.prevAll('.show').first().attr('data-target')
                let prevStep   = $openWrap.prevAll('.show').first().attr('data-target')
                $(this).attr('data-from', prevTarget)
                $(this).attr('data-step', prevStep)
            }
        })

        /**
         * Add Show-Class
         * Add Datastep/from to Go-Back-Button
         */
        function manageDashcontainer(target, step=1){
            //$('.container, .container>div').removeClass('show');
            $('div[data-target='+target+']').addClass('show');
            $goback.attr('data-from', target).attr('data-step', step)
        }
    }

    function examClockNavigation(){
        let $changer = $('#manage-exam-clock-buttons > div')

        $changer.on('click', function(){
            let target = $(this).attr('id') + '-input'
            $('#manage-exam-clock-wrapper > div.step-2').hide()
            $('#'+target).show()
        })
    }

    function showDasboard(){
        $('#memy-menu-dashboard').on('click', ()=>{
            $('.container').removeClass('show')
            $('.container div').removeClass('show')
        })
    }

    /**
     * Short Info Popup
     * needs:
     *      class info-popup-wrap
     * Nächstliegende Infobox wird geöffnet
     */
    function manageShortInfoPopup(){
        $('.haspopup-info').on('click', function(){
            $(this).next().addClass('show');
        })
        $('.info-popup-wrap .close-btn').on('click', function(){
            $(this).parent().removeClass('show');
        })
    }

    /**
     * Custom Numberfield Arrow control
     */
    function numberInputArrow(){
        let wrap = '.number-input-container'
        $(document).on('click',wrap + ' .input-number-arrow-up', function () {
            $(this).closest(wrap).find('input[type="number"]')[0].stepUp();
            });

        $(document).on('click',wrap + ' .input-number-arrow-down', function () {
            $(this).closest(wrap).find('input[type="number"]')[0].stepDown();
        });
    }

    /**
     * Padding auf rechter Main-Container-Seite &  hinzufügen
     */
    function getNaviSpaceToDashboard(){
        let menuWrap = $('.user-menu').width()
        let menu     = $('#memy-user-menu').width()
        let space    = menuWrap - menu
        $('.user-content').css('paddingRight', space + 'px')
        //$('.message-container').css('marginRight', space + 'px')
        $('.message-container').css('width', 'calc(60% - ' +space + 'px)')
    }

    /**
	 *  Neuberechnung nach Resize
     * */
    function mmsiDebounce(fn, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
            fn.apply(this, args);
            }, delay);
        };
    }

    //Funktionensammler für Neuberechnung
    function refreshFunctions() {
        getNaviSpaceToDashboard()
    }

    window.addEventListener("resize", mmsiDebounce(refreshFunctions, 300));
})(jQuery)