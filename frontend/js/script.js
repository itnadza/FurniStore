$(function() {
    var app = $.spapp({
        defaultView: "#home",   // first page to load
        templateDir: "./pages/"       // folder where your pages are
    });

    app.run();
});


