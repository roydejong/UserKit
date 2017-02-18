var UserKit = {
    bootstrap: function () {
        UserKitLoader.loadCss('lib/bootstrap/bootstrap.min.css');
        UserKitLoader.loadCss('style/userkit.css');
        UserKitLoader.allReady(function () {
            UserKit.pageReady();
        });
    },

    pageReady: function () {
        $('#loader').hide();
    }
};

$(document).ready(function () {
    UserKit.bootstrap();
});