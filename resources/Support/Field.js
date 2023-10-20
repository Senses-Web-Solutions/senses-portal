// Nicer way of defining fields for SensesForm/FormGroup
/*
    Field.text('first_name', {
        autocomplete: 'no'
    })
*/

const Field = {};

Field.create = (type, key, data = {}) => ({
    type,
    key,
    ...data,
});

Field.text = (key, data = {}) => Field.create('text', key, data);
Field.textarea = (key, data = {}) => Field.create('textarea', key, data);
Field.richTextarea = (key, data = {}) => Field.create('rich-textarea', key, data);
Field.toggle = (key, data = {}) => Field.create('toggle', key, data);
Field.email = (key, data = {}) => Field.create('email', key, data);
Field.number = (key, data = {}) => Field.create('number', key, data);
Field.colour = (key, data = {}) => Field.create('colour', key, data);
Field.selectBasic = (key, data = {}) => Field.create('select-basic', key, data);
Field.select = (key, data = {}) => Field.create('select', key, data);
Field.selectSearch = (key, data = {}) => Field.create('select-search', key, data);
Field.file = (key, data = {}) => Field.create('file', key, data);
Field.filePicker = (key, data = {}) => Field.create('file-picker', key, data);
Field.time = (key, data = {}) => Field.create('time-picker', key, data);
Field.date = (key, data = {}) => Field.create('date-picker', key, data);
Field.dateTime = (key, data = {}) => Field.create('date-time-picker', key, data);
Field.dateRange = (key, data = {}) => Field.create('date-range-picker', key, data);
Field.dateTimeRange = (key, data = {}) => Field.create('date-time-range-picker', key, data);

Field.template = (key, data = {}) => Field.create('template', key, data);

export default Field;
