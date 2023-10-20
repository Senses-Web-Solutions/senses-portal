import genKey from "./genKey";

// Generates a unique ID for an element if there isn't one already set.
export default function getUniqueElementIdentifier(element) {
    // if there's no element we cant ID it
    if (!element) {
        return 0;
    }

    // if we've already set the identifier, we can just return it
    if (element.dataset.seIdentifier) {
        return element.dataset.seIdentifier;
    }

    // generate a new identifier for the element
    const identifier = `se-${genKey()}`;
    // store it as a data attibute
    element.setAttribute('data-se-identifier', identifier);
    // return the identifier
    return identifier;
}