function getCurrencies() {
    $.get('http://127.0.0.1:9090/v1/currency', function() {})
        .done(function(response) {
            if (response.status === 'success') {
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
                        getCurrency($(this).attr('data'));
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
     $.get('http://127.0.0.1:9090/v1/currency/'+ currency, function() {})
        .done(function(response) {
            if (response.status === 'success') {
                $('#currency-select').html('<h2>Enter either an amount you would to buy or pay</h2><div id="currency" class="currency-options"></div>');

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
                    '<div class="col-xs-6 col-md-2">' +
                    '<button class="btn-lg btn-primary" id="buy" data="'+ response.data.currency_code +'">Buy '+ response.data.currency_code +'</button></div>' +
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
                       getQuote();
                    });

                $('#cancel')
                    .on('click', function() {
                        $(location).attr('href', '/');
                    });
            } else {
                alert('Failed to load currency: ' + currency);
            }
        })
        .fail(function() {
            alert('Failed to load currency: ' + currency);
        });
}

function getQuote() {
    var currencyCode = $('#buy').attr('data');
    var currencyAmount = $('#purchase').val();
    var payableAmount = $('#pay').val();

    if (currencyAmount !== '' && !$.isNumeric(currencyAmount)) {
        alert('The amount you specifed is not valid');
        return false;
    }


    if (payableAmount !== '' && !$.isNumeric(payableAmount)) {
        alert('The amount you specifed is not valid');
        return false;
    }

    $.post('http://127.0.0.1:9090/v1/orders/quote',
        JSON.stringify({
            currency_code: currencyCode,
            currency_amount: currencyAmount,
            payable_amount: payableAmount
        }),
        function() {}, 'json')
        .done(function(response) {
            if (response.status === 'success') {
                displaySummary('Quotation', currencyCode, response, 'Payable');

                html = '<div class="row" id="purchase-buttons">'+
                    '<div class="col-xs-6 col-md-2">' +
                    '<button class="btn-lg btn-primary" id="buy" data-currency-code="'+ response.data.currency_code +'"' +
                    ' data-currency-amount="'+ response.data.currency_amount +'">Purchase</button></div>' +
                    '<div class="col-xs-6 col-md-2"><button class="btn-lg btn-danger" id="cancel">Cancel</button></div></div>';
                $('#currency-select').append(html);

                $('#buy')
                    .on('click', function() {
                        createOrder();
                    });

                $('#cancel')
                    .on('click', function() {
                        $(location).attr('href', '/');
                    });
            } else {
                alert('Failed to get a quote');
            }
        })
        .fail(function() {
            alert('Failed to get a quote');
        });
}

function displaySummary(title, currencyCode, response, term) {
    $('#currency-select').html('<h2>'+ title +'</h2><div id="currency" class="currency-options"></div>');

    var html = '<div class="row">';

    if (response.data.order_id !== undefined) {
        html += '<div class="row"><div class="col-xs-6 col-md-4 title">Order #</div>' +
            '<div class="col-xs-6 col-md-4">'+ response.data.order_id +'</div></div>';
    }

    html += '<div class="row"><div class="col-xs-6 col-md-4 title">Currency Amount</div>' +
        '<div class="col-xs-6 col-md-4"><span class="decimal">'+ response.data.currency_amount + '</span> '+ currencyCode +'</div></div>' +
        '<div class="row"><div class="col-xs-6 col-md-4 title">Exchange Rate</div>' +
        '<div class="col-xs-6 col-md-4">'+ response.data.exchange_rate +'</div></div>' +
        '<div class="row"><div class="col-xs-6 col-md-4 title">Surcharge Percentage</div>' +
        '<div class="col-xs-6 col-md-4">'+ response.data.surcharge_percentage +'%</div></div>' +
        '<div class="row"><div class="col-xs-6 col-md-4 title">Surcharge Amount (USD)</div>' +
        '<div class="col-xs-6 col-md-4">$<span class="decimal">'+ response.data.surcharge_amount +'</span></div></div>' +
        '<div class="row"><div class="col-xs-6 col-md-4 title">Amount '+ term +' (USD)</div>' +
        '<div class="col-xs-6 col-md-4">$<span class="decimal">'+ response.data.payable_amount +'</span></div></div>' +
        '<div class="row"><div class="col-xs-6 col-md-4 title">Amount '+ term +' (ZAR)</div>' +
        '<div class="col-xs-6 col-md-4">R<span class="decimal">'+ response.data.zar_amount +'</span></div></div>';
    $('#currency').append(html);

    $('.decimal')
        .each(function() {
            $(this).number(true, 2);
        });
}

function createOrder() {
    var currencyCode = $('#buy').attr('data-currency-code');
    var currencyAmount = $('#buy').attr('data-currency-amount');

    $.post('http://127.0.0.1:9090/v1/orders',
        JSON.stringify({
            currency_code: currencyCode,
            currency_amount: currencyAmount
        }),
        function() {}, 'json')
        .done(function(response) {
            if (response.status === 'success') {
                displaySummary('Order Receipt', currencyCode, response, 'Paid');

                html = '<div class="row" id="purchase-buttons">'+
                    '<div class="col-xs-6 col-md-2"><button class="btn-lg btn-danger" id="cancel">Cancel</button></div></div>';
                $('#currency-select').append(html);

                $('#buy')
                    .on('click', function() {
                        createOrder();
                    });

                $('#cancel')
                    .on('click', function() {
                        $(location).attr('href', '/');
                    });
            } else {
                alert('Failed to create order');
            }
        })
        .fail(function() {
            alert('Failed to create order');
        });
}

$(document).ready(function() {
    getCurrencies();
});