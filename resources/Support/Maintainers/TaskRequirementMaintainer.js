import useEcho from '../useEcho';
import highlight from "../highlight";
import {nextTick} from 'vue'

const echo = useEcho();
export default class {
    constructor(data, id) {
        this.data = data;
        this.id = id;



        echo.private(`tasks.${this.id}.task-requirements`).listen(
            'Requirements\\TaskRequirementCreated',
            (pusherEvent) => {
                this.data.push(pusherEvent.taskRequirement);
                nextTick(() => {
                    this.highlightTaskRequirement(pusherEvent.taskRequirement.id);
                });
            }
        ).listen(
            'Requirements\\TaskRequirementUpdated',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.taskRequirement.id);
                if (index > -1 && this.data[index]) {
                    this.data[index] = pusherEvent.taskRequirement;
                    nextTick(() => {
                        this.highlightTaskRequirement(pusherEvent.taskRequirement.id);
                    });
                }
            }
        ).listen(
            'Requirements\\TaskRequirementDeleted',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.taskRequirement.id);
                if (index > -1 && this.data[index]) {
                    this.data.splice(index, 1);
                }
            }
        );

    }

    destroy(){
        echo.leave(`tasks.${this.id}.task-requirements`);
    }

    highlightTaskRequirement(requirementId) {
        highlight(document.querySelector("[data-collapse-id='task']"));
        highlight(document.querySelector("[data-task-requirement-id='" + requirementId + "']"));
    }

}
