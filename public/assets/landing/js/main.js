(function ($) {
    "user strict";
    $(window).on("load", () => {
        $("#landing-loader").fadeOut(1000);
    });
    $(document).ready(function () {
        //Header Bar
        $(".nav-toggle").on("click", () => {
            $(".nav-toggle").toggleClass("active");
            $(".menu").toggleClass("active");
        });

        $(".counter-item").each(function () {
            $(this).isInViewport(function (e) {
                if ("entered" === e)
                    for (
                        var i = 0;
                        i < document.querySelectorAll(".odometer").length;
                        i++
                    ) {
                        var n = document.querySelectorAll(".odometer")[i];
                        n.innerHTML = n.getAttribute("data-odometer-final");
                    }
            });
        });
        var header = $("header");
        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 300) {
                header.addClass("active");
            } else {
                header.removeClass("active");
            }
        });
        $(".main-category-slider").owlCarousel({
            loop: true,
            nav: false,
            dots: true,
            items: 1,
            margin: 12,
            autoplay: true,
        });

        $(".testimonial-slider").owlCarousel({
            loop: true,
            margin: 22,
            responsiveClass: true,
            nav: false,
            dots: false,
            loop: true,
            autoplay: true,
            autoplayTimeout: 2000,
            autoplayHoverPause: true,
            items: 1,
            responsive: {
                768: {
                    items: 2,
                },
                992: {
                    items: 3,
                },
                1200: {
                    items: 3,
                },
            },
        });

        $(".owl-prev").html('<i class="fas fa-angle-left">');
        $(".owl-next").html('<i class="fas fa-angle-right">');

        if ($(".wow").length) {
            var wow = new WOW({
                boxClass: "wow",
                animateClass: "animated",
                offset: 0,
                mobile: true,
                live: true,
            });
            wow.init();
        }

        $(".learn-feature-wrapper").on("scroll", function () {
            $(".learn-feature-item-group").addClass("stop-animation");
        });
        $(".learn-feature-wrapper").on("mouseover mouseleave", function () {
            $(".learn-feature-item-group").removeClass("stop-animation");
        });

        $(".section-header .title").each(function () {
            var $this = $(this);
            function getWords(text) {
                $this.html("");
                let x = text.replace(/[^A-Za-z0-9]+/g, " ");
                let newArr = x.trim().split(" ");

                for (var i = 0; i <= newArr.length; i++) {
                    if (newArr[i] != undefined) {
                        if (i + 1 < newArr.length) {
                            $this.append(`<span>${newArr[i]} ${" "}</span>`);
                        } else {
                            $this.append(
                                `<span class="text--base">${
                                    newArr[i]
                                } ${" "}</span>`
                            );
                        }
                    }
                }
            }

            getWords($(this).text());
        });

        var sync1 = $("#sync1");
        var sync2 = $("#sync2");
        var thumbnailItemClass = ".owl-item";
        var slides = sync1
            .owlCarousel({
                startPosition: 12,
                items: 1,
                loop: false,
                margin: 0,
                mouseDrag: true,
                touchDrag: true,
                pullDrag: false,
                scrollPerPage: true,
                autoplayHoverPause: false,
                nav: false,
                dots: false,
                // center: true,
            })
            .on("changed.owl.carousel", syncPosition);

        function syncPosition(el) {
            $owl_slider = $(this).data("owl.carousel");
            var loop = $owl_slider.options.loop;

            if (loop) {
                var count = el.item.count - 1;
                var current = Math.round(
                    el.item.index - el.item.count / 2 - 0.5
                );
                if (current < 0) {
                    current = count;
                }
                if (current > count) {
                    current = 0;
                }
            } else {
                var current = el.item.index;
            }

            var owl_thumbnail = sync2.data("owl.carousel");
            var itemClass = "." + owl_thumbnail.options.itemClass;

            var thumbnailCurrentItem = sync2
                .find(itemClass)
                .removeClass("synced")
                .eq(current);
            thumbnailCurrentItem.addClass("synced");

            if (!thumbnailCurrentItem.hasClass("active")) {
                var duration = 500;
                sync2.trigger("to.owl.carousel", [current, duration, true]);
            }
        }
        var thumbs = sync2
            .owlCarousel({
                startPosition: 12,
                items: 2,
                loop: false,
                margin: 10,
                autoplay: false,
                nav: false,
                dots: false,
                // center: true,
                mouseDrag: true,
                touchDrag: true,
                responsive: {
                    400: {
                        items: 3,
                    },
                    768: {
                        items: 5,
                    },
                    1200: {
                        items: 6,
                    },
                },
                onInitialized: function (e) {
                    var thumbnailCurrentItem = $(e.target)
                        .find(thumbnailItemClass)
                        .eq(this._current);
                    thumbnailCurrentItem.addClass("synced");
                },
            })
            .on("click", thumbnailItemClass, function (e) {
                e.preventDefault();
                var duration = 500;
                var itemIndex = $(e.target).parents(thumbnailItemClass).index();
                sync1.trigger("to.owl.carousel", [itemIndex, duration, true]);
            })
            .on("changed.owl.carousel", function (el) {
                var number = el.item.index;
                $owl_slider = sync1.data("owl.carousel");
                $owl_slider.to(number, 500, true);
            });
        sync1.owlCarousel();
    });
})(jQuery);
