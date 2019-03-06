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

}(jQuery));