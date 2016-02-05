function getCurrencies() {
    var jqxhr = $.get("http://127.0.0.1:9090/v1/currency", function() {

    })
        .done(function(response) {
            if (response.status == 'success') {
                var row = '<div class="row title">' +
                    '<div class="col-xs-6 col-md-4">Currency</div>' +
                    '<div class="col-xs-6 col-md-2">Exchange Rate</div>' +
                    '<div class="col-xs-6 col-md-1 col-centered">Surcharge</div>' +
                    '</div>';
                $('#currencies').append(row);

                $.each(response.data, function (item, data) {
                    var row = '<div class="row selectable" data="'+ data.currency_code +'">' +
                        '<div class="col-xs-6 col-md-4">'+ data.currency_name +' ('+ data.currency_code +')</div>' +
                        '<div class="col-xs-6 col-md-2">'+ data.exchange_rate + '</div>' +
                        '<div class="col-xs-6 col-md-1 col-centered">'+ data.currency_surcharge + '%</div>' +
                        '</div>';
                    $('#currencies').append(row);
                });

                $('.selectable')
                    .on('mouseover', function(event) {
                        event.stopPropagation();
                        $(this).addClass('highlight');
                    })
                    .on('mouseout', function(event) {
                        event.stopPropagation();
                        $(this).removeClass('highlight');
                    })
                    .on('click', function() {
                        var currencyCode = $(this).attr('data');
                        $('#currency-select').html('<div id="currency" class="currency-options"></div>');
                        getCurrency(currencyCode);
                    });
            } else {
                alert("Failed to load currency data");
            }
        })
        .fail(function() {
            alert("Failed to load currency data");
        });
}

function getCurrency(currency) {
    var jqxhr = $.get("http://127.0.0.1:9090/v1/currency/"+ currency, function() {

    })
        .done(function(response) {
            if (response.status == 'success') {
                var html = '<div class="row"><div class="col-xs-6 col-md-4 title">Currency</div>' +
                    '<div class="col-xs-6 col-md-4">'+ response.data.currency_name +' ('+ response.data.currency_code +')</div></div>' +
                    '<div class="row"><div class="col-xs-6 col-md-4 title">Exchange Rate</div>' +
                    '<div class="col-xs-6 col-md-4">'+ response.data.exchange_rate +'</div></div>' +
                    '<div class="row"><div class="col-xs-6 col-md-4 title">Surcharge</div>' +
                    '<div class="col-xs-6 col-md-4">'+ response.data.currency_surcharge +'%</div></div>';
                $('#currency').append(html);

                html = '<div id="currency-inputs"><div class="row">' +
                    '<div class="col-xs-6 col-md-2"><h3>'+ response.data.currency_code +' to buy</h3></div>' +
                    '<div class="col-xs-1 col-md-1"><h3>OR</h3></div>' +
                    '<div class="col-xs-6 col-md-2"><h3>USD to pay</h3></div></div>' +
                    '<div class="row">' +
                    '<div class="col-xs-6 col-md-2"><input type="text" name="purchase" id="purchase" /></div>' +
                    '<div class="col-xs-1 col-md-1">&nbsp;</div>' +
                    '<div class="col-xs-6 col-md-2"><input type="text" name="pay" id="pay" /></div></div>' +
                    '<div class="row" id="purchase-buttons">'+
                    '<div class="col-xs-6 col-md-2"><button class="btn-lg btn-primary" id="buy">Buy '+ response.data.currency_code +'</button></div>'+
                    '<div class="col-xs-1 col-md-1">&nbsp;</div>' +
                    '<div class="col-xs-6 col-md-2"><button class="btn-lg btn-danger" id="cancel">Cancel</button></div></div></div>';
                $('#currency-select').append(html);

                $('#purchase')
                    .on('change focus', function() {
                        $('#pay').val('');
                    });

                $('#pay')
                    .on('change focus', function() {
                        $('#purchase').val('');
                    });

                $('#buy')
                    .on('click', function() {
                        // TODO: Implement this
                    });

                $('#cancel')
                    .on('click', function() {
                        $(location).attr('href', '/');
                    });
            } else {
                alert("Failed to load currency: " + currency);
            }
        })
        .fail(function() {
            alert("Failed to load currency: " + currency);
        });
}

$(document).ready(function() {
    getCurrencies();
});