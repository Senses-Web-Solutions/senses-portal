import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    //todo lots of duplicated code in maintainers, combine??
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`users.${this.id}.user-reviews`)
            .listen(
                'UserReviews\\UserReviewCreated',
                this.userUserReviewCreated.bind(this)
            )
            .listen(
                'UserReviews\\UserReviewUpdated',
                this.userUserReviewUpdated.bind(this)
            )
            .listen(
                'UserReviews\\UserReviewDeleted',
                this.userUserReviewDeleted.bind(this)
            );
    }

    userUserReviewCreated(payload) {
        payload.userReview.highlight = true;
        this.rows.data = [payload.userReview].concat(this.rows.data);
    }

    userUserReviewUpdated(payload) {
        payload.userReview.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.userReview.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.userReview;
        }
    }

    userUserReviewDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.userReview.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`users.${this.id}.user-reviews`);
    }
}
