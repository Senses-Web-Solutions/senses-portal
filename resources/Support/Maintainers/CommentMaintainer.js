import Pluralize from 'pluralize';
import { nextTick } from 'vue';
import highlight from '../highlight';
import useEcho from '../useEcho';

// const echo = useEcho();
const echo = useEcho();
export default class {
    constructor(data, commentableType, commentableId, publicOnly) {
        this.data = data;
        this.commentableType = commentableType;
        this.commentableId = commentableId;
        this.data.forEach((comment, index) => {
            this.initialiseCommentListener(comment, index);
        });
        echo.private(
            `${Pluralize(this.commentableType)}.${this.commentableId}.comments`
        ).listen('Comments\\CommentCreated', (pusherEvent) => {
            if(publicOnly && pusherEvent.comment.type !== 'public'){
                return;
            }
            const index = this.data.push(pusherEvent.comment) - 1;
            nextTick(() => {
                this.highlightComment(pusherEvent.comment.id);
                this.data[index].comments = [];
                this.initialiseCommentListener(pusherEvent.comment, index);
            });
        });
        echo.private(
            `${Pluralize(this.commentableType)}.${this.commentableId}.comments`
        ).listen('Comments\\CommentUpdated', (pusherEvent) => {
            if(publicOnly && pusherEvent.comment.type !== 'public'){
                return;
            }
            const index = this.data.findIndex(
                (item) => item.id === pusherEvent.comment.id
            );
            if (index > -1 && this.data[index]) {
                this.data[index] = pusherEvent.comment;
                nextTick(() => {
                    this.highlightComment(pusherEvent.comment.id);
                });
            }
        });
        echo.private(
            `${Pluralize(this.commentableType)}.${this.commentableId}.comments`
        ).listen('Comments\\CommentDeleted', (pusherEvent) => {
            const index = this.data.findIndex(
                (item) => item.id === pusherEvent.comment.id
            );
            if (index > -1 && this.data[index]) {
                this.data.splice(index, 1);
            }
        });
    }

    initialiseCommentListener(comment, index) {
        echo.private(`comments.${comment.id}.comments`).listen(
            'Comments\\CommentCreated',
            (pusherEvent) => {
                this.data[index].comments.push(pusherEvent.comment);
                nextTick(() => {
                    this.highlightComment(pusherEvent.comment.commentable_id);
                });
            }
        );
        echo.private(`comments.${comment.id}.comments`).listen(
            'Comments\\CommentUpdated',
            (pusherEvent) => {
                const childIndex = this.data[index].comments.findIndex(
                    (item) => item.id === pusherEvent.comment.id
                );
                if (childIndex > -1 && this.data[index].comments[childIndex]) {
                    this.data[index].comments[childIndex] = pusherEvent.comment;
                    nextTick(() => {
                        this.highlightComment(pusherEvent.comment.commentable_id);
                    });
                }
            }
        );
        echo.private(`comments.${comment.id}.comments`).listen(
            'Comments\\CommentDeleted',
            (pusherEvent) => {
                const childIndex = this.data[index].comments.findIndex(
                    (item) => item.id === pusherEvent.comment.id
                );
                if (childIndex > -1 && this.data[index].comments[childIndex]) {
                    this.data[index].comments.splice(childIndex, 1);
                }
            }
        );
    }

    destroy() {
        echo.leave(
            `${Pluralize(this.commentableType)}.${this.commentableId}.comments`
        );
    }

    highlightComment(commentId) {
        highlight(document.querySelector(`#comment-${commentId}`));
        highlight(document.querySelector(`#comment-${commentId}-bottom-piece-1`));
        highlight(document.querySelector(`#comment-${commentId}-bottom-piece-2`));
    }
}
