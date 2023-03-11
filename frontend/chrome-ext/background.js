

function allowPermission( args, sendResponse ){
  var type = args['type'];
  var pattern  = _getPattern( args['url'] );

	chrome.contentSettings[type].set({ 
  		'primaryPattern' : pattern,
  		'setting'    : 'allow',
  	}, function() {
  		sendResponse({status : 'OK', debug : pattern, function : 'allowPermission' });
  	})
}

function _getPattern( url ){
  var locationData = _getLocation( url );
  return locationData['protocol'] + "//" + locationData['host'] + "/*" ;
}


function getPermission( args, sendResponse ){
  // chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
  //   var current = tabs[0];
  //   incognito = current.incognito;

    var type = args['type'];
    var url  = args['url'];

    chrome.contentSettings[type].get({
      'primaryUrl': url,
      // 'incognito' : incognito
    }, function(  details ){
      sendResponse({ status : 'OK', setting : details.setting ,debug : details ,function : 'getPermission'});
    });
  // });
  
}

function clearPermission( args, sendResponse){
  var type = args['type'];
  var pattern  = _getPattern( args['url'] );

  chrome.contentSettings[type].clear({ scope : 'regular' } , function(   ){
    sendResponse({ status : 'OK', debug : args });
  })
  chrome.contentSettings[type].set({ 
      'primaryPattern' : pattern,
      'setting'    : 'ask'
    }, function() {
      sendResponse({status : 'OK', debug : args , function : 'clearPermission'});
    })

}




chrome.runtime.onMessageExternal.addListener(
  function(request, sender, sendResponse) {
    // verify `sender.url`, read `request` object, reply with `sednResponse(...)`...
    console.log("MESSAGE SENT", request);
    console.log("SENDER", sender);

    var functionName = request.function;
    var args = request.args;
    switch( functionName ) {
      case 'allowPermission' :
        allowPermission(args, sendResponse );
        break;
      case 'getPermission' :
        getPermission(args, sendResponse);
        break;
      case 'clearPermission':
        clearPermission(args, sendResponse );
        break;
    }
  });

function _getLocation(href) {
    var match = href.match(/^(https?\:)\/\/(([^:\/?#]*)(?:\:([0-9]+))?)([\/]{0,1}[^?#]*)(\?[^#]*|)(#.*|)$/);
    return match && {
        href: href,
        protocol: match[1],
        host: match[2],
        hostname: match[3],
        port: match[4],
        pathname: match[5],
        search: match[6],
        hash: match[7]
    }
}