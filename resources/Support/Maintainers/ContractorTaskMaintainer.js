import useEcho from '../useEcho';
import highlight from "../highlight";
import { nextTick } from 'vue'
const echo = useEcho();
export default class {
    constructor(data) {
        this.data = data;

        echo.private(`task-contractors.${this.data.id}.main`).listen(
            'TaskContractors\\TaskContractorCreated',
            (pusherEvent) => {
                this.data.push(pusherEvent.taskContractor);
                nextTick(() => {
                    this.highlightTaskContractor(pusherEvent.taskContractor.id);
                });
            }
        ).listen(
            'TaskContractors\\TaskContractorUpdated',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.taskContractor.id);
                if (index > -1 && this.data[index]) {
                    this.data[index] = pusherEvent.taskContractor;
                    nextTick(() => {
                        this.highlightTaskContractor(pusherEvent.taskContractor.id);
                    });
                }
            }
        ).listen(
            'TaskContractors\\TaskContractorDeleted',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.taskContractor.id);
                if (index > -1 && this.data[index]) {
                    this.data.splice(index, 1);
                }
            }
        );
    }

    destroy(){
        echo.leave(`task-contractors.${this.data.id}.main`);
    }

    highlightTaskContractor(taskContractorID){
        highlight(document.querySelector("[data-task-contractor-id='" + taskContractorID + "']"));
    }

}
