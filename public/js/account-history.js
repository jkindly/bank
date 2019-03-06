(function($) {

    let transfers = $('.transfers');
    let tbody = $('.tbody-transfer-history');
    // let showMoreBtn = $('.show-more-transfers');
    let transferHistory = $('.transfer-history');
    let loading = $('.loading');
    let endTransfers;
    let accountNumber;

    $('.choose-account').click(function() {
        accountNumber = $(this).children().children('.choose-account-number').text();
        tbody.html("");
        $('div').remove('.show-more-transfers');
        endTransfers = false;
        loading.css('top', '0');

        $.ajax({
            url:      '/history',
            type:     'POST',
            data:     {
                accountNumber: accountNumber
            },
            dataType: 'json',
            async:     true,
            beforeSend: function() {
                loading.show();
            },
            success: function(data) {
                $('.show-more-transfers').css('display', 'none');
                tbody.css('display', 'none');
                tbody.html("");
                $('div').remove('.show-more-transfers');

                if (data.length === 0) {
                    tbody.append('' +
                        '<tr>' +
                            '<td colspan="4">Brak historii transakcji dla tego rachunku.</td>' +
                        '</tr>'
                    ).stop().show(800);
                } else {
                    for (let i = 0; i < data.length; i++){
                        let transfer = data[i];
                        if (transfer['senderAccountNumber'] === accountNumber) {
                            let string =
                                "<tr class='transfer-history-element' id='" + transfer['transferId'] + "'>" +
                                    "<td><i class='fas fa-long-arrow-alt-right' title='Przelew wychodzący'></i></td>" +
                                    "<td>" + transfer['createdAt'] + "</td>" +
                                    "<td>" + transfer['receiverName'] + "</td>" +
                                    "<td class='transfer-amount transfer-amount-outgoing'> -" + transfer['amount'] + " PLN</td>" +
                                "</tr>";
                            tbody.append(string).stop().show(800);
                        } else if (transfer['receiverAccountNumber'] === accountNumber) {
                            let string =
                                "<tr class='transfer-history-element' id='" + transfer['transferId'] + "'>" +
                                    "<td><i class='fas fa-long-arrow-alt-left' title='Przelew przychodzący'></i></td>" +
                                    "<td>" + transfer['createdAt'] + "</td>" +
                                    "<td>" + transfer['receiverName'] + "</td>" +
                                    "<td class='transfer-amount transfer-amount-incoming'> " + transfer['amount'] + " PLN</td>" +
                                "</tr>";
                            tbody.append(string).stop().show(800);
                        }
                    }
                    transferHistory.append(
                        "<div class='show-more-transfers'>Pokaż więcej</div>"
                    );
                }
            },
            complete: function() {
              loading.hide();
            },
            error: function() {
                tbody.html("");
                tbody.append(
                    '<tr>' +
                        '<td colspan="4">Przepraszamy, wystąpił błąd podczas ładowania histori transakcji.</td>' +
                    '</tr>'
                );
            }
        });
    });

    transferHistory.on('click', '.show-more-transfers', function() {
        if ($('.choose-account').length === 1) {
            accountNumber = $('.choose-account').children().children('.choose-account-number').text();
        }
        let transferId = parseInt($('.transfer-history-element').last().attr('id'));
        $('.show-more-transfers').css('display', 'none');
        $.ajax({
            url:      '/history/show-more-transfers',
            type:     'POST',
            data:     {
                accountNumber: accountNumber,
                transferId: transferId
            },
            dataType: 'json',
            async:     true,
            beforeSend: function() {
                if (!endTransfers) {
                    transfers.css('opacity', '0.2');
                    loading.show();
                        // .animate({
                        //     top: '+=150px'
                        // });
                }
            },
            success: function(data) {
                if (data.length === 0) {
                    endTransfers = true;
                } else {
                    for (let i = 0; i < data.length; i++){
                        let transfer = data[i];
                        if (transfer['senderAccountNumber'] === accountNumber) {
                            let string =
                                "<tr class='transfer-history-element' id='" + transfer['transferId'] + "'>" +
                                    "<td><i class='fas fa-long-arrow-alt-right' title='Przelew wychodzący'></i></td>" +
                                    "<td>" + transfer['createdAt'] + "</td><td>" + transfer['receiverName'] + "</td>" +
                                    "<td class='transfer-amount transfer-amount-outgoing'> -" + transfer['amount'] + " PLN</td>" +
                                "</tr>";
                            tbody.append(string).stop().show(800);
                        }
                        if (transfer['receiverAccountNumber'] === accountNumber) {
                            let string =
                                "<tr class='transfer-history-element' id='" + transfer['transferId'] + "'>" +
                                    "<td><i class='fas fa-long-arrow-alt-left' title='Przelew przychodzący'></i></td>" +
                                    "<td>" + transfer['createdAt'] + "</td>" +
                                    "<td>" + transfer['receiverName'] + "</td>" +
                                    "<td class='transfer-amount transfer-amount-incoming'> " + transfer['amount'] + " PLN</td>" +
                                "</tr>"
                            tbody.append(string).stop().show(800);
                        }
                    }
                    $('.show-more-transfers').show();
                }
            },
            complete: function() {
                transfers.css('opacity', '1');
                loading.hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                //todo error displaying when ajax fail
                // console.log(errorThrown);
            }
        });

    }).stop();

    tbody.on('click', '.transfer-history-element', function() {
        let transferId = parseInt($(this).attr('id'));
        let currentElement = $(this);
        let elementExpanded = $('.transfer-history-element-expanded');
        $('.transfer-history-element').addClass('disabled');

        // if element doesn't exists in DOM then append it after clicked element
        if (!elementExpanded[0]) {
            getHistoryDetails(transferId, currentElement)
            // if element exists in DOM then check if clicked element has
            // expanded element after itself, if yes remove it, if not remove
            // element in dom, and add new after clicked element
        } else {
            if ($(currentElement).next().is('.transfer-history-element-expanded')) {
                elementExpanded.remove();
                $('.transfer-history-element').removeClass('disabled');
            } else {
                elementExpanded.remove();
                getHistoryDetails(transferId, currentElement);
            }
        }
    });

    function getHistoryDetails(transferId, currentElement) {
        $.ajax({
            url:      '/history/transfer-details',
            type:     'POST',
            data:     {
                transferId: transferId
            },
            dataType: 'json',
            async:     true,
            beforeSend: function() {
                let loader =
                    "<tr class='loading-details'>" +
                    "<td colspan='4'>" +
                    "<img src='img/loading.gif' width='150px' height='150px' alt='Ładowanie'>" +
                    "</td>"
                "</tr>";
                currentElement.after(loader);
            },
            success: function(data) {
                if (data['receiverAddress'] === null) data['receiverAddress'] = '';
                if (data['receiverCity'] === null) data['receiverCity'] = '';
                let string =
                    "<td colspan='4' class='transfer-history-element-expanded' class='p-0'>" +
                        "<table>" +
                            "<tr class='history-row-underline'>" +
                                "<td width='25%'>Tytuł przelewu</td>" +
                                "<td>" + data['title'] + "</td>" +
                            "</tr>" +
                            "<tr>" +
                                "<td width='25%'>Rachunek nadawcy</td>" +
                                "<td>" + data['senderAccountNumber'] +"</td>" +
                            "</tr>" +
                            "<tr class='history-row-underline'>" +
                                "<td>Nadawca</td>" +
                                "<td>" + data['senderFirstName'] + " " + data['senderLastName'] + " <br> " +
                                    "ul. " + data['senderAddress'] + "<br>" +
                                    data['senderCity'] +
                                "</td>" +
                            "</tr>" +
                            "<tr>" +
                                "<td>Rachunek odbiorcy</td>" +
                                "<td>" + data['receiverAccountNumber'] + "</td>" +
                            "</tr>" +
                            "<tr class='history-row-underline'>" +
                                "<td>Odbiorca</td>" +
                                "<td>" + data['receiverName'] + " <br> " +
                                    data['receiverAddress'] + "<br>" +
                                    data['receiverCity'] +
                                "</td>" +
                            "</tr>" +
                            "<tr>" +
                                "<td>Kwota transakcji</td>" +
                                "<td>" + data['amount'] + " PLN</td>" +
                            "</tr>" +
                            "<tr class='history-row-underline'>" +
                                "<td>Saldo po transakcji</td>" +
                                "<td>2311.23 PLN</td>" +
                            "</tr>" +
                            "<tr>" +
                                "<td>Data transakcji</td>" +
                                "<td>" + data['createdAt'] + "</td>" +
                            "</tr>" +
                            "<tr>" +
                                "<td>Numer transakcji</td>" +
                                "<td>" + data['transferId'] + "</td>" +
                            "</tr>" +
                        "</table>" +
                    "</td>";
                currentElement.after(string);
                console.log(data);
            },
            complete: function() {
                $('.loading-details').remove();
                $('.transfer-history-element').removeClass('disabled');
            },
            error: function() {
                let string =
                    "<td colspan='4' class='transfer-history-element-expanded' class='p-0'>" +
                        "<div style='color: red;'>Wystąpił błąd podczas ładowania szczegółów transakcji.</div>" +
                    "</td>";
                currentElement.after(string);
            }
        });
    }

}(jQuery));