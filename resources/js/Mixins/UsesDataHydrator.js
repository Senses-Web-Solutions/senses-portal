import axios from 'axios';

export default (dataKey) => ({
    props: {
        url: {
            type: String,
        },
        data: {
            type: [Object, Array, null],
        }
    },
    methods: {
        async load() {
            if(this.data) {
                this[dataKey] = this.data;
            } else if (this.url) {
                const response = await axios.get(this.url);
                if(response.data.data) {
                    this[dataKey] = response.data.data;
                } else {
                    this[dataKey] = response.data;
                }
            } else {
                throw new Error('Your component needs to be provided with data or a url.')
            }
        }
    },
    created() {
        this.load();
    }
});