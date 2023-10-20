export default function processFormRelationships (fields, form) {
    // Relationships need their models converting to IDs
    let flatFields = [];
    // Extract all the field definitions into a flat array
    fields.forEach(
        (fieldGroup) => (flatFields = flatFields.concat(fieldGroup.fields))
    );

    // Filter all the fields for relationships and then loop through
    flatFields
        .filter((field) => field.relationship)
        .forEach((field) => {
            // If it's a single relationship
            if ((field.relationship === 'single' || field.relationship === 'single-polymorphic') && field.relationshipKey) {
                // Set the relationshipKey to the id

                form[field.relationshipKey] = form[field.key]?.id ?? null;

                if (field.relationship === 'single-polymorphic') {
                    form[field.relationshipKey.replace('_id', '_type')] = form[field.key]?.object;
                }
                // If it's a multiple relationship
            } else if (
                field.relationship === 'multiple' &&
                field.relationshipKey
            ) {

                if (!Object.keys(form).includes(field.key)) {
                    // this is for company form because it doesn't have the correct keys on create
                    form[field.key] = [];
                }

                let fieldData = form[field.key];
                if (!fieldData) {
                    return;
                }
                if (!Array.isArray(fieldData)) {
                    // wrap in array if not already, to allow single values to be used with relation multiple flag.
                    fieldData = [fieldData];
                }

                // Set the relationshipKey to a mapped array of ids
                form[field.relationshipKey] = fieldData.map((item) => item.id);

            }
        });
    return form;
}
