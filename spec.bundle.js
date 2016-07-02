import 'angular';
import 'angular-mocks';

let context = require.context('./src/app', true, /\.spec\.js/);
context.keys().forEach(context);