$(document).ready(function() {
    var jqxhr = $.get("http://127.0.0.1:9090/v1/currency", function() {

    })
        .done(function(data) {
            alert( "Data Loaded: " + data );
        })
        .fail(function() {
            alert("Failed to load currency data");
        });
});