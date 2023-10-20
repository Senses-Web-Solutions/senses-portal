/* eslint-disable prefer-arrow-callback */
import Route from '../Route';

window.senses_test = {
    location: {
        pathname: '/'
    }
}

const urls = [
    '/tasks',
    '/tasks/123',
    '/tasks/123/highlights',
    '/assets',
    '/assets/123',
    '/assets/123/highlights',
    '/reports/breakdown-report',
    '/reports/stw-report/123'
];

let passes = 0;
let failures = 0;

function testRouteIs(...args) {
    if (Route.is(...args)) {
        console.log('\x1b[32m%s\x1b[0m', `Test passed for ${window.senses_test.location.pathname} | route is ${args.join(', ')}`);
        passes += 1;
    } else {
        console.log('\x1b[31m%s\x1b[0m', `Test failed for ${window.senses_test.location.pathname} | route is ${args.join(', ')}`);
        failures += 1;
    }
}

function testRouteIsnt(...args) {
    if (!Route.is(...args)) {
        console.log('\x1b[32m%s\x1b[0m', `Test passed for ${window.senses_test.location.pathname} | route isn't ${args.join(', ')}`);
        passes += 1;
    } else {
        console.log('\x1b[31m%s\x1b[0m', `Test failed for ${window.senses_test.location.pathname} | route isn't ${args.join(', ')}`);
        failures += 1;
    }
}

(function testTaskIndex() {
    window.senses_test.location.pathname = '/tasks';

    testRouteIs('tasks');
    testRouteIs('tasks', 'any');
    testRouteIsnt('tasks', 'show');
    testRouteIsnt('assets');
})();

(function testTaskShow() {
    window.senses_test.location.pathname = '/tasks/123';

    testRouteIsnt('tasks');
    testRouteIs('tasks', 'any');
    testRouteIs('tasks', 'show');
    testRouteIsnt('assets');
})();

(function testAssetIndex() {
    window.senses_test.location.pathname = '/assets';

    testRouteIsnt('tasks');
    testRouteIsnt('tasks', 'any');
    testRouteIs('assets');
    testRouteIsnt('assets', 'show');
})();