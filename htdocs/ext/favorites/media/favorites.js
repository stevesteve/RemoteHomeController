

var sortables = [];
function enableEditMode () {
	$('#favoriteform').addClass('editable');
	var lists = document.getElementsByClassName('list-favorites');
	for (var i = lists.length - 1; i >= 0; i--) {
		sortables.push(new Sortable.create(lists[i],{
			group: 'list-favorites',
			onEnd: onEnd
		}));
	}
}

function disableEditMode () {
	$('#favoriteform').removeClass('editable');
	$('.favorite.editable').removeClass('editable');
	for (var i = sortables.length - 1; i >= 0; i--) {
		sortables[i].destroy();
	}
}

function onEnd (event) {
	console.log(event.item);
	var item = $(event.item);
	var category = item.parents('.category').attr('data-id');
	item.find('input.input-category').val(category);
}

function addFavorite () {

	var fav = $('<div>').addClass('favorite editable');

	var a = $('<a>');
	var img = $('<div class="image">')
		.append('<span class="helper" >')
		.append('<img src="">');
	var name = $('<span class="name">');
	a.append(img);
	a.append(name);
	fav.append(a);

	var editicon = $('<img src="/ext/favorites/media/img/edit-icon.png">')
		.addClass('btn-edit-fav');
	var deleteicon = $('<img src="/ext/favorites/media/img/delete-icon.png">')
		.addClass('btn-delete-fav');
	fav.append(editicon);
	fav.append(deleteicon);

	var editcontainer = $(
		'<div class="editcontainer">'+
	 		'<input type="hidden" class="input-category"'+
	 			'name="favorites[category][]"'+
	 			'value="">'+
	 		'<label>URL</label>'+
	 		'<input type="text" class="input-url"'+
	 			'name="favorites[url][]"'+
	 			'value="">'+
	 		'<label>Image</label>'+
	 		'<input type="text" class="input-image"'+
	 			'name="favorites[image][]"'+
	 			'value="">'+
	 		'<label>Label</label>'+
	 		'<input type="text" class="input-label"'+
	 			'name="favorites[label][]"'+
	 			'value="">'+
	 	'</div>');
	fav.append(editcontainer);

	var category = $('.category').eq(0);
	var categoryId = category.attr('data-id');
	editcontainer.find('.input-category').val(categoryId);
	category.find('.list-favorites').append(fav);
}

var newCategoryCounter = 0;
function addCateogry () {
	var categoryId = newCategoryCounter++;
	var category = $(
		'<div class="category" data-id="new'+categoryId+'">'+
			'<h2>'+
				'<span class="categorylabel"></span>'+
				'<input name="categories[new'+categoryId+']" type="text">'+
				'<img src="/ext/favorites/media/img/delete-icon.png"'+
					'class="btn-delete-category">'+
			'</h2>'+
			'<div class="list-favorites"></div>'+
		'</div>');
	$('#favoriteform').append(category);
	sortables.push(new Sortable.create(category.find('.list-favorites')[0],{
		group: 'list-favorites',
		onEnd: onEnd
	}));
}

$('#btn-edit').on('click', function(event) {
	event.preventDefault();
	enableEditMode();
});

$('body').on('click', '.editable a', function(event) {
	event.preventDefault();
});

$('body').on('click', '.btn-edit-fav', function(event) {
	event.preventDefault();
	var favoriteContainer = $(event.target).parents('.favorite');
	if (favoriteContainer.hasClass('editable')) {
		favoriteContainer.removeClass('editable');
		var img = favoriteContainer.find('input.input-image').val();
		favoriteContainer.find('.image img').attr('src',img);
		var label = favoriteContainer.find('input.input-label').val();
		favoriteContainer.find('.name').html(label);
		var url = favoriteContainer.find('input.input-url').val();
		favoriteContainer.find('a').attr('href',url);
	} else {
		favoriteContainer.addClass('editable');
	}
});
$('body').on('click', '.btn-delete-fav', function(event) {
	event.preventDefault();
	var favoriteContainer = $(event.target).parents('.favorite');
	favoriteContainer.remove();
});
$('body').on('click', '.btn-delete-category', function(event) {
	event.preventDefault();
	var category = $(event.target).parents('.category');
	category.remove();
});

$('body').on('click', '#btn-addFav', function (event) {
	event.preventDefault();
	addFavorite();
});
$('body').on('click', '#btn-addCategory', function (event) {
	event.preventDefault();
	addCateogry();
});
