import StateMachine from './StateMachine';

class FormState extends StateMachine 
{
    initial() {
        return [ FormState.IDLE ];
    }
}

FormState.LOADING = 'loading';
FormState.IDLE = 'idle';
FormState.ERROR = 'error';
FormState.SUBMITTING = 'submitting';

export default FormState;