import useEcho from '../useEcho';
import EventHub from '../EventHub';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.task-instructions`).listen(
            'AssignmentGroups\\AssignmentGroupCreated',
            this.taskTaskInstructionCreated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.task-instructions`).listen(
            'AssignmentGroups\\AssignmentGroupUpdated',
            this.taskTaskInstructionUpdated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.task-instructions`).listen(
            'AssignmentGroups\\AssignmentGroupDeleted',
            this.taskTaskInstructionDeleted.bind(this)
        );
        // setTimeout(function(){
        //     console.log(data);
        // }, 5000);
    }

    taskTaskInstructionCreated(payload) {
        console.log('created', payload.assignmentGroup);
        if(!this.isTaskInstruction(payload)) {
            return;
        }

        payload.assignmentGroup.highlight = true;
        if(this.rows === null) {
            EventHub.emit('task-task-instruction-created');
            return;
        }

        this.rows.data = [payload.assignmentGroup].concat(this.rows.data);
    }

    taskTaskInstructionUpdated(payload) {
        console.log('updated', payload.assignmentGroup);

        if(!this.isTaskInstruction(payload)) {
            if (payload.assignmentGroup.instruction) {
                this.taskTaskInstructionDeleted(payload);
                return;
            }
            return;
        }

        payload.assignmentGroup.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assignmentGroup.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assignmentGroup;
        }
    }

    taskTaskInstructionDeleted(payload) {
        if(!this.isTaskInstruction(payload)) {
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
        echo.leave(`tasks.${this.id}.task-instructions`);
    }

    isTaskInstruction(payload) {
        return (payload.assignmentGroup.assignment_type === 'instruction')
    }
}
