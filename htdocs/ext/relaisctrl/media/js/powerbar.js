var deviceBusy = false;
function initiatePowerbar() {

	function getSwitchStates (callback,currentTry) {
		var maxRetry = 3;
		currentTry = currentTry!==undefined?currentTry:1;

		$.ajax({
			url: '/ajax/relaisctrl/state',
			type: 'GET',
			dataType: 'json',
		})
		.done(function(response) {
			callback(response);
		})
		.fail(function() {
			console.log(currentTry);
			console.log("getting switch states failed, retrying "+currentTry+"/3...");
			if (currentTry<maxRetry) {
				getSwitchStates(callback,currentTry+1);
			} else {
				console.log("fail");
				// #@todo handle error
			}
		})
		.always(function() {
		});
	}
	function updateSwitchStates () {
		getSwitchStates(function (state) {
			for (var i = 0; i < state.length; i++) {
				if (state[i]) {
					$('.powerbutton[data-id='+i+'] img, img.powerbutton[data-id='+i+']').attr('src',
						'/ext/relaisctrl/media/img/powerbuttons/red.png');
				} else {
					$('.powerbutton[data-id='+i+'] img, img.powerbutton[data-id='+i+']').attr('src',
						'/ext/relaisctrl/media/img/powerbuttons/black.png');
				}
			}
		});
	}
	function toggleSwitch (id) {
		if (deviceBusy) {
			return;
		}
		$('.powerbutton[data-id='+id+'] img, img.powerbutton[data-id='+id+']').attr('src',
			'/ext/relaisctrl/media/img/powerbuttons/yellow.png');
		deviceBusy = true;

		$.ajax({
			url: '/ajax/relaisctrl/toggle/'+id,
			type: 'POST',
			dataType: 'json',
		})
		.done(function() {
		})
		.fail(function() {
		})
		.always(function() {
			deviceBusy = false;
			updateSwitchStates();
		});
	}

	function getButtonConfig () {
		$.ajax({
			url: '/ajax/relaisctrl/powerbar',
			type: 'GET',
			dataType: 'json',
		})
		.done(function(powerbar) {
			for (index in powerbar) {
				var button = $('<div class="powerbutton">');
				button.attr('data-id',powerbar[index]['id']);
				var img = $('<img>');
				img.attr('src','/ext/relaisctrl/media/img/powerbuttons/yellow.png');
				button.append(img);
				var label = $('<span>').html(powerbar[index]['label']);
				button.append(label);
				$('#powerbuttons').append(button);
			}
			$('#powerbuttons').css('width',powerbar.length*84+'px');
			updateSwitchStates();
		})
		.fail(function() {
		})
		.always(function() {
		});
	}

	function shake (left,direction) {
		console.log(direction);
		$('#powerbuttons').animate({
			left: left+10*direction,
		}, 25, function() {
			$('#powerbuttons').animate({
				left: left-10*direction,
			}, 50, function() {
				$('#powerbuttons').animate({
					left: left,
				}, 25);
			});
		});
	}

	function scrollBar (distance, direction) {
		var curLeft = $('#powerbuttons').css('left');
		curLeft = parseInt(curLeft.substr(0,curLeft.length-2));
		if (isNaN(curLeft)) {
			curLeft = 0;
		}
		var delta = distance*direction*-1;
		var newLeft = curLeft+delta;
		var minLeft = -1 * ($('#powerbuttons').width() + distance - $('#scrollwrapper').width());
		if ((newLeft>0&&direction===-1)||
			(newLeft<minLeft&&direction===1)) {
			shake(curLeft,direction);
			return;
		}
		$('#powerbuttons').animate({
			left: newLeft+'px'
		}, 100);

	}

	$('body').on('click', '.powerbutton', function(event) {
		var button = this;
		var id = $(button).attr('data-id');
		toggleSwitch(id);
	});
	$('.arrowbox.arrowbox-left').on('click', function(event) {
		scrollBar(84*4, -1);
	});
	$('.arrowbox.arrowbox-right').on('click', function(event) {
		scrollBar(84*4, 1);
	});

	getButtonConfig();

	tmp = scrollBar;
}
var tmp;

$(document).ready(function() {
	initiatePowerbar();
});
