$(document).ready(function () {
    function removeDate() {
        $(this).closest('.date-pair').remove();
    }

    function updateMinEndDate() {
        const startDateInput = $(this);
        const endDateInput = startDateInput.next('input[type="date"]');
        endDateInput.attr('min', startDateInput.val());
    }

    $('#add-more-dates').click(function () {
        const newDatePair = $('<div class="date-pair"><input type="date" name="start_date[]" required><input type="date" name="end_date[]" required><button type="button" class="remove-date">Remover</button></div>');
        newDatePair.find('.remove-date').click(removeDate);
        newDatePair.find('input[type="date"]:first').on('change', updateMinEndDate);
        $('#date-pairs-container').append(newDatePair);
    });

    $('#clear-all-dates').click(function () {
        $('#date-pairs-container').empty();
        $('#add-more-dates').trigger('click');
    });

    $('#date-pairs-form').submit(function (event) {
        event.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'calculate.php',
            data: formData,
            success: function (response) {
                $('#result').text(`Total de dias não concomitantes: ${response} dias`).show();
            },
            error: function () {
                alert('Ocorreu um erro ao calcular os intervalos de datas.');
            }
        });
    });

    $('.remove-date').click(removeDate);

    $('input[name="start_date[]"]').on('change', updateMinEndDate);
});