import DOMPurify from "dompurify";

export default function (content) {
    // Trim trailing whitespace
    content = content.trim();

    // Use DOMPurify to sanitise the content
    let sanitisedContent = DOMPurify.sanitize(content);

    // Remove &nbsp; characters
    sanitisedContent = sanitisedContent.replace(/&nbsp;/g, " ");

    return sanitisedContent;
}
