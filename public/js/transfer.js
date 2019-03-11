(function($) {

    $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    };

    // only numbers in the amount field
    $(".send-transfer-amount").inputFilter(function(value) {
        return /^\d*[.,]?\d{0,2}$/.test(value);
    });

    // only numbers and white spaces in receiver account number field
    $(".send-transfer-receiver-account-number").inputFilter(function(value) {
        return /^[\d+ ]*$/.test(value);
    });

    // accepting or declining transfers
    $('.transfer-queue-decision').children().click(function() {
        let transferDecision;
        if ($(this).parent().hasClass('transfer-queue-decline')) {
            transferDecision = 'decline';
        } else if ($(this).parent().hasClass('transfer-queue-confirm')) {
            transferDecision = 'confirm';
        } else {
            transferDecision = 'error';
        }
        let transferId = parseInt($(this).parent().parent().attr('id'));
        let transferRow = $(this).parent().parent();
        let transferTBody = $('.transfer-queue > tbody');
        let transferCount = transferTBody.children('tr').length;
        $.ajax({
            url: '/manage/transfer-queue/decision',
            type: 'POST',
            data: {
                transferId : transferId,
                transferDecision: transferDecision
            },
            dataType: 'json',
            async: true,
            success: function(data) {
                if (transferCount === 1) {
                    transferRow.remove();
                    transferTBody.html("<tr><td colspan='9'>Brak transferów do realizacji</td></tr>");
                } else {
                    transferRow.remove();
                }
            }
        });
    });
    // $('.transfer-queue-decision > .fa-check-circle').click(function() {
    //     let transferId = parseInt($(this).parent().parent().attr('id'));
    //     let transferRow = $(this).parent().parent();
    //     let transferTBody = $('.transfer-queue > tbody');
    //     let transferCount = transferTBody.children('tr').length;
    //     $.ajax({
    //         url: '/transfer/queue/accept',
    //         type: 'POST',
    //         data: {transferId : transferId},
    //         dataType: 'json',
    //         async: true,
    //         success: function(data) {
    //             if (transferCount === 1) {
    //                 transferRow.remove();
    //                 transferTBody.html("<tr><td colspan='9'>Brak transferów do realizacji</td></tr>");
    //             } else {
    //                 transferRow.remove();
    //             }
    //         }
    //     });
    // });

}(jQuery));