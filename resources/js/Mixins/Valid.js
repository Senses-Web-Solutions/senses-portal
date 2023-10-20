export default {
    data () {
        return {
            data: null,
        };
    },
    props: {
        valid: {
            type: Boolean,
            default: null,
        }
    },
    methods: {
        isValid (field, errors) {
            if (this.valid !== null) {
                return this.valid;
            }
            if (errors) {
                this.data = errors;
            }

            if (this.data && this.data.errors) {
                return !(this.data.errors[field]);
            }

            return true;
        }
    }
}
