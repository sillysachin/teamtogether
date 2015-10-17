define([],
    function (JSNVisualDesign) {
        return function (JSNVisualDesign, language) {
            /* Standard Group */
            //Single line text controls
            JSNVisualDesign.register('single-line-text', {
                caption:'Single Line Text',
                group:'standard',
                defaults:{
                    label:'Single Line Text',
                    instruction:'',
                    required:0,
                    limitation:0,
                    limitMin:0,
                    limitMax:0,
                    limitType:'Words',
                    size:'jsn-input-medium-fluid',
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',

                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }

                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        value:{
                            type:'text',
                            label:'Predefined Value'
                        },

                        limit:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><limitation/><limitMin/><limitMax/><limitType/></div>',
                            elements:{
                                limitation:{
                                    type:'checkbox',
                                    label:'Limit text'
                                },
                                limitMin:{
                                    type:'number',
                                    label:'within',
                                    validate:['number']
                                },
                                limitMax:{
                                    type:'number',
                                    label:'and',
                                    validate:['number']
                                },
                                limitType:{
                                    type:'select',
                                    options:{
                                        'Words':'Words',
                                        'Characters':'Characters'
                                    },
                                    attrs:{
                                        'class':'input-small'
                                    }
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><input type="text" placeholder="${value}" class="${size}"/></div></div>'
            });
            // Choices controls
            JSNVisualDesign.register('choices', {
                caption:'Multiple Choice',
                group:'standard',
                defaults:{
                    label:'Multiple Choice',
                    instruction:'',
                    required:0,
                    randomize:0,
                    labelOthers:'Others',
                    layout:'columns-count-one',
                    items:[
                        {
                            text:'Choice 1',
                            checked:true
                        },
                        {
                            text:'Choice 2',
                            checked:false
                        },
                        {
                            text:'Choice 3',
                            checked:false
                        }
                    ],
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><layout/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                layout:{
                                    type:'select',
                                    label:'Layout',
                                    options:{
                                        'jsn-columns-count-one':'One Column',
                                        'jsn-columns-count-two':'Two Columns',
                                        'jsn-columns-count-three':'Three Columns',
                                        'jsn-columns-count-no':'Side by Side'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        items:{
                            type:'itemlist',
                            label:'Items',
                            actionField:true,
                            multipleCheck:false
                        },
                        itemAction:{
                            type:'hidden'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><div class="control-group {{if hideField}}jsn-hidden-field{{/if}}"><div class="controls jsn-allow-other"><allowOther/><labelOthers/></div></div></div><div class="pull-right"><randomize/></div><div class="clearbreak"></div></div>',
                            elements:{
                                allowOther:{
                                    type:'checkbox',
                                    field:'allowOther',
                                    label:language['JSN_UNIFORM_ALLOW_USER_CHOICE']
                                },
                                labelOthers:{
                                    type:'_text',
                                    field:'allowOther',
                                    attrs:{
                                        'class':'text jsn-input-small-fluid'
                                    }
                                },
                                randomize:{
                                    type:'checkbox',
                                    label:'Randomize Items'
                                }
                            }
                        }

                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><div class="jsn-columns-container ${layout}">{{each(i, val) items}}<div class="jsn-column-item"><label class="radio"><input name="${identify}" type="radio" {{if val.checked == true || val.checked=="true"}}checked{{/if}} />${val.text}</label></div>{{/each}}{{if allowOther}}<div class="jsn-column-item"><label class="radio lbl-allowOther"><input class="allowOther" value="Others" type="radio" />${labelOthers}</label><textarea rows="3"></textarea></div>{{/if}}<div class="clearbreak"></div></div></div></div>'
            });
            //dropdown controls
            JSNVisualDesign.register('dropdown', {
                caption:'Dropdown',
                group:'standard',
                defaults:{
                    label:'Dropdown',
                    instruction:'',
                    required:0,
                    labelOthers:'Others',
                    size:'jsn-input-fluid',
                    items:[
                        {
                            text:'- Select Value -',
                            checked:true
                        },
                        {
                            text:'Value 1',
                            checked:false
                        },
                        {
                            text:'Value 2',
                            checked:false
                        },
                        {
                            text:'Value 3',
                            checked:false
                        }
                    ],
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-fluid':'Auto',
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        items:{
                            type:'itemlist',
                            label:'Items',
                            actionField:true,
                            multipleCheck:false
                        },
                        itemAction:{
                            type:'hidden'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><div class="control-group {{if hideField}}jsn-hidden-field{{/if}}"><div class="controls jsn-allow-other"><allowOther/><labelOthers/></div></div></div><div class="pull-right"><randomize/></div><div class="clearbreak"></div></div><div class="jsn-form-bar"><div class="pull-left"><firstItemAsPlaceholder/></div><div class="clearbreak"></div></div>',
                            elements:{
                                allowOther:{
                                    type:'checkbox',
                                    field:'allowOther',
                                    label:language['JSN_UNIFORM_ALLOW_USER_CHOICE']
                                },
                                labelOthers:{
                                    type:'_text',
                                    field:'allowOther',
                                    attrs:{
                                        'class':'text jsn-input-small-fluid'
                                    }
                                },
                                randomize:{
                                    type:'checkbox',
                                    label:'Randomize Items'
                                },
                                firstItemAsPlaceholder:{
                                    type:'checkbox',
                                    label:language['JSN_UNIFORM_SET_ITEM_PLACEHOLDER']
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><select class="${size}" >{{each(i, val) items}}<option value="${val.text}" {{if val.checked == true || val.checked=="true"}}selected{{/if}}>${val.text}</option>{{/each}}</select></div></div>'
            });
            // Paragraph Text controls
            JSNVisualDesign.register('paragraph-text', {
                caption:'Paragraph Text',
                group:'standard',
                defaults:{
                    label:'Paragraph Text',
                    instruction:'',
                    required:0,
                    limitation:0,
                    limitMin:0,
                    limitMax:0,
                    rows:8,
                    size:'jsn-input-xlarge-fluid',
                    limitType:'Words',
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><rows/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                rows:{
                                    type:'number',
                                    label:'Rows',
                                    validate:['number']
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        value:{
                            type:'textarea',
                            label:'Predefined Value',
                            attrs:{
                                'rows':'10'
                            }
                        },
                        limit:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><limitation/><limitMin/><limitMax/><limitType/></div>',
                            elements:{
                                limitation:{
                                    type:'checkbox',
                                    label:'Limit text'
                                },
                                limitMin:{
                                    type:'number',
                                    label:'within',
                                    validate:['number']
                                },
                                limitMax:{
                                    type:'number',
                                    label:'and',
                                    validate:['number']
                                },
                                limitType:{
                                    type:'select',
                                    options:{
                                        'Words':'Words',
                                        'Characters':'Characters'
                                    },
                                    attrs:{
                                        'class':'input-small'
                                    }
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><textarea class="${size}" rows="${rows}" placeholder="${value}"></textarea></div></div>'
            });
            /*  Checkboxes control */
            JSNVisualDesign.register('checkboxes', {
                caption:'Checkboxes',
                group:'standard',
                defaults:{
                    label:'Checkboxes',
                    instruction:'',
                    required:0,
                    randomize:0,
                    allowOther:0,
                    layout:'columns-count-one',
                    labelOthers:'Others',
                    items:[
                        {
                            text:'Checkbox 1',
                            checked:false
                        },
                        {
                            text:'Checkbox 2',
                            checked:false
                        },
                        {
                            text:'Checkbox 3',
                            checked:false
                        }
                    ],
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><layout/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                layout:{
                                    type:'select',
                                    label:'Layout',
                                    options:{
                                        'jsn-columns-count-one':'One Column',
                                        'jsn-columns-count-two':'Two Columns',
                                        'jsn-columns-count-three':'Three Columns',
                                        'jsn-columns-count-no':'Side by Side'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        items:{
                            type:'itemlist',
                            label:'Items',
                            actionField:true,
                            multipleCheck:true
                        },
                        itemAction:{
                            type:'hidden'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><div class="control-group {{if hideField}}jsn-hidden-field{{/if}}"><div class="controls jsn-allow-other"><allowOther/><labelOthers/></div></div></div><div class="pull-right"><randomize/></div><div class="clearbreak"></div></div>',
                            elements:{
                                allowOther:{
                                    type:'checkbox',
                                    field:'allowOther',
                                    label:language['JSN_UNIFORM_ALLOW_USER_CHOICE']
                                },
                                labelOthers:{
                                    type:'_text',
                                    field:'allowOther',
                                    attrs:{
                                        'class':'text jsn-input-small-fluid'
                                    }
                                },
                                randomize:{
                                    type:'checkbox',
                                    label:'Randomize Items'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><div class="jsn-columns-container ${layout}">{{each(i, val) items}}<div class="jsn-column-item"><label class="checkbox"><input type="checkbox" {{if val.checked == true || val.checked == "true"}}checked{{/if}} />${val.text}</label></div>{{/each}}{{if allowOther==true || allowOther=="true"}}<div class="jsn-column-item"><label class="checkbox lbl-allowOther"><input class="allowOther" value="Others" type="checkbox" />${labelOthers}</label><textarea rows="3"></textarea></div>{{/if}}<div class="clearbreak"></div></div></div></div>'
            });
            //List controls
            JSNVisualDesign.register('list', {
                caption:'List',
                group:'standard',
                defaults:{
                    label:'List',
                    instruction:'',
                    required:0,
                    size:'jsn-input-fluid',
                    items:[
                        {
                            text:'Value 1',
                            checked:false
                        },
                        {
                            text:'Value 2',
                            checked:false
                        },
                        {
                            text:'Value 3',
                            checked:false
                        }
                    ],
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-fluid':'Auto',
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        items:{
                            type:'itemlist',
                            label:'Items',
                            actionField:false,
                            multipleCheck:true
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><multiple/></div><div class="pull-right"><randomize/></div><div class="clearbreak"></div></div>',
                            elements:{
                                multiple:{
                                    type:'checkbox',
                                    label:'Allow multiple selection'
                                },
                                randomize:{
                                    type:'checkbox',
                                    label:'Randomize Items'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><select multiple class="${size}" >{{each(i, val) items}}<option value="${val.text}" {{if val.checked == true || val.checked=="true"}}selected{{/if}}>${val.text}</option>{{/each}}</select></div></div>'
            });
            /* End Standard Group */
            /*Static content controls*/
            JSNVisualDesign.register('static-content', {
                caption:'Static Content',
                group:'standard',
                defaults:{
                    label:'Static Content',
                    value:'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fermentum odio sed ipsum fringilla ut tempor magna accumsan. Aliquam erat volutpat. Vestibulum euismod ipsum non risus dignissim hendrerit. Nam metus arcu, blandit in cursus nec, placerat vitae arcu. Maecenas ornare porta mi, et tincidunt nulla luctus non.”'
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        value:{
                            type:'textarea',
                            label:'Text',
                            attrs:{
                                'rows':'6'
                            }
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><div class="controls clearfix">{{html value}}</div></div>'
            });
            /* End Static content controls*/
            /*Google Maps controls*/
            JSNVisualDesign.register('google-maps', {
                caption:'Google Maps',
                group:'extra',
                defaults:{
                    label:'Google Maps',
                    width:100,
                    formatWidth:'%',
                    height:300,
                    googleMaps:'{\"center\":{\"lb\":12.372121848436315,\"mb\":-0.6382415506872352},\"zoom\":1}'
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="row-fluid"><div class="pull-left"><div class="control-group"><label for="option-width-number" class="control-label">Width</label><div class="controls input-append"><width/><formatWidth/></div></div></div><div class="pull-right"><div class="control-group"><label for="option-width-number" class="control-label">Height</label><div class="controls input-append"><height/><span class="add-on">px</span></div></div></div></div><div class="jsn-form-bar"><div class="pull-left"><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },

                                width:{
                                    type:'number',
                                    group:'horizontal',
                                    field:'input-inline',
                                    attrs:{
                                        'class':'number input-small'
                                    }
                                },
                                formatWidth:{
                                    type:'select',
                                    group:'horizontal',
                                    field:'input-inline',
                                    options:{
                                        '%':'%',
                                        'px':'px'
                                    },
                                    attrs:{
                                        'class':'add-on input-mini'
                                    }
                                },
                                height:{
                                    type:'number',
                                    group:'horizontal',
                                    field:'input-inline',
                                    attrs:{
                                        'class':'number input-small'
                                    }
                                }

                            }
                        }
                    },
                    values:{
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><div id="google-maps-search"><div class="jsn-search-google-maps"><input id="places-search" placeholder="Search…" class="input search-query btn-icon input-xlarge" type="text"/><a href="javascript:void(0);" title="Clear Search" class="jsn-reset-search"><i class="icon-remove"></i></a></div></div></div><div class="pull-right"><div class="btn-group"><button type="button" class="btn btn-google-location btn-icon"><i class="icon-location"></i></button></div></div><div class="clearbreak"></div></div><div class="row-fluid"><div class="google_maps map rounded"></div><div id="marker-google-maps" class="hide"><googleMaps/><googleMapsMarKer/></div></div>',
                            title:'Predefined Value',
                            elements:{
                                googleMaps:{
                                    type:'hidden'
                                },
                                googleMapsMarKer:{
                                    type:'hidden'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><div class="content-google-maps clearfix" data-width="${width}${formatWidth}" data-height="${height}" data-value="${googleMaps}" data-marker="${googleMapsMarKer}"><div class="google_maps map rounded"></div></div></div>'
            });
            /* End Static content controls*/
            /* Advanced Group */
            //Name controls
            JSNVisualDesign.register('name', {
                caption:'Name',
                group:'extra',
                defaults:{
                    label:'Name',
                    instruction:'',
                    required:0,
                    size:'jsn-input-mini-fluid',
                    items:[
                        {
                            text:"Mrs",
                            checked:false
                        },
                        {
                            text:"Mr",
                            checked:true
                        },
                        {
                            text:"Ms",
                            checked:false
                        },
                        {
                            text:"Baby",
                            checked:false
                        },
                        {
                            text:"Master",
                            checked:false
                        },
                        {
                            text:"Prof",
                            checked:false
                        },
                        {
                            text:"Dr",
                            checked:false
                        },
                        {
                            text:"Gen",
                            checked:false
                        },
                        {
                            text:"Rep",
                            checked:false
                        },
                        {
                            text:"Sen",
                            checked:false
                        },
                        {
                            text:"St",
                            checked:false
                        }
                    ]
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-fluid':'Auto',
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        extra:{
                            type:'group',
                            decorator:'<div class="row-fluid"><div class="span6 jsn-items-list-container" id="jsn-field-name"><label for="option-name-itemlist" class="control-label">Fields</label><ul class="jsn-items-list ui-sortable"><vtitle/><vfirst/><vmiddle/><vlast/></ul><sortableField/></div><div id="jsn-name-default-titles" class="span6"><items/></div></div>',
                            title:'Predefined Value',
                            elements:{
                                items:{
                                    type:'itemlist',
                                    label:'Titles'
                                },
                                vtitle:{
                                    field:'name',
                                    type:'checkbox',
                                    label:language['TITLES']
                                },
                                vfirst:{
                                    field:'name',
                                    type:'checkbox',
                                    label:language['FIRST']
                                },
                                vmiddle:{
                                    field:'name',
                                    type:'checkbox',
                                    label:language['MIDDLE']
                                },
                                vlast:{
                                    field:'name',
                                    type:'checkbox',
                                    label:language['LAST']
                                },
                                sortableField:{
                                    type:'hidden'

                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls">' + '{{if vtitle}}<select class="input-small" >{{each(i, val) items}}<option value="${val.text}" {{if val.checked == true || val.checked=="true"}}selected{{/if}}>${val.text}</option>{{/each}}</select>&nbsp;{{/if}}' + '{{if vfirst}}<input type="text" class="${size}" placeholder="' + language['FIRST'] + '" />&nbsp;{{/if}}' + '{{if vmiddle}}<input type="text" class="${size}" placeholder="' + language['MIDDLE'] + '" />&nbsp;{{/if}}' + '{{if vlast}}<input type="text" class="${size}" placeholder="' + language['LAST'] + '" />{{/if}}</div></div>'
            });
            //Email controls
            JSNVisualDesign.register('email', {
                caption:'Email',
                group:'extra',
                defaults:{
                    label:'Email',
                    instruction:'',
                    required:0,
                    noDuplicates:0,
                    size:'jsn-input-medium-fluid',
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><noDuplicates/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>' +
                                '<div class="jsn-form-bar"><div class="pull-left"><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                noDuplicates:{
                                    type:'checkbox',
                                    label:'No Duplicates',
                                    title:'JSN_UNIFORM_IF_CHECKED_VALUE_DUPLICATION'
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        value:{
                            type:'text',
                            label:'Predefined Value'
                        },
                        valueConfirm:{
                            type:'text'
                        },
                        requiredConfirm:{
                            type:'checkbox',
                            label:'Required Confirmation'
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><div class="row-fluid"><input class="${size}" type="text" placeholder="${value}" /></div>{{if requiredConfirm}}<div class="row-fluid"><input class="${size}" type="text" placeholder="${valueConfirm}" /></div>{{/if}}</div></div>'
            });
            //File upload controls
            JSNVisualDesign.register('file-upload', {
                caption:'File Upload',
                group:'extra',
                defaults:{
                    label:'File Upload',
                    instruction:'',
                    required:0,
                    allowedExtensions:'png,jpg,gif,zip,rar,txt,doc,pdf',
                    maxSize:0,
                    maxSizeUnit:'KB'
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><multiple/></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                multiple:{
                                    type:'checkbox',
                                    label:'Multiple Upload'
                                }
                            }
                        },

                        limit:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><limitFileExtensions/><allowedExtensions/><i class="icon-question-sign" id="limit-extensions" original-title=""></i></div><div class="jsn-form-bar"><limitFileSize/><maxSize/><maxSizeUnit/><i id="limit-size-upload" class="icon-question-sign" original-title=""></i></div>',
                            elements:{
                                limitFileExtensions:{
                                    type:'checkbox',
                                    label:'Limit file'
                                },
                                limitFileSize:{
                                    type:'checkbox',
                                    label:'Limit file'
                                },
                                allowedExtensions:{
                                    type:'text',
                                    label:'extensions',
                                    attrs:{
                                        'class':'input-large'
                                    }
                                },
                                maxSize:{
                                    type:'number',
                                    label:'Size',
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },

                                maxSizeUnit:{
                                    type:'select',
                                    options:{
                                        'KB':'KB',
                                        'MB':'MB'
                                    },
                                    attrs:{
                                        'class':'input-small'
                                    }

                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><input type="file" placeholder="${value}" /></div></div>'
            });
            //Name controls
            JSNVisualDesign.register('likert', {
                caption:'Likert',
                group:'extra',
                defaults:{
                    label:'Likert',
                    instruction:'',
                    required:0,
                    size:'jsn-input-mini-fluid',
                    rows:[
                        {
                            text:"Statement 1",
                            checked:false
                        },
                        {
                            text:"Statement 2",
                            checked:false
                        },
                        {
                            text:"Statement 3",
                            checked:false
                        }
                    ],
                    columns:[
                        {
                            text:"Good",
                            checked:false
                        },
                        {
                            text:"Average",
                            checked:false
                        },
                        {
                            text:"Poor",
                            checked:false
                        }
                    ]
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        extra:{
                            type:'group',
                            decorator:'<div class="row-fluid"><div class="span6 jsn-items-list-container" id="jsn-rows-likert"><rows/></div><div id="jsn-columns-likert" class="span6"><columns/></div></div>',
                            title:'Predefined Value',
                            elements:{
                                rows:{
                                    type:'itemlist',
                                    label:'Rows',
                                    classHidden:'hide'
                                },
                                columns:{
                                    type:'itemlist',
                                    label:'Columns',
                                    classHidden:'hide'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><table class="table table-bordered table-striped"><thead><th></th>{{each(i, column) columns}}<th class="center">${column.text}</th>{{/each}}</thead><tbody>{{each(j, row) rows}}<tr><td>${row.text}</td>{{each(i, column) columns}}<td class="center"><input type="radio"/></td>{{/each}}</tr>{{/each}}</tbody></table></div></div>'
            });
            /* Address field */
            JSNVisualDesign.register('address', {
                caption:'Address',
                group:'extra',
                defaults:{
                    label:'Address',
                    instruction:'',
                    required:0,
                    vstreetAddress:0,
                    vstreetAddress2:0,
                    vcity:0,
                    vstate:0,
                    vcode:0,
                    vcountry:0,
                    country:[
                        {
                            text:"Afghanistan",
                            checked:true
                        },
                        {
                            text:"Albania",
                            checked:false
                        },
                        {
                            text:"Algeria",
                            checked:false
                        },
                        {
                            text:"Andorra",
                            checked:false
                        },
                        {
                            text:"Angola",
                            checked:false
                        },
                        {
                            text:"Antigua and Barbuda",
                            checked:false
                        },
                        {
                            text:"Argentina",
                            checked:false
                        },
                        {
                            text:"Armenia",
                            checked:false
                        },
                        {
                            text:"Australia",
                            checked:false
                        },
                        {
                            text:"Austria",
                            checked:false
                        },
                        {
                            text:"Azerbaijan",
                            checked:false
                        },
                        {
                            text:"Bahamas",
                            checked:false
                        },
                        {
                            text:"Bahrain",
                            checked:false
                        },
                        {
                            text:"Bangladesh",
                            checked:false
                        },
                        {
                            text:"Barbados",
                            checked:false
                        },
                        {
                            text:"Belarus",
                            checked:false
                        },
                        {
                            text:"Belgium",
                            checked:false
                        },
                        {
                            text:"Belize",
                            checked:false
                        },
                        {
                            text:"Benin",
                            checked:false
                        },
                        {
                            text:"Bhutan",
                            checked:false
                        },
                        {
                            text:"Bolivia",
                            checked:false
                        },
                        {
                            text:"Bosnia and Herzegovina",
                            checked:false
                        },
                        {
                            text:"Botswana",
                            checked:false
                        },
                        {
                            text:"Brazil",
                            checked:false
                        },
                        {
                            text:"Brunei",
                            checked:false
                        },
                        {
                            text:"Bulgaria",
                            checked:false
                        },
                        {
                            text:"Burkina Faso",
                            checked:false
                        },
                        {
                            text:"Burundi",
                            checked:false
                        },
                        {
                            text:"Cambodia",
                            checked:false
                        },
                        {
                            text:"Cameroon",
                            checked:false
                        },
                        {
                            text:"Canada",
                            checked:false
                        },
                        {
                            text:"Cape Verde",
                            checked:false
                        },
                        {
                            text:"Central African Republic",
                            checked:false
                        },
                        {
                            text:"Chad",
                            checked:false
                        },
                        {
                            text:"Chile",
                            checked:false
                        },
                        {
                            text:"China",
                            checked:false
                        },
                        {
                            text:"Colombi",
                            checked:false
                        },
                        {
                            text:"Comoros",
                            checked:false
                        },
                        {
                            text:"Congo (Brazzaville)",
                            checked:false
                        },
                        {
                            text:"Congo",
                            checked:false
                        },
                        {
                            text:"Costa Rica",
                            checked:false
                        },
                        {
                            text:"Cote d'Ivoire",
                            checked:false
                        },
                        {
                            text:"Croatia",
                            checked:false
                        },
                        {
                            text:"Cuba",
                            checked:false
                        },
                        {
                            text:"Cyprus",
                            checked:false
                        },
                        {
                            text:"Czech Republic",
                            checked:false
                        },
                        {
                            text:"Denmark",
                            checked:false
                        },
                        {
                            text:"Djibouti",
                            checked:false
                        },
                        {
                            text:"Dominica",
                            checked:false
                        },
                        {
                            text:"Dominican Republic",
                            checked:false
                        },
                        {
                            text:"East Timor (Timor Timur)",
                            checked:false
                        },
                        {
                            text:"Ecuador",
                            checked:false
                        },
                        {
                            text:"Egypt",
                            checked:false
                        },
                        {
                            text:"El Salvador",
                            checked:false
                        },
                        {
                            text:"Equatorial Guinea",
                            checked:false
                        },
                        {
                            text:"Eritrea",
                            checked:false
                        },
                        {
                            text:"Estonia",
                            checked:false
                        },
                        {
                            text:"Ethiopia",
                            checked:false
                        },
                        {
                            text:"Fiji",
                            checked:false
                        },
                        {
                            text:"Finland",
                            checked:false
                        },
                        {
                            text:"France",
                            checked:false
                        },
                        {
                            text:"Gabon",
                            checked:false
                        },
                        {
                            text:"Gambia, The",
                            checked:false
                        },
                        {
                            text:"Georgia",
                            checked:false
                        },
                        {
                            text:"Germany",
                            checked:false
                        },
                        {
                            text:"Ghana",
                            checked:false
                        },
                        {
                            text:"Greece",
                            checked:false
                        },
                        {
                            text:"Grenada",
                            checked:false
                        },
                        {
                            text:"Guatemala",
                            checked:false
                        },
                        {
                            text:"Guinea",
                            checked:false
                        },
                        {
                            text:"Guinea-Bissau",
                            checked:false
                        },
                        {
                            text:"Guyana",
                            checked:false
                        },
                        {
                            text:"Haiti",
                            checked:false
                        },
                        {
                            text:"Honduras",
                            checked:false
                        },
                        {
                            text:"Hungary",
                            checked:false
                        },
                        {
                            text:"Iceland",
                            checked:false
                        },
                        {
                            text:"India",
                            checked:false
                        },
                        {
                            text:"Indonesia",
                            checked:false
                        },
                        {
                            text:"Iran",
                            checked:false
                        },
                        {
                            text:"Iraq",
                            checked:false
                        },
                        {
                            text:"Ireland",
                            checked:false
                        },
                        {
                            text:"Israel",
                            checked:false
                        },
                        {
                            text:"Italy",
                            checked:false
                        },
                        {
                            text:"Jamaica",
                            checked:false
                        },
                        {
                            text:"Japan",
                            checked:false
                        },
                        {
                            text:"Jordan",
                            checked:false
                        },
                        {
                            text:"Kazakhstan",
                            checked:false
                        },
                        {
                            text:"Kenya",
                            checked:false
                        },
                        {
                            text:"Kiribati",
                            checked:false
                        },
                        {
                            text:"Korea, North",
                            checked:false
                        },
                        {
                            text:"Korea, South",
                            checked:false
                        },
                        {
                            text:"Kuwait",
                            checked:false
                        },
                        {
                            text:"Kyrgyzstan",
                            checked:false
                        },
                        {
                            text:"Laos",
                            checked:false
                        },
                        {
                            text:"Latvia",
                            checked:false
                        },
                        {
                            text:"Lebanon",
                            checked:false
                        },
                        {
                            text:"Lesotho",
                            checked:false
                        },
                        {
                            text:"Liberia",
                            checked:false
                        },
                        {
                            text:"Libya",
                            checked:false
                        },
                        {
                            text:"Liechtenstein",
                            checked:false
                        },
                        {
                            text:"Lithuania",
                            checked:false
                        },
                        {
                            text:"Luxembourg",
                            checked:false
                        },
                        {
                            text:"Macedonia",
                            checked:false
                        },
                        {
                            text:"Madagascar",
                            checked:false
                        },
                        {
                            text:"Malawi",
                            checked:false
                        },
                        {
                            text:"Malaysia",
                            checked:false
                        },
                        {
                            text:"Maldives",
                            checked:false
                        },
                        {
                            text:"Mali",
                            checked:false
                        },
                        {
                            text:"Malta",
                            checked:false
                        },
                        {
                            text:"Marshall Islands",
                            checked:false
                        },
                        {
                            text:"Mauritania",
                            checked:false
                        },
                        {
                            text:"Mauritius",
                            checked:false
                        },
                        {
                            text:"Mexico",
                            checked:false
                        },
                        {
                            text:"Micronesia",
                            checked:false
                        },
                        {
                            text:"Moldova",
                            checked:false
                        },
                        {
                            text:"Monaco",
                            checked:false
                        },
                        {
                            text:"Mongolia",
                            checked:false
                        },
                        {
                            text:"Morocco",
                            checked:false
                        },
                        {
                            text:"Mozambique",
                            checked:false
                        },
                        {
                            text:"Myanmar",
                            checked:false
                        },
                        {
                            text:"Namibia",
                            checked:false
                        },
                        {
                            text:"Nauru",
                            checked:false
                        },
                        {
                            text:"Nepa",
                            checked:false
                        },
                        {
                            text:"Netherlands",
                            checked:false
                        },
                        {
                            text:"New Zealand",
                            checked:false
                        },
                        {
                            text:"Nicaragua",
                            checked:false
                        },
                        {
                            text:"Niger",
                            checked:false
                        },
                        {
                            text:"Nigeria",
                            checked:false
                        },
                        {
                            text:"Norway",
                            checked:false
                        },
                        {
                            text:"Oman",
                            checked:false
                        },
                        {
                            text:"Pakistan",
                            checked:false
                        },
                        {
                            text:"Palau",
                            checked:false
                        },
                        {
                            text:"Panama",
                            checked:false
                        },
                        {
                            text:"Papua New Guinea",
                            checked:false
                        },
                        {
                            text:"Paraguay",
                            checked:false
                        },
                        {
                            text:"Peru",
                            checked:false
                        },
                        {
                            text:"Philippines",
                            checked:false
                        },
                        {
                            text:"Poland",
                            checked:false
                        },
                        {
                            text:"Portugal",
                            checked:false
                        },
                        {
                            text:"Qatar",
                            checked:false
                        },
                        {
                            text:"Romania",
                            checked:false
                        },
                        {
                            text:"Russia",
                            checked:false
                        },
                        {
                            text:"Rwanda",
                            checked:false
                        },
                        {
                            text:"Saint Kitts and Nevis",
                            checked:false
                        },
                        {
                            text:"Saint Lucia",
                            checked:false
                        },
                        {
                            text:"Saint Vincent",
                            checked:false
                        },
                        {
                            text:"Samoa",
                            checked:false
                        },
                        {
                            text:"San Marino",
                            checked:false
                        },
                        {
                            text:"Sao Tome and Principe",
                            checked:false
                        },
                        {
                            text:"Saudi Arabia",
                            checked:false
                        },
                        {
                            text:"Senegal",
                            checked:false
                        },
                        {
                            text:"Serbia and Montenegro",
                            checked:false
                        },
                        {
                            text:"Seychelles",
                            checked:false
                        },
                        {
                            text:"Sierra Leone",
                            checked:false
                        },
                        {
                            text:"Singapore",
                            checked:false
                        },
                        {
                            text:"Slovakia",
                            checked:false
                        },
                        {
                            text:"Slovenia",
                            checked:false
                        },
                        {
                            text:"Solomon Islands",
                            checked:false
                        },
                        {
                            text:"Somalia",
                            checked:false
                        },
                        {
                            text:"South Africa",
                            checked:false
                        },
                        {
                            text:"Spain",
                            checked:false
                        },
                        {
                            text:"Sri Lanka",
                            checked:false
                        },
                        {
                            text:"Sudan",
                            checked:false
                        },
                        {
                            text:"Suriname",
                            checked:false
                        },
                        {
                            text:"Swaziland",
                            checked:false
                        },
                        {
                            text:"Sweden",
                            checked:false
                        },
                        {
                            text:"Switzerland",
                            checked:false
                        },
                        {
                            text:"Syria",
                            checked:false
                        },
                        {
                            text:"Taiwan",
                            checked:false
                        },
                        {
                            text:"Tajikistan",
                            checked:false
                        },
                        {
                            text:"Tanzania",
                            checked:false
                        },
                        {
                            text:"Thailand",
                            checked:false
                        },
                        {
                            text:"Togo",
                            checked:false
                        },
                        {
                            text:"Tonga",
                            checked:false
                        },
                        {
                            text:"Trinidad and Tobago",
                            checked:false
                        },
                        {
                            text:"Tunisia",
                            checked:false
                        },
                        {
                            text:"Turkey",
                            checked:false
                        },
                        {
                            text:"Turkmenistan",
                            checked:false
                        },
                        {
                            text:"Tuvalu",
                            checked:false
                        },
                        {
                            text:"Uganda",
                            checked:false
                        },
                        {
                            text:"Ukraine",
                            checked:false
                        },
                        {
                            text:"United Arab Emirates",
                            checked:false
                        },
                        {
                            text:"United Kingdom",
                            checked:false
                        },
                        {
                            text:"United States",
                            checked:false
                        },
                        {
                            text:"Uruguay",
                            checked:false
                        },
                        {
                            text:"Uzbekistan",
                            checked:false
                        },
                        {
                            text:"Vanuatu",
                            checked:false
                        },
                        {
                            text:"Vatican City",
                            checked:false
                        },
                        {
                            text:"Venezuela",
                            checked:false
                        },
                        {
                            text:"Vietnam",
                            checked:false
                        },
                        {
                            text:"Yemen",
                            checked:false
                        },
                        {
                            text:"Zambia",
                            checked:false
                        },
                        {
                            text:"Zimbabwe",
                            checked:false
                        }
                    ]
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }
                    },
                    values:{
                        extra:{
                            type:'group',
                            decorator:'<div class="row-fluid"><div class="span6 jsn-items-list-container" id="jsn-field-address"><label for="option-country-itemlist" class="control-label">Fields</label><ul class="jsn-items-list ui-sortable"><vstreetAddress/><vstreetAddress2/><vcity/><vstate/><vcode/><vcountry/></ul><sortableField/></div><div id="jsn-address-default-country" class="span6"><country/></div></div>',
                            title:'Predefined Value',
                            elements:{
                                country:{
                                    type:'itemlist',
                                    label:'Countries',
                                    multipleCheck:false
                                },
                                vstreetAddress:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['STREET_ADDRESS']
                                },
                                vstreetAddress2:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['ADDRESS_LINE_2']
                                },
                                vcity:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['CITY']
                                },
                                vstate:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['STATE_PROVINCE_REGION']
                                },
                                vcode:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['POSTAL_ZIP_CODE']
                                },
                                vcountry:{
                                    field:'address',
                                    type:'checkbox',
                                    label:language['COUNTRY']
                                },
                                sortableField:{
                                    type:'hidden'
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group {{if hideField}}jsn-hidden-field{{/if}} jsn-group-field">' +
                    '<label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label>' +
                    '<div class="controls">' +
                    '{{if vstreetAddress}}<div class="row-fluid"><input type="text" placeholder="' + language['STREET_ADDRESS'] + '" class="jsn-input-xxlarge-fluid" /></div>{{/if}}' +
                    '{{if vstreetAddress2}}<div class="row-fluid"><input type="text" placeholder="' + language['ADDRESS_LINE_2'] + '" class="jsn-input-xxlarge-fluid" /></div>{{/if}}' +
                    '{{if vcity || vstate}}<div class="row-fluid">' +
                    '{{if vcity}}<div class="span6"><input type="text" class="jsn-input-xlarge-fluid" placeholder="' + language['CITY'] + '" /></div>{{/if}}' +
                    '{{if vstate}}<div class="span6"><input type="text" class="jsn-input-xlarge-fluid" placeholder="' + language['STATE_PROVINCE_REGION'] + '" /></div>{{/if}}' +
                    '</div>{{/if}} {{if vcode || vcountry}}<div class="row-fluid">' +
                    '{{if vcode}}<div class="span6"><input type="text" class="jsn-input-xlarge-fluid" placeholder="' + language['POSTAL_ZIP_CODE'] + '" /></div>{{/if}}' +
                    '{{if vcountry}}<div class="span6"><select class="jsn-input-xlarge-fluid">{{each(i, val) country}}<option value="${val.text}" {{if val.checked == true || val.checked=="true"}}selected{{/if}}>${val.text}</option>{{/each}}</select></div>{{/if}}' +
                    '</div>{{/if}}</div></div>'
            });

            //Website controls
            JSNVisualDesign.register('website', {
                caption:'Website',
                group:'extra',
                defaults:{
                    label:'Website',
                    instruction:'',
                    required:0,
                    noDuplicates:0,
                    size:'jsn-input-medium-fluid',
                    value:'http://'
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><noDuplicates/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>' +
                                '<div class="jsn-form-bar"><div class="pull-left"><hideField/></div><div class="clearbreak"></div></div>',
                            elements:{
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                noDuplicates:{
                                    type:'checkbox',
                                    label:'No Duplicates',
                                    title:'JSN_UNIFORM_IF_CHECKED_VALUE_DUPLICATION'
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        value:{
                            type:'text',
                            label:'Predefined Value'
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><input class="${size}" type="text" placeholder="${value}" /></div></div>'
            });
            //Date controls
            JSNVisualDesign.register('date', {
                caption:'Date/Time',
                group:'extra',
                defaults:{
                    label:'Date/Time',
                    instruction:'',
                    required:0,
                    enableRageSelection:0,
                    size:'jsn-input-small-fluid',
                    timeFormat:0,
                    dateFormat:0,
                    yearRangeMin:'1930',
                    yearRangeMax:(new Date).getFullYear() + 10
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }


                    },
                    /* Parameters on values tab */
                    values:{
                        extra:{
                            type:'horizontal',
                            decorator:'<dateValue/> <dateValueRange/>',
                            title:'Predefined Value',
                            elements:{
                                dateValue:{
                                    type:'text',
                                    group:'horizontal',
                                    attrs:{
                                        'class':'input-date-time'
                                    }
                                },
                                dateValueRange:{
                                    type:'text',
                                    group:'horizontal',
                                    attrs:{
                                        'class':'input-date-time'
                                    }
                                }
                            }
                        },
                        selection:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><enableRageSelection/></div><div class="jsn-form-bar"><dateFormat/><dateOptionFormat/></div><div id="jsn-custom-date" class="jsn-form-bar hide"><customFormatDate/></div><div class="jsn-form-bar"><timeFormat/><timeOptionFormat/></div>',
                            elements:{
                                dateFormat:{
                                    type:'checkbox',
                                    label:language['JSN_UNIFORM_SHOW_DATE_FORMAT']
                                },
                                timeFormat:{
                                    type:'checkbox',
                                    label:language['JSN_UNIFORM_SHOW_TIME_FORMAT']
                                },
                                enableRageSelection:{
                                    type:'checkbox',
                                    label:language['JSN_UNIFORM_ENABLE_RANGE_SELECTION']
                                },
                                dateOptionFormat:{
                                    type:'select',
                                    options:{
                                        'mm/dd/yy':'Default - mm/dd/yy',
                                        'yy-mm-dd':'ISO 8601 - yy-mm-dd',
                                        'd M, y':'Short - d M, y',
                                        'd MM, y':'Medium - d MM, y',
                                        'DD, d MM, yy':'Full - DD, d MM, yy',
                                        'custom':'Custom format'
                                    }
                                },
                                customFormatDate:{
                                    type:'text',
                                    attrs:{
                                        'id':'jsn-custom-date-field',
                                        'placeholder':language['JSN_UNIFORM_CUSTOM_DATE_FORMAT']
                                    }
                                },
                                timeOptionFormat:{
                                    type:'select',
                                    options:{
                                        'hh:mm tt':'AM/PM',
                                        'HH:mm':'12/24'
                                    }
                                }
                            }
                        },
                        dateRange:{
                            type:'horizontal',
                            decorator:'<div class="jsn-form-bar"><yearRangeMin/><span class="jsn-field-prefix">To</span><yearRangeMax/></div>',
                            title:'Year Range Selection',
                            elements:{
                                yearRangeMin:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'input-inline',
                                    validate:['number'],
                                    attrs:{
                                        'class':'input-small'
                                    }
                                },
                                yearRangeMax:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'input-inline',
                                    validate:['number'],
                                    attrs:{
                                        'class':'input-small'
                                    }
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><div class="input-append jsn-inline"><input placeholder="${dateValue}" class="{{if (timeFormat==1 || timeFormat =="1")&&(dateFormat==1 || dateFormat =="1")  }} input-medium {{else}} input-small {{/if}} uniform-date-time" dateFormat="{{if dateFormat==1||dateFormat=="1"}}${dateOptionFormat}{{/if}}" timeFormat="{{if timeFormat==1||timeFormat=="1"}}${timeOptionFormat}{{/if}}"  type="text" /></div> {{if enableRageSelection==1||enableRageSelection=="1"}}<div class="input-append jsn-inline"><input placeholder="${dateValueRange}" class="{{if  (timeFormat==1 || timeFormat =="1")&&(dateFormat==1 || dateFormat =="1") }} input-medium {{else}} input-small {{/if}} uniform-date-time" dateFormat="{{if dateFormat==1||dateFormat=="1"}}${dateOptionFormat}{{/if}}" timeFormat="{{if timeFormat==1||timeFormat=="1"}}${timeOptionFormat}{{/if}}" type="text" /></div>{{/if}}</div></div>'
            });
            /* End Advanced Group */
            //Country controls
            JSNVisualDesign.register('country', {
                caption:'Country',
                group:'extra',
                defaults:{
                    label:'Country',
                    instruction:'',
                    required:0,
                    size:'jsn-input-small-fluid',
                    items:[
                        {
                            text:"Afghanistan",
                            checked:true
                        },
                        {
                            text:"Albania",
                            checked:false
                        },
                        {
                            text:"Algeria",
                            checked:false
                        },
                        {
                            text:"Andorra",
                            checked:false
                        },
                        {
                            text:"Angola",
                            checked:false
                        },
                        {
                            text:"Antigua and Barbuda",
                            checked:false
                        },
                        {
                            text:"Argentina",
                            checked:false
                        },
                        {
                            text:"Armenia",
                            checked:false
                        },
                        {
                            text:"Australia",
                            checked:false
                        },
                        {
                            text:"Austria",
                            checked:false
                        },
                        {
                            text:"Azerbaijan",
                            checked:false
                        },
                        {
                            text:"Bahamas",
                            checked:false
                        },
                        {
                            text:"Bahrain",
                            checked:false
                        },
                        {
                            text:"Bangladesh",
                            checked:false
                        },
                        {
                            text:"Barbados",
                            checked:false
                        },
                        {
                            text:"Belarus",
                            checked:false
                        },
                        {
                            text:"Belgium",
                            checked:false
                        },
                        {
                            text:"Belize",
                            checked:false
                        },
                        {
                            text:"Benin",
                            checked:false
                        },
                        {
                            text:"Bhutan",
                            checked:false
                        },
                        {
                            text:"Bolivia",
                            checked:false
                        },
                        {
                            text:"Bosnia and Herzegovina",
                            checked:false
                        },
                        {
                            text:"Botswana",
                            checked:false
                        },
                        {
                            text:"Brazil",
                            checked:false
                        },
                        {
                            text:"Brunei",
                            checked:false
                        },
                        {
                            text:"Bulgaria",
                            checked:false
                        },
                        {
                            text:"Burkina Faso",
                            checked:false
                        },
                        {
                            text:"Burundi",
                            checked:false
                        },
                        {
                            text:"Cambodia",
                            checked:false
                        },
                        {
                            text:"Cameroon",
                            checked:false
                        },
                        {
                            text:"Canada",
                            checked:false
                        },
                        {
                            text:"Cape Verde",
                            checked:false
                        },
                        {
                            text:"Central African Republic",
                            checked:false
                        },
                        {
                            text:"Chad",
                            checked:false
                        },
                        {
                            text:"Chile",
                            checked:false
                        },
                        {
                            text:"China",
                            checked:false
                        },
                        {
                            text:"Colombi",
                            checked:false
                        },
                        {
                            text:"Comoros",
                            checked:false
                        },
                        {
                            text:"Congo (Brazzaville)",
                            checked:false
                        },
                        {
                            text:"Congo",
                            checked:false
                        },
                        {
                            text:"Costa Rica",
                            checked:false
                        },
                        {
                            text:"Cote d'Ivoire",
                            checked:false
                        },
                        {
                            text:"Croatia",
                            checked:false
                        },
                        {
                            text:"Cuba",
                            checked:false
                        },
                        {
                            text:"Cyprus",
                            checked:false
                        },
                        {
                            text:"Czech Republic",
                            checked:false
                        },
                        {
                            text:"Denmark",
                            checked:false
                        },
                        {
                            text:"Djibouti",
                            checked:false
                        },
                        {
                            text:"Dominica",
                            checked:false
                        },
                        {
                            text:"Dominican Republic",
                            checked:false
                        },
                        {
                            text:"East Timor (Timor Timur)",
                            checked:false
                        },
                        {
                            text:"Ecuador",
                            checked:false
                        },
                        {
                            text:"Egypt",
                            checked:false
                        },
                        {
                            text:"El Salvador",
                            checked:false
                        },
                        {
                            text:"Equatorial Guinea",
                            checked:false
                        },
                        {
                            text:"Eritrea",
                            checked:false
                        },
                        {
                            text:"Estonia",
                            checked:false
                        },
                        {
                            text:"Ethiopia",
                            checked:false
                        },
                        {
                            text:"Fiji",
                            checked:false
                        },
                        {
                            text:"Finland",
                            checked:false
                        },
                        {
                            text:"France",
                            checked:false
                        },
                        {
                            text:"Gabon",
                            checked:false
                        },
                        {
                            text:"Gambia, The",
                            checked:false
                        },
                        {
                            text:"Georgia",
                            checked:false
                        },
                        {
                            text:"Germany",
                            checked:false
                        },
                        {
                            text:"Ghana",
                            checked:false
                        },
                        {
                            text:"Greece",
                            checked:false
                        },
                        {
                            text:"Grenada",
                            checked:false
                        },
                        {
                            text:"Guatemala",
                            checked:false
                        },
                        {
                            text:"Guinea",
                            checked:false
                        },
                        {
                            text:"Guinea-Bissau",
                            checked:false
                        },
                        {
                            text:"Guyana",
                            checked:false
                        },
                        {
                            text:"Haiti",
                            checked:false
                        },
                        {
                            text:"Honduras",
                            checked:false
                        },
                        {
                            text:"Hungary",
                            checked:false
                        },
                        {
                            text:"Iceland",
                            checked:false
                        },
                        {
                            text:"India",
                            checked:false
                        },
                        {
                            text:"Indonesia",
                            checked:false
                        },
                        {
                            text:"Iran",
                            checked:false
                        },
                        {
                            text:"Iraq",
                            checked:false
                        },
                        {
                            text:"Ireland",
                            checked:false
                        },
                        {
                            text:"Israel",
                            checked:false
                        },
                        {
                            text:"Italy",
                            checked:false
                        },
                        {
                            text:"Jamaica",
                            checked:false
                        },
                        {
                            text:"Japan",
                            checked:false
                        },
                        {
                            text:"Jordan",
                            checked:false
                        },
                        {
                            text:"Kazakhstan",
                            checked:false
                        },
                        {
                            text:"Kenya",
                            checked:false
                        },
                        {
                            text:"Kiribati",
                            checked:false
                        },
                        {
                            text:"Korea, North",
                            checked:false
                        },
                        {
                            text:"Korea, South",
                            checked:false
                        },
                        {
                            text:"Kuwait",
                            checked:false
                        },
                        {
                            text:"Kyrgyzstan",
                            checked:false
                        },
                        {
                            text:"Laos",
                            checked:false
                        },
                        {
                            text:"Latvia",
                            checked:false
                        },
                        {
                            text:"Lebanon",
                            checked:false
                        },
                        {
                            text:"Lesotho",
                            checked:false
                        },
                        {
                            text:"Liberia",
                            checked:false
                        },
                        {
                            text:"Libya",
                            checked:false
                        },
                        {
                            text:"Liechtenstein",
                            checked:false
                        },
                        {
                            text:"Lithuania",
                            checked:false
                        },
                        {
                            text:"Luxembourg",
                            checked:false
                        },
                        {
                            text:"Macedonia",
                            checked:false
                        },
                        {
                            text:"Madagascar",
                            checked:false
                        },
                        {
                            text:"Malawi",
                            checked:false
                        },
                        {
                            text:"Malaysia",
                            checked:false
                        },
                        {
                            text:"Maldives",
                            checked:false
                        },
                        {
                            text:"Mali",
                            checked:false
                        },
                        {
                            text:"Malta",
                            checked:false
                        },
                        {
                            text:"Marshall Islands",
                            checked:false
                        },
                        {
                            text:"Mauritania",
                            checked:false
                        },
                        {
                            text:"Mauritius",
                            checked:false
                        },
                        {
                            text:"Mexico",
                            checked:false
                        },
                        {
                            text:"Micronesia",
                            checked:false
                        },
                        {
                            text:"Moldova",
                            checked:false
                        },
                        {
                            text:"Monaco",
                            checked:false
                        },
                        {
                            text:"Mongolia",
                            checked:false
                        },
                        {
                            text:"Morocco",
                            checked:false
                        },
                        {
                            text:"Mozambique",
                            checked:false
                        },
                        {
                            text:"Myanmar",
                            checked:false
                        },
                        {
                            text:"Namibia",
                            checked:false
                        },
                        {
                            text:"Nauru",
                            checked:false
                        },
                        {
                            text:"Nepa",
                            checked:false
                        },
                        {
                            text:"Netherlands",
                            checked:false
                        },
                        {
                            text:"New Zealand",
                            checked:false
                        },
                        {
                            text:"Nicaragua",
                            checked:false
                        },
                        {
                            text:"Niger",
                            checked:false
                        },
                        {
                            text:"Nigeria",
                            checked:false
                        },
                        {
                            text:"Norway",
                            checked:false
                        },
                        {
                            text:"Oman",
                            checked:false
                        },
                        {
                            text:"Pakistan",
                            checked:false
                        },
                        {
                            text:"Palau",
                            checked:false
                        },
                        {
                            text:"Panama",
                            checked:false
                        },
                        {
                            text:"Papua New Guinea",
                            checked:false
                        },
                        {
                            text:"Paraguay",
                            checked:false
                        },
                        {
                            text:"Peru",
                            checked:false
                        },
                        {
                            text:"Philippines",
                            checked:false
                        },
                        {
                            text:"Poland",
                            checked:false
                        },
                        {
                            text:"Portugal",
                            checked:false
                        },
                        {
                            text:"Qatar",
                            checked:false
                        },
                        {
                            text:"Romania",
                            checked:false
                        },
                        {
                            text:"Russia",
                            checked:false
                        },
                        {
                            text:"Rwanda",
                            checked:false
                        },
                        {
                            text:"Saint Kitts and Nevis",
                            checked:false
                        },
                        {
                            text:"Saint Lucia",
                            checked:false
                        },
                        {
                            text:"Saint Vincent",
                            checked:false
                        },
                        {
                            text:"Samoa",
                            checked:false
                        },
                        {
                            text:"San Marino",
                            checked:false
                        },
                        {
                            text:"Sao Tome and Principe",
                            checked:false
                        },
                        {
                            text:"Saudi Arabia",
                            checked:false
                        },
                        {
                            text:"Senegal",
                            checked:false
                        },
                        {
                            text:"Serbia and Montenegro",
                            checked:false
                        },
                        {
                            text:"Seychelles",
                            checked:false
                        },
                        {
                            text:"Sierra Leone",
                            checked:false
                        },
                        {
                            text:"Singapore",
                            checked:false
                        },
                        {
                            text:"Slovakia",
                            checked:false
                        },
                        {
                            text:"Slovenia",
                            checked:false
                        },
                        {
                            text:"Solomon Islands",
                            checked:false
                        },
                        {
                            text:"Somalia",
                            checked:false
                        },
                        {
                            text:"South Africa",
                            checked:false
                        },
                        {
                            text:"Spain",
                            checked:false
                        },
                        {
                            text:"Sri Lanka",
                            checked:false
                        },
                        {
                            text:"Sudan",
                            checked:false
                        },
                        {
                            text:"Suriname",
                            checked:false
                        },
                        {
                            text:"Swaziland",
                            checked:false
                        },
                        {
                            text:"Sweden",
                            checked:false
                        },
                        {
                            text:"Switzerland",
                            checked:false
                        },
                        {
                            text:"Syria",
                            checked:false
                        },
                        {
                            text:"Taiwan",
                            checked:false
                        },
                        {
                            text:"Tajikistan",
                            checked:false
                        },
                        {
                            text:"Tanzania",
                            checked:false
                        },
                        {
                            text:"Thailand",
                            checked:false
                        },
                        {
                            text:"Togo",
                            checked:false
                        },
                        {
                            text:"Tonga",
                            checked:false
                        },
                        {
                            text:"Trinidad and Tobago",
                            checked:false
                        },
                        {
                            text:"Tunisia",
                            checked:false
                        },
                        {
                            text:"Turkey",
                            checked:false
                        },
                        {
                            text:"Turkmenistan",
                            checked:false
                        },
                        {
                            text:"Tuvalu",
                            checked:false
                        },
                        {
                            text:"Uganda",
                            checked:false
                        },
                        {
                            text:"Ukraine",
                            checked:false
                        },
                        {
                            text:"United Arab Emirates",
                            checked:false
                        },
                        {
                            text:"United Kingdom",
                            checked:false
                        },
                        {
                            text:"United States",
                            checked:false
                        },
                        {
                            text:"Uruguay",
                            checked:false
                        },
                        {
                            text:"Uzbekistan",
                            checked:false
                        },
                        {
                            text:"Vanuatu",
                            checked:false
                        },
                        {
                            text:"Vatican City",
                            checked:false
                        },
                        {
                            text:"Venezuela",
                            checked:false
                        },
                        {
                            text:"Vietnam",
                            checked:false
                        },
                        {
                            text:"Yemen",
                            checked:false
                        },
                        {
                            text:"Zambia",
                            checked:false
                        },
                        {
                            text:"Zimbabwe",
                            checked:false
                        }
                    ],
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        extra:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        items:{
                            type:'itemlist',
                            label:'Items',
                            multipleCheck:false
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><select class="${size}" >{{each(i, val) items}}<option value="${val.text}" {{if val.checked == true || val.checked=="true"}}selected{{/if}}>${val.text}</option>{{/each}}</select></div></div>'
            });
            // Number controls
            JSNVisualDesign.register('number', {
                caption:'Number',
                group:'extra',
                defaults:{
                    label:'Number',
                    instruction:'',
                    required:0,
                    limitation:0,
                    limitMin:0,
                    limitMax:0,
                    size:'jsn-input-mini-fluid',
                    value:''
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-large-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                }
                            }
                        }
                    },
                    /* Parameters on values tab */
                    values:{
                        extra:{
                            type:'horizontal',
                            decorator:'<value/><span class="jsn-field-prefix">.</span><decimal/>',
                            title:'Predefined Value',
                            elements:{
                                value:{
                                    type:'number',
                                    group:'horizontal',
                                    field:'number',
                                    attrs:{
                                        'class':'jsn-input-small-fluid'
                                    }
                                },
                                decimal:{
                                    type:'number',
                                    group:'horizontal',
                                    field:'number',
                                    attrs:{
                                        'class':'input-mini'
                                    }
                                }
                            }
                        },
                        allowUser:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><showDecimal/></div>',
                            elements:{
                                showDecimal:{
                                    type:'checkbox',
                                    label:'Show decimal'
                                }
                            }
                        },
                        limit:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><limitation/><limitMin/><limitMax/></div>',
                            elements:{
                                limitation:{
                                    type:'checkbox',
                                    label:'Limit number'
                                },
                                limitMin:{
                                    type:'number',
                                    label:'within',
                                    validate:['number']
                                },
                                limitMax:{
                                    type:'number',
                                    label:'and',
                                    validate:['number']
                                }
                            }
                        }

                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls clearfix"><input type="text" class="${size}" placeholder="${value}" />{{if showDecimal}}<span class="jsn-field-prefix">.</span><input type="text" class="input-mini" placeholder="${decimal}" />{{/if}}</div></div>'
            });
            //End Country
            //Phone controls
            JSNVisualDesign.register('phone', {
                caption:'Phone',
                group:'extra',
                defaults:{
                    label:'Phone',
                    instruction:'',
                    required:0,
                    format:'1-text',
                    value:''
                },
                params:{
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"></div><div class="clearbreak"></div></div>',
                            elements:{
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }
                    },
                    values:{
                        value:{
                            type:'text',
                            label:'Predefined Value'
                        },
                        extra:{
                            type:'horizontal',
                            decorator:'<oneField/><span class="jsn-field-prefix">-</span><twoField/><span class="jsn-field-prefix">-</span><threeField/>',
                            title:'Predefined Value',
                            elements:{
                                oneField:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'input-inline',
                                    attrs:{
                                        'class':'input-small'
                                    }
                                },
                                twoField:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'input-inline',
                                    attrs:{
                                        'class':'input-small'
                                    }
                                },
                                threeField:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'input-inline',
                                    attrs:{
                                        'class':'input-small'
                                    }
                                }
                            }
                        },
                        format:{
                            type:'select',
                            label:'Phone Format',
                            options:{
                                '1-field':'1 field',
                                '3-field':'3 field'
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls">{{if format=="1-field"}}<input class="jsn-input-medium-fluid" type="text" placeholder="${value}" />{{else}}<div class="jsn-inline"><input type="text" class="jsn-input-mini-fluid" placeholder="${oneField}"></div><span class="jsn-field-prefix">-</span><div class="jsn-inline"><input type="text" class="jsn-input-mini-fluid" placeholder="${twoField}"></div><span class="jsn-field-prefix">-</span><div class="jsn-inline"><input type="text" class="jsn-input-mini-fluid" placeholder="${threeField}"></div>{{/if}}</div></div>'
            });
            //Currency controls
            JSNVisualDesign.register('currency', {
                caption:'Currency',
                group:'extra',
                defaults:{
                    label:'Currency',
                    instruction:'',
                    required:0,
                    format:'Dollars',
                    value:'',
                    showCurrencyTitle:'Yes'
                },
                params:{
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"></div><div class="clearbreak"></div></div>',
                            elements:{
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }
                    },
                    values:{
                        extra:{
                            type:'horizontal',
                            decorator:'<value/><span class="jsn-field-prefix">.</span><cents/>',
                            title:'Predefined Value',
                            elements:{
                                value:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'currency',
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                cents:{
                                    type:'text',
                                    group:'horizontal',
                                    field:'currency',
                                    attrs:{
                                        'class':'input-mini'
                                    }
                                }
                            }
                        },
                        format:{
                            type:'select',
                            label:'Currency Format',
                            options:{
                                'Haht':'฿ Baht',
                                'Dollars':'$ Dollars',
                                'Euros':'€ Euros',
                                'Forint':'Ft Forint',
                                'Francs':'CHF Francs',
                                'Koruna':'Kč Koruna',
                                'Krona':'kr Krona',
                                'Pesos':'$ Pesos',
                                'Pounds':'£ Pounds Sterling',
                                'Ringgit':'RM Ringgit',
                                'Shekel':'₪ Shekel',
                                'Yen':'¥ Yen',
                                'Zloty':'zł Złoty',
                                'Rupee':'₹ Rupee'
                            }
                        },
                        extraShowTitle:{
                            type:'group',
                            decorator:'<div class="form-inline"><showCurrencyTitle/><div>',
                            title:'Predefined Value',
                            elements:{
                                showCurrencyTitle:{
                                    type:'radio',
                                    label:'Show Currency Title',
                                    options:{
                                        'Yes':'Yes',
                                        'No':'No'
                                    }
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}}">' +
                    '<label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}' +
                    '{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label>' +
                    '<div class="controls clearfix">' +
                    '<div class="input-prepend jsn-inline currency-value">' +
                    '<div class="controls-inner">' +
                    '<span class="add-on">{{if format=="Haht"}}฿{{else format=="Rupee"}}₹{{else format=="Dollars"}}&#36;{{else format=="Euros"}}€{{else format=="Forint"}}Ft{{else format=="Francs"}}CHF{{else format=="Koruna"}}Kč{{else format=="Krona"}}kr{{else format=="Pesos"}}&#36;{{else format=="Pounds"}}£{{else format=="Ringgit"}}RM{{else format=="Shekel"}}₪{{else format=="Yen"}}¥{{else format=="Zloty"}}zł{{/if}}</span>' +
                    '<input class="input-medium" type="text" placeholder="${value}" />' +
                    '</div>' +
                    '{{if showCurrencyTitle=="Yes"}}' +
                    '<span class="jsn-help-block-inline">${format}</span>' +
                    '{{/if}}</div>' +
                    '{{if format!="Yen" && format!="Rupee"}}' +
                    '<div class="jsn-inline currency-cents">' +
                    '<div class="controls-inner">' +
                    '<input class="input-mini" type="text" placeholder="${cents}" />' +
                    '</div>' +
                    '{{if showCurrencyTitle=="Yes"}}' +
                    '<span class="jsn-help-block-inline">{{if format=="Haht"}}Satang{{else format=="Dollars"}}Cents{{else format=="Euros"}}Cents{{else format=="Forint"}}Filler{{else format=="Francs"}}Rappen{{else format=="Koruna"}}Haléřů{{else format=="Krona"}}Ore{{else format=="Pesos"}}Cents{{else format=="Pounds"}}Pence{{else format=="Ringgit"}}Sen{{else format=="Shekel"}}Agora{{else format=="Zloty"}}Grosz{{/if}}</span>' +
                    '{{/if}}</div>{{/if}}' +
                    '</div>' +
                    '</div>'
            });
            //Password controls
            JSNVisualDesign.register('password', {
                caption:'Password',
                group:'extra',
                defaults:{
                    label:'Password',
                    instruction:'',
                    required:0,
                    limitMin:0,
                    limitMax:0,
                    confirmation:false,
                    encrypt:'text',
                    hideField:false,
                    value:''
                },
                params:{
                    general:{
                        label:{
                            type:'text',
                            label:'Title'
                        },
                        customClass:{
                            type:'text',
                            label:'Class'
                        },
                        instruction:{
                            type:'textarea',
                            label:'Instruction'
                        },
                        inputsize:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><required/><hideField/></div><div class="pull-right"><size/></div><div class="clearbreak"></div></div>',
                            elements:{
                                size:{
                                    type:'select',
                                    label:'Size',
                                    options:{
                                        'jsn-input-mini-fluid':'Mini',
                                        'jsn-input-small-fluid':'Small',
                                        'jsn-input-medium-fluid':'Medium',
                                        'jsn-input-xlarge-fluid':'Large'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }
                                },
                                hideField:{
                                    type:'checkbox',
                                    label:'Hidden'
                                },
                                required:{
                                    type:'checkbox',
                                    label:'Required'
                                }
                            }
                        }
                    },
                    values:{
                        value:{
                            type:'text',
                            label:'Predefined Value'
                        },
                        valueConfirmation:{
                            type:'text'
                        },
                        optionsPassword:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><div class="pull-left"><confirmation/></div><div class="pull-right"><encrypt/></div><div class="clearbreak"></div></div>',
                            elements:{
                                confirmation:{
                                    type:'checkbox',
                                    label:'Require Confirmation'
                                },
                                encrypt:{
                                    type:'select',
                                    label:'Encryption',
                                    options:{
                                        'text':'No encryption',
                                        'md5':'MD5',
                                        'sha1':'SHA-1'
                                    },
                                    attrs:{
                                        'class':'input-medium'
                                    }

                                }
                            }
                        },
                        limit:{
                            type:'group',
                            decorator:'<div class="jsn-form-bar"><limitation/><limitMin/><limitMax/> characters</div>',
                            elements:{
                                limitation:{
                                    type:'checkbox',
                                    label:'Require length'
                                },
                                limitMin:{
                                    type:'number',
                                    label:'within',
                                    validate:['number']
                                },
                                limitMax:{
                                    type:'number',
                                    label:'and',
                                    validate:['number']
                                }
                            }
                        }
                    }
                },
                tmpl:'<div class="control-group ${customClass} {{if hideField}}jsn-hidden-field{{/if}} jsn-group-field"><label class="control-label">${label}{{if required==1||required=="1"}}<span class="required">*</span>{{/if}}{{if instruction}}<i class="icon-question-sign"></i>{{/if}}</label><div class="controls"><input type="password" placeholder="${value}"  class="${size}"/>{{if confirmation}}<br/><input type="password" placeholder="${valueConfirmation}"  class="${size}"/>{{/if}}</div></div>'
            });
            /** action Hide */
                //form action
            JSNVisualDesign.register('form-actions', {
                caption:'Form Action',
                group:'extra',
                defaults:{
                    btnSubmit:'Submit',
                    btnReset:'Reset',
                    btnNext:'Next',
                    btnPrev:'Prev'
                },
                params:{
                    /* Parameters on general tab */
                    general:{
                        btnSubmit:{
                            type:'text',
                            label:'Submit Button Text'
                        },
                        stateBtnReset:{
                            type:'radio',
                            options:{
                                'No':'No',
                                'Yes':'Yes'
                            },
                            class:'radio inline',
                            label:'Show Button Reset'
                        },
                        btnReset:{
                            type:'text',
                            label:'Reset Button Text',
                            attrs:{
                                'class':'hide'
                            }
                        },
                        btnNext:{
                            type:'text',
                            label:'Next Button Text'
                        },
                        btnPrev:{
                            type:'text',
                            label:'Prev Button Text'
                        }
                    }
                },
                tmpl:'Form Action'
            });
        }
    });