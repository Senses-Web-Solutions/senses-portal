import StateMachine from './StateMachine';

class AssignmentGroupTimePickerState extends StateMachine 
{
    initial() {
        return [ AssignmentGroupTimePickerState.IDLE ];
    }
}

AssignmentGroupTimePickerState.SELECTING_DATE = 'selecting-date';
AssignmentGroupTimePickerState.IDLE = 'idle';

export default AssignmentGroupTimePickerState;