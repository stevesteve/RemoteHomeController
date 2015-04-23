$('.user-delete').on('click', function(event) {
	event.preventDefault();
	if (confirm('Delete user?')) {
		document.location.href = $(this).attr('href');
	}
});
console.log("asdf");
