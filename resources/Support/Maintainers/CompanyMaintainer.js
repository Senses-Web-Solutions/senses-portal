// import useEcho from '../useEcho';
import useEcho from '../useEcho';
import { forIn } from 'lodash-es';

// const echo = useEcho();
const echo = useEcho();

export default class {
    constructor(data) {
        this.data = data;
        echo.private(`companies.${this.data.id}.main`).listen("Companies\\CompanyUpdated", this.companyUpdate.bind(this));
        echo.private(
            `companies.${this.data.id}.comment-stats`).listen(
            'Comments\\CompanyCommentStatsUpdated',
            this.commentStatsUpdate.bind(this)
        );
        echo.private(
            `companies.${this.data.id}.quote-stats`).listen(
            'Quotes\\CompanyQuoteStatsUpdated',
            this.quoteStatsUpdate.bind(this)
        );
        echo.private(
            `companies.${this.data.id}.file-stats`).listen(
            'Files\\CompanyFileStatsUpdated',
            this.fileStatsUpdate.bind(this)
        );
    }

    companyUpdate (payload) {
        forIn(payload.company, (value, key) => {
            this.data[key] = value;
        });
    }

    commentStatsUpdate (payload) {
        this.data.comments_count = payload.commentsCount;
    }

    quoteStatsUpdate (payload) {
        this.data.quotes_total = payload.quotesTotal;
        this.data.quotes_approved_count = payload.quotesApprovedCount;
        this.data.quotes_pending_count = payload.quotesPendingCount;
    }

    fileStatsUpdate (payload) {
        this.data.files_count = payload.filesCount;
    }

    destroy () {
        echo.leave(`companies.${this.data.id}.main`);
        echo.leave(`companies.${this.data.id}.comment-stats`);
        echo.leave(`companies.${this.data.id}.quote-stats`);
        echo.leave(`companies.${this.data.id}.file-stats`);
    }
}
