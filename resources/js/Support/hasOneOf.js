export default function hasOneOf(ob, keys) {
    return keys.some((key) => {
        return Object.prototype.hasOwnProperty.call(ob, key) && ob[key] !== null;
    });
}
