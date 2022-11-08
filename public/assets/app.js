/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// Setting Routing as global there
global.Routing = Routing;

// any CSS you import will output into a single css file (app.css in this case)
import './css/app.css';
import './css/ext-main.css';
import './css/webLVS-desktop.css';
import './css/webLVS-tablet.css';
import './css/webLVS-mobile.css';

// Import JS

// start the Stimulus application
import './bootstrap';
import 'jquery'
import './js/jquery-ui.js'
import './js/jquery.dataTables.js'
import './js/dataTables.buttons.js'
import './js/dataTables.select.js'
//import './js/moment.WithLocales.js'
import './js/dataTables.sum.js'
import './js/jszip.js'
import './js/pdfmake.js'
import './js/vfs_fonts.js'
import './js/buttons.html5.js'
import './js/buttons.print.js'
import './js/webWmsCoreJs/custom.js'
import './js/jquery.webWMS.Core.js'
import './jBox/dist/jBox.js'
import './js/ChartJs/chart'
//const $ = require('jquery'); global.$ = global.jQuery = $;

import $ from 'jquery';
window.jQuery = $;
window.$ = $;
