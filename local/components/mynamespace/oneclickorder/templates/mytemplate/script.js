$(document).ready(function() {
	$(document).on('submit', '#oneclickorder', function(e){
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function (data) {
				let dataParse = $.parseJSON(data);
				if(dataParse.status){
					document.location.href = 'http://shop.ru/personal/order/make/';
				}else{
					alert('Ошибка. Не удалось найти покупателя!');
				}
			}
		})
	})
});