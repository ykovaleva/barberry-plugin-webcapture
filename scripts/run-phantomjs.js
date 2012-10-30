/* requires phantomjs min version 1.5.0 */

var page = require('webpage').create();
var system = require('system');

page.open(system.args[1], function(status) {
    if (status !== "success") {
        console.log("PhantomJS: Unable to access network");
    } else {
        page.zoomFactor = system.args[3];
        page.paperSize = { format: system.args[4] };
        page.render(system.args[2]);
    }
    phantom.exit();
});