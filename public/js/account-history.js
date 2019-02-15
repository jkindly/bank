$(document).ready(function() {

    $('.choose-account').click(function() {
        let accountNumber = $(this).children('.choose-account-number').text();
        let tbody = $('.tbody-transfer-history');
        tbody.html("");

        $.ajax({
            url:      '/history',
            type:     'POST',
            data:     {
                accountNumber: accountNumber
            },
            dataType: 'json',
            async:     true,
            success: function(data, status) {
                tbody.css('display', 'none');
                tbody.html("");
                if (data.length == 0) {
                    tbody.append('<tr><td colspan="4">Brak historii transakcji dla tego rachunku.</td></tr>').stop().show(800);
                }
                for (i = 0; i < data.length; i++){
                    let transfer = data[i];
                    if (transfer['senderAccountNumber'] === accountNumber) {
                        let string = "<tr class='transfer-history-element'>\
                            <td><i class='fas fa-long-arrow-alt-right' title='Przelew wychodzący'></i></td>\
                            <td>" + transfer['createdAt'] + "</td>\
                            <td>" + transfer['receiverName'] + "</td>\
                            <td class='transfer-amount transfer-amount-outgoing'> -" + transfer['amount'] + " PLN</td>\
                        </tr>"
                        tbody.append(string).stop().show(800);
                    } else {
                        let string = "<tr class='transfer-history-element'>\
                            <td><i class='fas fa-long-arrow-alt-left' title='Przelew wychodzący'></i></td>\
                            <td>" + transfer['createdAt'] + "</td>\
                            <td>" + transfer['receiverName'] + "</td>\
                            <td class='transfer-amount transfer-amount-incoming'> " + transfer['amount'] + " PLN</td>\
                        </tr>"
                        tbody.append(string).stop().show(800);
                    }
                }

            },
            error: function(xhr, textStatus, errorThrown) {
                tbody.html("");
                tbody.append('<tr><td colspan="4">Przepraszamy, wystąpił błąd podczas ładowania histori transakcji.</td></tr>');
            }
        });
    });
});