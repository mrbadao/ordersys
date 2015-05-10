/* Author: Alicia Liu */

(function ($) {
	
	$.widget("ui.tagify", {
		options: {
			delimiters: [13, 188, 44],          // what user can type to complete a tag in char codes: [enter], [comma]
			outputDelimiter: ',',           // delimiter for tags in original input field
			cssClass: 'tagify-container',   // CSS class to style the tagify div and tags, see stylesheet
			addTagPrompt: '',       // placeholder text
			addTagOnBlur: false				// Add a tag on blur when not empty
		},
		
		_create: function() {
			var self = this,
				el = self.element,
				opts = self.options;

			this.tags = [];
			
			// hide text field and replace with a div that contains it's own input field for entering tags
			this.tagInput = $("<input type='text'>")
				.attr( 'readonly', "readonly" )
				.attr( 'placeholder', opts.addTagPrompt )
				.keypress( function(e) {
					var $this = $(this),
					    pressed = e.which;

					for ( i in opts.delimiters ) {
						
						if (pressed == opts.delimiters[i]) {
							self.add( $this.val() );
							e.preventDefault(); 
							return false;
						}
					}
				})
				// we record the value of the textfield before the key is pressed
				// so that we get rid of the backspace issue
				.keydown(function(e){
					self.keyDownValue = $(this).val();
				})
				// for some reason, in Safari, backspace is only recognized on keyup
				.keyup( function(e) {
					var $this = $(this),
					    pressed = e.which;

					// if backspace is hit with no input, remove the last tag
					if (pressed == 8) { // backspace
						if ( self.keyDownValue == '' ) {
							self.remove();
							return false;
						}
						return;
					}
				});
			
			// Add tags blur event when required	
			if (opts.addTagOnBlur) {
				// When needed, add tags on blur
				this.tagInput.blur( function(e) {
					var $this = $(this);
					
					// if lose focus on input field, check if length is empty
					if ('' !== $this.val()) {
						self.add( $this.val() );
						e.preventDefault(); 
						return false;
					}
				})
			}	
				
			this.tagDiv = $("<div></div>")
			    .addClass( opts.cssClass )
				.attr('id', el.attr('class'))
			    .click( function() {
			        $(this).children('input').focus();
			    })
			    .append( this.tagInput )
				.insertAfter( el.hide() );
				
			// if the field isn't empty, parse the field for tags, and prepopulate existing tags
			var initVal = $.trim( el.val() );
initVal = '';

			if ( initVal ) {
				var initTags = initVal.split( opts.outputDelimiter );
				$.each( initTags, function(i, tag) {
				    self.add( tag );
				});
			}
		},
		
		_setOption: function( key, value ) {
			options.key = value;
		},
		
		// add a tag, public function		
		add: function(text,id) {
    		var self = this;
			text = text || self.tagInput.val();
			if (text) {
				var tagIndex = self.tags.length;
                var class_name = self.tagDiv.attr('id');
				
				var removeButton = $("<a href='#'>x</a>")
					.click( function() {
						self.remove( tagIndex, class_name );
						return false;
					});
				var newTag = $("<span data-id='"+id+"'></span>")
					.text( text )
					.append( removeButton );
				
				self.tagInput.before( newTag );
				self.tags.push( text );
				self.tagInput.val('');
			}
		},

        remove: function( tagIndex, class_name ) {
			var self = this;
            
            $('.' + class_name).tagify('sync_remove', tagIndex);
                
			/*if ( tagIndex == null  || tagIndex === (self.tags.length - 1) ) {
				this.tagDiv.children("span").last().remove();
				self.tags.pop();
			}
			if ( typeof(tagIndex) == 'number' ) {
				// otherwise just hide this tag, and we don't mess up the index
				this.tagDiv.children( "span:eq(" + tagIndex + ")" ).hide();
				 // we rely on the serialize function to remove null values
				delete( self.tags[tagIndex] );
			}:*/
		},
        
        sync_remove: function( tagIndex ) {
			var self = this;
			if ( tagIndex == null  || tagIndex === (self.tags.length - 1) ) {
				this.tagDiv.children("span").last().remove();
				self.tags.pop();
			}
			if ( typeof(tagIndex) == 'number' ) {
				// otherwise just hide this tag, and we don't mess up the index
				this.tagDiv.children( "span:eq(" + tagIndex + ")" ).hide();
				 // we rely on the serialize function to remove null values
				delete( self.tags[tagIndex] );
			}
		},

        // all remove a tag, public function
		all_remove: function() {
			var self = this;
			var length = self.tags.length
			for (i = 0; i < length; i++) {
                this.tagDiv.children("span").last().remove();
			}
            this.tags = [];
		},
		
		// serialize the tags with the given delimiter, and write it back into the tagified field
		serialize: function() {
			var self = this;
			var delim = self.options.outputDelimiter;
			var tagsStr = self.tags.join( delim );
var ids = new Array();
var idx = 0;
self.tagDiv.children("span").each(function(){
  $data = $(this);
  if ($(this).css('display') != 'none') {
    ids[idx] = $(this).attr('data-id')+',';
    idx++;
  }
});
var outputStr = ids.join(',');
			
			// our tags might have deleted entries, remove them here
			var dupes = new RegExp(delim + delim + '+', 'g'); // regex: /,,+/g
			var ends = new RegExp('^' + delim + '|' + delim + '$', 'g');  // regex: /^,|,$/g
var notdata = new RegExp('(undefined)','g');
			//var outputStr = tagsStr.replace( dupes, delim ).replace(ends, '');
outputStr = outputStr.replace( dupes, delim,notdata ).replace(ends, '');
			
			self.element.val(outputStr);
			return outputStr;
		},
		
		inputField: function() {
		    return this.tagInput;
		},
		
		containerDiv: function() {
		    return this.tagDiv;
		},
		
		// remove the div, and show original input
		destroy: function() {
		    $.Widget.prototype.destroy.apply(this);
			this.tagDiv.remove();
			this.element.show();
		}
	});

})(jQuery);
