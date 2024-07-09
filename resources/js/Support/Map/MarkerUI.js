/* eslint-disable camelcase */
/* eslint-disable max-len */
import { parseISO, format } from 'date-fns';

export default {
    methods: {
        /**
         *
         * @param {array} arr - Either markerData.lines or markerData.render_lines
         * @returns {string} Returns a string of HTML to be used in the popup
         */
        generatePopupHTML(arr, item = null) {
            let html = '';
            const htmlGenerators = {
                text: (item) =>
                    `<div class="mb-1 flex justify-between">${
                        item.label
                            ? `<span class="text-gray-600">${item.label}:</span>`
                            : ''
                    } ${item.text}</div>`,
                address: (item) =>
                    `<div class="mb-1 flex justify-between">${item.address.join(
                        ', '
                    )}</div>`,
                // eslint-disable-next-line no-unused-vars
                divider: (item) => (html === '' ? '' : `<hr class="my-2" />`),
                datetime: (item) =>
                    `<div class="mb-1 flex justify-between"><span class="text-gray-600">${item.label}:</span> ${item.value}</div>`,
                duration: (item) =>
                    `<div class="mb-1 flex justify-between"><span class="text-gray-600">${item.label}:</span> ${item.value} Mins</div>`,
                distance: (item) => {
                    item.value = (item.value / 1609.344).toFixed(2);
                    return `<div class="mb-1 flex justify-between"><span class="text-gray-600">${item.label}:</span> ${item.value} Miles</div>`;
                },
            };

            arr.forEach((item) => {
                if (
                    item.value == null ||
                    (['datetime', 'duration'].includes(item.type) &&
                        item.value === 0)
                ) {
                    item.value = 0;
                }

                if (
                    item.type === 'datetime' &&
                    typeof item.value === 'string' &&
                    !item.value.includes('/')
                ) {
                    item.value = format(
                        parseISO(item.value),
                        'dd/MM/yyyy HH:mm'
                    );
                }

                const generateHtml = htmlGenerators[item.type];
                if (generateHtml) {
                    html += generateHtml(item);
                }
            });

            if (item !== null) {
                if (item.resource_type === 'asset') {
                    html += `<div class="mb-1 flex justify-between"><span class="text-gray-600">Asset:</span> ${item.resource.registration}</div>`;

                    html += `<div class="mb-1 flex justify-between"><span class="text-gray-600">Asset ID:</span> ${item.resource_id}</div>`;
                } else if (item.resource_type === 'user') {
                    html += `<div class="mb-1 flex justify-between"><span class="text-gray-600">User:</span> ${item.resource.full_name}</div>`;

                    html += `<div class="mb-1 flex justify-between"><span class="text-gray-600">User ID:</span> ${item.resource_id}</div>`;
                }
            }

            return html;
        },
    },
};
