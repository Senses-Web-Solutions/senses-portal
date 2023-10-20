import useEcho from '../useEcho';
import EventHub from '../EventHub';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.assignment-groups`).listen(
            'AssignmentGroups\\AssignmentGroupCreated',
            this.taskAssignmentGroupCreated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.assignment-groups`).listen(
            'AssignmentGroups\\AssignmentGroupUpdated',
            this.taskAssignmentGroupUpdated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.assignment-groups`).listen(
            'AssignmentGroups\\AssignmentGroupDeleted',
            this.taskAssignmentGroupDeleted.bind(this)
        );
        // setTimeout(function(){
        //     console.log(data);
        // }, 5000);
    }

    taskAssignmentGroupCreated(payload) {
        if(this.isTaskInstruction(payload)) {
            return;
        }

        // console.log(payload.assignmentGroup);
        payload.assignmentGroup.highlight = true;
        if(this.rows === null) {
            EventHub.emit('task-assignment-group-created');
            return;
        }
        this.rows.data = [payload.assignmentGroup].concat(this.rows.data);
    }

    taskAssignmentGroupUpdated(payload) {
        if(this.isTaskInstruction(payload)) {
            return;
        }

        // console.log(payload.assignmentGroup);
        payload.assignmentGroup.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assignmentGroup.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assignmentGroup;
        }
    }

    taskAssignmentGroupDeleted(payload) {
        if(this.isTaskInstruction(payload)) {
            return;
        }
        
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assignmentGroup.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.assignment-groups`);
    }

    isTaskInstruction(payload) {
        return (payload.assignmentGroup.assignment_type === 'instruction')
    }
}
