var formidable = require('formidable');

Array.prototype.contains = function(k, callback) {
  var self = this;
  return (function check(i) {
    if (i >= self.length) {
      return callback(false);
    }
    if (self[i] === k) {
      return callback(true);
    }
    return process.nextTick(check.bind(null, i+1));
  }(0));
};

module.exports.isEmpty = function(obj) {
  for(var prop in obj) {
    if(obj.hasOwnProperty(prop))
      return false;
  }
  return true;
}

module.exports.getRedisValue = function(data, name) {
  var redisBits = data.split(";");
  for (var i in redisBits) {
    if (redisBits.hasOwnProperty(i)) {
      if (redisBits[i].substring(0, name.length) == name) {
        var value = redisBits[i].split("|")[1].split(":")[2].replace("\"", "").replace("\"", "");
        return(value);
      }
    }
  }
};

// Handle http requests sent to the Node.js server
module.exports.httpHandler = function(req, res) {
  switch(req.url) {
    case '/ping':
      if (req.method == 'GET') {
//        console.log("Received a [200] " + req.method + " to " + req.url);
        res.writeHead(200, {'Content-Type': 'text/plain'});
        res.end('');
      }
      break;
    case '/push':
      if (req.method == 'POST') {
//        console.log("Received a [200] " + req.method + " to " + req.url);
        form = new formidable.IncomingForm();
        form.parse(req, function(e, fields, files) {
          res.writeHead(200, {'Content-Type': 'text/plain'});
          res.end('');
          httpHandleServerPostRequest(fields);
        });
      }
      break;
    default:
      send404(res);
  };
};

send404 = function(res) {
  res.writeHead(404);
  res.write('404');
  res.end();
};
