$(document).ready(function() {
    // Обработка ввода в поле "Имя клиента"
    $('#client_name').on('input', function() {
        var clientName = $(this).val();

        if (clientName.length > 2) { // Минимальная длина для поиска
            // Выполняем AJAX-запрос к `find_client.php`
            $.ajax({
                url: '../includes/find_client.php', // URL обработки запроса
                method: 'GET',
                data: { 'client_name': clientName }, // Передаем ФИО клиента
                success: function(data) {
                    var clientData = JSON.parse(data); // Обрабатываем данные JSON

                    if (clientData && clientData.client_name) { // Если клиент найден
                        $('#phone_number').val(clientData.phone_number);
                        $('#device_type').val(clientData.device_type);
                        $('#device_model').val(clientData.model);
                        $('#status').val(clientData.status);
                        $('#issue').val(clientData.issue);
                        $('#cost').val(clientData.cost);
                    } else {
                        // Очищаем поля, если клиент не найден
                        $('#phone_number').val('');
                        $('#device_type').val('');
                        $('#device_model').val('');
                        $('#status').val('');
                        $('#issue').val('');
                        $('#cost').val('');
                    }
                },
                error: function() {
                    console.error('Ошибка при поиске клиента.');
                }
            });
        }
    });
});
