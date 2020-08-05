var $s = jQuery.noConflict();

$s(window).on('load resize',function() {

    var topo = $s('.header-language-background').outerHeight();
    var header = $s('header.header').outerHeight();
    var banner = $s('.banner-slide').outerHeight();
    var footer = $s('.footer-container').outerHeight();

    var container = $s(window).height() - (topo + header + banner + footer);

    $s('.main-container').css('min-height', container);

});