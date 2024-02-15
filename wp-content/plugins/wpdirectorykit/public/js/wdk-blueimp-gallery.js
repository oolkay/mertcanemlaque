jQuery(document).ready(function($){

    /* Start Image gallery 
    *    use css/blueimp-gallery.min.css
    *    use js/blueimp-gallery.min.js
    *    Site https://github.com/blueimp/Gallery/blob/master/README.md#setup
    */
    if(!$('#blueimp-gallery').length){
        $('body').append('<div id="blueimp-gallery" class="blueimp-gallery">\n\
            <div class="slides"></div>\n\
            <h3 class="title"></h3>\n\
            <div class="description"></div>\n\
            <a class="prev">&lsaquo;</a>\n\
            <a class="next">&rsaquo;</a>\n\
            <a class="close">&times;</a>\n\
            <a class="play-pause"></a>\n\
            <ol class="indicator"></ol>\n\
            </div>')
    }

    /* images gellary for listing preview images */
    $('.wdk_js_gallery').each(function(){
        var gallery = $(this)

        if(gallery.find('.slick-slider').length) {
            gallery = gallery.find('.slick-slider .slick-slide:not(.slick-cloned)')
        }

        gallery.find('.wdk-listing-image-card:not(.skip)').on('dragstart', function (e) {
            e.preventDefault();
            $(this).data('dragging', true);
        }).on("click", function(e)
        {
            e.preventDefault();

            if ($(this).data('dragging')) {
                $(this).data('dragging', false);
                return false;
            } 

            var myLinks = new Array();
            var current = $(this).attr('href') || '';
            if($(this).attr('data-fullsrc')){
                current = $(this).attr('data-fullsrc');
            } else if($(this).attr('src')) {
                current = $(this).attr('src');
            }

            if(current == '') {
                if($(this).find('img,video').attr('data-fullsrc')){
                    current = $(this).find('img,video').attr('data-fullsrc');
                } else {
                    current = $(this).find('img,video').attr('src');
                }
            }

            var curIndex = 0;
            gallery.find('img,video').each(function (i) {
                var img_href = '';
                if($(this).attr('data-fullsrc')){
                    img_href = $(this).attr('data-fullsrc');
                } else {
                    img_href = $(this).attr('src');
                }
                myLinks[i] = new Array();
                /* Example Title myLinks[i]['title'] = 'test';*/
                myLinks[i]['href'] = img_href;
                myLinks[i]['thumbnail'] = img_href;

                if(current == img_href)
                    curIndex = i;

                if(img_href.indexOf('.mp4') !=-1 || img_href.indexOf('.flv') !=-1 || img_href.indexOf('.mkv') !=-1 || img_href.indexOf('.avi') !=-1 
                    || img_href.indexOf('.webm') !=-1 || img_href.indexOf('.mov') !=-1) {
                        myLinks[i]['type'] = 'video/mp4';
                    }
            });
    
            var options = {index: curIndex}
            blueimp.Gallery(myLinks, options);

            return false;
        });

    });

});