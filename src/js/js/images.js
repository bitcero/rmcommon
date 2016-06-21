var cuImagesManager;

(function($){

    this.CUImagesManager = function(){

        // All uploaded images
        this.uploadedIds = [];

        // Current resized image
        this.currentResizing = 0;

        // Dropzone
        this.dropzone;

        // Icons
        this.okIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><path d="M13.062 3.486c-.626-1.072-1.476-1.92-2.548-2.547S8.27 0 7 0C5.73 0 4.56.313 3.486.94 2.414 1.563 1.566 2.413.94 3.485.312 4.56 0 5.73 0 7s.313 2.44.94 3.514 1.474 1.922 2.546 2.547C4.56 13.69 5.73 14 7 14c1.27 0 2.44-.313 3.514-.94 1.072-.624 1.922-1.474 2.548-2.546S14 8.27 14 7c0-1.27-.312-2.44-.938-3.514zM11.54 5.934l-4.95 4.95c-.116.114-.255.173-.42.173-.157 0-.293-.06-.41-.174l-3.3-3.3c-.108-.11-.163-.246-.163-.41 0-.17.055-.31.164-.42l.83-.82c.12-.115.26-.173.41-.173.16 0 .3.058.41.173l2.06 2.06 3.72-3.71c.12-.115.26-.173.41-.173.16 0 .3.058.41.173l.83.82c.11.11.17.25.17.42 0 .165-.054.3-.163.41z"/></svg>';
        this.errorIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><path d="M7 0C3.134 0 0 3.134 0 7c0 3.865 3.134 7 7 7 3.865 0 7-3.135 7-7 0-3.866-3.135-7-7-7zm0 11.36c-.482 0-.875-.39-.875-.874S6.518 9.61 7 9.61c.483 0 .875.392.875.876s-.392.875-.875.875zm.875-3.485c0 .482-.392.875-.875.875-.482 0-.875-.393-.875-.875V3.5c0-.482.393-.875.875-.875.483 0 .875.393.875.875v4.375z"/></svg>';

        this.init = function(url, size){

            var $this = this;

            Dropzone.options.imagesUploader = false;
            this.dropzone = new Dropzone("form#images-uploader", {
                url: url,
                acceptedFiles: '.png, .jpg, .gif, .jpeg, .svg',
                maxFileSize: size,
                parallelUpload: 1,
                autoProcessQueue: true,
                params: {
                    CUTOKEN_REQUEST: $("#cu-token").val()
                },

                // JS strings for rmcommon must be included
                dictDefaultMessage: cuLanguage.dzDefault,

                // Events listerners
                init: function(){
                    this.on('success', function(file, response){
                        $this.serverResponse(file, response, $this);
                    });

                    this.on('queuecomplete', function(){
                        $("#files-container").slideUp(300);
                        $("#images-resizing").slideDown(300, function(){
                            $this.resizeImages($this);
                        });
                    });
                }
            });

        };

        /**
         * Print a message in "Uploading messages" log
         * @param type
         * @param message
         * @param $this
         */
        this.printMessage = function(type, message, $this){

            var $messages = $("#uploading-messages > ul");

            if(!$("#uploading-messages").is(":visible")){
                $("#uploading-messages").slideDown(200);
            }

            if('error' == type){
                $messages.append('<li class="error">' + $this.errorIcon + ' ' + message);
            } else {
                $messages.append('<li class="success">' + $this.okIcon + ' ' + message);
            }
        }

        /**
         * Update the progress bar
         * @param $this
         */
        this.updateProgress = function($this){

            var total = $this.uploadedIds.length;
            var percent = 1/total*100;

            $("#bar-indicator").animate({
                width: percent*($this.currentResizing)+'%'
            }, 200);

            //$("#bar-indicator").css('width', percent*(current+1)+'%');
            $("#bar-indicator").html(Math.round(percent*$this.currentResizing+1)+'%');

            if (percent * $this.currentResizing > 25)
                $("#bar-indicator").removeClass('progress-bar-danger').addClass('progress-bar-warning');

            if ( percent * $this.currentResizing > 65 )
                $("#bar-indicator").removeClass('progress-bar-warning').addClass('progress-bar-info');

            this.currentResizing++;
        }

        /**
         * Process the server response
         * @param file
         * @param response
         * @param $this
         * @returns {boolean}
         */
        this.serverResponse = function(file, response, $this){

            response = JSON.parse(response);


            if(undefined != response.token && '' != response.token){
                $("#cu-token").val(response.token);
            }

            if(undefined == response || response.type=='error'){
                $this.printMessage('error', file.name + ': ' + response.message, $this);
                return false;
            }

            $this.printMessage('success', response.message, $this);
            $this.uploadedIds.push(response.id);
            return true;

        };

        /**
         * Resize a image element
         * @param $this
         */
        this.resizeImages = function($this){

            var ids = $this.uploadedIds;

            if (ids.length<=0) return;

            if(ids[$this.currentResizing]==undefined){
                $("#bar-indicator").html('100%');
                $("#bar-indicator").animate({
                        width: '100%'
                    }, 200)
                    .removeClass('progress-bar-info')
                    .removeClass('active')
                    .addClass('progress-bar-success');
                $this.currentResizing = 0;
                $this.uploadedIds = [];
                $("button[data-action='upload-more']").fadeIn(300);
                return;
            }

            $this.sendResizeCommand(ids[$this.currentResizing], $this);

        }

        /**
         * Send the instruction to resize a specific image
         * @param id
         * @param $this
         */
        this.sendResizeCommand = function(id, $this){

            var params = {
                action: 'resize',
                CUTOKEN_REQUEST: $("#cu-token").val(),
                img: id
            };

            $.get(xoUrl + '/modules/rmcommon/images.php', params, function(response){

                if (response.type == 'error'){
                    $this.printMessage('error', response.message, $this);
                    $this.updateProgress($this);
                    $this.resizeImages($this);
                    return;
                }

                $this.printMessage('success', response['message'], $this);
                $this.updateProgress($this);
                $("#images-resizing span.message").html(cuLanguage.resizingLegend.replace('%1', $this.currentResizing).replace('%2', $this.uploadedIds.length));
                $this.resizeImages($this);

            }, "json");
            
        },

        this.uploadMore = function(){

            var $this = this;

            $("#images-resizing").slideUp(300, function(){
                $("#uploading-messages").slideUp(300, function(){
                    $(this).find("ul > li").remove();
                    $("#files-container").slideDown(300);
                    $this.dropzone.removeAllFiles();
                    $("button[data-action='upload-more']").fadeOut(300);
                });
            })

        }
    };

    cuImagesManager = new CUImagesManager();

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