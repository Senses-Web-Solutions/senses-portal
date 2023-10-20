import { setupDevtoolsPlugin } from '@vue/devtools-api';
import { watch } from 'vue';

// https://devtools.vuejs.org/plugin/api-reference.html#sendinspectortree
export default function (app, asides) {
    setupDevtoolsPlugin({
        // Let devtools know about the plugin
        app,
        id: 'uk.co.senses.asides',
        label: 'Senses Asides',
        packageName: 'asides',
        homepage: 'https://senses.co.uk',
        logo: 'https://avatars.githubusercontent.com/u/63702205?s=200&v=4',
        componentStateTypes: [
            'Asides'
        ]
    }, api => {
        let selectedAside = null;
        // register an "inspector"
        api.addInspector({
            id: 'asides-inspector',
            label: 'Asides',
            icon: 'view_sidebar',
            treeFilterPlaceholder: 'Search for an aside...',
            actions: [
                {
                    icon: 'sync_alt',
                    tooltip: 'Go to aside',
                    action: () => {
                        api.highlightElement(selectedAside);
                    }
                }
            ]
        });

        // this generates the tree of items that devtools will use.
        // we're going to map all the asides into objects with an ID and label.
        const getInspectorTree = () => ([
            {
                id: 'senses-aside',
                label: 'Asides',
                children: asides.all.map(item => ({
                    id: item.data.key,
                    label: item.name
                }))
            }
        ]);

        // When devtools wants to load the tree
        // we need to give it the data.
        api.on.getInspectorTree(payload => {
            if(payload.inspectorId === 'asides-inspector') {
                payload.rootNodes = getInspectorTree()
            }
        });

        // Watch for any changes in the asides and tell
        // devtools it needs to update its data.
        watch(asides.all, () => {
            api.sendInspectorTree('asides-inspector');
            api.sendInspectorState('asides-inspector');
        }, { deep: true });

        // When one of the items is selected, we need to populate
        // the right side with data.
        api.on.getInspectorState(async (payload, ctx) => {
            if (payload.inspectorId === 'asides-inspector') {
                const a = asides.findByKey(payload.nodeId);
                const instances = await api.getComponentInstances(app);

                // Keys are unique, we can use it to locate our form component.
                selectedAside = instances.find(item => item.props.data?.key === payload.nodeId);

                if(selectedAside) {
                    api.highlightElement(selectedAside);
                } else {
                    api.unhighlightElement();
                }

                if (a) {
                    payload.state = {
                        // Map an object with key: value to { key, value }
                        'Aside Data': Object.keys(a.data).map(key => {
                            return {
                                key,
                                value: a.data[key]
                            }
                        })
                    }
                }
            }
        })
    })
}
