"use strict";

jQuery.fn.wpMediaMultiple = function (options) 
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
        frame: {}
    };
    
    var options = jQuery.extend(defaults, options);
          
    if(typeof wpmediamultiple_parameters !== 'undefined' && typeof options.frame.title == 'undefined'){
        options.frame.title = wpmediamultiple_parameters.text.frame_title;
    } else if(typeof options.frame.title == 'undefined') {
        options.frame.title = 'Select or Upload Media Of Your Chosen Persuasion';
    }

    if(typeof wpmediamultiple_parameters !== 'undefined' && typeof options.frame.button == 'undefined'){
        options.frame.button = wpmediamultiple_parameters.text.frame_button;
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
                type: [ 'video', 'image' ]
            },
            button: {
                text: options.frame.button
            },
            multiple: true
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
        //var attachment = options.frame.state().get('selection').first().toJSON();

        var attachments = options.frame.state().get('selection').toJSON();

        //console.log(attachments);

        // Send the attachment URL to our custom image input field.
        var input_values = options.imgIdInput.val();

        for (var item in attachments) {
            if(attachments[item].mime.indexOf('video') != -1) {
                options.imgContainer.append( '<div class="winter_mvc-media-card" data-media-id="'+attachments[item].id+'"><video src="'+attachments[item].url+'" controls class="thumbnail"></video><a href="#" class="remove"></a><span href="#" class="move"><span class="dashicons dashicons-editor-expand"></span></span></div>' );
            } else {
                options.imgContainer.append( '<div class="winter_mvc-media-card" data-media-id="'+attachments[item].id+'"><img src="'+attachments[item].url+'" alt="" class="thumbnail"/><a href="#" class="remove"></a></div>' );
            }
            
            if (input_values.slice(-1) != ',')
                input_values += ',';
            
            input_values += attachments[item].id + ',';
        }

        // Send the attachment id to our hidden input
        options.imgIdInput.val( input_values );

        // Hide the add image link
        options.addImgLink.addClass( 'uploaded-hidden' );

        // Unhide the remove image link
        options.delImgLink.removeClass( 'hidden' );

        //trigger update widget
        options.imgIdInput.change();

        // start js manager feature
        /* order */
        var re_order = function(media_element){
            var list_media = '';
            media_element.find('.winter_mvc-media-card').each(function(){
                if(list_media !='')
                    list_media +=',';

                list_media += jQuery(this).attr('data-media-id');
            })
            media_element.closest('.postbox-upload-multiple').find('.logo_image_id').val(list_media);
        }
        /* Sort table */
         options.imgIdInput.closest( '.postbox-upload-multiple').find( '.winter_mvc-media' ).sortable({
            update: function(event, ui) {
                re_order(jQuery(this));
            }
        });
        /* remove media */
        options.imgIdInput.closest( '.postbox-upload-multiple').find('.winter_mvc-media .winter_mvc-media-card .remove').off().on('click', function(e){
            e.preventDefault();
            var media = jQuery(this).closest('.winter_mvc-media')
            jQuery(this).closest('.winter_mvc-media-card').remove();
            re_order(media)
        })
        // end js manager feature
    }

}

/* fix for mobile draggable */ 
/*!
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
*/

!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);