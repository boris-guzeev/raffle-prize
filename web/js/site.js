(function(){
    // розыгрыш
    $('#play').click(function () {
        $.ajax({
            url: $(this).attr('url'),
            dataType: 'json',
            success: function (data) {
                let prizes = {
                    'points': 'баллы',
                    'items': 'товар',
                    'money': 'деньги'
                };
                $('#message').text('Вы выиграли ' + prizes[data.type] + ': ' + data.result);
                if (data.type === 'money') {
                    $('#message').append('. ' + '<button id="convert">' + 'Конвертировать в ' + (data.result * $('[name="ratio"]').val()) + ' балл(ов) лояльности?' + '</button>');
                    $('#convert').click(function () {
                        $.ajax({
                            url: $('[name="action"]').val(),
                            success: function () {
                                window.location.reload();
                            }
                        });
                    });
                } else {
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000)
                }

            }
        });
    });

    // обмен товара на деньги
    $('a.item').click(function (e) {
        e.preventDefault();
        let result = confirm('Вы действительно хотите поменять товар на деньги?');
        if (result) {
            $.ajax({
                url: $(this).attr('href'),
                data: {
                    id: $(this).attr('id')
                },
                success: function (data) {
                    //window.location.reload();
                }
            });
        }
    });
})();