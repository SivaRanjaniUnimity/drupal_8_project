(function ($, Drupal) {
    "use strict";

    Drupal.behaviors.DX8MediaElement = {

        attach: function (context) {

            // Ensure hidden elements do not get automatically run when in a hidden 'something' (only applicable to native HTML5 videos)
            $.each($('.coh-video > .coh-video-inner:not(".mejs__container"):hidden', context), function(){

                // Handle Embeds (YouTube, Vimeo etc)
                if($(this)[0].player && $(this)[0].player.media) {
                    $(this)[0].player.media.pause();
                }

                // HTML5 players
                this.pause();
            });

            $.each($('.coh-video > .coh-video-inner:not(".mejs__container"):visible', context).once('dx8-js-mediaelement-init'), function () {

                var $this = $(this);
                var $videoContainer = $this.closest('.coh-video');
                var options = $this.data('cohVideo');
                var videoSrc = $this.attr('src');
                var videoType = mejs.Utils.getTypeFromFile(videoSrc);

                options.success = function (mediaElement) {

                    // add video renderer class to video container
                    $videoContainer.addClass('coh-video-' + mediaElement.rendererName);

                    // hide center play button if toggled off in sidebar editor
                    if (options.showPlayCenter === false) {
                        $videoContainer.addClass('coh-video-hide-center-play');
                    }

                    // ensure center play button is still clickable if `clickToPlayPause` is disabled
                    if (options.clickToPlayPause === false) {
                        $videoContainer.find('.mejs__overlay-play .mejs__overlay-button').on('click', function () {
                            mediaElement.play();
                        });
                    }

                    // add control hide class to video container if all controls toggled off in sidebar editor
                    if (!options.features.length) {
                        $videoContainer.addClass('coh-video-hide-controls');
                    }

                    function playVideoHover() {
                        $videoContainer.addClass('coh-video-hover');
                        mediaElement.play();
                    }

                    function pauseVideoHover() {
                        $videoContainer.removeClass('coh-video-hover');
                        mediaElement.pause();
                    }

                    // Play video on hover if setting enabled
                    if (options.playOnHover === true) {
                        $videoContainer.on('mouseenter', playVideoHover);
                        $videoContainer.on('mouseleave', pauseVideoHover);
                    }

                    $('body').on('tabs-activate', function (e) {
                        //If the tab contains this media element, fire a window resize event to ensure the video fills the tab.
                        if(e.target.contains(e.target)) {
                            window.dispatchEvent(new Event('resize'));
                        }
                    });
                };

                // If Vimeo embed, hide mediaelement controls
                if (videoType === 'video/x-vimeo' || videoType === 'video/vimeo') {
                    options.features = [];
                    options.showPlayCenter = false;
                }

                // If YouTube embed, hide mediaelement controls
                if (videoType === 'video/x-youtube' || videoType === 'video/youtube') {
                    options.features = [];
                    options.showPlayCenter = false;

                    // If URL doesn't contain `controls=0`, show native controls
                    if (videoSrc.indexOf('controls=0') === -1) {
                        options.youtube = {
                            controls: 1
                        };
                    }
                }

                $this.mediaelementplayer(options);

            });
        },

        detach: function(context, settings, cause)  {

            // Ensure when the video is unloaded or hidden from it is paused
            $.each($('video', context), function () {
                // Handle Embeds (YouTube, Vimeo etc)
                if($(this)[0].player && $(this)[0].player.media) {
                    $(this)[0].player.media.pause();
                }

                // Handle native HTML5 videos
                this.pause();
            });
        }
    };

})(jQuery, Drupal);
