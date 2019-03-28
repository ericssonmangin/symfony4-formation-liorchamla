/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// Include CSS
require('../css/app.scss');

// Include JS
let $ = require('jquery');
global.$ = global.jQuery = $;

require('bootstrap');
require('./ad_form_config');
require('@fortawesome/fontawesome-free');