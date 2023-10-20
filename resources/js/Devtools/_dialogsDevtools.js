import { setupDevtoolsPlugin } from '@vue/devtools-api';
import { watch } from 'vue';

// https://devtools.vuejs.org/plugin/api-reference.html#sendinspectortree
export default function (app, dialogs) {
    setupDevtoolsPlugin({
        // Let devtools know about the plugin
        app,
        id: 'uk.co.senses.dialogs',
        label: 'Senses Dialogs',
        packageName: 'dialogs',
        homepage: 'https://senses.co.uk',
        logo: 'https://avatars.githubusercontent.com/u/63702205?s=200&v=4',
        componentStateTypes: [
            'Dialogs'
        ]
    }, api => {
        // register an "inspector"
        api.addInspector({
            id: 'dialogs-inspector',
            label: 'Dialogs',
            icon: 'view_sidebar',
            treeFilterPlaceholder: 'Search for a dialog...'
        });

        // this generates the tree of items that devtools will use.
        // we're going to map all the asides into objects with an ID and label.
        const getInspectorTree = () => ([
            {
                id: 'senses-dialog',
                label: 'Dialog'
            }
        ]);

        // When devtools wants to load the tree
        // we need to give it the data.
        api.on.getInspectorTree(payload => {
            if(payload.inspectorId === 'dialogs-inspector') {
                payload.rootNodes = getInspectorTree()
            }
        });

        // Watch for any changes in the asides and tell
        // devtools it needs to update its data.
        watch(() => dialogs.dialog, () => {
            api.sendInspectorTree('dialogs-inspector');
            api.sendInspectorState('dialogs-inspector');
        }, { deep: true });

        // When one of the items is selected, we need to populate
        // the right side with data.
        api.on.getInspectorState(async (payload, ctx) => {
            if (payload.inspectorId === 'dialogs-inspector') {
                if (dialogs.dialog) {
                    payload.state = {
                        // Map an object with key: value to { key, value }
                        'Dialog Data': Object.keys(dialogs.dialog).map(key => {
                            return {
                                key,
                                value: dialogs.dialog[key]
                            }
                        })
                    }
                } else {
                    payload.state = {
                        // Map an object with key: value to { key, value }
                        'Dialog Data': []
                    }
                }
            }
        })
    })
}
