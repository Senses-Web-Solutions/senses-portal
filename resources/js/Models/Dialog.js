export default class Dialog {
    constructor(type, data = {}, resolve = () => {}) {
        this.type = type;
        this.data = data;
        this.resolve = resolve;
    }
}