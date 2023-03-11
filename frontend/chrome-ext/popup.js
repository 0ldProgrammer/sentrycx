document.addEventListener('DOMContentLoaded', function() {
    var checkPageButton = document.getElementById('clickIt');
    checkPageButton.addEventListener('click', function() {

	    // var current = tabs[0];
	  	var incognito = false;
	  	var url = 'http://localhost:4200/client/device-check'


      chrome.tabs.getSelected(null, function(tab) {
        alert("Hello..! It's my first chrome extension.");
      });
    }, false);
  }, false);
