if ($('input[name=js-check-reorder-permission]').val()==='true') {
	var list = $('tbody')[0];
	var sortable = new Sortable(list,{
		filter: 'input'
	});
}

$('input[type=text]').on('click',function (event) {
	this.focus();
});

$('.switchlabel').on('keyup', function (event) {
	if ($(this).val()==="") {
		$(this).parents('tr').find('input[type=checkbox]')
			.prop('checked',false);
	} else {
		$(this).parents('tr').find('input[type=checkbox]')
			.prop('checked',true);
	}
});
