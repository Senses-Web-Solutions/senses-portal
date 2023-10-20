import pluralize from 'pluralize'

class Route {
    constructor() {
        // Note: The first element will be an empty string
        // /companies is exploded to ["", "companies"], for example

        const path = window.location.pathname.split('/');

        // Default to index
        this.type = 'index';
        this.name = '';
        this.id = null;
        this.subPage = null;

        this.segments = path.slice(1, path.length).map(segment => Number.isInteger(parseInt(segment)) ? parseInt(segment) : segment);

        switch (path.length) {
        case 2:
            // ["", "companies"]
            this.name = pluralize.singular(path[1]);
            break;
        case 3:
            // ["", "reports", "breakdown-report"]
            if (Number.isNaN(parseInt(path[2]))) {
                this.name = pluralize.singular(path[1]);
                this.id = path[2];
                // ["", "companies", "123"]
            } else {
                this.name = pluralize.singular(path[1]);
                this.type = 'show';
                this.id = parseInt(path[2]);
            }
            break;
        case 4:
            // ["", "tasks", "123", "highlights"]
            if (Number.isNaN(parseInt(path[2]))) {
                this.name = pluralize.singular(path[2]);
                // ["", "companies", "123"]
            } else {
                this.name = pluralize.singular(path[1]);
                this.type = 'show';
                this.id = parseInt(path[2]);
                this.subPage = path[3];
            }
            break;
        default:
            break;
        }
    }
}

Route.is = (model, type = 'index', id = null, subpage = null) => {
    // [tasks, show, 1, highlights]

    const route = new Route();

    // tasks/1/any
    // tasks/1
    // tasks
    if (type === 'any') {
        return model === route.segments[0];
    }

    // tasks/1/highlights
    // reports/stw-report/123
    if (route.segments.length === 3) {

        if (Number.isInteger(route.segments[1])) {
            // tasks/1/highlights
            // subpage
            return model === route.segments[0] && type === 'index' && id === route.segments[1] && subpage === route.segments[2];
        }
        if (Number.isInteger(route.segments[2])) {
            // reports/stw-report/123
            return model === route.segments[0] && type === 'show' && id === route.segments[1] && subpage === route.segments[2];
        }
    }
    if (route.segments.length === 2) {
        // block-groups/1
        // block-groups/create

        if (Number.isInteger(route.segments[1])) {
            // block-groups/1
            if (id) {
                return model === route.segments[0] && type === 'show' && id === route.segments[1] && subpage === null;
            }
            // block-groups/
            return model === route.segments[0] && type === 'show' && subpage === null;
        }

        if (route.segments[1] === 'create') {
            return model === route.segments[0] && type === 'create';
        }
        return model === route.segments[0] && type === route.segments[1];
    }
    if (route.segments.length === 1) {
        return model === route.segments[0] && type === 'index' && id === null;
    }

    return false;

    // name = pluralize.singular(name);

    // if(type == null){
    //     console.log(name, type, id);
    //     console.log(name, route.name, type, route.type);
    //     return name === route.name && id == null;
    // }

    // if (id == null) {
    //     // console.log(name, type, id);
    //     // console.log(name, route.name, type, route.type);
    //     return name === route.name && type === route.type && subPage == null;
    // }

    // if (subPage == null) {
    //     return name === route.name && type === route.type && id === route.id;
    // }

    // return name === route.name && type === route.type && id === route.id && subPage === route.subPage;
}

Route.id = () => {
    const path = window.location.pathname.split('/');

    if(path.length > 2) {
        return parseInt(path[2]);
    }

    return null;
}

Route.startsWith = (str) => window.location.pathname.startsWith(str);

Route.matches = (str) => window.location.pathname === str;

export default Route;
