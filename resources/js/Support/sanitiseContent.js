import DOMPurify from "dompurify";

export default function (content) {
    // Sanitize the content, remove any extra whitespace, check for empty content, check for dangerous content
    content = content.trim();
    content = content.replace(/ +(?= )/g, "");
    // Use DOMPurify to sanitise the content
    return DOMPurify.sanitize(content);
}
