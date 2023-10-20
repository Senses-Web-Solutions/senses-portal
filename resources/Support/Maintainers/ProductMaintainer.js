import useEcho from '../useEcho';
import Maintainer from './Maintainer';

const echo = useEcho();

export default class extends Maintainer {
    constructor(data) {
        super('product', data);
        this.data = data;
        echo.private(`products.${this.data.id}.main`).listen(
            'Products\\ProductUpdated',
            this.productUpdate.bind(this)
        );
        echo.private(`products.${this.data.id}.stock-levels`).listen(
            'Products\\ProductStockLevelsUpdated',
            this.productStockLevelsUpdate.bind(this)
        );
    }

    productUpdate(payload) {
        this.data = payload.product;
    }

    productStockLevelsUpdate(payload) {
        this.data.stock_remaining = payload.stockRemaining;
        this.data.available_stock_remaining = payload.availableStockRemaining;
        this.data.stock_required = payload.stockRequired;
        this.data.quantity_ordered = payload.quantityOrdered;
        this.data.available_quantity_ordered = payload.availableQuantityOrdered;
        this.data.quantity_adjusted = payload.quantityAdjusted;
    }

    destroy() {
        echo.leave(`products.${this.data.id}.main`);
        echo.leave(`products.${this.data.id}.stock-levels`);
    }
}
