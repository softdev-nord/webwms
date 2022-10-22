(function ($) {

    $(document).on('click','.sidebar-dropdown > a',function() {
        $(".sidebar-submenu").slideUp(200);
        if (
            $(this)
                .parent()
                .hasClass("active")
        ) {
            $(".sidebar-dropdown").removeClass("active");
            $(this)
                .parent()
                .removeClass("active");
        } else {
            $(".sidebar-dropdown").removeClass("active");
            $(this)
                .next(".sidebar-submenu")
                .slideDown(200);
            $(this)
                .parent()
                .addClass("active");
        }
    });

    $(document).on('click','.sidebar-submenu-dropdown > a',function() {
        $(".sidebar-submenu-sub").slideUp(200);
        if (
            $(this)
                .parent()
                .hasClass("active")
        ) {
            $(".sidebar-submenu-dropdown").removeClass("active");
            $(this)
                .parent()
                .removeClass("active");
        } else {
            $(".sidebar-submenu-dropdown").removeClass("active");
            $(this)
                .next(".sidebar-submenu-sub")
                .slideDown(200);
            $(this)
                .parent()
                .addClass("active");
        }
    });

    $(document).on('click','#close-sidebar',function() {
        $(".page-wrapper").removeClass("toggled");
        $(".wrapper-form").removeClass("vertical-theme").addClass("toggled");
        $(".wrapper-form-content").removeClass("vertical-theme").addClass("toggled");
        $("#aftTable").css({'width':'100%'});
    });

    $(document).on('click','#show-sidebar',function() {
        $(".page-wrapper").addClass("toggled");
        $(".wrapper-form").addClass("vertical-theme").removeClass("toggled");
        $(".wrapper-form-content").addClass("vertical-theme").removeClass("toggled");
        $("#aftTable").css({'width':'100%'});
    });

})(jQuery);