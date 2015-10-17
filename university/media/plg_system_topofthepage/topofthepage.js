var plg_system_topofthepage_class = new Class({
    Implements: [Options],
    options: {
        buttontext:false,
        topalways:false,
        styles:false,
        spyposition:200,
        visibleopacity:1,
        smoothscroll:true,
        slidein:0,
        slideindir:'bottom',
        slider:false,
        zindex:0
    },
    initialize: function(options) { 
        var self = this;
        options.visibleopacity = (options.visibleopacity <= 100 && options.visibleopacity > 0)?options.visibleopacity/100:1;
        self.setOptions(options);
        if(options.topalways) window.scrollTo(0,0);
        if(self.options.zindex > 0) {
            self.options.zindex = highZ(document.body) + 1;
        }
        self.createButton();
        self.scrollSpy();
        self.smoothScroll();
    },
    createButton:function(){  
        var self = this;
        if(self.options.styles.left === 'center') {
            Object.erase(self.options.styles,'left');
            var center = true;
        }
        if(self.options.zindex > 0) self.options.styles['z-index'] = self.options.zindex;
        var href = '#topofthepage';
        var base = $$('base');
        if(base.length) {
            var uri = new URI(base[0].getAttribute('href'));
            uri.set('fragment','topofthepage');
            href = uri.toURI();
        }
        var pageuri = new URI(window.location);
        if(pageuri.get('query').length) {
            pageuri.set('fragment','topofthepage');
            href = pageuri.toURI();
        }
        var gototop = new Element('a',{
            'id':'gototop',
            'href':href.toString(),
            'styles':self.options.styles,
            'rel':'noindex,nofollow'
        }).inject(document.body,'bottom');
        if(self.options.icon !== false) {
            var icon = new Element('span',{
                'class':self.options.icon
            }).inject(gototop,'bottom');
        }
        if(self.options.buttontext !== false) {
            if(self.options.icon !== false) gototop.appendText(" ",'bottom');
            gototop.appendText(self.options.buttontext,'bottom');
        }
        if(center) {
            var page = window.getScrollSize().x/2;
            var buttonsize = gototop.measure(function(){
                return this.getSize();
            });
            gototop.setStyle('left',(page-(buttonsize.x/2)));
        }
    },
    scrollSpy:function(){
        var self = this;     
        var buttonMorph = new Fx.Morph('gototop',{
            duration:self.options.displaydur,
            transition:'linear'
        });
        var buttonMorphIn = {"opacity":[0,self.options.visibleopacity]};
        var buttonMorphOut = {"opacity":[self.options.visibleopacity,0]};
        if(parseInt(self.options.slidein)) {
            var slideamount = 0;
            switch(self.options.slideindir) {
                case 'top':
                    slideamount = document.id('gototop').getSize().y;
                    property = 'margin-bottom';
                    break;
                case 'bottom':
                    slideamount = document.id('gototop').getSize().y;
                    property = 'margin-top';
                    break;
                case 'left':
                    slideamount = document.id('gototop').getSize().x;
                    property = 'margin-right';
                    break;
                case 'right':
                    slideamount = document.id('gototop').getSize().x;
                    property = 'margin-left';
                    break;
            }
            var v = document.id('gototop').getStyle(property);
            var h = parseInt(v.replace('px',''))+slideamount;
            buttonMorphIn[property]=[h+'px',v];
            buttonMorphOut[property]=[v,h+'px'];
        }
        var scrollspy = new ScrollSpy({
            min:self.options.spyposition,
            container: window,
            onEnter: function(position,enters) {
                buttonMorph.start(buttonMorphIn);
            },
            onLeave: function(position,leaves) {
                buttonMorph.start(buttonMorphOut);
            }
        });
    },
    smoothScroll:function(){
        var self = this;
        var totpscroll = new Fx.Scroll(document.body,self.options.smoothscroll);
        document.id('gototop').addEvent('click',function(){
            totpscroll.toTop();
            return false;
        });
    }
});
window.addEvent('domready',function(){
    var totp = new plg_system_topofthepage_class(window.plg_system_topofthepage_options);
});
function highZ(parent, limit){
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
}

function deepCss(who, css) {
    var sty, val, dv=document.defaultView || window;
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
}