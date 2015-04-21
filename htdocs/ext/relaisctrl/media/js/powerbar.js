(function() {

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
					$('.powerbutton[data-id='+i+'] img').attr('src',
						'/ext/relaisctrl/media/img/powerbuttons/red.png');
				} else {
					$('.powerbutton[data-id='+i+'] img').attr('src',
						'/ext/relaisctrl/media/img/powerbuttons/black.png');
				}
			}
		});
	}
	var deviceBusy = false;
	function toggleSwitch (id) {
		console.log(1,id,deviceBusy);
		if (deviceBusy) {
			return;
		}
		$('.powerbutton[data-id='+id+'] img').attr('src',
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

	updateSwitchStates();

	$('.powerbutton').on('click', function(event) {
		var button = this;
		var id = $(button).attr('data-id');
		toggleSwitch(id);
	});

})();
