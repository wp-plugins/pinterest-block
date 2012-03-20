
//jQuery doc ready
//See http://digwp.com/2011/09/using-instead-of-jquery-in-wordpress/
jQuery(document).ready(function($) {
    
	//Enable collapse/expand toggle of admin boxes (like WP dashboard)
	//Don't use 'dashboard' and 'postbox' scripts built-into WP like WordPress SEO
	//plugin as they conflict on other pages
	
    $(".pblock-hndle").toggle(function() {
        $(this).next(".inside").slideToggle("fast");
    }, function () {
        $(this).next(".inside").slideToggle("fast");
    });
	
    $(".pblock-handlediv").toggle(function() {
        $(this).next(".pblock-hndle").next(".inside").slideToggle("fast");
    }, function() {
        $(this).next(".pblock-hndle").next(".inside").slideToggle("fast");
    });	
	
});
