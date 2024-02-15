"use strict";

jQuery.fn.wpMediaElement = function (options) 
{
    var defaults = {
        obj: null,
        mediaControl: null,
        addImgLinkSelector: '.upload-custom-img',
        delImgLinkSelector: '.delete-custom-img',
        imgContainerSelector: '.custom-img-container',
        imgIdInputSelector: '.logo_image_id',
        addImgLink: null,
        delImgLink: null,
        imgContainer: null,
        imgIdInput: null,
        frame: {},
        isfileUpload: false,
    };
    
    var options = jQuery.extend(defaults, options);
          
    if(typeof wpmediaelement_parameters !== 'undefined' && typeof options.frame.title == 'undefined'){
        options.frame.title = wpmediaelement_parameters.text.frame_title;
    } else if(typeof options.frame.title == 'undefined') {
        options.frame.title = 'Select or Upload Media Of Your Chosen Persuasion';
    }

    if(typeof wpmediaelement_parameters !== 'undefined' && typeof options.frame.button == 'undefined'){
        options.frame.button = wpmediaelement_parameters.text.frame_button;
    } else if(typeof options.frame.button == 'undefined') {
        options.frame.button = 'Use this media';
    }

    /* Public API */
    this.getCurrent = function()
    {
        return options.obj;
    }
        
    return this.each (function () 
    {
        options.obj = jQuery(this);

        options.addImgLink = options.obj.find(options.addImgLinkSelector);
        options.delImgLink = options.obj.find(options.delImgLinkSelector);
        options.imgContainer = options.obj.find(options.imgContainerSelector);
        options.imgIdInput = options.obj.find(options.imgIdInputSelector);

        init_start();

        return this;
    });

    function init_start()
    {
        console.log('init_start'+options.obj.attr('id'));
        
        options.frame = wp.media({
            title: options.frame.title,
            library: {
                type: 'image'
            },
            button: {
                text: options.frame.button
            },
            multiple: false
        });
        
        options.frame.on( 'open', updateFrame ).state('library').on( 'select', selectImg );
        
		options.addImgLink.on( 'click', function( e ) {
			e.preventDefault();
			
			options.frame.open();
		});
        
        // DELETE IMAGE LINK
        options.delImgLink.on( 'click', function( event ){
            
            event.preventDefault();
            
            // Clear out the preview image
            options.imgContainer.html( '' );
            
            // Un-hide the add image link
            options.addImgLink.removeClass( 'hidden' );
            
            // Hide the delete image link
            options.delImgLink.addClass( 'hidden' );
            
            // Delete the image id from the hidden input
            options.imgIdInput.val( '' );
            
            //trigger update widget
            options.imgIdInput.change();
        });

    }
    
    function updateFrame()
    {
        // Do something when the media frame is opened.
    }
    
    function selectImg()
    {
      // Get media attachment details from the frame state
      var attachment = options.frame.state().get('selection').first().toJSON();
      
      // Send the attachment URL to our custom image input field.
      if (options.isfileUpload) {
        options.imgContainer.append( attachment.name ); 
      } else {
        options.imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );
      }
      // Send the attachment id to our hidden input
      options.imgIdInput.val( attachment.id );
    
      // Hide the add image link
      options.addImgLink.addClass( 'hidden' );
    
      // Unhide the remove image link
      options.delImgLink.removeClass( 'hidden' );
      
      //trigger update widget
      options.imgIdInput.change();
    }

}