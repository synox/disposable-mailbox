import template from './app.html';
import './app.css';

let appComponent = () => {
    return {
        template,
        restrict: 'E'
    };
};

export default appComponent;