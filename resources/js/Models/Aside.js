export default class Aside {
    constructor(name, data = {}) {
        this.name = name;
        this.data = data;
        this.confirmClose = this.data.confirmClose || false;
    }

    requireConfirmationToClose(value = true) {
        this.confirmClose = value;
    }
}