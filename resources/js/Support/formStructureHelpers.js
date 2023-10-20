export function addFieldGroup(structure, title, description, fields=[]) {
    return structure.push({title: title, description: description, fields: fields});
}

export function getFieldGroupByTitle(title, structure) {
    return structure.find((fieldGroup) => fieldGroup.title === title);
}

export function addFieldToFieldGroup(field, fieldGroupTitle, structure) {
    const fieldGroup = getFieldGroupByTitle(fieldGroupTitle, structure);
    if (fieldGroup) {
        fieldGroup.fields.push(field);
    }
}

export function addFieldsToFieldGroup(fields, fieldGroupTitle, structure) {
    const fieldGroup = getFieldGroupByTitle(fieldGroupTitle, structure);
    if (fieldGroup) {
        fields.forEach((field) => fieldGroup.fields.push(field));
    }
}

export function getField(key, structure) {
    for (let i = 0; i < structure.length; i += 1) {
        const fieldGroup = structure[i];

        const field = fieldGroup.fields.find((f) => f.key === key);

        if (field) {
            return field;
        }
    }

    return null;
}

export function deleteField(key, structure) {
    return Object.values(structure).forEach((fieldGroup) => {
        fieldGroup.fields = fieldGroup.fields.filter(
            (field) => field.key !== key
        );
    });
}

export function deleteFields(keys, structure) {
    keys.forEach((key) => {
        deleteField(key, structure);
    })
}

export function deleteFieldGroup(title, structure) {
    const fieldGroupIndex = structure.findIndex(
        (fieldGroup) => fieldGroup.title === title
    );

    structure.splice(fieldGroupIndex, 1);
}
