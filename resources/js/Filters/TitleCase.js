import { startCase, toLower } from 'lodash-es';
export default function (string) {
    //lodash doesn't have titleCase, but startCase and toLower cover it, also convert - and _ to space for convenience
    if (!string) {
        return '';
    }

    return startCase(toLower(string.replaceAll(/[\-_]/g, ' ')));
}
