/*!
 * More info at [www.rmcommon.com](http://www.rmcommon.com)
 *
 * Author:  Eduardo Cortés
 * URI:     http://eduardocortes.mx
 * Parte del proyecto "Common Utilities"
 *
 * Copyright (c) 2016, Eduardo Cortés Hervis
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

var cuImagesManager;

(function($){

    cuImagesManager = new CUImagesManager("form#images-uploader");

    $(document).ready(function(){

        $("button[data-action='upload-more']").click(function(){
            cuImagesManager.uploadMore();
        });

    });

}(jQuery));


function show_image_pop(url){
    
  var img = new Image();
  
  // wrap our new image in jQuery, then:
  $(img)
    // once the image has loaded, execute this code
    .load(function () {
      // set the image hidden by default    
      $(this).hide('slow', function(){
          $(this).fadeIn('slow', function(){
              $('#image-loader')
                // then insert our image
                .html(this)
                
                .animate({
                    width: $(this).width()+'px',
                    height: $(img).height()+'px',
                    marginLeft: '-'+($(img).width()/2)+'px',
                    marginTop: '-'+($(img).height()/2)+'px'
                });
          });
      });        
    
    })
    
    // if there was an error loading the image, react accordingly
    .error(function () {
      // notify the user that the image could not be loaded
    })
    
    // *finally*, set the src attribute of the new image to our image
    .attr('src', url)
    .attr('onclick','$("#image-loader").hide("slow");');
    
    //alert($('#image-loader img').attr('src'));

}