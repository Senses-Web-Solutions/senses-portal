import useEcho from '../useEcho';
import highlight from "../highlight";
import {nextTick} from 'vue'

const echo = useEcho();
export default class {
    constructor(data, id) {
        this.data = data;
        this.id = id;

        echo.private(`tasks.${this.id}.assignment-group-requirements`).listen(
            'Requirements\\AssignmentGroupRequirementCreated',
            (pusherEvent) => {
                this.data.push(pusherEvent.assignmentGroupRequirement);
                nextTick(() => {
                    this.highlightAssignmentGroupRequirement(pusherEvent.assignmentGroupRequirement.id);
                });
            }
        ).listen(
            'Requirements\\AssignmentGroupRequirementUpdated',
            (pusherEvent) => {
                if(this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id] && this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements){
                    const index = this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements.findIndex(item => item.id === pusherEvent.assignmentGroupRequirement.id);
                    if (index > -1 && this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements[index]) {
                        this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements[index] = pusherEvent.assignmentGroupRequirement;
                        nextTick(() => {
                            this.highlightAssignmentGroupRequirement(pusherEvent.assignmentGroupRequirement.id);
                        });
                    }
                }

            }
        ).listen(
            'Requirements\\AssignmentGroupRequirementDeleted',
            (pusherEvent) => {
                if(this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id] && this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements){
                    const index = this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements.findIndex(item => item.id === pusherEvent.assignmentGroupRequirement.id);
                    if (index > -1 && this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements[index]) {
                        this.data[pusherEvent.assignmentGroupRequirement.assignment_group_id].assignment_group_requirements.splice(index, 1);
                    }
                }
            }
        );
    }

    destroy(){
        echo.leave(`tasks.${this.id}.assignment-group-requirements`);
    }

    highlightAssignmentGroupRequirement(requirementId) {
        highlight(document.querySelector("[data-collapse-id='assignment-group']"));
        highlight(document.querySelector("[data-assignment-group-requirement-id='" + requirementId + "']"));
    }

}
