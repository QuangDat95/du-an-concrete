$(document).ready(function () {
    zoomImageThumbnail();

    $(function ($) {
        $("#main-menu-navigation li a").click(function (e) {
            $('.menu-content li').removeClass('active');
            $(".nav-item-parent").click(function (e) {
                $(this).find('a').addClass('active');
            });
            var link = $(this);
            var item = link.parent("li");
            if (item.children(".main-menu-content").length > 0) {
                link.attr("href", "#");
                e.preventDefault();
            }
        }).each(function () {
            var link = $(this);
            if (link.get(0).href === location.href) {
                if (link.parents(".menu-content").length > 0) {
                    link.addClass("active").parents("li").addClass("open");
                }
                else {
                    link.addClass("active").parents("li").addClass("active");
                }
                return false;
            }

        });
    });

    function zoomImageThumbnail() {
        $('body').on('click', '.click-image-thumbnail', function () {
            var that = $(this);
            var src = that.attr('src');
            $('.image-thumbnail').attr('src', src);
        });
    }
});