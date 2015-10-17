var plg_system_topofthepage_admin_class = new Class({
    Implements:[Options],
    options:{
        version:2.5,
        parentelement:'li'
    },
    elements:{},
    initialize: function(options) {         
        var self = this;
        self.setOptions(options);
        switch(parseInt(options.version)) {
            case 2:
                self.options.parentelement = 'li';
                break;
            default:
                self.options.parentelement = 'div.control-group';
                break;
        }
        self.elements['transition'] = document.id('jform_params_smoothscrolltransition');
        self.toggleTransition();
        self.elements.transition.addEvent('change',function(){self.toggleTransition();});
        
        self.elements['omittext0'] = document.id('jform_params_omittext0');
        self.elements['omittext1'] = document.id('jform_params_omittext1');
        self.toggleOmittext();
        self.elements.omittext0.addEvent('click',function(){self.toggleOmittext();})
        self.elements.omittext1.addEvent('click',function(){self.toggleOmittext();})
        
        self.elements['slidein0'] = document.id('jform_params_slidein0');
        self.elements['slidein1'] = document.id('jform_params_slidein1');
        self.toggleSlidein();
        self.elements.slidein0.addEvent('click',function(){self.toggleSlidein();})
        self.elements.slidein1.addEvent('click',function(){self.toggleSlidein();})
        
        self.elements['usestyle0'] = document.id('jform_params_usestyle0');
        self.elements['usestyle1'] = document.id('jform_params_usestyle1');
        self.toggleCSS();
        self.elements.usestyle0.addEvent('click',function(){self.toggleCSS();})
        self.elements.usestyle1.addEvent('click',function(){self.toggleCSS();})
        
        self.elements['jsframework0'] = document.id('jform_params_jsframework0');
        self.elements['jsframework1'] = document.id('jform_params_jsframework1');
        self.togglejQuery();
        self.elements.jsframework0.addEvent('click',function(){self.togglejQuery();})
        self.elements.jsframework1.addEvent('click',function(){self.togglejQuery();})
    },
    toggleTransition:function(){        
        var self = this;
        if(['linear','swing'].contains(self.elements.transition.value)) {
            self.hideIt('smoothscrolltransition');           
        } else {
            self.showIt('smoothscrolltransition');  
        }        
    },
    toggleOmittext:function(){
        var self = this;
        if(self.elements.omittext1.checked) {
            self.hideIt('omittext');                 
        } else {
            self.showIt('omittext');                  
        }
    },
    toggleSlidein:function(){
        var self = this;
        if(self.elements.slidein0.checked) {
            self.hideIt('slidein');     
        } else {
            self.showIt('slidein');            
        }
    },
    toggleCSS:function(){
        var self = this;
        if(self.elements.usestyle0.checked) {
            self.hideIt('usestyle');  
        } else {
            self.showIt('usestyle');               
        }
    },
    togglejQuery:function(){
        var self = this;
        if(self.elements.jsframework1.checked || self.options.version > 2) {
            self.hideIt('jquery');           
        } else {
            self.showIt('jquery');              
        }
    },
    hideIt:function(classname){
        var self = this;
        $$('.'+classname).each(function(el){
            el.getParent(self.options.parentelement).hide();
        });                    
    },
    showIt:function(classname){
        var self = this;
        $$('.'+classname).each(function(el){
            el.getParent(self.options.parentelement).show();
        });                    
    }
})
window.addEvent('domready',function(){
    var totpa = new plg_system_topofthepage_admin_class(window.plg_system_topofthepage_admin_config);
})