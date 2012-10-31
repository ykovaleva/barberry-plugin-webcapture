/* requires phantomjs min version 1.5.0 */

var page = require('webpage').create();
var system = require('system');

var url = system.args[1];
var destinationFile = system.args[2];
var viewportSize = system.args[3];
var zoom = system.args[4];
var paperSize = system.args[5]; /* paper format for pdf */

page.onLoadFinished = function (status) {
    if (status !== 'success') {
        console.log("PhantomJS: Unable to access network");
    } else {
        if (viewportSize != '') {
            var params = viewportSize.split('x');
            var width = params[0];
            var height = params[1];
            page.viewportSize = { width: width, height: height };
        }
        page.zoomFactor = zoom;
        page.paperSize = { format: paperSize };
        page.render(destinationFile);
    }
    phantom.exit();
}

page.open(url);
