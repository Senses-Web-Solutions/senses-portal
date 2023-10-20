import { isNumber } from 'lodash-es';

export default (string, precision = 2) => {
    let amount = 0;
    if (string !== null && string !== undefined && isNumber(string)) {
        amount = string;
    } else if (string !== null && string !== undefined) {
        amount = parseFloat(string);
    }

    if (amount < 0) {
        amount = amount * -1;
        return ('- £' + amount.toFixed(precision).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
    }

    return amount || amount == 0 || amount == 1 ? '£' + amount.toFixed(precision).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '';
};
