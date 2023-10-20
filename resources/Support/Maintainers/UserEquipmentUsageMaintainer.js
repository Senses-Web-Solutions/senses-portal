import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    //todo lots of duplicated code in maintainers, combine??
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`users.${this.id}.equipment-usages`)
            .listen(
                'EquipmentUsages\\EquipmentUsageCreated',
                this.userEquipmentUsageCreated.bind(this)
            )
            .listen(
                'EquipmentUsages\\EquipmentUsageUpdated',
                this.userEquipmentUsageUpdated.bind(this)
            )
            .listen(
                'EquipmentUsages\\EquipmentUsageDeleted',
                this.userEquipmentUsageDeleted.bind(this)
            );
    }

    userEquipmentUsageCreated(payload) {
        payload.equipmentUsage.highlight = true;
        this.rows.data = [payload.equipmentUsage].concat(this.rows.data);
    }

    userEquipmentUsageUpdated(payload) {
        payload.equipmentUsage.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.equipmentUsage.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.equipmentUsage;
        }
    }

    userEquipmentUsageDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.equipmentUsage.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`users.${this.id}.equipment-usages`);
    }
}
