$(document).ready(function() {
	$(document).on('submit', '#oneclickorder', function(e){
		e.preventDefault();
		let phone = $('input[name="phone"]').val();
		if(phone){
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				success: function (data) {
					let dataParse = $.parseJSON(data);
					let message;
					if(dataParse)
						message = 'Заказ успешно создан! Ваш номер: ' + phone;
					else
						message = 'Ошибка создания заказа.'
					alert(message);
				}
			})
		}else{
			alert('Укажите телефон.');
		}
	})
});