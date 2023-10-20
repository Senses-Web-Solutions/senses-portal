import StateMachine from './StateMachine';

class AjaxSelectState extends StateMachine 
{
    initial() {
        return [ AjaxSelectState.IDLE ];
    }
}

AjaxSelectState.LOADING = 'loading';
AjaxSelectState.IDLE = 'idle';
AjaxSelectState.ERROR = 'error';

export default AjaxSelectState;