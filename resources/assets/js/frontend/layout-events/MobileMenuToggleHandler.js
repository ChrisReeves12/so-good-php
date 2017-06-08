export default class MobileMenuToggleHandler
{
    static initialize()
    {
        $('#main_mobile_menu_toggle').on('click', (e) => {

            e.preventDefault();

            // Initialize side menu
            if(!this.side_menu)
            {
                this.side_menu = $('#side_mobile_menu');
                this.menuHeight = $(window).height();

                // Set up mobile menu
                this.side_menu.css({
                    left: 0,
                    top: 0
                });

                $(window).bind('resize', () => {
                    this.menuHeight = $(window).height();
                    this.side_menu.css({height: `${this.menuHeight}px`});
                });
            }

            let left_slide_limit = this.side_menu.width();

            // Open the menu
            if(!this.is_open)
            {
                // Get the side mobile menu and position it
                this.side_menu.css({display: 'block', height: `${this.menuHeight}px`});

                $('.site-wrapper').animate({left: `${left_slide_limit}px`}, 400, 'swing', () => {
                    this.is_open = true;
                });
            }
            else
            {
                $('.site-wrapper').animate({left: 0}, 400, 'swing', () => {
                    this.is_open = false;
                    this.side_menu.css({display: 'none'});
                })
            }
        });
    }
}