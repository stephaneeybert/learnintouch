// Display the message of the error in the page

var ieReportStatus = new Array();

function ieReport(msg) {
  ieReportStatus.push(msg);
  }

function ieShowReport(err) {
  alert(ieReportStatus.join("\n"));
  }

window.onerror = function (err, url, line) {
  ieReport( err + " [" + url + " - line " + line + "]" );

  ieShowReport();
  }

