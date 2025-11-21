$(document).ready(function () {

    //popuver close on body click
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });


    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }

    /*//hover menu
    if ($(window).width() > 768) {
        $('.navbar .dropdown').on('mouseover', function(){
            $('.dropdown-toggle', this).trigger('click');
        }).on('mouseout', function(){
            $('.dropdown-toggle', this).trigger('click').blur();
        });
    }*/

    // MetsiMenu
    //$('#side-menu').metisMenu();

    // Collapse ibox function
    $('.collapse-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').on('click', function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Fullscreen ibox function
    $('.fullscreen-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });

    // Close menu in canvas mode
    $('.close-canvas-menu').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // Run menu of canvas
    $('body.canvas-menu .sidebar-collapse').slimScroll({
        height: '100%',
        railOpacity: 0.9
    });

    // Open close right sidebar
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Initialize slimscroll for right sidebar
    $('.sidebar-container').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
    });

    // Open close small chat
    $('.open-small-chat').on('click', function () {
        $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
        $('.small-chat-box').toggleClass('active');
    });

    // Initialize slimscroll for small chat
    $('.small-chat-box .content').slimScroll({
        height: '234px',
        railOpacity: 0.4
    });

    // Small todo handler
    $('.check-link').on('click', function () {
        var button = $(this).find('i');
        var label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });

    // Minimalize menu
    $('.navbar-minimalize').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();

    });

    // Tooltips demo
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });


    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

        var navbarHeigh = $('nav.navbar-default').height();
        var wrapperHeigh = $('#page-wrapper').height();

        if (navbarHeigh > wrapperHeigh) {
            $('#page-wrapper').css("min-height", navbarHeigh + "px");
        }

        if (navbarHeigh < wrapperHeigh) {
            $('#page-wrapper').css("min-height", $(window).height() + "px");
        }

        if ($('body').hasClass('fixed-nav')) {
            if (navbarHeigh > wrapperHeigh) {
                $('#page-wrapper').css("min-height", navbarHeigh - 60 + "px");
            } else {
                $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
            }
        }

    }

    fix_height();

    // Fixed Sidebar
    $(window).bind("load", function () {
        if ($("body").hasClass('fixed-sidebar')) {
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }
    });

    // Move right sidebar top after scroll
    $(window).scroll(function () {
        if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
            $('#right-sidebar').addClass('sidebar-top');
        } else {
            $('#right-sidebar').removeClass('sidebar-top');
        }
    });

    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });

    $("[data-toggle=popover]")
        .popover();

    // Add slimscroll to element
    $('.full-height-scroll').slimscroll({
        height: '100%'
    })
});


// Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
});

// Local Storage functions
// Set proper body class and plugins based on user configuration
$(document).ready(function () {
    if (localStorageSupport()) {

        var collapse = localStorage.getItem("collapse_menu");
        var fixedsidebar = localStorage.getItem("fixedsidebar");
        var fixednavbar = localStorage.getItem("fixednavbar");
        var boxedlayout = localStorage.getItem("boxedlayout");
        var fixedfooter = localStorage.getItem("fixedfooter");

        var body = $('body');

        if (fixedsidebar == 'on') {
            body.addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }

        if (collapse == 'on') {
            if (body.hasClass('fixed-sidebar')) {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }
            } else {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }

            }
        }

        if (fixednavbar == 'on') {
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            body.addClass('fixed-nav');
        }

        if (boxedlayout == 'on') {
            body.addClass('boxed-layout');
        }

        if (fixedfooter == 'on') {
            $(".footer").addClass('fixed');
        }
    }
});

// check if browser support HTML5 local storage
function localStorageSupport() {
    return (('localStorage' in window) && window['localStorage'] !== null)
}


function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () {
            element.addClass('animated ' + animation);
        },
        function () {
            //wait for animation to finish before removing classes
            window.setTimeout(function () {
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
    }
}

// Dragable panels
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable(
        {
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8
        })
        .disableSelection();
}

function loading()
{
    $('#loads').css('display','block');
}

function unloading()
{
    $('#loads').css('display','none');
}


function showPopup(div)
{
    $(div).css({
        'visibility':'visible',
        'opacity': 1,
        'top':0,
        'left':0,
        'right':0,
        'bottom':0,
        'margin':0
    });
}

function hidePopup(div)
{
    $(div).css({
        'visibility':'hidden',
        'opacity': 0,
        'margin-top': '-200px'
    });
}

//contextMenu
(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        factory(jQuery);
    }
})(function($) {
    var ContextMenu, Plugin, defaults, old;
    defaults = {
        before: function() {
            return true;
        },
        target: null,
        menuIdentifier: ".dropdown-menu"
    };

    /*
     FILETREE CLASS DEFINITION
     */
    ContextMenu = (function() {
        function ContextMenu(element, options) {
            this.element = element;
            this.settings = $.extend({}, defaults, options);
            if (options.target) {
                $(this.element).data('target', options.target);
            }
            this.init();
        }

        ContextMenu.prototype.init = function() {
            $(this.element).on('contextmenu.menu.context', this.show.bind(this));
            $('html, body').on('click.menu.context', this.hide.bind(this));
        };

        ContextMenu.prototype.destroy = function() {
            $(this.element).off('.menu.context').data('$.contextmenu', null);
            $('html, body').off('.menu.context');
        };

        ContextMenu.prototype.show = function(event) {
            var $menu, relatedTarget, targetPosition, that;
            if (this.isDisabled()) {
                return;
            }
            this.hide();
            if (!this.settings.before.call(this, event, $(event.currentTarget))) {
                return;
            }
            relatedTarget = {
                relatedTarget: this,
                bubbles: false
            };
            $menu = this.getMenu();
            $menu.trigger($.Event('show.menu.context', relatedTarget));
            targetPosition = this._getPosition(event, $menu);
            that = this;
            $menu.attr('style', '').css(targetPosition).addClass('open').one('click.menu.context', 'li:not(.divider)', {
                context: event.target
            }, function(e) {
                var data;
                data = {
                    item: e.target,
                    context: e.data.context
                };
                $menu.trigger('click.item.context', [data]);
                that.hide.call(that);
                return false;
            }).trigger($.Event('shown.menu.context', relatedTarget));
            $('html').on('click.menu.context', $menu.selector, this.hide.bind(this));
            return false;
        };

        ContextMenu.prototype.hide = function() {
            var $menu, relatedTarget;
            $menu = this.getMenu();
            if (!$menu.hasClass('open')) {
                return;
            }
            relatedTarget = {
                relatedTarget: this
            };
            $menu.trigger($.Event('hide.menu.context', relatedTarget));
            $menu.removeClass('open').off('click.menu.context', 'li:not(.divider)').trigger($.Event('hidden.menu.context', relatedTarget));
            $('html').off('click.menu.context', $menu.selector);
            return false;
        };

        ContextMenu.prototype.isDisabled = function() {
            return $(this.element).hasClass("disabled") || $(this.element).attr('disabled');
        };

        ContextMenu.prototype.getMenu = function() {
            var $menu, selector;
            selector = $(this.element).data('target');
            if (!selector) {
                selector = $(this.element).attr('href');
                selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '');
            }
            $menu = $(selector);
            if (!$menu.length) {
                $menu = $(this.element).find(selector);
            }
            return $menu;
        };

        ContextMenu.prototype._getPosition = function(e, $menu) {
            var limitX, limitY, menuHeight, menuStyles, menuWidth, mouseX, mouseY, parentOffset, parentScrollLeft, parentScrollTop, scrollLeft, scrollTop;
            mouseX = e.clientX;
            mouseY = e.clientY;
            limitX = $(window).width();
            limitY = $(window).height();
            menuWidth = $menu.find(this.settings.menuIdentifier).outerWidth();
            menuHeight = $menu.find(this.settings.menuIdentifier).outerHeight();
            scrollLeft = $(window).scrollLeft();
            scrollTop = $(window).scrollTop();
            menuStyles = {
                position: "fixed",
                "z-index": 9999
            };
            menuStyles.top = mouseY + scrollTop;
            if (mouseY + menuHeight > limitY) {
                menuStyles.top -= menuHeight;
            }
            menuStyles.left = mouseX + scrollLeft;
            if (mouseX + menuWidth > limitX) {
                menuStyles.left -= menuWidth;
            }
            parentOffset = $menu.offsetParent().offset();
            parentScrollLeft = $menu.offsetParent().scrollLeft();
            parentScrollTop = $menu.offsetParent().scrollTop();
            menuStyles.left -= parentOffset.left + parentScrollLeft;
            menuStyles.top -= parentOffset.top + parentScrollTop;
            return menuStyles;
        };

        return ContextMenu;

    })();

    /*
     PLUGIN DEFINITION
     */
    Plugin = function(options, obj) {
        return this.each(function() {
            var $this, data, retVal;
            $this = $(this);
            data = $this.data('$.contextmenu');
            if (!data) {
                $this.data("$.contextmenu", (data = new ContextMenu(this, options)));
            }
            if (typeof options === 'string' && options.substr(0, 1) !== '_') {
                retVal = data[options].call(data, obj);
            }
        });
    };
    old = $.fn.contextmenu;
    $.fn.contextmenu = Plugin;
    $.fn.contextmenu.Constructor = ContextMenu;

    /*
     NO CONFLICT
     */
    $.fn.contextmenu.noConflict = function() {
        $.fn.contextmenu = old;
        return this;
    };
});


