(function ($) {
    'use strict';

    /* Global Variables */
    var is_scroll = false;
    var current_page = 1;
    var queryArgs;
    queryArgs = {
        'meta_key': '',
        'orderby': 'date',
        'order': 'DESC',
        'is_random': 'yes',
        'is_format': '',
    };
    
    jQuery(document).ready(function ($) {

        /**
         * Featured Images Slider
         */
        $('.slider').slick({
            dots: false,
            infinite: true,
            speed: 500,
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 2000,
            arrows: true,
            // /mobileFirst:true,
            prevArrow: '<button class="slick-prev slick-arrow"><i class="fa fa-arrow-left"></i></button>',
            nextArrow: '<button class="slick-next slick-arrow"><i class="fa fa-arrow-right"></i></button>',
            responsive: [
                {
                    breakpoint: 1100,
                    settings: {
                        arrows: true,
                        slidesToShow: 5,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        arrows: true,
                        slidesToShow: 4,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        arrows: true,
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        arrows: true,
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 500,
                    settings: {
                        arrows: true,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        arrows: true,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 300,
                    settings: {
                        arrows: true,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        /**
         * Get video status (playing or paused)
         */
        Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
            get: function () {
                return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
            }
        });
        
        /**
         * Auto play/play video when popup show
         */
        jQuery('#cinema').click(function () {
            var VidPlay = false;
            var videoPost = document.querySelector('[id^="arve-postvideo-"] video');
            var videoModel = document.querySelector('[id^="arve-modalvideo-"] video');
            setTimeout(function () {
                if (videoPost.playing) {
                    videoPost.pause();
                    VidPlay = true;
                }
                if (VidPlay) {
                    videoModel.play();
                } else {
                    videoModel.pause();
                }
                videoModel.currentTime = videoPost.currentTime;
            }, 100);
        });
        
        /**
         * Auto Play/Pause on clsoing modal without clicking on the cross icon
         */
        var VidPause = false;
        $('[id="videoPopup"]').on('hidden.bs.modal', function () {
            setTimeout(function () {
                var videoPost = document.querySelector('[id^="arve-postvideo-"] video');
                var videoModel = document.querySelector('[id^="arve-modalvideo-"] video');
                if (videoModel.paused) {
                    videoModel.pause();
                    VidPause = false;
                } else {
                    videoModel.pause();
                    VidPause = true;
                }
                if (VidPause) {
                    videoPost.play();
                } else {
                    videoPost.pause();
                }
                videoPost.currentTime = videoModel.currentTime;
            }, 100);
        })
        
        /**
         * Auto play/play video when popup close
         */
//        jQuery('button.close').click(function () {
//            setTimeout(function () {
//                var videoPost = document.querySelector('[id^="arve-postvideo-"] video');
//                var videoModel = document.querySelector('[id^="arve-modalvideo-"] video');
//                if (videoModel.paused) {
//                    videoModel.pause();
//                    VidPause = false;
//                } else {
//                    videoModel.pause();
//                    VidPause = true;
//                }
//                if (VidPause) {
//                    videoPost.play();
//                } else {
//                    videoPost.pause();
//                }
//                videoPost.currentTime = videoModel.currentTime;
//            }, 100);
//        });
        
        /**
         * Video comes on start when ended
         */
        jQuery('video').on('ended', function () {
            var videoPost = document.querySelector('[id^="arve-postvideo-"] video');
            videoPost.paused;
            videoPost.currentTime = 0;
            
//            var videoModal = document.querySelector('[id^="arve-modalvideo-"] video');
//            videoModal.paused;
//            videoModal.currentTime = 0;
//            videomodal.load();
        });
        
        /**
         * Formats Tooltip
         */
        $('[data-toggle="tooltip"]').tooltip();
        
        /**
         * Isotope Initialization
         */
        var isotope_container = $('.isotope');
        isotope_container.imagesLoaded(function () {
            isotope_container.isotope({
                itemSelector: '.col-md-4',
                layoutMode: 'masonry',
                isOriginLeft: true,
                sortAscending: true,
                transitionDuration: '0.5s',
                //sortBy : 'random',
                masonry: {
                    gutterHeight: 40
                }
            });
        });

        var $filters = $('.formats').on('click', 'span', function () {
            /**
             * Add isotopre-hidden class
             */
            var itemHide = Isotope.Item.prototype.hide;
            Isotope.Item.prototype.hide = function () {
                itemHide.apply(this, arguments);
                $(this.element).addClass('isotope-hidden');
            };
            /**
             * Remove isotopre-hidden class
             */
            var itemReveal = Isotope.Item.prototype.reveal;
            Isotope.Item.prototype.reveal = function () {
                itemReveal.apply(this, arguments);
                $(this.element).removeClass('isotope-hidden');
            };
            $('.clear-filter-btn').fadeIn();
            $('.clear-filter-btn').addClass('is-checked');
            var $this = $(this);
            var filterValue;
            if ($this.is('.is-checked')) {
                filterValue = '*';
            } else {
                filterValue = $this.attr('data-filter');
                $filters.find('.is-checked').removeClass('is-checked');
            }
            //$this.toggleClass('is-checked');
            isotope_container.isotope({filter: filterValue});
            if ($(".isotope-hidden").length == $(".isotope .col-md-4").length) {
                $(".no_posts").show();
            } else {
                $(".no_posts").hide();
            }
        });
                
        /**
         * Reset Listing and remove filter
         */
        $('.clear-filter-btn').click(function () {
            $('.formats-img').removeClass('active').removeClass('iamselect');
            $(this).hide();
            
            current_page = 1;
            queryArgs['is_format'] = '';
            th_load_posts(queryArgs);
        });
        
        jQuery('.formats-img').click(function() {
            current_page = 1;
            
            $('.formats-img').addClass('active');
            $(this).removeClass('active');
            $(this).addClass('iamselect');
            
            queryArgs['is_format'] = $(this).data('termid');
            
            console.log(queryArgs);
            console.log(current_page);
            $("#th_no_posts").hide();
            th_load_posts(queryArgs);
        });

        setTimeout(function () {
            th_load_posts(queryArgs);
        }, 100);
        
        /**
         * Load post fucntion - Ajax Initilizer
         */
        function th_load_posts(queryArgs) {
            var dataToPass = {
                'action': 'more_post_ajax',
                'page': current_page,
                'queryArgs': queryArgs,
            };
            //console.log(queryArgs);
            $.ajax({
                type: "POST",
                url: ajax_call.ajaxurl,
                data: dataToPass,
                beforeSend: function () {
                    $("#th_load_more").show();
                    $('#th_load_more .loaderPosts').show();
                },
                success: function (data) {
                    var $data = $(data);
                    if ($data.length) {
                        if (current_page == 1) {
                            $('.home_posts .isotope').empty();
                            isotope_container.isotope('reloadItems');
                        }
                        $('.home_posts .isotope').isotope('insert', $data);
                        var resetGrid = $('.isotope');
                        resetGrid.imagesLoaded(function () {
                            resetGrid.isotope();
                        });
                        $("#th_load_more").hide();
                        $('#th_load_more .loaderPosts').hide();
                        is_scroll = true;
                        current_page++;
                    } else {
                        $("#th_load_more").hide();
                        $('#th_load_more .loaderPosts').hide();
                        $("#th_no_posts").show();
                        is_scroll = false;
                    }
                }
            });
        }

        /**
         * Sticky Header
         */
        $(window).scroll(function () {

            if ($(window).scrollTop() >= 200) {
                $('header').addClass('fixed-header');
            } else {
                $('header').removeClass('fixed-header');
            }
            
            var isotopeHeight = $('.isotope').innerHeight();
            var scrollHeight = $(window).scrollTop() + 600;
            if (scrollHeight > isotopeHeight) {
                if (is_scroll == true) {
                    is_scroll = false;
                    setTimeout(function () {
                        th_load_posts(queryArgs);
                    }, 1000);
                }
            }
        });
        
        /**
         * Handle Order By button click event
         */
        jQuery('.thorderbybtn').click(function() {
            current_page = 1;
            $('.thorderbybtn').removeClass('active');
            $(this).addClass('active');
            
            queryArgs['meta_key'] = $(this).data('meta_key');
            queryArgs['orderby'] = $(this).data('orderby');
            queryArgs['order'] = $(this).data('order');
            queryArgs['is_random'] = '';
            
            $("#th_no_posts").hide();
            th_load_posts(queryArgs);
            
        });
        
        
        /*
         * Post like functionality
         */
        jQuery(document).on('click', '.post-like', function() {
            var clickDiv = $(this);
            var post_id = $(this).data('post_id');
            var vote_action = $(this).data('action');
            var like_counts, new_votes_count;

            var dataToPass = {
                'action': 'handle_post_likes',
                'post_id': post_id,
                'vote_action': vote_action,
            };
            
            if (clickDiv.find('.like-count').length) {
                like_counts = clickDiv.find('.like-count').text();
            } else {
                like_counts = 0;
            }
            
            if(vote_action == 'vote') {
                clickDiv.removeClass('has-not-voted').addClass('has-voted');
                new_votes_count = parseInt(like_counts) + 1;
                clickDiv.data('action', 'unvote');
                if (clickDiv.find('.like-count').length) {
                    console.log('Vote if' + new_votes_count);
                    clickDiv.find('.like-count').html('');
                    clickDiv.find('.like-count').text(new_votes_count);
                } else {
                    console.log('Vote else' + new_votes_count);
                    clickDiv.append('<span class="like-count">' + new_votes_count + '</span>');
                }
            } else {
                clickDiv.removeClass('has-voted').addClass('has-not-voted');
                new_votes_count = parseInt(like_counts) - 1;
                clickDiv.data('action', 'vote');
                if (clickDiv.find('.like-count').length) {
                    clickDiv.find('.like-count').html('');
                    clickDiv.find('.like-count').text(new_votes_count);
                    console.log('unVote if' + new_votes_count);
                } else {
                    console.log('unVote else' + new_votes_count);
                   clickDiv.append('<span class="like-count">' + new_votes_count + '</span>');
                }
            }
            
            $.ajax({
                type: "POST",
                url: ajax_call.ajaxurl,
                data: dataToPass,
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.status == 200) {
                    }
                    
                }
            });
        });

        // get your select element and listen for a change event on it
        $('#categoryPage').change(function () {
            window.location = $(this).val();
        });
        
    });

    $(document).ready(function() {
        $(document).on("contextmenu",function(e){
           return false;
        }); 
    }); 
    
})(jQuery);

