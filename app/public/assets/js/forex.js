$(document).ready(function() {
    var jqxhr = $.get("http://127.0.0.1:9090/v1/currency", function() {

    })
        .done(function(response) {
            if (response.status == 'success') {
                var row = '<div class="row title">' +
                    '<div class="col-xs-6 col-md-4">Currency</div>' +
                    '<div class="col-xs-6 col-md-2">Exchange Rate</div>' +
                    '<div class="col-xs-6 col-md-2">Surcharge</div>' +
                    '</div>';
                $('#currencies').append(row);

                $.each(response.data, function (item, data) {
                    var row = '<div class="row">' +
                        '<div class="col-xs-6 col-md-4">'+ data.currency_name +' ('+ data.currency_code +')</div>' +
                        '<div class="col-xs-6 col-md-2">'+ data.exchange_rate + '</div>' +
                        '<div class="col-xs-6 col-md-2">'+ data.currency_surcharge + '</div>' +
                        '</div>';
                    $('#currencies').append(row);
                })
            }
        })
        .fail(function() {
            alert("Failed to load currency data");
        });
});