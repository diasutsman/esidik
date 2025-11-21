(function( $ ){
    var plugin = {
        name: 'TreeView',
        version: '1.0.1'
    }
    var defaults = {
        autoExpand : false,
        css : {
            list : 'fa-ul',
            listItem : 'fa-li fa',
            collapsed : 'fa-caret-right',
            expanded : 'fa-caret-down'
        }
    }
    
    var settings;
    var me = null;
    
    function __changeHandler( e ) {
        var currentTarget = $(this);
        var isChecked = currentTarget.is(':checked');
        
        if (!isChecked) {
            currentTarget.parent()
                         .find('input.tw-control')
                         .prop('checked', false);
        }
        
        if (isChecked) {
            currentTarget.parents('li')
                         .find('>input.tw-control')
                         .prop('checked', true);
        }
        
        _toggleCollapse( currentTarget );
        
        me.trigger('treeview.change', currentTarget, me);
    }
    
    function _toggleCollapse ( element ) {
        var chk = $('input[type="checkbox"]:checked');
        
        if (chk.is(':checked')) {
            chk.parent()
               .find('>ul.collapse')
               .collapse('show')
               .parent()
               .find('>i.fa-li')
               .removeClass(settings.css.collapsed)
               .addClass(settings.css.expanded);
        }
        
        if (!element.is(':checked')) {
            element.parent()
                   .find('ul.collapse')
                   .collapse('hide')
                   .parent()
                   .find('i.fa-li')
                   .removeClass(settings.css.expanded)
                   .addClass(settings.css.collapsed);
        }
    }
    
    function _init() {
        me.on('change', 'input.tw-control', __changeHandler);
        
        me.find('>ul')
          .addClass(settings.css.list)
          .find('ul')
          .addClass('collapse ' +  settings.css.list)
          .parent()
          .prepend(
              $('<i></i>').addClass(settings.css.listItem + ' ' +
                                    settings.css.collapsed)
          );
          
        if (settings.autoExpand) {
            me.find('ul.collapse').collapse('show');
        }
        
        me.find('li').each(function( index, element ) {
            var elmt = $(element);
            
            var chk = $('<input/>').prop('type', 'checkbox')
                                   .prop('class', 'tw-control')
                                   .prop('value', elmt.attr('data-value'));
            
            var isChecked = elmt.attr('data-checked');
            
            elmt.prepend(chk);
            
            if ( isChecked ) {
                chk.prop('checked', true);
                
                chk.trigger('change');
            }
        });
    }
    
    function _fill( data ) {
        $( data ).each(function (index, element) {
            me.find('input[value="' + element + '"]')
              .prop('checked', true)
              .trigger('change');
        });
    }
    
    var publicMethods = {
        init : function( options ) {
            me = this;
            
            settings = $.extend( defaults, options );
            
            _init();
            
            _fill ( options.data );
            
            return this;
        },
        selectedValues: function() {

            var chk = me.find('input[type="checkbox"]:checked');
            var output = [];
            
            chk.each(function(index, item) {
                var item = $(item);
                
                if(typeof item.parent().attr('data-value') !== typeof undefined) {
                    output.push(item.attr('value'));
                }
            })
            
            return output;
        }
    }
    
    $.fn.treeview = function (options) {
        if ( publicMethods[options] ) {
            return publicMethods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof options === 'object' || ! options ) {
            return publicMethods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  options  + ' does not exist on jQuery.treeview' );
        }
    }
    
}( jQuery ));