import 'angular';
import 'angular-mocks';

let context = require.context('./src/mailbox', true, /\.spec\.js/);
context.keys().forEach(context);