import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    //todo lots of duplicated code in maintainers, combine??
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`tasks.${this.id}.equipment-usages`)
            .listen(
                'EquipmentUsages\\EquipmentUsageCreated',
                this.equipmentUsageCreated.bind(this)
            )
            .listen(
                'EquipmentUsages\\EquipmentUsageUpdated',
                this.equipmentUsageUpdated.bind(this)
            )
            .listen(
                'EquipmentUsages\\EquipmentUsageDeleted',
                this.equipmentUsageDeleted.bind(this)
            );
    }

    equipmentUsageCreated(payload) {
        console.log('a');
        payload.equipmentUsage.highlight = true;
        this.rows.data = [payload.equipmentUsage].concat(this.rows.data);
    }

    equipmentUsageUpdated(payload) {
        payload.equipmentUsage.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.equipmentUsage.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.equipmentUsage;
        }
    }

    equipmentUsageDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.equipmentUsage.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.equipment-usages`);
    }
}
