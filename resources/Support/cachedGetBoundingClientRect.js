
export default function cachedGetBoundingClientRect(element) {
    if(!element.boundingClientRect) {
        element.boundingClientRect = element.getBoundingClientRect();
    }
    return element.boundingClientRect;
}