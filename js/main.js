(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar + Topbar — Direct Style Manipulation (bypass CSS specificity)
    $(window).scroll(function () {
        var scrolled = $(this).scrollTop() > 40;
        var $topbar = $('#topbar');
        var $navbar = $('.navbar-dark:visible').first();

        if (scrolled) {
            $topbar.css({
                'background': '#ffffff',
                'box-shadow': '0 2px 12px rgba(0,0,0,0.08)',
                'border-bottom': 'none'
            });
            $topbar.find('.text-white-50').css('color', '#888888');
            $topbar.find('.topbar-btn').css('color', '#555555');
            $topbar.find('button.topbar-btn i, a.topbar-btn i').css('color', '#a07850');
            $topbar.find('.btn-outline-light').css({
                'color': '#a07850',
                'border-color': 'rgba(0,0,0,0.15)',
                'background': 'rgba(0,0,0,0.03)'
            });
            $navbar.css({
                'background': '#ffffff',
                'box-shadow': '0 2px 12px rgba(0,0,0,0.06)'
            });
        } else {
            $topbar.css({
                'background': 'transparent',
                'box-shadow': 'none',
                'border-bottom': '1px solid rgba(255,255,255,0.08)'
            });
            $topbar.find('.text-white-50').css('color', '');
            $topbar.find('.topbar-btn').css('color', '');
            $topbar.find('button.topbar-btn i, a.topbar-btn i').css('color', '');
            $topbar.find('.btn-outline-light').css({
                'color': '',
                'border-color': '',
                'background': ''
            });
            $navbar.css({
                'background': 'transparent',
                'box-shadow': 'none'
            });
        }
    });

    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";
    
    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: true,
        loop: true,
        center: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Vendor carousel
    $('.vendor-carousel').owlCarousel({
        loop: true,
        margin: 45,
        dots: false,
        loop: true,
        autoplay: true,
        smartSpeed: 1000,
        responsive: {
            0:{
                items:2
            },
            576:{
                items:4
            },
            768:{
                items:6
            },
            992:{
                items:8
            }
        }
    });
    
})(jQuery);

