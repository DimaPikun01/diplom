$(document).ready(function() {
    // Применяем Select2 к селекторам
    $('#device_type').select2({
        placeholder: 'Выберите тип устройства',
        allowClear: true,
        width: '250px'
    });

    $('#device_model').select2({
        placeholder: 'Выберите модель',
        allowClear: true,
        width: '250px'
    });

    // AJAX-запрос для заполнения типов устройства
    $.ajax({
        url: '../includes/get_device_data.php',
        method: 'GET',
        success: function(data) {
            var deviceTypes = data.device_types;
            var deviceModels = data.device_models;

            // Заполнение селектора типа устройства
            $('#device_type').empty();
            $('#device_type').append('<option></option>'); // Опция по умолчанию
            deviceTypes.forEach(function(type) {
                var option = $('<option>').attr('value', type).text(type);
                $('#device_type').append(option);
            });

            // Обновление моделей при выборе типа устройства
            $('#device_type').on('change', function() {
                var deviceType = $(this).val();
                $('#device_model').empty(); // Очищаем селектор моделей

                if (deviceType) {
                    $('#device_model').append('<option></option>'); // Опция по умолчанию
                    deviceModels.forEach(function(model) {
                        if (model.device_type === deviceType) { // Только для выбранного типа
                            var option = $('<option>').attr('value', model.model_name).text(model.model_name);
                            $('#device_model').append(option);
                        }
                    });
                }
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Ошибка при получении данных:', textStatus, errorThrown);
        }
    });
});
