export default class Modal {
    constructor(name, data = {}) {
        this.name = name;
        this.data = data;
        this.size = this.data.size || "regular";
        this.confirmClose = this.data.confirmClose || false;
    }
}
