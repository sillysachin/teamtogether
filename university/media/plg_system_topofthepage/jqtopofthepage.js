(function($){
    $(document).ready(function(){
        if(window.plg_system_topofthepage_options.topalways) window.scrollTo(0,0);
        $(document.body).append('<a id="gototop" href="#top" style="display:none" rel="noindex,nofollow">'+(window.plg_system_topofthepage_options.buttontext?window.plg_system_topofthepage_options.buttontext:'')+'</a>');
        if(window.plg_system_topofthepage_options.icon) {
            var space = window.plg_system_topofthepage_options.buttontext?' ':'';
            $('#gototop').prepend('<span class="'+window.plg_system_topofthepage_options.icon+'"></span>'+space);
        }
        // position it
        styles = window.plg_system_topofthepage_options.styles;
        styles.opacity = 0;
        styles.display = 'block';
        if(styles.left === 'center') {
            var page = $(window).width()/2;
            var buttonsize = $("#gototop").width()/2;
            styles.left = (page-buttonsize)+'px';
        }
        if(window.plg_system_topofthepage_options.zIndex) {
            styles['z-index'] = highZ(document.body) + 1;
        }
        $('#gototop').css(styles);
        // fade in #back-top
        $(function () {
            var opacity = window.plg_system_topofthepage_options.visibleopacity/100;
            opacity = (opacity > 1 || opacity < 0)?1:opacity;
            var show = {opacity:opacity};
            var hide = {opacity:0};
            if(parseInt(window.plg_system_topofthepage_options.slidein)) {
                var slideamount = 0;
                switch(window.plg_system_topofthepage_options.slideindir) {
                    case 'top':
                        slideamount = $('#gototop').height();
                        property = 'margin-bottom';
                        break;
                    case 'bottom':
                        slideamount = $('#gototop').height();
                        property = 'margin-top';
                        break;
                    case 'left':
                        slideamount = $('#gototop').width();
                        property = 'margin-right';
                        break;
                    case 'right':
                        slideamount = $('#gototop').width();
                        property = 'margin-left';
                        break;                    
                }
                var v = $('#gototop').css(property);
                var h = parseInt(v.replace('px',''))+slideamount;
                show[property]=v;
                hide[property]=h+'px';
                $('#gototop').css(hide);
            }
            $(window).scroll(function () {
                if ($(this).scrollTop() >= window.plg_system_topofthepage_options.spyposition) {
                    if(parseInt($('#gototop').css('opacity')) === 0)
                        $('#gototop').animate(show,{queue:false,duration:parseInt(window.plg_system_topofthepage_options.displaydur),easing:'linear'});
                } else {
                    if($('#gototop').css('opacity') !== 0)
                        $('#gototop').animate(hide,{queue:false,duration:parseInt(window.plg_system_topofthepage_options.displaydur),easing:'linear'});
                }
            });
            // scroll body to 0px on click
            $('#gototop').click(function (e) {
                button = this;
                $('body,html').animate({
                        scrollTop: 0
                    },
                    window.plg_system_topofthepage_options.smoothscroll.duration,
                    window.plg_system_topofthepage_options.smoothscroll.transition
                );
                e.preventDefault();
                return false;
            });
        });        
    });
})(jQuery);
var highZ = function(parent, limit){
    limit = limit || Infinity;
    parent = parent || document.body;
    var who, temp, max= 1, A= [], i= 0;
    var children = parent.childNodes, length = children.length;
    while(i<length){
        who = children[i++];
        if (who.nodeType !== 1) continue; // element nodes only
        if (deepCss(who,"position") !== "static") {
            temp = deepCss(who,"z-index");
            if (temp === "auto") { // z-index is auto, so not a new stacking context
                temp = highZ(who);
            } else {
                temp = parseInt(temp, 10) || 0;
            }
        } else { // non-positioned element, so not a new stacking context
            temp = highZ(who);
        }
        if (temp > max && temp <= limit) max = temp;                
    }
    return max;
};
var deepCss = function(who, css) {
    var sty, val, dv= document.defaultView || window;
    if (who.nodeType === 1) {
        sty = css.replace(/\-([a-z])/g, function(a, b){
            return b.toUpperCase();
        });
        val = who.style[sty];
        if (!val) {
            if(who.currentStyle) val= who.currentStyle[sty];
            else if (dv.getComputedStyle) {
                val= dv.getComputedStyle(who,"").getPropertyValue(css);
            }
        }
    }
    return val || "";
};