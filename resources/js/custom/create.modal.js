$('#createModal').on('hidden.bs.modal', (e) => {
	$('#pbc-errors').addClass('d-none');
	$('#pbc-errors ul').empty();
})

$('#pbc-submit').click((e) => {
	e.preventDefault();

	let data = new FormData()
	data.set('first_name', $('#pbc-first_name').val())
	data.set('last_name', $('#pbc-last_name').val())
	data.set('title', $('#pbc-title').val())
	data.set('phone', $('#pbc-phone').val())
	if (document.querySelector('#pbc-image').files[0] !== undefined) {
		data.set('avatar', document.querySelector('#pbc-image').files[0])
	}

	axios.post('/contact/create', data)
	.then(response => {
		sessionStorage.setItem('pb.flash.message', response.data.message)
		window.location.reload()
	})
	.catch(errors => {
		$('#pbc-errors ul').empty();
		$('#pbc-errors').removeClass('d-none');
		for (let e in errors.response.data.errors) {
			$('#pbc-errors ul').append(`<li>${errors.response.data.errors[e]}</li>`);
		}
	})
})
