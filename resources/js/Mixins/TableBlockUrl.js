import pluralize from 'pluralize';

export default {
    computed: {
        url() {
            var url = '/api/v2/table-block-data/' + this.type + '/' + this.modelType;
            var filters = this.filters;
            if (this.filters) {
                var filterArray = [];
                var filterKeys = [
                    'company',
                    'department',
                    'depot',
                    'task_type',
                    'work_stream',
                    'work_package',
                    'project',
                    'asset_type',
                    'user',
                ];

                filterKeys.forEach((filter) => {
                    var pluralFilter = pluralize(filter);
                    if (
                        filters[filter] &&
                        filters[filter] !== null
                    ) {
                        filterArray.push(
                            'filters[' + filter + ']=' + filters[filter]
                        );
                    } else if (
                        filters[pluralFilter] &&
                        filters[pluralFilter] !== null &&
                        Object.keys(filters[pluralFilter]).length > 0
                    ) {
                        filterArray.push(
                            'filters[' +
                                pluralFilter +
                                ']=' +
                                filters[pluralFilter].join(',')
                        );
                    }
                });

                if (filters['date'] && filters['date'] !== null) {
                    filterArray.push('filters[date]=' + filters['date']);
                } else if (
                    filters['date_range'] &&
                    filters['date_range'] !== null
                ) {
                    filterArray.push(
                        'filters[date_range]=1' //this is for validation, I can't pass in date_range as an object like I could before the SensesTable changes
                    );
                    filterArray.push(
                        'filters[start_date]=' + filters['date_range']['start_date']
                    );
                    filterArray.push(
                        'filters[end_date]=' + filters['date_range']['end_date']
                    );
                }
                var filterString = filterArray.join('&');
                return filterString == '' ? url : url + '?' + filterString;
            }

            return url;
        },
    },
};
