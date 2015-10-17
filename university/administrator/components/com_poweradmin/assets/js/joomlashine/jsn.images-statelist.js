(function($){
	$.fn.imagesStateList = function( ops ){
		
		this.options = $.extend(
			{
				elementClass      : 'imgStatelist',
				imgStatelistName  : 'imgStatelist',
				imgState          : []
			},
			$.fn.imagesStateList._default(),
			//Apply your options
			ops
		);
		
		//Create an list to store 
		this.list = new Array();
		
		var i = 0, $this = this;
		//Scan all elements and add to list
		$(this).find( 'img.'+this.options.elementClass ).each( function(){
			$this.list[i] = {
				img   : undefined,
				input : undefined
			};
			
			var state = $.fn.imagesStateList.getState( $this.options.imgState, $(this).attr('value') );
			
			//Add image state to image tag
			$(this).attr('src', state.imgPath);
			$(this).wrap('<div class="'+$this.options.elementClass+'" style="float:right;cursor:pointer;" item="'+i+'"/>');
			
			var wrap  = $(this).parent();
			var input = $('<input />', {
				type  : 'hidden',
				name  : $this.options.imgStatelistName+'['+$(this).attr('name')+']',
				value : state.value
			}).appendTo( wrap );
			
			$this.list[i].img   = $(this);
			$this.list[i].input = input;
			
			$(this).removeClass($this.options.elementClass).removeAttr('name').removeAttr('value').attr('title', state.title);
			
			//Add event to change 
			wrap.click(function(){
				var itemIndex    = $(this).attr('item');
				var currentState = $this.list[itemIndex].input.attr('value');
				var nextState    = $.fn.imagesStateList.getNextState( $this.options.imgState, currentState );
				
				$this.list[itemIndex].img.attr('src', nextState.imgPath);
				$this.list[itemIndex].img.attr('title', nextState.title);
				$this.list[itemIndex].input.attr('value', nextState.value);
			});
			
			i++;
		});
	};
	
	/**
	 * Default options
	 */
	$.fn.imagesStateList._default = function(){
		return {
			elementClass      : 'imgStatelist',
			imgStatelistName  : 'imgStatelist',
			imgState          : [
									{
									 	'value'   : 'globally',
									 	'title'   : 'Save to "All pages globally/This page only"',
										'imgPath' : baseUrl + 'administrator/components/com_poweradmin/assets/images/globally.png'
									},
									{
										'value'   : 'only',
										'title'   : 'Save to "All pages globally/This page only"',
										'imgPath' : baseUrl + 'administrator/components/com_poweradmin/assets/images/only.png'
									}
			                    ]
		};
	};
	/**
	 * Get state
	 */
	$.fn.imagesStateList.getState = function( states, currState ){
		var _state = states[0];
		for(k in states){
			var state = states[k];
			if (state.imgPath != undefined && state.value == currState){
				_state = state;
			}
		}
		
		return _state;
	};
	/**
	 * Get next state
	 */
	$.fn.imagesStateList.getNextState = function( states, currState ){
		var _state = states[0];
		for(k in states){
			var state = states[k];
			if (state.imgPath != undefined ){
				if (state.value == currState){
					n = parseInt(k) + 1;
					if ( states[n] != undefined && states[n].imgPath != undefined ){
						_state = states[n];
					}else if(states[n] != undefined && states[n].imgPath == undefined ){
						_state = states[0];
					}
				}
			}
		}
		
		return _state;
	};
})(JoomlaShine.jQuery);
