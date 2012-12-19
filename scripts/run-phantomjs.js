/* requires phantomjs min version 1.5.0 */

var page = require('webpage').create();
var system = require('system');

var url = system.args[1];
var destinationFile = system.args[2];
var viewportSize = system.args[3];
var zoom = system.args[4];
var paperSize = system.args[5]; /* paper format for pdf */

if (viewportSize !== undefined) {
    var params = viewportSize.split('x');
    var width = params[0];
    var height = params[1];
    page.viewportSize = { width: width, height: height };
}
page.zoomFactor = zoom || 1;
if (paperSize !== undefined) {
    page.paperSize = { format: paperSize };
}

var requested = 0;
var loaded = 0;
page.onResourceRequested = function() { requested++; }
page.onResourceReceived = function(response) {
    if (response.stage == 'end') {
        loaded++;
    }
}

page.open(url, function(status) {
    if (status !== 'success') {
        console.log("PhantomJS: Unable to access network");
        phantom.exit();
    } else {
        var attempt = 0;
        var delay = window.setInterval(function() {
            if (attempt < 10) {
                if (requested == loaded) {
                    window.clearInterval(delay);
                    page.render(destinationFile);
                    phantom.exit();
                } else {
                    attempt++;
                }
            } else {
                window.clearInterval(delay);
                page.render(destinationFile);
                console.log('Failed to wait for all page resources being loaded.');
                phantom.exit();
            }
        }, 500);
    }
});