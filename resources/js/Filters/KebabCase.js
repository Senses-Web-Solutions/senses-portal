import { kebabCase } from 'lodash-es';
export default function (string) {
    //lodash doesn't have titleCase, but startCase and toLower cover it, also convert - and _ to space for convenience
    return kebabCase(string);
}
