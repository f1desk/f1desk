function ping() {
  var time = 1000 * 60 * 10;
  window.setInterval( function() {
    var tParams = {'enqueue':0};
    var tUrl = 'pong.php';
    xhr.makeRequest('ping',tUrl,tParams);
  },time);
}

ping();