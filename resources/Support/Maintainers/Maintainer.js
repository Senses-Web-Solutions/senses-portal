import pluralize from 'pluralize';
import { startCase, forIn, kebabCase, camelCase } from 'lodash-es';
import useEcho from '../useEcho';

const echo = useEcho();

export default class Maintainer {
    constructor(model, data = {}, hasFiles=false, hasComments=false) {
        this.model = model; // task
        this.pluralModel = pluralize(model); // tasks
        this.kebabModel = kebabCase(model);
        this.camelModel = camelCase(model);
        this.modelNamespace = startCase(this.pluralModel).replaceAll(' ', ''); // Tasks
        this.modelClass = startCase(this.model).replaceAll(' ', ''); // Task
        this.data = data;

        echo.private(`${this.pluralModel}.${this.data.id}.main`).listen(
            `${this.modelNamespace}\\${this.modelClass}Updated`,
            this.update.bind(this)
        );

        if (hasFiles) {
            echo.private(`${this.pluralModel}.${this.data.id}.file-stats`).listen(
                `Files\\${this.modelClass}FileStatsUpdated`,
                this.fileStatsUpdate.bind(this)
            );
        }

        if (hasComments) {
            echo.private(`${this.pluralModel}.${this.data.id}.comment-stats`).listen(
                `Comments\\${this.modelClass}CommentStatsUpdated`,
                this.commentStatsUpdate.bind(this)
            );
        }
    }

    update(response) {
        forIn(response[this.camelModel], (value, key) => {
            if (
                typeof value === 'object' &&
                !Array.isArray(value) &&
                value !== null
            ) {
                forIn(value, (subValue, subKey) => {
                    if (this.data[key]) {
                        // console.log(this.data[key], subValue);
                        this.data[key][subKey] = subValue;
                    }
                });
            } else {
                this.data[key] = value;
            }
        });
    }

    commentStatsUpdate (payload) {
        this.data.comments_count = payload.commentsCount;
    }

    fileStatsUpdate (payload) {
        this.data.files_count = payload.filesCount;
    }

    destroy() {
        echo.leave(`${this.pluralModel}.${this.data.id}.main`);
    }
}
