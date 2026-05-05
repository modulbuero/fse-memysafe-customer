<?php 
/**
 * Title: User Menu
 * Slug: fse-memysafe-customer/user-menu
 * Inserter: no
 * Benutzt in user-menu.html
 * Author: Modulbüro
 */
?>

    <div id="menu">
        
        <!-- wp:list -->
        <ul class="wp-block-list">
        <!-- wp:list-item -->
        <li id="memy-menu-dashboard"><i class="mmsi-icon dashboard"></i> <span>Dashboard</span></li>
        <!-- /wp:list-item -->

        <?php if( get_user_meta(get_current_user_id(), 'first_settings', true) && current_user_can('administrator')) : ?>            
        <!-- wp:list-item -->
        <!--<li id="memy-menu-einstellungen" data-goto="memy-settings" class="dash-goto-btn"><i class="mmsi-icon setting"></i> <span>Einstellungen</span></li>-->
        <li id="memy-menu-einstellungen" class="disabled"><i class="mmsi-icon setting"></i> Einstellungen</li>
        <!-- /wp:list-item -->

        <!-- wp:list-item -->
        <li id="memy-menu-abo" class="disabled"><i class="mmsi-icon abo"></i> Abo: That's me</li>
        <!-- /wp:list-item -->

        <!-- wp:list-item -->
        <li id="memy-menu-profile" data-goto="memy-profile" class="dash-goto-btn"><i class="mmsi-icon profil"></i> <span>Profil</span></li>
        <!-- /wp:list-item -->
        <?php endif; ?>
        
        <form method="post" action="/wp-login.php" id="logout-form">
            <input type="hidden" name="action" value="logout">
            <input type="hidden" name="redirect_to" value="/">
            <i class="mmsi-icon logout"></i> <input type="submit" class="icon-logout-btn" value="Abmelden">
        </form>

        </ul>
        <!-- /wp:list -->
    </div>
    

    <?php if( get_user_meta(get_current_user_id(), 'first_settings', true) || in_array( 'subscriber', (array) wp_get_current_user()->roles ) ) : ?>
    <button id="goback" data-from="">
        <svg xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            id="zurueck-btn-animation"
            width="30.21" height="21" viewBox="4.94 14.29 28.14 19.56">
            <defs>
                <clipPath id="clip-MMSI_Icon_RETURN">
                <rect x="4.94" y="14.29" width="28.14" height="19.56"/>
            </clipPath>
            </defs>
            
            <g id="MMSI_Icon_RETURN" clip-path="url(#clip-MMSI_Icon_RETURN)">

                <path id="icon_RETURN"
                d="M2272.861,1258.6h-5.094l.285-.285,1.512-1.512-1.512-1.512-3.31,3.31h-.021v.021l-1.464,1.464,4.795,4.795,1.512-1.512-1.512-1.512h0l-1.117-1.118h6.47v.027a6.42,6.42,0,0,1,5.871,6.387,6,6,0,0,1-5.871,5.993v.007h-8.682v2.138h8.552a8.137,8.137,0,0,0,8.137-8.137,8.551,8.551,0,0,0-8.552-8.552Z"
                transform="translate(-2248.334 -1241.288)"
                fill="currentColor">
            
                <animate id="anim-in"
                    attributeName="d"
                    dur="0.32s"
                    calcMode="spline"
                    keySplines="0.4 0 0.2 1"
                    fill="freeze"
                    begin="indefinite"
                    from="M2272.861,1258.6h-5.094l.285-.285,1.512-1.512-1.512-1.512-3.31,3.31h-.021v.021l-1.464,1.464,4.795,4.795,1.512-1.512-1.512-1.512h0l-1.117-1.118h6.47v.027a6.42,6.42,0,0,1,5.871,6.387,6,6,0,0,1-5.871,5.993v.007h-8.682v2.138h8.552a8.137,8.137,0,0,0,8.137-8.137,8.551,8.551,0,0,0-8.552-8.552Z"
                    to="M2272.861,1258.6h-15.094l.285-.285,1.512-1.512-1.512-1.512-3.31,3.31h-.021v.021l-1.464,1.464,4.795,4.795,1.512-1.512-1.512-1.512h0l-1.117-1.118h16.47v.027a6.42,6.42,0,0,1,5.871,6.387,6,6,0,0,1-5.871,5.993v.007h-8.682v2.138h8.552a8.137,8.137,0,0,0,8.137-8.137,8.551,8.551,0,0,0-8.552-8.552Z"
                />
                <animate id="anim-out"
                    attributeName="d"
                    dur="0.32s"
                    calcMode="spline"
                    keySplines="0.4 0 0.2 1"
                    fill="freeze"
                    begin="indefinite"
                    from="M2272.861,1258.6h-15.094l.285-.285,1.512-1.512-1.512-1.512-3.31,3.31h-.021v.021l-1.464,1.464,4.795,4.795,1.512-1.512-1.512-1.512h0l-1.117-1.118h16.47v.027a6.42,6.42,0,0,1,5.871,6.387,6,6,0,0,1-5.871,5.993v.007h-8.682v2.138h8.552a8.137,8.137,0,0,0,8.137-8.137,8.551,8.551,0,0,0-8.552-8.552Z"
                    to="M2272.861,1258.6h-5.094l.285-.285,1.512-1.512-1.512-1.512-3.31,3.31h-.021v.021l-1.464,1.464,4.795,4.795,1.512-1.512-1.512-1.512h0l-1.117-1.118h6.47v.027a6.42,6.42,0,0,1,5.871,6.387,6,6,0,0,1-5.871,5.993v.007h-8.682v2.138h8.552a8.137,8.137,0,0,0,8.137-8.137,8.551,8.551,0,0,0-8.552-8.552Z"
                />
                </path>
            
            </g>
            
            <script>
                (($) => {
                    $(document).ready(()=>{
                        const $target = $('#goback');

                        $target.on('mouseenter', () => {
                        $('#anim-out')[0].endElement();
                        $('#anim-in')[0].beginElement();
                        });

                        $target.on('mouseleave', () => {
                        $('#anim-in')[0].endElement();
                        $('#anim-out')[0].beginElement();
                        });
                    })
                })(jQuery)
            </script>
        </svg>
        Zurück
    </button>
    <?php endif; ?>