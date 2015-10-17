define([
    'jquery',
    'jquery.ui',
    'jquery.tmpl',
    'jquery.colorpicker',
    'jquery.select2',
    'jquery.gradientPicker',
    'jquery.json'],
    function ($) {
        var JSNVisualDesignStyle = function (params) {
            this.params = params;
            this.lang = params.language;
        }
        JSNVisualDesignStyle.prototype = {
            createModalGenerateStyle:function (_this, options, valueDefault, buttons, title, width, height) {
                $(".jsn-block").removeClass("jsn-edit-state");
                $(_this).parents(".jsn-block").addClass("jsn-edit-state");
                var self = this;
                $("#jsn-block-container").remove();
                var dialog = $("<div/>", {
                    "id":"jsn-block-container"
                }).append(
                    $("<div/>", {
                        "class":"ui-dialog-content-inner jsn-bootstrap"
                    }).append(
                        $("<div/>", {"class":"form-horizontal"}).append(self.generate(options, valueDefault))
                    )
                );
                $("body").append(dialog);

                $(dialog).dialog({
                    height:height,
                    width:width,
                    title:title,
                    draggable:false,
                    resizable:false,
                    autoOpen:true,
                    modal:true,
                    buttons:buttons
                });
                $(dialog).next().find("button").attr("class", "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only");
                self.registerEvent($(dialog));
            },
            generate:function (options, defaultValue) {
                var self = this;
                var tabs = $("<ul/>"), html = $("<div/>", {"class":"jsn-tabs"}).append($(tabs));
                $.each(options, function (key, val) {
                    $(tabs).append($("<li/>").append($("<a/>", {"href":"#" + key, "id":"tab_" + key, "text":val.title})));
                    var containerTabs = $("<div/>", {"id":key}), containerContent;
                    if (val.tabContent) {
                        containerContent = self.generateTabsStyle(val.tabContent, defaultValue);
                        $.each(containerContent, function () {
                            $(containerTabs).append(this);
                        });
                    }
                    if (val.subTabs) {
                        var stabs = $("<ul/>");
                        var subtabs = $("<div/>", {"class":"jsn-tabs"}).append($(stabs));
                        $(containerTabs).append(subtabs);
                        $.each(val.subTabs, function (skey, sval) {
                            $(stabs).append($("<li/>").append($("<a/>", {"href":"#" + skey, "id":"tab_" + skey, "text":sval.title})));
                            var containerSubTabs = $("<div/>", {"id":skey}), containerContent = "";
                            if (sval.tabContent) {
                                containerContent = self.generateTabsStyle(sval.tabContent, defaultValue);
                                $.each(containerContent, function () {
                                    $(containerSubTabs).append(this);
                                });
                                $(subtabs).append($(containerSubTabs));
                            }
                        });
                    }
                    $(html).append(containerTabs);
                });
                return  $(html);
            },
            registerEvent:function (container) {
                var self = this;
                $(container).find(".jsn-select-color").each(function () {
                    var inputParent = $(this).parent();
                    var selfColor = this;
                    $(this).find("div").css("background-color", $(inputParent).find("input").val());
                    $(this).ColorPicker({
                        color:$(inputParent).find("input").val(),
                        onChange:function (hsb, hex, rgb) {
                            $(inputParent).find("input").val("#" + hex);
                            $(inputParent).find("div.jsn-select-color div").css("background-color", "#" + hex);
                        }
                    });
                });
                $(container).find(".jsn-grad-ex").each(function () {
                    var parentGradEx = $(this).parent();
                    var inputParent = $(parentGradEx).find("input.jsn-grad-ex");
                    var valuePicker = inputParent.val() ? inputParent.val() : '-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)';
                    valuePicker = valuePicker.match(/\((.*?)\)/);
                    valuePicker = valuePicker[1].split(',');
                    var valueP = [];
                    $.each(valuePicker, function (i, val) {
                        if (val && val != "-90deg" && val != "left") {
                            valueP.push($.trim(val));
                        }
                    });
                    $(this).gradientPicker({
                        change:function (points, styles) {
                            for (i = 0; i < styles.length; ++i) {
                                inputParent.val(styles[i]);
                            }
                        },
                        fillDirection:"-90deg",
                        controlPoints:valueP
                    });
                });
                $(container).find(".jsn-fontFaceType").each(function () {
                    self.changeFontFaceType($(this));
                });
                $(container).find(".jsn-fontFaceType").change(function () {
                    self.changeFontFaceType($(this));
                });

                $(container).find(".jsn-tabs").tabs();
                $(".jsn-backgroundType").change(function () {
                    self.changeBackgroundType();
                }).trigger("change");
            },
            generateTabsStyle:function (tabContent, values) {
                var self = this, container = [];
                $.each(tabContent, function () {
                    if (this.type == "fieldset") {
                        var fieldSetContents = $("<fieldset/>").append(
                            $("<legend/>", {"text":this.title})
                        );
                        $.each(this.fieldsetContent, function (i, val) {

                            var comboSubContent = $("<div/>", {"class":"controls clearafter"});
                            if (val && val.type && val.type == "comboContent") {
                                $.each(val.content, function () {
                                    var attrClass = this.class ? this.class : "";
                                    var attrId = this.id ? this.id : "";
                                    if (this.type) {
                                        $(comboSubContent).append($("<div/>", {"class":"combo-item " + attrClass, "id":attrId}).append(self.createElementStyle(this, values[this.name])));
                                    }

                                });
                                $(fieldSetContents).append($("<div/>", {"class":"control-group combo-group"}).append($("<label/>", {"class":"control-label", "text":val.title})).append($(comboSubContent))).append('<div class="clearbreak"></div>');
                            } else if (val && val.type) {
                                $(fieldSetContents).append($("<div/>", {"class":"control-group"}).append($("<label/>", {"class":"control-label", "text":val.label})).append($("<div/>", {"class":"controls"}).append(self.createElementStyle(this, values[this.name]))));
                            }
                        });
                        container.push(fieldSetContents);
                    }
                });
                return container;
            },
            changeFontFaceType:function (_this) {
                var self = this;
                var divParent = $(_this).parents(".controls");
                if ($(_this).val() == "standard fonts") {
                    var listFontStandard = self.listFontFaceStandard();
                    $(divParent).find("select.jsn-fontFace").html("");
                    $.each(listFontStandard, function (i, val) {
                        if (i == $(divParent).find("select.jsn-fontFace").attr("data-selected")) {
                            $(divParent).find("select.jsn-fontFace").append(
                                $("<option/>", {"selected":"selected", "value":i, "text":val, "class":"jsn-fontFace-" + i.toLowerCase().replace(/\ /g, "-")})
                            )
                        } else {
                            $(divParent).find("select.jsn-fontFace").append(
                                $("<option/>", {"value":i, "text":val, "class":"jsn-fontFace-" + i.toLowerCase().replace(/\ /g, "-")})
                            )
                        }
                    });
                } else if ($(_this).val() == "google fonts") {
                    var listFontGoogle = self.listFontFaceGoogle();
                    $(divParent).find("select.jsn-fontFace").html("");
                    $.each(listFontGoogle, function (i, val) {
                        if (i == $(divParent).find("select.jsn-fontFace").attr("data-selected")) {
                            $(divParent).find("select.jsn-fontFace").append(
                                $("<option/>", {"selected":"selected", "value":i, "text":val, "class":"jsn-fontFace-" + i.toLowerCase().replace(/\ /g, "-")})
                            )
                        } else {
                            $(divParent).find("select.jsn-fontFace").append(
                                $("<option/>", {"value":i, "text":val, "class":"jsn-fontFace-" + i.toLowerCase().replace(/\ /g, "-")})
                            )
                        }
                    });
                }
                $(divParent).find("select.jsn-fontFace").select2({
                    dropdownCssClass:'jsn-list-fontFace'
                });
                $(divParent).find("select.jsn-fontFaceType").select2({
                    minimumResultsForSearch:99
                });
            },
            changeBackgroundType:function () {
                $(".jsn-backgroundType").each(function () {
                    var parents = $(this).parents(".control-group"),
                        soildColor = $(parents).find(".jsn-soildColor"),
                        gradientColor = $(parents).find(".jsn-gradientColor");
                    if ($(this).val() == "Solid") {
                        $(soildColor).parents(".combo-item").show();
                        $(gradientColor).parents(".combo-item").hide();
                    } else {
                        $(soildColor).parents(".combo-item").hide();
                        $(gradientColor).parents(".combo-item").show();
                    }
                });
            },
            createElementStyle:function (options, value) {
                var control = null;
                var element = $('<div/>');
                var setAttributes = function (element, attrs) {
                    var elm = $(element),
                        field = elm.is(':input') ? elm : elm.find(':input');
                    field.attr(attrs);
                };
                var templates = {
                    'checkbox':'<input type="checkbox" value="1" name="${options.name}" id="${id}" {{if value==1 || value == "1"}}checked{{/if}} />',
                    'radio':'<div class="form-inline">{{each(i, val) options.options}}<label for="${id}-${i}" class="radio"><input type="radio" class="jsn-m-radio" name="${options.name}" value="${i}" id="${id}-${i}" {{if value==val}}checked{{/if}} />${val}</label>{{/each}}</div>',
                    'gradient-color':'<input type="hidden" name="${options.name}" id="${id}" class="jsn-grad-ex" value="${value}"><div class="jsn-grad-ex"></div>',
                    'number-px':'<div class="input-append"><input type="number"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span class="add-on">px</span></div>',
                    'input-prepend':'<div class="input-prepend"><span title="${options.titlePrefix}" class="add-on">${options.prefix}</span><input type="${options.attType}"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"></div>',
                    'input-append':'<div class="input-prepend"><input type="${options.attType}"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span title="${options.titlePrefix}" class="add-on">${options.prefix}</span></div>',
                    'input-append-prepend':'<div class="input-prepend input-append"><span title="${options.titlePrefixTop}" class="add-on">${options.prefixTop}</span><input type="${options.attType}"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span title="${options.titlePrefixBottom}" class="add-on">${options.prefixBottom}</span></div>',
                    'number':'<input type="number" class="jsn-input-number input-mini" id="${id}" name="${options.name}" value="${value}">',
                    'text':'<input type="text" class="jsn-input-number" id="${id}" name="${options.name}" value="${value}">',
                    'textarea':'<textarea name="${options.name}" id="${id}" rows="3" class="textarea jsn-input-xxlarge-fluid">${value}</textarea>',
                    'select':'<select name="${options.name}" id="${id}" data-selected="${value}" class="select jsn-input-fluid">{{each(i, val) options.options}}<option value="${i}" {{if val==value || (typeof(i) == "string" && i==value)}}selected{{/if}}>${val}</option>{{/each}}</select>',
                    'color':'<input type="text" value="{{if value == ""}}#ffffff{{else}}${value}{{/if}}" name="${options.name}" class="jsn-input-fluid" id="${id}" /><div class="jsn-select-color"><div style="background: #ccccc;"></div></div>',
                    'spacing-top':'<div class="input-prepend input-append"><span title="${options.titlePrefix}" class="add-on"><i class="icon-arrow-up"></i></span><input type="number"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span class="add-on">px</span></div>',
                    'spacing-right':'<div class="input-prepend input-append"><span title="${options.titlePrefix}" class="add-on"><i class="icon-arrow-right"></i></span><input type="number"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span class="add-on">px</span></div> ',
                    'spacing-bottom':'<div class="input-prepend input-append"><span title="${options.titlePrefix}" class="add-on"><i class="icon-arrow-down"></i></span><input type="number"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span class="add-on">px</span></div>',
                    'spacing-left':'<div class="input-prepend input-append"><span title="${options.titlePrefix}" class="add-on"><i class="icon-arrow-left"></i></span><input type="number"  id="${id}" class="jsn-input-number input-mini" name="${options.name}" value="${value}"><span class="add-on">px</span></div>'
                };
                var elementId = 'option-' + options.name + '-' + options.type;
                if (templates[options.type] !== undefined) {
                    if (!value) {
                        value = options.value;
                    }
                    control = $.tmpl(templates[options.type], {
                        options:options,
                        value:value,
                        id:elementId
                    });
                    if ($.isPlainObject(options.attrs))
                        setAttributes(control, options.attrs);
                }
                return control;
            },
            listFontFaceStandard:function () {
                var listFont = {
                    "Verdana":"Verdana",
                    "Georgia":"Georgia",
                    "Courier New":"Courier New",
                    "Arial":"Arial",
                    "Tahoma":"Tahoma",
                    "Trebuchet MS":"Trebuchet MS"
                }
                return listFont;
            },
            listFontFaceGoogle:function () {
                var listFont = {
                    "Open Sans":"Open Sans", "Oswald":"Oswald", "Droid Sans":"Droid Sans", "Lato":"Lato", "Open Sans Condensed":"Open Sans Condensed", "PT Sans":"PT Sans", "Ubuntu":"Ubuntu", "PT Sans Narrow":"PT Sans Narrow",
                    "Yanone Kaffeesatz":"Yanone Kaffeesatz", "Roboto Condensed":"Roboto Condensed", "Source Sans Pro":"Source Sans Pro", "Nunito":"Nunito", "Francois One":"Francois One", "Roboto":"Roboto", "Raleway":"Raleway", "Arimo":"Arimo",
                    "Cuprum":"Cuprum", "Play":"Play", "Dosis":"Dosis", "Abel":"Abel", "Droid Serif":"Droid Serif", "Arvo":"Arvo", "Lora":"Lora", "Rokkitt":"Rokkitt", "PT Serif":"PT Serif", "Bitter":"Bitter", "Merriweather":"Merriweather", "Vollkorn":"Vollkorn",
                    "Cantata One":"Cantata One", "Kreon":"Kreon", "Josefin Slab":"Josefin Slab", "Playfair Display":"Playfair Display", "Bree Serif":"Bree Serif", "Crimson Text":"Crimson Text", "Old Standard TT":"Old Standard TT", "Sanchez":"Sanchez",
                    "Crete Round":"Crete Round", "Cardo":"Cardo", "Noticia Text":"Noticia Text", "Judson":"Judson", "Lobster":"Lobster", "Unkempt":"Unkempt", "Changa One":"Changa One", "Special Elite":"Special Elite",
                    "Chewy":"Chewy", "Comfortaa":"Comfortaa", "Boogaloo":"Boogaloo", "Fredoka One":"Fredoka One", "Luckiest Guy":"Luckiest Guy", "Cherry Cream Soda":"Cherry Cream Soda",
                    "Lobster Two":"Lobster Two", "Righteous":"Righteous", "Squada One":"Squada One", "Black Ops One":"Black Ops One", "Happy Monkey":"Happy Monkey", "Passion One":"Passion One", "Nova Square":"Nova Square", "Metamorphous":"Metamorphous", "Poiret One":"Poiret One", "Bevan":"Bevan", "Shadows Into Light":"Shadows Into Light", "The Girl Next Door":"The Girl Next Door", "Coming Soon":"Coming Soon",
                    "Dancing Script":"Dancing Script", "Pacifico":"Pacifico", "Crafty Girls":"Crafty Girls", "Calligraffitti":"Calligraffitti", "Rock Salt":"Rock Salt", "Amatic SC":"Amatic SC", "Leckerli One":"Leckerli One", "Tangerine":"Tangerine", "Reenie Beanie":"Reenie Beanie", "Satisfy":"Satisfy", "Gloria Hallelujah":"Gloria Hallelujah", "Permanent Marker":"Permanent Marker", "Covered By Your Grace":"Covered By Your Grace", "Walter Turncoat":"Walter Turncoat", "Patrick Hand":"Patrick Hand", "Schoolbell":"Schoolbell", "Indie Flower":"Indie Flower"
                }
                return listFont;
            },
            generateStyleCombo:function (name, type, options) {
                var listStyle = {};
                listStyle.comboDimension = {
                    type:"comboContent",
                    title:"Dimension",
                    content:{
                        width:{
                            name:name + "_width",
                            label:"",
                            type:'input-append-prepend',
                            prefixTop:'W',
                            prefixBottom:'px',
                            titlePrefixTop:'Width',
                            attType:"number",
                            attrs:{
                                'class':'jsn-width input-mini'
                            }
                        },
                        height:{
                            name:name + "_height",
                            label:"",
                            type:'input-append-prepend',
                            prefixTop:'H',
                            prefixBottom:'px',
                            titlePrefixTop:'Height',
                            attType:"number",
                            attrs:{
                                'class':'jsn-height input-mini'
                            }
                        }

                    }
                };
                listStyle.comboContentBorder = {
                    type:"comboContent",
                    title:"Border",
                    content:{
                        borderThickness:{
                            name:name + "_borderThickness",
                            label:"",
                            type:'number-px',
                            attrs:{
                                'class':'jsn-borderThickness input-mini'
                            }
                        },
                        borderStyle:{
                            type:'select',
                            label:"",
                            name:name + "_borderStyle",
                            options:{
                                'solid':'Solid',
                                'dotted':'Dotted',
                                'dashed':'Dashed',
                                'double':'Double',
                                'groove':'Groove',
                                'ridge':'Ridge',
                                'inset':'Inset',
                                'outset':'Outset'
                            },
                            attrs:{
                                'class':'jsn-borderStyle input-medium'
                            }
                        },
                        borderColor:{
                            name:name + "_borderColor",
                            label:"",
                            type:'color',
                            attrs:{
                                class:"jsn-select-color jsn-borderColor input-small"
                            }
                        }
                    }
                };
                listStyle.comboContentBackground = {
                    title:"Background Type",
                    type:"comboContent",
                    content:{
                        backgroundType:{
                            type:'select',
                            label:"",
                            name:name + "_backgroundType",
                            options:{
                                'Solid':'Solid Color',
                                'Gradient':'Gradient Color'
                            },
                            attrs:{
                                'class':'jsn-backgroundType jsn-input-fluid'
                            }
                        },
                        soildColor:{
                            name:name + "_soildColor",
                            label:"",
                            type:'color',
                            attrs:{
                                class:"jsn-select-color jsn-soildColor input-small"
                            }
                        },
                        gradientColor:{
                            name:name + "_gradientColor",
                            label:"",
                            type:'gradient-color',
                            attrs:{
                                'class':'jsn-grad-ex jsn-gradientColor'
                            }
                        }
                    }
                };
                listStyle.comboContentShadow = {
                    type:"comboContent",
                    title:"Shadow",
                    content:{
                        shadowSpread:{
                            name:name + "_shadowSpread",
                            label:"",
                            type:'number-px',
                            attrs:{
                                class:"jsn-input-number input-mini"
                            }
                        },
                        shadowColor:{
                            name:name + "_shadowColor",
                            label:"",
                            type:'color',
                            attrs:{
                                class:"jsn-select-color jsn-shadowColor input-mini"
                            }
                        }
                    }
                };
                listStyle.comboContentRadius = {
                    name:name + "_roundedCornerRadius",
                    label:"Rounded Corner Radius",
                    type:'number-px',
                    attrs:{
                        'class':'jsn-roundedCornerRadius input-mini'
                    }
                };
                var spacingPosition = {'top':'top', 'right':'right', 'bottom':'bottom', 'left':'left'};
                var spacingLeft = {}, spacingRight = {}, spacingTop = {}, spacingBottom = {};
                if (options) {
                    spacingPosition = options;
                }
                var j = 0;
                $.each(spacingPosition, function (i, val) {
                    j++;
                    var attrclass = "";

                    if (j == 3) {
                        attrclass = "clearbreak";
                    }

                    var spacingContent = {};
                    if (type == "comboMargin") {
                        spacingContent = {
                            name:name + "_margin" + val,
                            label:"",
                            type:'spacing-' + val,
                            class:attrclass
                        }
                    }
                    if (type == "comboPadding") {
                        spacingContent = {
                            name:name + "_padding" + val,
                            label:"",
                            type:'spacing-' + val,
                            class:attrclass
                        }
                    }
                    if (val == "left") {
                        spacingLeft = spacingContent;
                        spacingLeft.titlePrefix = "Left Padding";
                    }
                    if (val == "right") {
                        spacingRight = spacingContent;
                        spacingRight.titlePrefix = "Right Padding";
                    }
                    if (val == "bottom") {
                        spacingBottom = spacingContent;
                        spacingBottom.titlePrefix = "Bottom Padding";
                    }
                    if (val == "top") {
                        spacingTop = spacingContent;
                        spacingTop.titlePrefix = "Top Padding";
                    }
                });

                listStyle.comboPadding = {
                    type:"comboContent",
                    title:"Padding",
                    content:{
                        spacingLeft:spacingLeft,
                        spacingRight:spacingRight,
                        spacingBottom:spacingBottom,
                        spacingTop:spacingTop
                    }
                }
                listStyle.comboMargin = {
                    type:"comboContent",
                    title:"Margin",
                    content:{
                        spacingLeft:spacingLeft,
                        spacingRight:spacingRight,
                        spacingBottom:spacingBottom,
                        spacingTop:spacingTop
                    }
                }

                listStyle.comboContentFace = {
                    type:"comboContent",
                    title:"Font Face",
                    content:{
                        fontFaceType:{
                            type:'select',
                            label:"",
                            name:name + "_fontFaceType",
                            options:{
                                'standard fonts':'Standard fonts',
                                'google fonts':'Google fonts'
                            },
                            attrs:{
                                'class':'jsn-fontFaceType input-medium'
                            }
                        },
                        fontFace:{
                            type:'select',
                            label:"",
                            name:name + "_fontFace",
                            options:{
                                'standard fonts':'Standard fonts',
                                'google fonts':'Google fonts'
                            },
                            attrs:{
                                'class':'jsn-fontFace input-medium',
                                'style':'width:250px'
                            }
                        }
                    }
                };
                listStyle.comboContentAttributes = {
                    type:"comboContent",
                    title:"Font Attributes",
                    content:{
                        fontSize:{
                            name:name + "_fontSize",
                            label:"",
                            type:'number-px'
                        },
                        fontStyle:{
                            type:'select',
                            label:'',
                            name:name + "_fontStyle",
                            options:{
                                'inherit':'Inherit',
                                'italic':'Italic',
                                'normal':'Normal',
                                'bold':'Bold'
                            }
                        },
                        fontColor:{
                            name:name + "_fontColor",
                            label:"Font Color",
                            type:'color',
                            value:'#000000',
                            attrs:{
                                class:"jsn-select-color jsn-fontColor input-small"
                            }
                        }
                    }
                };
                listStyle.comboContent = {
                    type:"comboContent",
                    title:"Border",
                    content:{
                        borderThickness:{
                            name:name + "_sublevel1_bo_borderThickness",
                            label:"",
                            type:'number-px',
                            attrs:{
                                'class':'jsn-borderThickness input-mini'
                            }
                        },
                        borderStyle:{
                            type:'select',
                            label:"",
                            name:name + "_sublevel1_bo_borderStyle",
                            options:{
                                'solid':'Solid',
                                'dotted':'Dotted',
                                'dashed':'Dashed',
                                'double':'Double',
                                'groove':'Groove',
                                'ridge':'Ridge',
                                'inset':'Inset',
                                'outset':'Outset'
                            },
                            attrs:{
                                'class':'jsn-borderStyle input-medium'
                            }
                        },
                        borderColor:{
                            name:name + "_sublevel1_bo_borderColor",
                            label:"",
                            type:'color',
                            attrs:{
                                class:"jsn-select-color jsn-borderColor input-small"
                            }
                        }
                    }
                };
                if (listStyle[type]) {
                    return listStyle[type];
                }

            },
            getBoxStyle:function (element) {
                var style = {
                    width:element.width(),
                    height:element.height(),
                    outerHeight:element.outerHeight(),
                    outerWidth:element.outerWidth(),
                    offset:element.offset(),
                    margin:{
                        left:parseInt(element.css('margin-left')),
                        right:parseInt(element.css('margin-right')),
                        top:parseInt(element.css('margin-top')),
                        bottom:parseInt(element.css('margin-bottom'))
                    },
                    padding:{
                        left:parseInt(element.css('padding-left')),
                        right:parseInt(element.css('padding-right')),
                        top:parseInt(element.css('padding-top')),
                        bottom:parseInt(element.css('padding-bottom'))
                    }
                };

                return style;
            }
        }
        return JSNVisualDesignStyle;
    })