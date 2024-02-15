jQuery(document).ready(function($){
    /* example html code */
    /*
    <div class="wdk_js_gallery_slider">
        <div class="links wdk-hidden">
            <a href="#" title="">
                <img src="#" alt="" />
            </a>
            <a href="#" title="">
                <img src="#" alt="" />
            </a>
            <a href="#" title="">
                <img src="#" alt="" />
            </a>
        </div>
        <div
            id="blueimp-image-carousel"
            class="blueimp-gallery blueimp-gallery-carousel"
            aria-label="image carousel"
            >
            <div class="slides" aria-live="off">
            </div>
            <a
                class="prev"
                aria-controls="blueimp-image-carousel"
                aria-label="previous slide"
            ></a>
            <a
                class="next"
                aria-controls="blueimp-image-carousel"
                aria-label="next slide"
            ></a>
            <a
                class="play-pause"
                aria-controls="blueimp-image-carousel"
                aria-label="play slideshow"
                aria-pressed="true"
                role="button"
            ></a>
        </div>
    </div>
    */
    /* images gellary for listing preview images */
    $('.wdk_js_gallery_slider').each(function(){
        var gallery = $(this)

        var myLinks = new Array();
        var current = $(this).attr('href');
      
        var curIndex = 0;
        gallery.find('.links img,.links video').each(function (i) {
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

            if(img_href.indexOf('.mp4') !=-1 || img_href.indexOf('.flv') !=-1 || img_href.indexOf('.mkv') !=-1 || img_href.indexOf('.avi') !=-1 
            || img_href.indexOf('.webm') !=-1 || img_href.indexOf('.mov') !=-1) {
                myLinks[i]['type'] = 'video/mp4';
            }
      
        });

        var options = {
            container: gallery.find('.blueimp-gallery-carousel'),
            carousel: true
        }

        blueimp.Gallery(myLinks, options);
    });

});