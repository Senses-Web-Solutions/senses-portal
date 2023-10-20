
import { get } from 'lodash-es';

let clientConfig = null;
let clientConfigLoaded = false;
let clientConfigCallbacks = [];

//php config defined into blade.
export function getBackendClientConfig(config) {
    if(config) {
        return get(window.sensesClientConfig, config);
    }
    return window.sensesClientConfig;
}

//Get/Check what client we are
export function getClient() {
    return window.sensesClient;
}

export function isClient(param = null) {
    if (!window.sensesClient) {
        return false;
    }

    if (typeof param === 'string') {
        return param === window.sensesClient;
    }

    if (Array.isArray(param)) {
        return param.includes(window.sensesClient);
    }

    if (param === null) {
        return window.sensesClient;
    }

    return false;
}

//dynamically loaded js config as we need to do this to get custom components in.
export function getClientConfig(config) {
    if(config) {
        return get(clientConfig, config);
    }

    return clientConfig;
}

export function loadClientConfig(client, callback) {
    clientConfigLoaded = false;

    import(`../../../clients/${client}/Resources/js/client.js`).then(({ default: clientData }) => {
        clientConfig = clientData;
        clientConfigLoaded = true;
        callback(clientConfig);
        clientConfigCallbacks.forEach(callback => callback(clientConfig));
    });
}

//use callback system as computed things break my brain atm.
export function onClientConfigLoad(callback) {
    if(clientConfigLoaded) {
        callback(clientConfig);
    }
    else {
        clientConfigCallbacks.push(callback);
    }
}

export function getClientConfigTable(table, fields) {
    if(clientConfig && clientConfig.tables && clientConfig.tables[table]) {
        return clientConfig.tables[table](fields);
    }
    return fields;
}

export function getClientConfigForm(form, fields, model, context = {}) {
    if(clientConfig && clientConfig.forms && clientConfig.forms[form]) {
        return clientConfig.forms[form](fields, model, context);
    }

    return fields;
}

export function getClientConfigTableTemplate(table) {
    if(clientConfig && clientConfig.tableTemplates && clientConfig.tableTemplates[table]) {
        return clientConfig.tableTemplates[table];
    }

    return [];
}
